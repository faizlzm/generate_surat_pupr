<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
    @page { size: A4 portrait; margin: 20mm 20mm 15mm 30mm; }
    * { box-sizing: border-box; }
    body { margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11pt; line-height: 1.35; color: #000; }
    .page { position: relative; }
    .page-break { page-break-after: always; break-after: page; }
    .page-header-num { text-align: center; font-size: 11pt; margin: -4mm 0 4mm; }
    .letterhead { text-align: center; border-bottom: 3px double #000; padding-bottom: 6px; margin-bottom: 10px; }
    .letterhead .kop-image { width: 100%; max-height: 110px; object-fit: contain; }
    .letterhead .fallback-kop { display: flex; align-items: center; gap: 14px; text-align: left; }
    .letterhead .logo-box { width: 70px; height: 70px; border: 1px solid #999; display: flex; align-items: center; justify-content: center; font-size: 8pt; color: #666; flex-shrink: 0; }
    .letterhead .logo-box img { max-width: 100%; max-height: 100%; }
    .letterhead-text { text-align: center; flex: 1; }
    .letterhead-text .ministry { font-size: 13pt; font-weight: 700; text-transform: uppercase; }
    .letterhead-text .directorate { font-size: 14pt; font-weight: 700; text-transform: uppercase; }
    .letterhead-text .address { font-size: 9.5pt; margin-top: 2px; }
    .title { text-align: center; margin: 8px 0 10px; }
    .title .name { font-weight: 700; text-decoration: underline; font-size: 12pt; letter-spacing: .3px; }
    .title .number { margin-top: 2px; font-size: 11pt; }
    table.meta { width: 100%; border-collapse: collapse; margin: 4px 0 8px; }
    table.meta td { vertical-align: top; padding: 1px 0; font-size: 11pt; }
    table.meta td.label { width: 80px; }
    table.meta td.sep { width: 12px; }
    .section { margin: 6px 0; }
    ol.refs { margin: 2px 0 8px 22px; padding: 0; }
    ol.refs li { margin: 2px 0; text-align: justify; }
    ol.body { margin: 4px 0 8px 22px; padding: 0; }
    ol.body > li { margin: 6px 0; text-align: justify; }
    table.identity { margin: 4px 0 2px 0; border-collapse: collapse; width: 100%; }
    table.identity td { vertical-align: top; padding: 1px 0; }
    table.identity td.ilabel { width: 110px; }
    table.identity td.isep { width: 12px; }
    .closing { margin-top: 8px; }
    .bottom { margin-top: 10px; width: 100%; }
    .bottom-row { display: table; width: 100%; table-layout: fixed; }
    .paraf-wrap { display: table-cell; width: 52%; vertical-align: top; }
    .sign-wrap { display: table-cell; width: 48%; vertical-align: top; text-align: left; padding-left: 10px; font-size: 11pt; }
    .sign-name { margin-top: 60px; }
    table.paraf { border-collapse: collapse; width: 100%; max-width: 340px; font-size: 10pt; }
    table.paraf th, table.paraf td { border: 1px solid #000; padding: 3px 4px; text-align: center; vertical-align: middle; }
    table.paraf th { font-weight: 700; }
    table.paraf td.space { height: 42px; }
    .cc { margin-top: 12px; font-size: 11pt; }
    .cc ol { margin: 2px 0 0 20px; padding: 0; }
</style>
</head>
<body>
@php
    $p = $payload ?? [];
    $s = $settings ?? [];
    $refs = $references ?? ($p['formatted_references'] ?? []);
    $cc = $p['daftar_tembusan'] ?? ($s['cc_list'] ?? []);
    if (!is_array($cc)) $cc = [];
    $nama = $p['nama_pegawai'] ?? '';
    $sapaan = $p['sapaan'] ?? 'Saudara';
    $instruksi = $p['instruksi'] ?? ($s['instruction_template'] ?? '');
    $instruksi = str_replace(['{{sapaan}}', '{sapaan}'], $sapaan, $instruksi);
    $pangkatGol = trim(($p['golongan'] ?? '').' '.(!empty($p['pangkat']) ? '('.$p['pangkat'].')' : ''));
    $pangkatGol = trim($pangkatGol) !== '' ? $pangkatGol : ($p['pangkat_golongan'] ?? '');
@endphp

@foreach([true, false] as $index => $withParaf)
    <div class="page {{ $withParaf ? 'page-break' : '' }}">
        @if($index === 1)
            <div class="page-header-num">- 2 -</div>
        @endif
        @include('pdf.components.letterhead', ['s' => $s])

        <div class="title">
            <div class="name">MEMORANDUM</div>
            <div class="number">NOMOR: {{ $p['nomor_surat'] ?? '' }}</div>
        </div>

        <table class="meta">
            <tr><td class="label">Yth</td><td class="sep">:</td><td>{{ $nama }}</td></tr>
            <tr><td class="label">Dari</td><td class="sep">:</td><td>{{ $p['jabatan_pengirim'] ?? ($s['sender_title'] ?? '') }}</td></tr>
            <tr><td class="label">Hal</td><td class="sep">:</td><td>{{ $p['hal'] ?? ($s['subject'] ?? 'Teguran Tertulis') }}</td></tr>
            <tr><td class="label">Tanggal</td><td class="sep">:</td><td>{{ $p['tanggal_surat_formatted'] ?? ($p['tanggal_surat'] ?? '') }}</td></tr>
        </table>

        <div class="section">Menindaklanjuti:</div>
        <ol class="refs">
            @foreach($refs as $ref)
                <li>{{ is_array($ref) ? ($ref['text'] ?? '') . ($ref['suffix'] ?? '') : $ref }}</li>
            @endforeach
        </ol>

        <div class="section">Bersama ini disampaikan hal-hal sebagai berikut:</div>
        <ol class="body">
            <li>
                Sesuai Laporan Hasil Pemeriksaan Inspektorat Jenderal, dengan ini kami memberikan <strong>Teguran Tertulis</strong>, kepada:
                <table class="identity">
                    <tr><td class="ilabel">Nama</td><td class="isep">:</td><td>{{ $nama }}</td></tr>
                    <tr><td class="ilabel">NIP</td><td class="isep">:</td><td>{{ $p['nip'] ?? '' }}</td></tr>
                    <tr><td class="ilabel">Pangkat/Gol</td><td class="isep">:</td><td>{{ $pangkatGol }}</td></tr>
                    <tr><td class="ilabel">Jabatan</td><td class="isep">:</td><td>{{ $p['jabatan'] ?? '' }}</td></tr>
                    <tr><td class="ilabel">Atas</td><td class="isep">:</td><td>{{ $p['uraian_teguran'] ?? '' }}</td></tr>
                </table>
            </li>
            <li>{{ $instruksi }}</li>
        </ol>

        <div class="closing">Atas perhatiannya kami ucapkan terima kasih.</div>

        <div class="bottom">
            <div class="bottom-row">
                <div class="paraf-wrap">
                    @if($withParaf)
                        @include('pdf.components.approval-table')
                    @endif
                </div>
                <div class="sign-wrap">
                    <div>{{ $p['jabatan_penandatangan'] ?? ($s['signatory_title'] ?? '') }},</div>
                    <div class="sign-name">{{ $p['nama_penandatangan'] ?? ($s['signatory_name'] ?? '') }}</div>
                </div>
            </div>
        </div>

        @if(!empty($cc))
            <div class="cc">
                Tembusan:
                <ol>
                    @foreach($cc as $item)
                        @if(trim((string)$item) !== '')
                            <li>{{ $item }}</li>
                        @endif
                    @endforeach
                </ol>
            </div>
        @endif
    </div>
@endforeach
</body>
</html>
