<?php

namespace App\Livewire\Letters;

use App\Models\AppSetting;
use App\Models\AuditReferenceGroup;
use App\Models\Employee;
use App\Models\Letter;
use App\Services\AuditReferenceFormatter;
use App\Services\LetterService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class CreateLetter extends Component
{
    public int $step = 1;

    public ?int $letterId = null;

    public ?int $employee_id = null;
    public ?int $audit_reference_group_id = null;

    public string $letter_number = '';
    public string $letter_date = '';
    public string $jabatan_pengirim = '';

    public string $nama_pegawai = '';
    public string $nip = '';
    public string $pangkat = '';
    public string $golongan = '';
    public string $jabatan = '';
    public string $sapaan = 'Saudara';

    /** @var array<int, string> */
    public array $daftar_referensi_audit = [''];

    public string $uraian_teguran = '';
    public string $instruksi = '';

    public string $jabatan_penandatangan = '';
    public string $nama_penandatangan = '';

    /** @var array<int, string> */
    public array $daftar_tembusan = [];

    public string $hal = 'Teguran Tertulis';

    public bool $isFinal = false;

    public function mount(?Letter $letter = null): void
    {
        $defaults = AppSetting::defaults();
        $this->letter_date = now()->toDateString();
        $this->jabatan_pengirim = (string) ($defaults['sender_title'] ?? '');
        $this->jabatan_penandatangan = (string) ($defaults['signatory_title'] ?? '');
        $this->nama_penandatangan = (string) ($defaults['signatory_name'] ?? '');
        $this->instruksi = (string) ($defaults['instruction_template'] ?? '');
        $this->hal = (string) ($defaults['subject'] ?? 'Teguran Tertulis');
        $cc = $defaults['cc_list'] ?? [];
        $this->daftar_tembusan = is_array($cc) && count($cc) ? array_values(array_map('strval', $cc)) : [''];

        if ($letter && $letter->exists) {
            $this->loadLetter($letter);
        }
    }

    protected function loadLetter(Letter $letter): void
    {
        $this->letterId = $letter->id;
        $this->isFinal = $letter->isFinal();
        $this->employee_id = $letter->employee_id;
        $this->audit_reference_group_id = $letter->audit_reference_group_id;
        $p = $letter->payload();

        $this->letter_number = (string) ($letter->letter_number ?? ($p['nomor_surat'] ?? ''));
        $date = $letter->letter_date?->toDateString() ?? ($p['tanggal_surat'] ?? '');
        $this->letter_date = (string) $date;
        $this->jabatan_pengirim = (string) ($p['jabatan_pengirim'] ?? $this->jabatan_pengirim);
        $this->nama_pegawai = (string) ($p['nama_pegawai'] ?? '');
        $this->nip = (string) ($p['nip'] ?? '');
        $this->pangkat = (string) ($p['pangkat'] ?? '');
        $this->golongan = (string) ($p['golongan'] ?? '');
        $this->jabatan = (string) ($p['jabatan'] ?? '');
        $this->sapaan = in_array(($p['sapaan'] ?? 'Saudara'), ['Saudara', 'Saudari'], true) ? $p['sapaan'] : 'Saudara';
        $refs = $p['daftar_referensi_audit'] ?? [];
        $this->daftar_referensi_audit = is_array($refs) && count($refs) ? array_values(array_map('strval', $refs)) : [''];
        $this->uraian_teguran = (string) ($p['uraian_teguran'] ?? '');
        $this->instruksi = (string) ($p['instruksi'] ?? $this->instruksi);
        $this->jabatan_penandatangan = (string) ($p['jabatan_penandatangan'] ?? $this->jabatan_penandatangan);
        $this->nama_penandatangan = (string) ($p['nama_penandatangan'] ?? $this->nama_penandatangan);
        $this->hal = (string) ($p['hal'] ?? $this->hal);
        $cc = $p['daftar_tembusan'] ?? $this->daftar_tembusan;
        $this->daftar_tembusan = is_array($cc) && count($cc) ? array_values(array_map('strval', $cc)) : [''];
    }

    public function getLetterModelProperty(): ?Letter
    {
        return $this->letterId ? Letter::query()->find($this->letterId) : null;
    }

    /** @return array<string, mixed> */
    public function formInput(): array
    {
        return [
            'employee_id' => $this->employee_id,
            'audit_reference_group_id' => $this->audit_reference_group_id,
            'nomor_surat' => $this->letter_number,
            'tanggal_surat' => $this->letter_date,
            'nama_pegawai' => $this->nama_pegawai,
            'nip' => $this->nip,
            'pangkat' => $this->pangkat,
            'golongan' => $this->golongan,
            'jabatan' => $this->jabatan,
            'sapaan' => $this->sapaan,
            'daftar_referensi_audit' => $this->daftar_referensi_audit,
            'uraian_teguran' => $this->uraian_teguran,
            'instruksi' => $this->instruksi,
            'jabatan_pengirim' => $this->jabatan_pengirim,
            'hal' => $this->hal,
            'jabatan_penandatangan' => $this->jabatan_penandatangan,
            'nama_penandatangan' => $this->nama_penandatangan,
            'daftar_tembusan' => $this->daftar_tembusan,
        ];
    }

    public function updatedEmployeeId($value): void
    {
        if (! $value) return;
        $employee = Employee::query()->find($value);
        if (! $employee) return;
        $this->nama_pegawai = (string) $employee->name;
        $this->nip = (string) ($employee->nip ?? '');
        $this->pangkat = (string) ($employee->rank ?? '');
        $this->golongan = (string) ($employee->grade ?? '');
        $this->jabatan = (string) ($employee->position ?? '');
    }

    public function updatedAuditReferenceGroupId($value): void
    {
        if (! $value) return;
        $group = AuditReferenceGroup::query()->find($value);
        if (! $group) return;
        $refs = $group->references();
        $this->daftar_referensi_audit = count($refs) ? $refs : [''];
    }

    public function addReference(): void
    {
        $this->daftar_referensi_audit[] = '';
    }

    public function removeReference(int $index): void
    {
        if (count($this->daftar_referensi_audit) <= 1) {
            $this->daftar_referensi_audit = [''];
            return;
        }
        unset($this->daftar_referensi_audit[$index]);
        $this->daftar_referensi_audit = array_values($this->daftar_referensi_audit);
    }

    public function moveReference(int $index, string $direction): void
    {
        $j = $direction === 'up' ? $index - 1 : $index + 1;
        if ($j < 0 || $j >= count($this->daftar_referensi_audit)) return;
        [$this->daftar_referensi_audit[$index], $this->daftar_referensi_audit[$j]] = [$this->daftar_referensi_audit[$j], $this->daftar_referensi_audit[$index]];
    }

    public function addTembusan(): void
    {
        $this->daftar_tembusan[] = '';
    }

    public function removeTembusan(int $index): void
    {
        if (count($this->daftar_tembusan) <= 1) {
            $this->daftar_tembusan = [''];
            return;
        }
        unset($this->daftar_tembusan[$index]);
        $this->daftar_tembusan = array_values($this->daftar_tembusan);
    }

    public function nextStep(): void
    {
        $this->step = min(5, $this->step + 1);
    }

    public function prevStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function goToStep(int $step): void
    {
        $this->step = max(1, min(5, $step));
    }

    protected function addValidationErrors(ValidationException $e): void
    {
        foreach ($e->errors() as $key => $messages) {
            foreach ((array) $messages as $message) {
                $this->addError($key, $message);
            }
        }
    }

    public function saveDraft(LetterService $service): void
    {
        try {
            $letter = $this->letterId ? Letter::query()->findOrFail($this->letterId) : null;
            $saved = $service->saveDraft($letter, $this->formInput());
            $this->letterId = $saved->id;
            session()->flash('success', 'Draft berhasil disimpan.');
            if (! $letter) {
                $this->redirectRoute('letters.edit', ['letter' => $saved->id], navigate: false);
            }
        } catch (ValidationException $e) {
            $this->addValidationErrors($e);
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal menyimpan draft: '.$e->getMessage());
        }
    }

    public function previewPdf(LetterService $service): void
    {
        try {
            $letter = $this->letterId ? Letter::query()->findOrFail($this->letterId) : null;
            $letter = $letter ?: $service->saveDraft(null, $this->formInput());
            $result = $service->renderPreview($letter, $this->formInput());
            $this->letterId = $result['letter']->id;
            session()->flash('success', 'Pratinjau PDF berhasil dibuat (2 halaman).');
            $this->redirectRoute('letters.preview', ['letter' => $result['letter']->id], navigate: false);
        } catch (ValidationException $e) {
            $this->addValidationErrors($e);
            session()->flash('error', 'Periksa kembali isian yang belum lengkap.');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function finalize(LetterService $service): void
    {
        try {
            $letter = $this->letterId ? Letter::query()->findOrFail($this->letterId) : null;
            $letter = $letter ?: $service->saveDraft(null, $this->formInput());
            $result = $service->finalize($letter, $this->formInput());
            $this->letterId = $result['letter']->id;
            $this->isFinal = true;
            session()->flash('success', 'Surat berhasil difinalisasi dan PDF tersimpan.');
            $this->redirectRoute('letters.index', navigate: false);
        } catch (ValidationException $e) {
            $this->addValidationErrors($e);
            session()->flash('error', 'Finalisasi gagal. Lengkapi data wajib dan pastikan PDF 2 halaman.');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render(AuditReferenceFormatter $formatter)
    {
        $formattedRefs = $formatter->format($this->daftar_referensi_audit);

        return view('livewire.letters.create-letter', [
            'employees' => Employee::query()->orderBy('name')->get(),
            'groups' => AuditReferenceGroup::query()->orderBy('name')->get(),
            'formattedRefs' => $formattedRefs,
        ])->layout('layouts.app')->title('Buat Surat');
    }
}
