<?php

namespace Tests\Unit;

use App\Services\AuditReferenceFormatter;
use PHPUnit\Framework\TestCase;

class AuditReferenceFormatterTest extends TestCase
{
    private AuditReferenceFormatter $formatter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->formatter = new AuditReferenceFormatter();
    }

    public function test_single_reference_ends_with_period(): void
    {
        $result = $this->formatter->format(['Dokumen A']);
        $this->assertCount(1, $result);
        $this->assertSame('1. Dokumen A.', $result[0]['formatted']);
        $this->assertSame('.', $result[0]['suffix']);
    }

    public function test_two_references_use_semicolon_dan_and_period(): void
    {
        $result = $this->formatter->format(['Dokumen A', 'Dokumen B']);
        $this->assertSame('1. Dokumen A; dan', $result[0]['formatted']);
        $this->assertSame('2. Dokumen B.', $result[1]['formatted']);
    }

    public function test_three_references_formatting(): void
    {
        $result = $this->formatter->format(['Dokumen A', 'Dokumen B', 'Dokumen C']);
        $this->assertSame('1. Dokumen A;', $result[0]['formatted']);
        $this->assertSame('2. Dokumen B; dan', $result[1]['formatted']);
        $this->assertSame('3. Dokumen C.', $result[2]['formatted']);
    }

    public function test_four_or_more_references_formatting(): void
    {
        $result = $this->formatter->format(['A', 'B', 'C', 'D']);
        $this->assertSame([';', ';', '; dan', '.'], array_column($result, 'suffix'));
        $this->assertSame(1, $result[0]['index']);
        $this->assertSame(4, $result[3]['index']);
    }

    public function test_empty_and_whitespace_inputs_are_filtered(): void
    {
        $result = $this->formatter->format(['  Dokumen A  ', '', '   ', null, 'Dokumen B']);
        $this->assertCount(2, $result);
        $this->assertSame('Dokumen A', $result[0]['text']);
        $this->assertSame('Dokumen B', $result[1]['text']);
        $this->assertSame('1. Dokumen A; dan', $result[0]['formatted']);
        $this->assertSame('2. Dokumen B.', $result[1]['formatted']);
    }

    public function test_normalizes_trailing_terminators_to_avoid_double_punctuation(): void
    {
        $result = $this->formatter->format(['Dokumen A;', 'Dokumen B.', 'Dokumen C;']);
        $this->assertSame('1. Dokumen A;', $result[0]['formatted']);
        $this->assertSame('2. Dokumen B; dan', $result[1]['formatted']);
        $this->assertSame('3. Dokumen C.', $result[2]['formatted']);
    }

    public function test_normalizes_copied_formatted_suffix(): void
    {
        $result = $this->formatter->format(['Dokumen A; dan', 'Dokumen B.']);
        $this->assertSame('Dokumen A', $result[0]['text']);
        $this->assertSame('Dokumen B', $result[1]['text']);
        $this->assertSame('1. Dokumen A; dan', $result[0]['formatted']);
        $this->assertSame('2. Dokumen B.', $result[1]['formatted']);
    }

    public function test_preserves_internal_punctuation_and_dates(): void
    {
        $text = 'Nota Dinas Inspektur Jenderal Nomor: PW0103/R/lj/2026/2317 Tanggal 7 Oktober 2026 Hal Laporan (Pembangunan Sekolah Rakyat)';
        $result = $this->formatter->format([$text]);
        $this->assertSame($text, $result[0]['text']);
        $this->assertSame('1. '.$text.'.', $result[0]['formatted']);
    }

    public function test_no_valid_references_returns_empty(): void
    {
        $this->assertSame([], $this->formatter->format(['', '  ', null, ';', '.']));
    }

    public function test_normalized_texts_helper(): void
    {
        $texts = $this->formatter->normalizedTexts(['  A;  ', '', 'B.']);
        $this->assertSame(['A', 'B'], $texts);
    }
}
