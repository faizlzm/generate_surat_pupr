<?php

declare(strict_types=1);

namespace Tests\Unit\WordGenerator;

use App\Services\WordGenerator\BatchValidator;
use App\Services\WordGenerator\PayloadNormalizer;
use App\Services\WordGenerator\ReferenceFormatter;
use PHPUnit\Framework\TestCase;

final class WordGeneratorRulesTest extends TestCase
{
    public function test_reference_endings_for_various_counts(): void
    {
        $cases = [
            1 => ['.'],
            2 => ['; dan', '.'],
            3 => [';', '; dan', '.'],
            4 => [';', ';', '; dan', '.'],
        ];
        foreach ($cases as $n => $expected) {
            $formatted = ReferenceFormatter::format(array_fill(0, $n, 'Contoh referensi;'));
            $actual = array_column($formatted, 'ending');
            $this->assertSame($expected, $actual, "Tanda baca untuk {$n} referensi");
            $this->assertSame($n.'.', $formatted[$n - 1]['number']);
        }
    }

    public function test_clean_removes_trailing_enumeration_marks(): void
    {
        $this->assertSame('Audit Maluku', ReferenceFormatter::clean('Audit Maluku; dan'));
        $this->assertSame('Audit Maluku', ReferenceFormatter::clean('Audit Maluku;'));
        $this->assertSame('Audit Maluku', ReferenceFormatter::clean('Audit Maluku.'));
        $this->assertSame('Nomor: PW.01/2026', ReferenceFormatter::clean('Nomor: PW.01/2026;'));
    }

    public function test_validate_sample_3_and_override(): void
    {
        $payload = $this->loadSample('contoh_3_surat.json');
        $errors = BatchValidator::validate($payload);
        $this->assertSame([], $errors, implode('; ', $errors));

        $groupById = [];
        foreach ($payload['kelompok_audit'] as $g) {
            $groupById[(string) $g['id']] = $g;
        }
        $r3 = $payload['penerima'][2];
        $refs = BatchValidator::referencesFor($r3, $groupById);
        $this->assertSame($r3['referensi_khusus'], $refs, 'Referensi khusus harus menggantikan grup');
    }

    public function test_validate_sample_30(): void
    {
        $payload = $this->loadSample('contoh_30_surat.json');
        $errors = BatchValidator::validate($payload);
        $this->assertSame([], $errors, implode('; ', $errors));
        $this->assertCount(30, $payload['penerima']);
        $this->assertCount(3, $payload['kelompok_audit']);
    }

    public function test_duplicate_number_rejected(): void
    {
        $payload = $this->loadSample('contoh_3_surat.json');
        $payload['penerima'][1]['nomor_memorandum'] = $payload['penerima'][0]['nomor_memorandum'];
        $errors = BatchValidator::validate($payload);
        $this->assertNotEmpty($errors);
    }

    public function test_payload_normalizer_maps_aliases(): void
    {
        $raw = [
            'informasi_bersama' => [
                'tanggal_surat' => '18 November 2026',
                'jabatan_pengirim' => 'A',
                'jabatan_penandatangan' => 'B,',
                'nama_penandatangan' => 'C',
                'tembusan' => "X\nY",
            ],
            'kelompok_audit' => [['id' => 'G1', 'nama' => 'Grup 1', 'referensi' => ['Ref 1']]],
            'penerima' => [[
                'nomor_surat' => 'N/1',
                'nama' => 'Nama',
                'nip' => 123, // akan dinormalisasi ke string (tetap harus 18 digit oleh validator, tapi alias terpetakan)
                'pangkat_gol' => 'III/a (Penata Muda)',
                'jabatan' => 'Jab',
                'atas' => 'Alasan',
                'sapaan' => 'Saudara',
                'kelompok_audit_id' => 'Grup 1',
                'referensi_khusus' => null,
            ]],
        ];
        $n = PayloadNormalizer::normalize($raw);
        $this->assertSame('N/1', $n['penerima'][0]['nomor_memorandum']);
        $this->assertSame('Alasan', $n['penerima'][0]['alasan_teguran']);
        $this->assertSame('III/a (Penata Muda)', $n['penerima'][0]['pangkat_golongan']);
        $this->assertSame('G1', $n['penerima'][0]['kelompok_audit_id']);
        $this->assertSame(['X', 'Y'], $n['informasi_bersama']['tembusan']);
    }

    /**
     * @return array<string, mixed>
     */
    private function loadSample(string $file): array
    {
        $path = dirname(__DIR__, 3).'/storage/app/private/generator_tmp/'.$file;
        $json = file_get_contents($path);
        $this->assertNotFalse($json, "Sample not found: {$path}");

        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }
}
