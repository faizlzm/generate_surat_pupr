<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Letter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Smalot\PdfParser\Parser as PdfParser;
use Spatie\LaravelPdf\Facades\Pdf;
use Throwable;

class PdfGenerationService
{
    public const TEMPLATE_VERSION = 'memorandum-v1';
    public const REQUIRED_PAGES = 2;

    public function __construct(private readonly AuditReferenceFormatter $formatter)
    {
    }

    /**
     * Build render payload for a letter from its snapshot and current/fallback settings.
     *
     * @return array{payload: array<string,mixed>, settings: array<string,mixed>}
     */
    public function buildRenderData(Letter $letter): array
    {
        $settings = AppSetting::defaults();
        $payload = $letter->payload();

        $references = $payload['daftar_referensi_audit'] ?? [];
        if (! is_array($references)) {
            $references = [];
        }
        $formatted = $this->formatter->format($references);
        $payload['formatted_references'] = $formatted;
        $payload['daftar_referensi_audit_normalized'] = array_column($formatted, 'text');

        $cc = $payload['daftar_tembusan'] ?? ($settings['cc_list'] ?? []);
        if (! is_array($cc)) {
            $cc = [];
        }
        $cc = array_values(array_filter(array_map(static fn ($v) => trim((string) $v), $cc), static fn ($v) => $v !== ''));
        $payload['daftar_tembusan'] = $cc;

        $sapaan = trim((string) ($payload['sapaan'] ?? 'Saudara'));
        $payload['sapaan'] = $sapaan !== '' ? $sapaan : 'Saudara';

        $instruksi = (string) ($payload['instruksi'] ?? ($settings['instruction_template'] ?? ''));
        $payload['instruksi'] = $instruksi;

        $tanggal = $payload['tanggal_surat'] ?? null;
        $payload['tanggal_surat_formatted'] = $this->formatTanggal($tanggal);

        $settings['logo_path'] = $this->resolveLogoPath($settings['logo_path'] ?? null);

        return ['payload' => $payload, 'settings' => $settings];
    }

    public function formatTanggal(mixed $value): string
    {
        if ($value instanceof \DateTimeInterface) {
            $date = \Carbon\Carbon::instance(\DateTime::createFromInterface($value));
        } elseif (is_string($value) && trim($value) !== '') {
            try {
                $date = \Carbon\Carbon::parse($value);
            } catch (Throwable) {
                return (string) $value;
            }
        } else {
            return '';
        }

        $months = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

        return $date->day.' '.($months[$date->month] ?? $date->format('F')).' '.$date->year;
    }

    public function resolveLogoPath(mixed $path): ?string
    {
        $candidates = [];
        if (is_string($path) && trim($path) !== '') {
            $candidates[] = $path;
        }
        $candidates[] = public_path('images/kop-logo.png');
        $candidates[] = storage_path('app/private/letters/kop-logo.png');

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && is_file($candidate)) {
                $real = realpath($candidate) ?: $candidate;
                $normalized = str_replace('\\', '/', $real);
                if (preg_match('/^[A-Za-z]:\//', $normalized)) {
                    return 'file:///'.$normalized;
                }

                return 'file://'.$normalized;
            }
        }

        return null;
    }

    /**
     * Render PDF bytes and return ['bytes'=>string,'page_count'=>int].
     *
     * @return array{bytes:string,page_count:int}
     */
    public function render(Letter $letter): array
    {
        $data = $this->buildRenderData($letter);
        $tmpDir = storage_path('app/tmp');
        if (! is_dir($tmpDir)) {
            @mkdir($tmpDir, 0775, true);
        }
        $tmpPath = $tmpDir.DIRECTORY_SEPARATOR.'memo-'.Str::uuid()->toString().'.pdf';

        try {
            Pdf::view('pdf.memorandum', $data)
                ->format('a4')
                ->margins(20, 20, 15, 30, 'mm')
                ->withBrowsershot(function ($browsershot): void {
                    $chromePath = config('laravel-pdf.browsershot.chrome_path');
                    if ($chromePath) {
                        $browsershot->setChromePath($chromePath);
                    }
                    $node = config('laravel-pdf.browsershot.node_binary');
                    if ($node) {
                        $browsershot->setNodeBinary($node);
                    }
                    $npm = config('laravel-pdf.browsershot.npm_binary');
                    if ($npm) {
                        $browsershot->setNpmBinary($npm);
                    }
                    $browsershot->showBackground();
                    $browsershot->timeout(120);
                    if (config('laravel-pdf.browsershot.no_sandbox')) {
                        $browsershot->noSandbox();
                    }
                })
                ->save($tmpPath);

            $bytes = file_get_contents($tmpPath);
            if ($bytes === false) {
                throw new RuntimeException('Gagal membaca berkas PDF sementara.');
            }

            $pageCount = $this->countPages($bytes);

            return ['bytes' => $bytes, 'page_count' => $pageCount];
        } finally {
            if (is_file($tmpPath)) {
                @unlink($tmpPath);
            }
        }
    }

    public function countPages(string $pdfBytes): int
    {
        try {
            $parser = new PdfParser();
            $pdf = $parser->parseContent($pdfBytes);
            $details = $pdf->getDetails();
            if (isset($details['Pages']) && is_numeric($details['Pages'])) {
                return (int) $details['Pages'];
            }
            $pages = $pdf->getPages();

            return is_array($pages) ? count($pages) : 0;
        } catch (Throwable $e) {
            // Fallback: count /Type /Page occurrences (rough) or /Count in Pages dict.
            if (preg_match('/\/Type\s*\/Pages[^>]*?\/Count\s+(\d+)/s', $pdfBytes, $m)) {
                return (int) $m[1];
            }
            if (preg_match_all('/\/Type\s*\/Page\b/', $pdfBytes, $m)) {
                return count($m[0]);
            }

            throw new RuntimeException('Gagal menghitung jumlah halaman PDF: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Validate render result meets the two-page contract.
     *
     * @param array{bytes:string,page_count:int} $rendered
     */
    public function assertTwoPages(array $rendered): void
    {
        $count = (int) ($rendered['page_count'] ?? 0);
        if ($count !== self::REQUIRED_PAGES) {
            throw new RuntimeException(
                'PDF harus tepat '.self::REQUIRED_PAGES.' halaman A4, tetapi hasil render berjumlah '.$count.' halaman. Periksa panjang isi (uraian/jabatan/referensi) lalu coba lagi.'
            );
        }
        if (($rendered['bytes'] ?? '') === '') {
            throw new RuntimeException('Berkas PDF kosong atau gagal dibuat.');
        }
    }

    public function storeFinal(Letter $letter, string $bytes): string
    {
        $disk = Storage::disk('letters');
        $safeNumber = $letter->letter_number ? preg_replace('/[^A-Za-z0-9_\-]+/', '_', (string) $letter->letter_number) : 'draft';
        $name = sprintf('memorandum-%d-%s-%s.pdf', $letter->id, $safeNumber, now()->format('YmdHis'));
        $path = $letter->id.'/'.$name;
        $disk->put($path, $bytes);

        return $path;
    }
}
