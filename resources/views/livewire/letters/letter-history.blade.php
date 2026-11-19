<div>
    <div class="page-head">
        <div>
            <h1>Riwayat Surat</h1>
            <p class="sub">Cari, buka, unduh, dan duplikasi memorandum.</p>
        </div>
        <a href="{{ route('letters.create') }}" class="btn primary">＋ Buat Surat</a>
    </div>

    <div class="card">
        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:14px;">
            <input class="input" style="max-width:360px;" type="search" wire:model.live.debounce.300ms="search" placeholder="Cari nomor surat atau nama penerima...">
            <select class="select" style="max-width:180px;" wire:model.live="status">
                <option value="all">Semua status</option>
                <option value="draft">Draft</option>
                <option value="final">Final</option>
            </select>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>No. Surat</th>
                    <th>Tanggal</th>
                    <th>Penerima</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($letters as $letter)
                    @php $p = $letter->payload(); @endphp
                    <tr wire:key="letter-{{ $letter->id }}">
                        <td>{{ $letter->letter_number ?: '—' }}</td>
                        <td>{{ $letter->letter_date?->format('d-m-Y') ?: ($p['tanggal_surat'] ?? '—') }}</td>
                        <td>{{ $p['nama_pegawai'] ?? '—' }}</td>
                        <td><span class="pill {{ $letter->status }}">{{ $letter->isFinal() ? 'Final' : 'Draft' }}</span></td>
                        <td>
                            <div class="btn-row">
                                @if($letter->isDraft())
                                    <a class="btn small" href="{{ route('letters.edit', $letter) }}">Edit</a>
                                    <a class="btn small" href="{{ route('letters.preview', $letter) }}" target="_blank">Pratinjau</a>
                                @else
                                    <a class="btn small" href="{{ route('letters.preview', $letter) }}" target="_blank">Lihat PDF</a>
                                    <a class="btn small primary" href="{{ route('letters.download', $letter) }}">Unduh</a>
                                @endif
                                <button type="button" class="btn small" wire:click="duplicate({{ $letter->id }})" wire:confirm="Duplikasi surat ini menjadi draft baru?">Duplikasi</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted" style="padding:18px 10px;">Belum ada surat yang sesuai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-space">
            {{ $letters->links() }}
        </div>

        <div class="help section-space">Surat final bersifat arsip (snapshot) dan tidak diubah oleh perubahan pengaturan global. Gunakan <strong>Duplikasi</strong> untuk membuat revisi sebagai draft baru. File PDF disimpan privat di storage lokal.</div>
    </div>
</div>
