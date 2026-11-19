<?php

declare(strict_types=1);

namespace App\Services\WordGenerator;

use InvalidArgumentException;
use PhpOffice\PhpWord\TemplateProcessor;
use RuntimeException;
use Throwable;
use ZipArchive;

final class DocxBatchGenerator
{
    public function __construct(private readonly string $masterTemplatePath)
    {
        if (! is_file($this->masterTemplatePath)) {
            throw new InvalidArgumentException('Master template Word tidak ditemukan.');
        }
    }

    /**
     * @return array{zip_path:string,documents:list<string>,output_dir:string}
     */
    public function generate(array $payload, string $outputDir): array
    {
        $payload = PayloadNormalizer::normalize($payload);
        $errors = BatchValidator::validate($payload);
        if ($errors !== []) {
            throw new InvalidArgumentException("Validasi gagal:\n- ".implode("\n- ", $errors));
        }
        if (! class_exists(TemplateProcessor::class)) {
            throw new RuntimeException('PHPWord belum tersedia. Jalankan composer install.');
        }
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('Ekstensi PHP zip belum aktif.');
        }
        if (! is_dir($outputDir) && ! mkdir($outputDir, 0700, true) && ! is_dir($outputDir)) {
            throw new RuntimeException('Direktori keluaran tidak dapat dibuat.');
        }

        $groups = [];
        foreach ($payload['kelompok_audit'] as $group) {
            $groups[(string) $group['id']] = $group;
        }

        $documents = [];
        $common = $payload['informasi_bersama'];

