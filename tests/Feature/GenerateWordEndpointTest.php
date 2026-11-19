<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;
use ZipArchive;

class GenerateWordEndpointTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanupGeneratorRuns();
    }

    protected function tearDown(): void
    {
        $this->cleanupGeneratorRuns();
        parent::tearDown();
    }

    public function test_generates_zip_for_three_recipients(): void
    {
        $payload = $this->samplePayload('contoh_3_surat.json');

        $response = $this->postJson('/buat-dokumen-word', $payload);

        $response->assertOk();
        
        $response->assertDownload();
        $this->assertStringEndsWith(
            '.zip"',
            $response->headers->get('Content-Disposition', '')
        );

        try {
            $body = $response->streamedContent();
        } catch (\Throwable) {
            $body = (string) $response->getContent();
        }
        $this->assertNotEmpty($body);
        $zipPath = $this->writeTempZip($body);
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($zipPath) === true, 'ZIP harus dapat dibuka');
        $this->assertSame(3, $zip->numFiles);
        $names = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $names[] = $zip->getNameIndex($i);
        }
        $zip->close();
        @unlink($zipPath);

        sort($names);
        $this->assertSame('001_Surat_Teguran_Pegawai_Simulasi_01.docx', $names[0]);
        $this->assertSame('003_Surat_Teguran_Pegawai_Simulasi_03.docx', $names[2]);

        $this->assertGeneratorStorageClean();
    }

    public function test_generates_zip_for_thirty_recipients(): void
    {
        $payload = $this->samplePayload('contoh_30_surat.json');

        $response = $this->postJson('/buat-dokumen-word', $payload);

        $response->assertOk();
        $response->assertDownload();

        try {
            $body = $response->streamedContent();
        } catch (\Throwable) {
            $body = (string) $response->getContent();
        }
        $this->assertNotEmpty($body);
        $zipPath = $this->writeTempZip($body);
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($zipPath) === true, 'ZIP harus dapat dibuka');
        $this->assertSame(30, $zip->numFiles);
        $zip->close();
        @unlink($zipPath);

        $this->assertGeneratorStorageClean();
    }

    public function test_validation_error_returns_422_and_leaves_no_artifacts(): void
    {
        $payload = $this->samplePayload('contoh_3_surat.json');
        // Duplikat nomor memorandum -> harus ditolak sebelum membuat ZIP
        $payload['penerima'][1]['nomor_memorandum'] = $payload['penerima'][0]['nomor_memorandum'];

        $response = $this->postJson('/buat-dokumen-word', $payload);

        $response->assertStatus(422);
        $response->assertJsonStructure(['pesan', 'errors', 'rincian']);
        $this->assertNotEmpty($response->json('errors'));

        $this->assertGeneratorStorageClean();
    }

    public function test_empty_payload_returns_422(): void
    {
        $response = $this->postJson('/buat-dokumen-word', []);

        $response->assertStatus(422);
        $response->assertJsonStructure(['pesan', 'errors']);
    }

    public function test_invalid_nip_is_rejected_per_recipient(): void
    {
        $payload = $this->samplePayload('contoh_3_surat.json');
        $payload['penerima'][0]['nip'] = '0123'; // tidak 18 digit

        $response = $this->postJson('/buat-dokumen-word', $payload);

        $response->assertStatus(422);
        $errors = implode(' ', $response->json('errors') ?? []);
        $this->assertStringContainsString('NIP', $errors);
        $this->assertGeneratorStorageClean();
    }

    public function test_accepts_ui_alias_fields_via_normalization(): void
    {
        $payload = $this->samplePayload('contoh_3_surat.json');
        // Simulasikan variasi field dari UI/adaptor
        foreach ($payload['penerima'] as &$r) {
            $r['nomor_surat'] = $r['nomor_memorandum'];
            unset($r['nomor_memorandum']);
            $r['atas'] = $r['alasan_teguran'];
            unset($r['alasan_teguran']);
            $r['pangkat_gol'] = $r['pangkat_golongan'];
            unset($r['pangkat_golongan']);
        }
        unset($r);
        $payload['informasi_bersama']['tembusan'] = implode("\n", $payload['informasi_bersama']['tembusan']);

        $response = $this->postJson('/buat-dokumen-word', $payload);

        $response->assertOk();
        $response->assertDownload();
        $this->assertGeneratorStorageClean();
    }

    /**
     * @return array<string, mixed>
     */
    private function samplePayload(string $file): array
    {
        $path = base_path('storage/app/private/generator_tmp/'.$file);
        $json = File::get($path);

        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    private function writeTempZip(string $content): string
    {
        $path = storage_path('app/private/generator_tmp/_test_result.zip');
        File::put($path, $content);

        return $path;
    }

    private function cleanupGeneratorRuns(): void
    {
        $base = storage_path('app/private/generator');
        if (! is_dir($base)) {
            return;
        }
        // Hapus semua direktori run (uuid) yang mungkin tertinggal dari pengujian.
        $items = glob($base.DIRECTORY_SEPARATOR.'*');
        if (is_array($items)) {
            foreach ($items as $item) {
                if (is_dir($item)) {
                    File::deleteDirectory($item);
                } elseif (is_file($item)) {
                    @unlink($item);
                }
            }
        }
        $testZip = storage_path('app/private/generator_tmp/_test_result.zip');
        if (is_file($testZip)) {
            @unlink($testZip);
        }
    }

    private function assertGeneratorStorageClean(): void
    {
        $base = storage_path('app/private/generator');
        if (! is_dir($base)) {
            $this->assertTrue(true);

            return;
        }
        $items = glob($base.DIRECTORY_SEPARATOR.'*');
        $remaining = is_array($items) ? array_values(array_filter($items, static fn ($p) => $p !== '.' && $p !== '..')) : [];
        $this->assertSame([], $remaining, 'Direktori sementara generator harus bersih setelah respons.');
    }
}
