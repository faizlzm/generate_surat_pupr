<div>
    <div class="page-head">
        <div>
            <h1>Pengaturan</h1>
            <p class="sub">Konfigurasi default surat dan data master yang dapat digunakan kembali.</p>
        </div>
    </div>

    <div class="card">
        <div class="stepper" style="margin-bottom:16px;">
            @foreach(['general' => 'Identitas & Pengesahan', 'employees' => 'Data Pegawai', 'groups' => 'Kelompok Referensi Audit'] as $key => $label)
                <button type="button" class="step {{ $tab === $key ? 'active' : '' }}" wire:click="setTab('{{ $key }}')">{{ $label }}</button>
            @endforeach
        </div>

        @if($tab === 'general')
            <form wire:submit="saveGeneral">
                <h2>Identitas Instansi & Kop</h2>
                <p class="sub">Digunakan sebagai default pada surat baru dan ikut tersnapshot saat finalisasi.</p>
                <div class="form-grid section-space">
                    <div class="field wide"><label>Nama kementerian</label><input class="input" type="text" wire:model.blur="ministry"></div>
                    <div class="field wide"><label>Nama direktorat/unit</label><input class="input" type="text" wire:model.blur="directorate"></div>
                    <div class="field wide"><label>Alamat</label><textarea class="textarea" rows="2" wire:model.blur="address"></textarea></div>
                </div>

                <h2 class="section-space">Pengirim & Pengesahan</h2>
                <div class="form-grid section-space">
                    <div class="field wide"><label>Dari (jabatan pengirim)</label><input class="input" type="text" wire:model.blur="sender_title"></div>
                    <div class="field"><label>Hal (perihal)</label><input class="input" type="text" wire:model.blur="subject"></div>
                    <div class="field"><label>Nama penandatangan</label><input class="input" type="text" wire:model.blur="signatory_name"></div>
                    <div class="field wide"><label>Jabatan penandatangan</label><input class="input" type="text" wire:model.blur="signatory_title"></div>
                    <div class="field wide">
                        <label>Redaksi instruksi default (butir 2)</label>
                        <textarea class="textarea" rows="3" wire:model.blur="instruction_template"></textarea>
                        <div class="help">Gunakan placeholder <code>@{{sapaan}}</code> atau <code>{sapaan}</code> (akan diganti Saudara/Saudari).</div>
                    </div>
                    <div class="field wide">
                        <label>Tembusan default</label>
                        @foreach($cc_list as $i => $cc)
                            <div style="display:flex; gap:8px; margin-bottom:8px; align-items:center;" wire:key="setcc-{{ $i }}">
                                <span style="width:22px; color:#64748b;">{{ $i+1 }}.</span>
                                <input class="input" type="text" wire:model.blur="cc_list.{{ $i }}">
                                <button type="button" class="btn small danger" wire:click="removeCc({{ $i }})">Hapus</button>
                            </div>
                        @endforeach
                        <button type="button" class="btn small" wire:click="addCc">＋ Tambah tembusan</button>
                    </div>
                </div>

                <div class="btn-row section-space">
                    <button type="submit" class="btn primary">Simpan Pengaturan</button>
                </div>
                <div class="help section-space">Logo kop: letakkan berkas di <code>public/images/kop-logo.png</code> (opsional, placeholder digunakan jika tidak ada). Perubahan pengaturan tidak mengubah arsip PDF final yang sudah dibuat (snapshot).</div>
            </form>

        @elseif($tab === 'employees')
            <div class="grid two" style="align-items:start;">
                <div>
                    <h2>{{ $editing_employee_id ? 'Edit Pegawai' : 'Tambah Pegawai' }}</h2>
                    <p class="sub">Data pegawai dapat dipilih saat membuat surat (disalin ke surat, tidak mengubah sumber saat diedit di surat).</p>
                    <form wire:submit="saveEmployee" class="section-space">
                        <div class="form-grid">
                            <div class="field wide"><label>Nama lengkap</label><input class="input" type="text" wire:model.blur="emp_name">@error('emp_name')<div class="help" style="color:#b42318">{{ $message }}</div>@enderror</div>
                            <div class="field"><label>NIP (teks)</label><input class="input" type="text" wire:model.blur="emp_nip"></div>
                            <div class="field"><label>Golongan</label><input class="input" type="text" wire:model.blur="emp_grade" placeholder="III/c"></div>
                            <div class="field"><label>Pangkat</label><input class="input" type="text" wire:model.blur="emp_rank" placeholder="Penata"></div>
                            <div class="field wide"><label>Jabatan</label><input class="input" type="text" wire:model.blur="emp_position"></div>
                        </div>
                        <div class="btn-row section-space">
                            <button type="submit" class="btn primary">{{ $editing_employee_id ? 'Perbarui' : 'Simpan' }}</button>
                            @if($editing_employee_id)
                                <button type="button" class="btn" wire:click="resetEmployeeForm">Batal</button>
                            @endif
                        </div>
                    </form>
                </div>
                <div>
                    <h2>Daftar Pegawai</h2>
                    <table class="table section-space">
                        <thead><tr><th>Nama</th><th>NIP</th><th>Jabatan</th><th>Aksi</th></tr></thead>
                        <tbody>
                            @forelse($employees as $e)
                                <tr wire:key="emp-{{ $e->id }}">
                                    <td>{{ $e->name }}</td>
                                    <td>{{ $e->nip ?: '—' }}</td>
                                    <td>{{ $e->position ?: '—' }}</td>
                                    <td>
                                        <div class="btn-row">
                                            <button type="button" class="btn small" wire:click="editEmployee({{ $e->id }})">Edit</button>
                                            <button type="button" class="btn small danger" wire:click="deleteEmployee({{ $e->id }})" wire:confirm="Hapus data pegawai ini?">Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="muted">Belum ada data pegawai.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        @elseif($tab === 'groups')
            <div class="grid two" style="align-items:start;">
                <div>
                    <h2>{{ $editing_group_id ? 'Edit Kelompok Referensi' : 'Tambah Kelompok Referensi' }}</h2>
                    <p class="sub">Kelompok referensi audit dapat dipilih dan disalin ke surat (tetap bisa diedit per surat).</p>
                    <form wire:submit="saveGroup" class="section-space">
                        <div class="form-grid">
                            <div class="field wide"><label>Nama kelompok</label><input class="input" type="text" wire:model.blur="group_name">@error('group_name')<div class="help" style="color:#b42318">{{ $message }}</div>@enderror</div>
                            <div class="field wide"><label>Deskripsi (opsional)</label><input class="input" type="text" wire:model.blur="group_description"></div>
                            <div class="field wide">
                                <label>Daftar referensi</label>
                                @foreach($group_references as $i => $ref)
                                    <div style="margin-bottom:8px;" wire:key="gref-{{ $i }}">
                                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                            <span class="muted">Referensi {{ $i+1 }}</span>
                                            <div class="btn-row">
                                                <button type="button" class="btn small" wire:click="moveGroupRef({{ $i }}, 'up')" @disabled($i===0)>↑</button>
                                                <button type="button" class="btn small" wire:click="moveGroupRef({{ $i }}, 'down')" @disabled($i===count($group_references)-1)>↓</button>
                                                <button type="button" class="btn small danger" wire:click="removeGroupRef({{ $i }})">Hapus</button>
                                            </div>
                                        </div>
                                        <textarea class="textarea" rows="2" wire:model.blur="group_references.{{ $i }}"></textarea>
                                    </div>
                                @endforeach
                                <button type="button" class="btn small" wire:click="addGroupRef">＋ Tambah referensi</button>
                            </div>
                        </div>
                        @if(count($groupFormattedPreview))
                            <div class="card section-space" style="background:#f8fafc;">
                                <strong>Pratinjau format:</strong>
                                <ol style="margin:6px 0 0 20px; font-size:13px;">
                                    @foreach($groupFormattedPreview as $item)
                                        <li>{{ $item['text'] }}{{ $item['suffix'] }}</li>
                                    @endforeach
                                </ol>
                            </div>
                        @endif
                        <div class="btn-row section-space">
                            <button type="submit" class="btn primary">{{ $editing_group_id ? 'Perbarui' : 'Simpan' }}</button>
                            @if($editing_group_id)
                                <button type="button" class="btn" wire:click="resetGroupForm">Batal</button>
                            @endif
                        </div>
                    </form>
                </div>
                <div>
                    <h2>Daftar Kelompok</h2>
                    <table class="table section-space">
                        <thead><tr><th>Nama</th><th>Jumlah Ref.</th><th>Aksi</th></tr></thead>
                        <tbody>
                            @forelse($groups as $g)
                                <tr wire:key="grp-{{ $g->id }}">
                                    <td>
                                        <strong>{{ $g->name }}</strong>
                                        @if($g->description)<div class="muted" style="font-size:12px;">{{ $g->description }}</div>@endif
                                    </td>
                                    <td>{{ count($g->references()) }}</td>
                                    <td>
                                        <div class="btn-row">
                                            <button type="button" class="btn small" wire:click="editGroup({{ $g->id }})">Edit</button>
                                            <button type="button" class="btn small danger" wire:click="deleteGroup({{ $g->id }})" wire:confirm="Hapus kelompok referensi ini?">Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="muted">Belum ada kelompok referensi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
