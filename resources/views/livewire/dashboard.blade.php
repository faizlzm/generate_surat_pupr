<div>

<div class="page-head">
        <div>
            <h1>Dashboard</h1>
            <p class="sub">Kelola pembuatan Memorandum Teguran Tertulis secara lokal.</p>
        </div>
        <a href="{{ route('letters.create') }}" class="btn primary">＋ Buat Surat Baru</a>
    </div>

    <div class="grid three" style="margin-bottom:16px;">
        <div class="card">
            <div class="label">Total Surat</div>
            <div class="metric">{{ $total }}</div>
            <p class="sub">Semua draft dan final</p>
        </div>
        <div class="card">
            <div class="label">Draft</div>
            <div class="metric">{{ $drafts }}</div>
            <p class="sub">Belum difinalisasi</p>
        </div>
        <div class="card">
            <div class="label">PDF Final</div>
            <div class="metric">{{ $finals }}</div>
            <p class="sub">Siap diunduh/arsip</p>
        </div>
    </div>

    <div class="grid two">
        <div class="card">
            <h2>Mulai cepat</h2>
            <div class="section-space"></div>
            <div class="list-row">
                <div><strong>Buat surat baru</strong><p class="sub">Isi formulir 5 langkah, pratinjau, lalu finalisasi PDF 2 halaman.</p></div>
                <a href="{{ route('letters.create') }}" class="btn small">Buka</a>
            </div>
            <div class="list-row">
                <div><strong>Riwayat surat</strong><p class="sub">Cari draft/final, buka, unduh, atau duplikasi.</p></div>
                <a href="{{ route('letters.index') }}" class="btn small">Lihat</a>
            </div>
            <div class="list-row" style="border-bottom:none;">
                <div><strong>Pengaturan</strong><p class="sub">Kop, pengirim, penandatangan, dan tembusan default.</p></div>
                <a href="{{ route('settings') }}" class="btn small">Atur</a>
            </div>
        </div>
        <div class="card">
            <h2>Aturan dokumen</h2>
            <div class="section-space"></div>
            <p>✓ Satu PDF = tepat <strong>2 halaman A4</strong> (portrait)</p>
            <p style="margin-top:8px;">✓ Halaman 1: dengan tabel paraf (Petugas, Pejabat Administrator, Pimpinan Tinggi Pratama)</p>
            <p style="margin-top:8px;">✓ Halaman 2: tanpa tabel paraf, isi identik</p>
            <p style="margin-top:8px;">✓ Referensi audit: nomor mulai 1, akhiran <code>;</code>, <code>; dan</code>, <code>.</code> otomatis</p>
            <p style="margin-top:8px;">✓ Finalisasi diblokir jika PDF bukan 2 halaman (isi terlalu panjang)</p>
            <p style="margin-top:8px;">✓ Surat final bersifat snapshot/arsip (tidak ditimpa perubahan pengaturan)</p>
        </div>
    </div>

    <div class="card section-space">
        <h2>Surat terbaru</h2>
        @if($recent->isEmpty())
            <p class="sub" style="margin-top:10px;">Belum ada surat. Mulai dengan membuat surat baru.</p>
        @else
            <table class="table section-space">
                <thead><tr><th>Nomor</th><th>Tanggal</th><th>Penerima</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                @foreach($recent as $letter)
                    @php $p = $letter->payload(); @endphp
                    <tr>
                        <td>{{ $letter->letter_number ?: '—' }}</td>
                        <td>{{ $letter->letter_date?->format('d-m-Y') ?: ($p['tanggal_surat'] ?? '—') }}</td>
                        <td>{{ $p['nama_pegawai'] ?? '—' }}</td>
                        <td><span class="pill {{ $letter->status }}">{{ $letter->isFinal() ? 'Final' : 'Draft' }}</span></td>
                        <td>
                            @if($letter->isFinal())
                                <a class="btn small" href="{{ route('letters.preview', $letter) }}" target="_blank">Lihat PDF</a>
                            @else
                                <a class="btn small" href="{{ route('letters.edit', $letter) }}">Edit</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
