<div>
    <div class="page-head">
        <div>
            <h1>{{ $letterId ? 'Edit Draft Surat' : 'Buat Surat' }}</h1>
            <p class="sub">{{ $letterId ? 'Perbarui isian draft Memorandum Teguran Tertulis.' : 'Formulir 5 langkah untuk Memorandum Teguran Tertulis.' }}</p>
        </div>
        <div class="btn-row">
            @if($letterId)
                <span class="pill draft">Draft #{{ $letterId }}</span>
                <a href="{{ route('letters.index') }}" class="btn small">← Riwayat</a>
            @else
                <span class="pill draft">Draft baru</span>
            @endif
        </div>
    </div>

    @if($isFinal)
        <div class="alert warn">Surat ini sudah final dan tidak dapat diubah. Duplikasi dari Riwayat untuk membuat draft baru.</div>
    @endif

    @if ($errors->any())
        <div class="alert error">
            <strong>Periksa kembali isian:</strong>
            <ul style="margin:6px 0 0 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="stepper">
        @foreach([1=>'Informasi Surat',2=>'Identitas Pegawai',3=>'Referensi Audit',4=>'Substansi Teguran',5=>'Review Data'] as $i => $label)
            <button type="button" wire:click="goToStep({{ $i }})" class="step {{ $step === $i ? 'active' : '' }}">{{ $i }}. {{ $label }}</button>
        @endforeach
    </div>

    <div class="grid two" style="align-items:start;">
        <div class="card">
            @if($step === 1)
                <h2>Informasi Surat</h2>
                <p class="sub">Metadata memorandum. Nomor boleh kosong saat draft, wajib saat finalisasi.</p>
                <div class="form-grid section-space">
                    <div class="field">
                        <label>Nomor memorandum</label>
                        <input class="input" type="text" wire:model.blur="letter_number" placeholder="Diisi sebelum finalisasi">
                        @error('nomor_surat')<div class="help" style="color:#b42318">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label>Tanggal surat</label>
                        <input class="input" type="date" wire:model.blur="letter_date">
                        @error('tanggal_surat')<div class="help" style="color:#b42318">{{ $message }}</div>@enderror
                    </div>
                    <div class="field wide">
                        <label>Dari</label>
                        <input class="input" type="text" wire:model.blur="jabatan_pengirim">
                    </div>
                    <div class="field wide">
                        <label>Hal</label>
                        <input class="input" type="text" wire:model.blur="hal">
                        <div class="help">Default: Teguran Tertulis.</div>
                    </div>
                </div>
            @elseif($step === 2)
                <h2>Identitas Pegawai</h2>
                <p class="sub">Nama yang sama dipakai pada bagian Yth dan Nama. NIP disimpan sebagai teks.</p>
                <div class="form-grid section-space">
                    <div class="field wide">
                        <label>Pilih data pegawai tersimpan (opsional)</label>
                        <select class="select" wire:model.live="employee_id">
                            <option value="">— Isi manual —</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }}{{ $emp->nip ? ' ('.$emp->nip.')' : '' }}</option>
                            @endforeach
                        </select>
                        <div class="help">Memilih pegawai akan menyalin data ke isian surat (dapat diedit tanpa mengubah data sumber).</div>
                    </div>
                    <div class="field wide">
                        <label>Nama lengkap</label>
                        <input class="input" type="text" wire:model.blur="nama_pegawai">
                        @error('nama_pegawai')<div class="help" style="color:#b42318">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label>NIP (18 digit, teks)</label>
                        <input class="input" type="text" wire:model.blur="nip" maxlength="30" placeholder="Contoh: 198001012006041001">
                        @error('nip')<div class="help" style="color:#b42318">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label>Sapaan</label>
                        <select class="select" wire:model.live="sapaan">
                            <option value="Saudara">Saudara</option>
                            <option value="Saudari">Saudari</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Golongan</label>
                        <input class="input" type="text" wire:model.blur="golongan" placeholder="Contoh: III/c">
                    </div>
                    <div class="field">
                        <label>Pangkat</label>
                        <input class="input" type="text" wire:model.blur="pangkat" placeholder="Contoh: Penata">
                    </div>
                    <div class="field wide">
                        <label>Jabatan</label>
                        <textarea class="textarea" rows="2" wire:model.blur="jabatan"></textarea>
                        @error('jabatan')<div class="help" style="color:#b42318">{{ $message }}</div>@enderror
                    </div>
                </div>
            @elseif($step === 3)
                <h2>Referensi Audit</h2>
                <p class="sub">Tambah, hapus, dan ubah urutan referensi. Nomor dan tanda baca akhir dibuat otomatis.</p>

                <div class="field wide section-space">
                    <label>Gunakan kelompok referensi tersimpan (opsional)</label>
                    <select class="select" wire:model.live="audit_reference_group_id">
                        <option value="">— Tidak menggunakan kelompok —</option>
                        @foreach($groups as $g)
                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                        @endforeach
                    </select>
                    <div class="help">Memilih kelompok akan menyalin daftar referensi ke surat ini (dapat diedit tanpa mengubah kelompok sumber).</div>
                </div>

                <div class="section-space">
                    @foreach($daftar_referensi_audit as $i => $ref)
                        <div class="card" style="margin-bottom:10px; padding:12px;" wire:key="ref-{{ $i }}">
                            <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:8px;">
                                <strong>Referensi {{ $i + 1 }}</strong>
                                <div class="btn-row">
                                    <button type="button" class="btn small" wire:click="moveReference({{ $i }}, 'up')" @disabled($i === 0)>↑</button>
                                    <button type="button" class="btn small" wire:click="moveReference({{ $i }}, 'down')" @disabled($i === count($daftar_referensi_audit)-1)>↓</button>
                                    <button type="button" class="btn small danger" wire:click="removeReference({{ $i }})">Hapus</button>
                                </div>
                            </div>
                            <textarea class="textarea" rows="3" wire:model.blur="daftar_referensi_audit.{{ $i }}" placeholder="Isi teks referensi audit (tanpa nomor/tanda baca akhir otomatis)"></textarea>
                        </div>
                    @endforeach
                    <button type="button" class="btn" wire:click="addReference">＋ Tambah referensi</button>
                    @error('daftar_referensi_audit')<div class="help" style="color:#b42318; margin-top:8px;">{{ $message }}</div>@enderror
                </div>

                <div class="card section-space" style="background:#f8fafc;">
                    <h3>Pratinjau format (otomatis)</h3>
                    <div class="section-space" style="font-size:13px;">
                        <div><strong>Menindaklanjuti:</strong></div>
                        @if(count($formattedRefs))
                            <ol style="margin:6px 0 0 20px;">
                                @foreach($formattedRefs as $item)
                                    <li style="margin:4px 0; text-align:justify;">{{ $item['text'] }}{{ $item['suffix'] }}</li>
                                @endforeach
                            </ol>
                        @else
                            <p class="muted" style="margin-top:6px;">Belum ada referensi valid. Tambahkan minimal satu referensi.</p>
                        @endif
                    </div>
                </div>
            @elseif($step === 4)
                <h2>Substansi Teguran</h2>
                <p class="sub">Uraian temuan (Atas) dan redaksi instruksi.</p>
                <div class="form-grid section-space">
                    <div class="field wide">
                        <label>Uraian Teguran (Atas)</label>
                        <textarea class="textarea" rows="5" wire:model.blur="uraian_teguran"></textarea>
                        @error('uraian_teguran')<div class="help" style="color:#b42318">{{ $message }}</div>@enderror
                        <div class="help">Isi tidak boleh terpotong. Jika teks terlalu panjang hingga melebihi 2 halaman, finalisasi akan ditolak.</div>
                    </div>
                    <div class="field wide">
                        <label>Redaksi instruksi (butir 2)</label>
                        <textarea class="textarea" rows="4" wire:model.blur="instruksi"></textarea>
                        <div class="help">Placeholder <code>@{{sapaan}}</code> atau <code>{sapaan}</code> akan diganti menjadi <strong>{{ $sapaan }}</strong> saat render.</div>
                    </div>
                    <div class="field">
                        <label>Jabatan penandatangan</label>
                        <input class="input" type="text" wire:model.blur="jabatan_penandatangan">
                    </div>
                    <div class="field">
                        <label>Nama penandatangan</label>
                        <input class="input" type="text" wire:model.blur="nama_penandatangan">
                    </div>
                    <div class="field wide">
                        <label>Tembusan</label>
                        @foreach($daftar_tembusan as $i => $cc)
                            <div style="display:flex; gap:8px; margin-bottom:8px; align-items:center;" wire:key="cc-{{ $i }}">
                                <span style="width:22px; color:#64748b;">{{ $i+1 }}.</span>
                                <input class="input" type="text" wire:model.blur="daftar_tembusan.{{ $i }}" placeholder="Jabatan/instansi tembusan">
                                <button type="button" class="btn small danger" wire:click="removeTembusan({{ $i }})">Hapus</button>
                            </div>
                        @endforeach
                        <button type="button" class="btn small" wire:click="addTembusan">＋ Tambah tembusan</button>
                    </div>
                </div>
            @elseif($step === 5)
                <h2>Review Data</h2>
                <p class="sub">Periksa kelengkapan sebelum pratinjau/finalisasi PDF.</p>
                <div class="section-space">
                    <div class="list-row"><span>Nomor memorandum</span><strong>{{ $letter_number ?: '— (wajib saat final)' }}</strong></div>
                    <div class="list-row"><span>Tanggal</span><strong>{{ $letter_date ?: '—' }}</strong></div>
                    <div class="list-row"><span>Penerima (Yth/Nama)</span><strong>{{ $nama_pegawai ?: '—' }}</strong></div>
                    <div class="list-row"><span>NIP</span><strong>{{ $nip ?: '—' }}</strong></div>
                    <div class="list-row"><span>Jabatan</span><strong>{{ $jabatan ?: '—' }}</strong></div>
                    <div class="list-row"><span>Sapaan</span><strong>{{ $sapaan }}</strong></div>
                    <div class="list-row"><span>Referensi audit</span><strong>{{ count($formattedRefs) }} dokumen</strong></div>
                    <div class="list-row"><span>Penandatangan</span><strong>{{ $nama_penandatangan ?: '—' }}</strong></div>
                    <div class="list-row"><span>Output</span><strong>1 PDF, 2 halaman A4 (hal. 1 paraf, hal. 2 tanpa)</strong></div>
                </div>
                <div class="alert warn section-space">Pratinjau dan finalisasi akan merender PDF aktual. Jika hasil bukan 2 halaman, proses akan ditolak dengan pesan kesalahan.</div>
                <div class="card section-space" style="background:#f8fafc;">
                    <strong>Ringkasan referensi:</strong>
                    @if(count($formattedRefs))
                        <ol style="margin:6px 0 0 20px; font-size:13px;">
                            @foreach($formattedRefs as $item)
                                <li style="margin:3px 0;">{{ $item['text'] }}{{ $item['suffix'] }}</li>
                            @endforeach
                        </ol>
                    @else
                        <p class="muted">—</p>
                    @endif
                </div>
            @endif

            <div style="display:flex; justify-content:space-between; gap:12px; margin-top:18px; flex-wrap:wrap;">
                <button type="button" class="btn" wire:click="prevStep" @disabled($step===1)>← Sebelumnya</button>
                <div class="btn-row">
                    <button type="button" class="btn" wire:click="saveDraft" wire:loading.attr="disabled">Simpan Draft</button>
                    @if($step < 5)
                        <button type="button" class="btn primary" wire:click="nextStep">Selanjutnya →</button>
                    @else
                        <button type="button" class="btn" wire:click="previewPdf" wire:loading.attr="disabled">Pratinjau PDF</button>
                        <button type="button" class="btn primary" wire:click="finalize" wire:loading.attr="disabled" wire:confirm="Finalisasi akan membuat PDF arsip (2 halaman) dan mengunci surat. Lanjutkan?">Finalisasi & Simpan PDF</button>
                    @endif
                </div>
            </div>
            <div wire:loading class="help section-space">Memproses… (render PDF dapat memakan waktu beberapa detik)</div>
        </div>

        <aside class="card" style="position:sticky; top:90px;">
            <h3>Ringkasan</h3>
            <p class="sub">Data yang sedang diisi</p>
            <div class="section-space">
                <div class="list-row"><span class="muted">Kepada</span><strong style="text-align:right; max-width:60%">{{ $nama_pegawai ?: '—' }}</strong></div>
                <div class="list-row"><span class="muted">Nomor</span><strong>{{ $letter_number ?: '—' }}</strong></div>
                <div class="list-row"><span class="muted">Tanggal</span><strong>{{ $letter_date ?: '—' }}</strong></div>
                <div class="list-row"><span class="muted">Referensi</span><strong>{{ count($formattedRefs) }}</strong></div>
                <div class="list-row" style="border-bottom:none;"><span class="muted">Output</span><strong>2 hlm A4</strong></div>
            </div>
            <div class="help section-space">Draft disimpan ke database lokal (SQLite). PDF final disimpan privat di storage dan bersifat snapshot (tidak berubah akibat pengaturan global setelah finalisasi).</div>
        </aside>
    </div>
</div>
