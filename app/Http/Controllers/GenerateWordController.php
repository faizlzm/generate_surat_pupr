<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\WordGenerator\BatchValidator;
use App\Services\WordGenerator\DocxBatchGenerator;
use App\Services\WordGenerator\PayloadNormalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Throwable;

class GenerateWordController extends Controller
{
    private const MAX_RECIPIENTS = 200;

    public function __invoke(Request $request): Response|JsonResponse
    {
        $payload = $request->json()->all();
        if ($payload === [] && $request->getContent() !== '' && $request->getContent() !== '[]') {
            // Body bukan JSON valid atau bukan object/array yang terurai.
            return $this->errorResponse('Format data tidak valid. Kirim payload JSON yang benar.', [], 422);
        }
        if (! is_array($payload) || $payload === []) {
            $payload = $request->all();
        }
        if (! is_array($payload) || $payload === []) {
            return $this->errorResponse('Payload tidak boleh kosong.', [], 422);
        }

        $normalized = PayloadNormalizer::normalize($payload);

        $recipients = $normalized['penerima'] ?? null;
        if (! is_array($recipients) || count($recipients) < 1) {
            return $this->errorResponse('Minimal satu penerima harus tersedia.', ['Minimal satu penerima harus tersedia.'], 422);
        }
        if (count($recipients) > self::MAX_RECIPIENTS) {
            return $this->errorResponse(
                'Jumlah penerima melebihi batas.',
                ['Maksimal '.self::MAX_RECIPIENTS.' penerima dalam satu proses.'],
                422
            );
        }

        $errors = BatchValidator::validate($normalized);
        if ($errors !== []) {
            return $this->errorResponse('Periksa data surat.', $errors, 422);
        }

        $runtimeError = $this->runtimeReadinessError();
        if ($runtimeError !== null) {
            return $this->errorResponse('Lingkungan belum siap membuat dokumen Word.', [$runtimeError], 500);
        }

        $baseDir = storage_path('app/private/generator');
        $runDir = $baseDir.DIRECTORY_SEPARATOR.(string) Str::uuid();
        $masterPath = resource_path('templates/master_template_teguran.docx');

        try {
            $generator = new DocxBatchGenerator($masterPath);
            $result = $generator->generate($normalized, $runDir);

            // ZIP sudah berisi semua DOCX; hapus berkas DOCX sementara agar tidak tertinggal.
            DocxBatchGenerator::cleanupFiles($result['documents']);

            $zipPath = $result['zip_path'];
            if (! is_file($zipPath)) {
                throw new \RuntimeException('Berkas ZIP tidak ditemukan setelah pembuatan.');
            }

            $zipContent = file_get_contents($zipPath);
            if ($zipContent === false) {
                throw new \RuntimeException('Berkas ZIP tidak dapat dibaca.');
            }

            $this->cleanupRunDir($runDir);

            $zipName = basename($zipPath);

            return response($zipContent, 200, [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'attachment; filename="'.$zipName.'"',
                'Content-Length' => (string) strlen($zipContent),
            ]);
        } catch (InvalidArgumentException $e) {
            $this->cleanupRunDir($runDir);

            return $this->errorResponse('Periksa data surat.', $this->splitLines($e->getMessage()), 422);
        } catch (Throwable $e) {
            $this->cleanupRunDir($runDir);
            report($e);

            $message = $e->getMessage();
            $details = $message !== '' ? [$message] : ['Terjadi kesalahan saat membuat dokumen.'];

            return $this->errorResponse('Dokumen gagal dibuat.', $details, 500);
        }
    }

    /**
     * @param  list<string>  $errors
     */
    private function errorResponse(string $message, array $errors, int $status): JsonResponse
    {
        return response()->json([
            'pesan' => $message,
            'message' => $message,
            'errors' => array_values($errors),
            'rincian' => $errors,
        ], $status);
    }

    /**
     * @return list<string>
     */
    private function splitLines(string $message): array
    {
        $lines = preg_split('/\r?\n/', $message) ?: [$message];
        $out = [];
        foreach ($lines as $line) {
            $line = trim($line);
            $line = preg_replace('/^-+\s*/', '', $line) ?? $line;
            if ($line === '' || $line === 'Validasi gagal:') {
                continue;
            }
            $out[] = $line;
        }

        return $out !== [] ? array_values($out) : [$message];
    }

    private function runtimeReadinessError(): ?string
    {
        if (! class_exists(\PhpOffice\PhpWord\TemplateProcessor::class)) {
            return 'Dependensi PHPWord belum terpasang. Jalankan composer install.';
        }
        if (! class_exists(\ZipArchive::class)) {
            return 'Ekstensi PHP zip belum aktif. Aktifkan ekstensi zip (contoh: php -d extension=zip).';
        }

        return null;
    }

    private function cleanupRunDir(string $runDir): void
    {
        if (! is_dir($runDir)) {
            return;
        }
        $items = glob($runDir.DIRECTORY_SEPARATOR.'*');
        if (is_array($items)) {
            foreach ($items as $item) {
                if (is_file($item)) {
                    @unlink($item);
                }
            }
        }
        @rmdir($runDir);
    }
}
