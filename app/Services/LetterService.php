<?php

namespace App\Services;

use App\Models\GeneratedDocument;
use App\Models\Letter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LetterService
{
    public function __construct(
        private readonly AuditReferenceFormatter $formatter,
        private readonly PdfGenerationService $pdfService,
    ) {
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function buildPayload(array $input): array
    {
        $refs = $input['daftar_referensi_audit'] ?? [];
        if (! is_array($refs)) {
            $refs = [];
        }
        $normalizedRefs = $this->formatter->normalizedTexts($refs);

        $cc = $input['daftar_tembusan'] ?? [];
        if (! is_array($cc)) {
            $cc = [];
        }
        $cc = array_values(array_filter(array_map(static fn ($v) => trim((string) $v), $cc), static fn ($v) => $v !== ''));

        return [
            'nomor_surat' => $this->str($input['nomor_surat'] ?? null),
            'tanggal_surat' => $this->str($input['tanggal_surat'] ?? null),
            'nama_pegawai' => $this->str($input['nama_pegawai'] ?? null),
            'nip' => $this->str($input['nip'] ?? null),
            'pangkat' => $this->str($input['pangkat'] ?? null),
            'golongan' => $this->str($input['golongan'] ?? null),
            'pangkat_golongan' => $this->str($input['pangkat_golongan'] ?? null),
            'jabatan' => $this->str($input['jabatan'] ?? null),
            'sapaan' => in_array(($input['sapaan'] ?? ''), ['Saudara', 'Saudari'], true) ? $input['sapaan'] : 'Saudara',
            'daftar_referensi_audit' => $normalizedRefs,
            'uraian_teguran' => $this->str($input['uraian_teguran'] ?? null),
            'instruksi' => $this->str($input['instruksi'] ?? null),
            'jabatan_pengirim' => $this->str($input['jabatan_pengirim'] ?? null),
            'hal' => $this->str($input['hal'] ?? 'Teguran Tertulis'),
            'jabatan_penandatangan' => $this->str($input['jabatan_penandatangan'] ?? null),
            'nama_penandatangan' => $this->str($input['nama_penandatangan'] ?? null),
            'daftar_tembusan' => $cc,
        ];
    }

    /**
     * @param  array<string, mixed>  $input
     */
    public function saveDraft(?Letter $letter, array $input): Letter
    {
        $payload = $this->buildPayload($input);

        $data = [
            'letter_number' => $payload['nomor_surat'] ?: null,
            'letter_date' => $payload['tanggal_surat'] ?: null,
            'status' => Letter::STATUS_DRAFT,
            'employee_id' => $input['employee_id'] ?? null,
            'audit_reference_group_id' => $input['audit_reference_group_id'] ?? null,
            'payload_json' => $payload,
        ];

        if ($letter) {
            if ($letter->isFinal()) {
                throw ValidationException::withMessages([
                    'letter' => 'Surat final tidak dapat diubah. Gunakan fitur duplikasi untuk membuat draft baru.',
                ]);
            }
            $letter->fill($data);
            $letter->save();

            return $letter->fresh();
        }

        return Letter::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array{letter:Letter,document:GeneratedDocument,preview_path:string}
     */
    public function renderPreview(Letter $letter, array $input): array
    {
        // Persist draft data first so preview is reproducible.
        $letter = $this->saveDraft($letter, $input);
        $this->validateForRender($letter);

        $rendered = $this->pdfService->render($letter);
        $this->pdfService->assertTwoPages($rendered);

        $disk = \Illuminate\Support\Facades\Storage::disk('letters');
        $previewPath = $letter->id.'/preview-latest.pdf';
        $disk->put($previewPath, $rendered['bytes']);

        $document = $letter->generatedDocuments()->updateOrCreate(
            ['file_path' => $previewPath],
            [
                'page_count' => $rendered['page_count'],
                'template_version' => PdfGenerationService::TEMPLATE_VERSION,
                'generated_at' => now(),
                'checksum' => hash('sha256', $rendered['bytes']),
            ]
        );

        return ['letter' => $letter->fresh(), 'document' => $document, 'preview_path' => $previewPath];
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array{letter:Letter,document:GeneratedDocument}
     */
    public function finalize(Letter $letter, array $input): array
    {
        if ($letter->isFinal()) {
            throw ValidationException::withMessages(['letter' => 'Surat ini sudah berstatus final.']);
        }

        $letter = $this->saveDraft($letter, $input);
        $this->validateForFinalization($letter);

        $rendered = $this->pdfService->render($letter);
        $this->pdfService->assertTwoPages($rendered);

        return DB::transaction(function () use ($letter, $rendered) {
            $path = $this->pdfService->storeFinal($letter, $rendered['bytes']);

            $document = $letter->generatedDocuments()->create([
                'file_path' => $path,
                'page_count' => $rendered['page_count'],
                'template_version' => PdfGenerationService::TEMPLATE_VERSION,
                'generated_at' => now(),
                'checksum' => hash('sha256', $rendered['bytes']),
            ]);

            $letter->forceFill(['status' => Letter::STATUS_FINAL])->save();

            // Keep preview record pointing to latest generated preview if any; final doc is separate.

            return ['letter' => $letter->fresh(), 'document' => $document];
        });
    }

    public function duplicate(Letter $letter): Letter
    {
        $payload = $letter->payload();
        $payload['nomor_surat'] = '';
        $payload['tanggal_surat'] = $payload['tanggal_surat'] ?? null;

        return Letter::query()->create([
            'letter_number' => null,
            'letter_date' => $payload['tanggal_surat'] ?: null,
            'status' => Letter::STATUS_DRAFT,
            'employee_id' => $letter->employee_id,
            'audit_reference_group_id' => $letter->audit_reference_group_id,
            'payload_json' => $payload,
        ]);
    }

    public function validateForRender(Letter $letter): void
    {
        $payload = $letter->payload();
        $errors = [];

        if (trim((string) ($payload['nama_pegawai'] ?? '')) === '') {
            $errors['nama_pegawai'] = 'Nama pegawai wajib diisi.';
        }
        $refs = $this->formatter->normalizedTexts($payload['daftar_referensi_audit'] ?? []);
        if (count($refs) < 1) {
            $errors['daftar_referensi_audit'] = 'Minimal satu referensi audit wajib diisi.';
        }
        if (trim((string) ($payload['uraian_teguran'] ?? '')) === '') {
            $errors['uraian_teguran'] = 'Uraian teguran (Atas) wajib diisi.';
        }
        if (trim((string) ($payload['jabatan'] ?? '')) === '') {
            $errors['jabatan'] = 'Jabatan pegawai wajib diisi.';
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
    }

    public function validateForFinalization(Letter $letter): void
    {
        $payload = $letter->payload();
        $errors = [];
        try {
            $this->validateForRender($letter);
        } catch (ValidationException $e) {
            $errors = $e->errors();
        }

        if (trim((string) ($payload['nomor_surat'] ?? '')) === '') {
            $errors['nomor_surat'] = 'Nomor surat wajib diisi sebelum finalisasi.';
        }
        if (trim((string) ($payload['tanggal_surat'] ?? '')) === '') {
            $errors['tanggal_surat'] = 'Tanggal surat wajib diisi sebelum finalisasi.';
        }
        $nip = trim((string) ($payload['nip'] ?? ''));
        if ($nip === '') {
            $errors['nip'] = 'NIP wajib diisi sebelum finalisasi.';
        } elseif (! preg_match('/^\d{18}$/', $nip)) {
            $errors['nip'] = 'NIP harus berupa 18 digit angka (teks).';
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function str(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        return trim((string) $value);
    }
}