        try {
            foreach (array_values($payload['penerima']) as $index => $recipient) {
                // Instance baru agar placeholder dan daftar referensi tidak bocor antar surat.
                $template = new TemplateProcessor($this->masterTemplatePath);
                $pangkat = self::resolvePangkatGolongan($recipient);

                $fields = [
                    'nomor_memorandum' => trim((string) $recipient['nomor_memorandum']),
                    'tanggal_surat' => trim((string) $common['tanggal_surat']),
                    'nama_pegawai' => trim((string) $recipient['nama']),
                    'nip' => (string) $recipient['nip'],
                    'pangkat_golongan' => $pangkat,
                    'jabatan_pegawai' => trim((string) $recipient['jabatan']),
                    'uraian_teguran' => trim((string) $recipient['alasan_teguran']),
                    'sapaan' => (string) $recipient['sapaan'],
                    'jabatan_pengirim' => trim((string) $common['jabatan_pengirim']),
                    'jabatan_penandatangan' => trim((string) $common['jabatan_penandatangan']),
                    'nama_penandatangan' => trim((string) $common['nama_penandatangan']),
                ];
                foreach ($fields as $key => $value) {
                    $template->setValue($key, $value);
                }

                $refs = ReferenceFormatter::format(BatchValidator::referencesFor($recipient, $groups));
                $copies = array_values($common['tembusan']);
                foreach ([1, 2] as $page) {
                    $template->cloneRow("ref_no_p{$page}", count($refs));
                    foreach ($refs as $offset => $ref) {
                        $n = $offset + 1;
                        $template->setValue("ref_no_p{$page}#{$n}", $ref['number']);
                        $template->setValue("ref_text_p{$page}#{$n}", $ref['text']);
                        $template->setValue("ref_end_p{$page}#{$n}", $ref['ending']);
                    }

                    try {
                        $template->cloneRow("tembusan_no_p{$page}", count($copies));
                        foreach ($copies as $offset => $copy) {
                            $n = $offset + 1;
                            $template->setValue("tembusan_no_p{$page}#{$n}", "{$n}.");
                            $template->setValue("tembusan_text_p{$page}#{$n}", trim((string) $copy));
                        }
                    } catch (Throwable) {
                        // Fallback di bawah jika placeholder tidak berada pada baris tabel.
                    }

                    $vars = $template->getVariables();
                    if (in_array("tembusan_no_p{$page}", $vars, true) || in_array("tembusan_text_p{$page}", $vars, true)) {
                        // Jika keduanya berada di sel/teks yang sama (template sederhana), hindari duplikasi
                        // dengan menuliskan nomor+teks pada placeholder nomor dan mengosongkan placeholder teks.
                        $template->setValue("tembusan_no_p{$page}", '1. '.trim((string) ($copies[0] ?? '')));
                        $template->setValue("tembusan_text_p{$page}", '');
                        if (count($copies) > 1) {
                            if (class_exists(\PhpOffice\PhpWord\Element\TextRun::class) && method_exists($template, 'setComplexValue')) {
                                $run = new \PhpOffice\PhpWord\Element\TextRun();
                                foreach ($copies as $offset => $copy) {
                                    $run->addText(($offset + 1).'. '.trim((string) $copy));
                                    if ($offset < count($copies) - 1) {
                                        $run->addTextBreak();
                                    }
                                }
                                $template->setComplexValue("tembusan_no_p{$page}", $run);
                            } else {
                                $lines = [];
                                foreach ($copies as $offset => $copy) {
                                    $lines[] = ($offset + 1).'. '.trim((string) $copy);
                                }
                                $template->setValue("tembusan_no_p{$page}", implode("\n", $lines));
                            }
                        }
                    }
                }

                $remaining = $template->getVariables();
                if ($remaining !== []) {
                    throw new RuntimeException('Masih ada variabel yang belum terisi: '.implode(', ', $remaining));
                }

                $safeName = self::safeFilename((string) $recipient['nama']);
                $filename = sprintf('%03d_Surat_Teguran_%s.docx', $index + 1, $safeName);
                $path = rtrim($outputDir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$filename;
                $template->saveAs($path);
                if (! is_file($path) || filesize($path) === 0) {
                    throw new RuntimeException("Dokumen gagal disimpan: {$filename}");
                }
                $documents[] = $path;
            }

            $zipPath = rtrim($outputDir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'Hasil_Surat_Teguran.zip';
            $zip = new ZipArchive();
            $opened = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            if ($opened !== true) {
                throw new RuntimeException('Tidak dapat membuat ZIP (kode: '.$opened.').');
            }
            foreach ($documents as $file) {
                if (! $zip->addFile($file, basename($file))) {
                    $zip->close();
                    throw new RuntimeException('Gagal menambahkan dokumen ke ZIP.');
                }
            }
            if (! $zip->close()) {
                throw new RuntimeException('Gagal menutup ZIP.');
            }
        } catch (Throwable $e) {
            // Jangan meninggalkan hasil parsial yang ambigu.
            self::cleanupFiles($documents);
            if (isset($zipPath) && is_file($zipPath)) {
                @unlink($zipPath);
            }
            throw $e;
        }

        return ['zip_path' => $zipPath, 'documents' => $documents, 'output_dir' => $outputDir];
    }

    /**
     * Hapus DOCX individual setelah ZIP dipakai (opsional, untuk kebersihan storage).
     *
     * @param  list<string>  $documents
     */
    public static function cleanupFiles(array $documents): void
    {
        foreach ($documents as $file) {
            if (is_string($file) && is_file($file)) {
                @unlink($file);
            }
        }
    }

    public static function cleanupOutputDir(string $outputDir): void
    {
        if (! is_dir($outputDir)) {
            return;
        }
        $items = glob($outputDir.DIRECTORY_SEPARATOR.'*');
        if (is_array($items)) {
            foreach ($items as $item) {
                if (is_file($item)) {
                    @unlink($item);
                }
            }
        }
        @rmdir($outputDir);
    }

    /**
     * @param  array<string, mixed>  $recipient
     */
    private static function resolvePangkatGolongan(array $recipient): string
    {
        $direct = $recipient['pangkat_golongan'] ?? null;
        if (is_string($direct) && trim($direct) !== '') {
            return trim($direct);
        }
        $golongan = trim((string) ($recipient['golongan'] ?? ''));
        $pangkat = trim((string) ($recipient['pangkat'] ?? ''));
        $combined = trim($golongan.($pangkat !== '' ? " ({$pangkat})" : ''));

        return $combined;
    }

    private static function safeFilename(string $name): string
    {
        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
        $ascii = $ascii === false ? $name : $ascii;
        $safe = preg_replace('/[^A-Za-z0-9_-]+/', '_', $ascii) ?? 'Penerima';
        $safe = trim($safe, '_');

        return substr($safe !== '' ? $safe : 'Penerima', 0, 55);
    }
}
