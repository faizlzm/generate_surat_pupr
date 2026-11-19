# Generator Surat Teguran (Laravel + Word/DOCX)

Aplikasi web lokal (single-user, localhost) untuk menyusun dan membuat **Surat/Memorandum Teguran Tertulis** secara massal. Alur utama menghasilkan **Microsoft Word `.docx`** (bukan PDF): **1 penerima = 1 berkas DOCX** yang berisi **dua versi memorandum** (halaman/versi pertama dengan tabel paraf, versi kedua tanpa tabel paraf), dan proses massal menghasilkan **1 arsip `Hasil_Surat_Teguran.zip`** berisi DOCX individual.

Implementasi mengikuti blueprint `../Generator_Surat_Teguran_Blueprint_Final/` (master template, pemetaan placeholder, dan aturan bisnis). Pratinjau di layar adalah representasi visual HTML, **bukan** hasil DOCX final.

## Fitur utama (alur Word)
- Halaman tunggal **Buat Surat** (tanpa Dashboard/wizard/draft/editor instruksi standar).
- Informasi bersama: tanggal, pengirim, perihal, penandatangan, tembusan.
- Multi **kelompok audit** (referensi dinamis per kelompok) + **referensi khusus per penerima** (menggantikan seluruh referensi kelompok penerima tersebut).
- Tabel penerima massal: tambah/salin/hapus/terpilih, terapkan kelompok massal, tempel dari Excel, impor CSV/TSV, unduh format CSV, cari & filter.
- **Validasi server-side** sebelum generate (nomor memorandum unik per proses, NIP string 18 digit, sapaan eksplisit Saudara/Saudari, referensi/kelompok/tembusan valid, min 1 & maks 200 penerima/proses).
- Penomoran & akhiran referensi otomatis (`;`, `; dan`, `.`), penomoran mulai 1 per surat.
- **Persistensi konfigurasi yang dapat dipakai ulang** (tanpa draft & tanpa riwayat hasil): pengaturan instansi/pengirim/penandatangan/tembusan/perihal dan kelompok audit+referensi disimpan di SQLite. **Data penerima tidak disimpan** (hilang saat reload/tutup).
- Keluaran: ZIP diunduh; berkas sementara proses dibuat di area privat (`storage/app/private/generator/...`) dan dibersihkan setelah unduhan (DOCX individual & direktori proses dihapus).

## Prasyarat
- PHP **8.3+** (dikembangkan/diverifikasi dengan PHP 8.4) dengan ekstensi umum Laravel.
- **Ekstensi PHP `zip` dan `gd` aktif** (wajib untuk pembuatan arsip ZIP dan pemrosesan oleh PHPWord/template). Jika `php.ini` belum mengaktifkannya, jalankan perintah PHP dengan `-d extension=zip -d extension=gd` atau aktifkan permanen di `php.ini` (lihat Catatan lingkungan).
- Composer.
- SQLite (default). Node/npm opsional (aset front-end sudah ter-bundle di view; tidak wajib untuk alur generate).

## Menjalankan (lokal)
```bash
cd laravel-app
composer install
copy .env.example .env   # bila belum ada (Linux/macOS: cp .env.example .env)
php artisan key:generate
php artisan migrate
php artisan serve --host=127.0.0.1 --port=8000
```
Buka: http://127.0.0.1:8000

> Pastikan PHP runtime yang melayani web (`php artisan serve` / web server) memiliki ekstensi `zip` dan `gd` aktif. Jika belum, aktifkan di `php.ini` yang digunakan runtime tersebut (pada Windows sering berupa `extension=zip` dan `extension=gd` di php.ini yang benar), lalu restart server.

## Cara pakai singkat
1. Isi **Informasi bersama** (tanggal, pengirim, perihal) dan, bila perlu, **Pengaturan** (kop/instansi, jabatan & nama penandatangan, tembusan). Simpan pengaturan untuk dipakai ulang.
2. Kelola **Kelompok referensi audit** (tambah/salin/hapus, isi referensi). Simpan kelompok agar dapat dimuat ulang.
3. Isi **Daftar penerima** (manual, salin, tempel Excel, atau impor CSV/TSV). Pilih kelompok audit per baris atau gunakan referensi khusus bila diperlukan. Pastikan status baris siap (nomor memorandum unik per proses, NIP 18 digit, sapaan dipilih, alasan/identitas terisi).
4. Gunakan **Lihat surat** untuk pratinjau visual HTML per penerima.
5. Klik **Buat dokumen Word (ZIP)** — server memvalidasi dan, jika valid, mengunduh `Hasil_Surat_Teguran.zip` berisi DOCX per penerima.
6. Buka hasil di **Microsoft Word** untuk pemeriksaan akhir tata letak/paginasi dan isi sebelum digunakan.

## Endpoint & perilaku penting
- `GET /` — halaman Buat Surat (memuat konfigurasi tersimpan).
- `POST /buat-dokumen-word` (`surat.word.generate`) — body JSON payload; sukses → unduhan ZIP; gagal → JSON `{pesan, message, errors, rincian}` (422 validasi / 500 kegagalan proses/lingkungan). Tidak ada ZIP parsial saat gagal.
- `POST /generator/simpan-pengaturan`, `POST /generator/simpan-kelompok-audit` — simpan konfigurasi yang dapat dipakai ulang.
- Rute alur lama (`/letters*`, `/settings`) dialihkan ke `/`.

## Struktur utama (alur Word)
- `app/Services/WordGenerator/` — `ReferenceFormatter`, `BatchValidator`, `PayloadNormalizer`, `DocxBatchGenerator` (PHPWord `TemplateProcessor`, instance baru per penerima, clone baris referensi/tembusan, cek placeholder tersisa, pembuatan ZIP).
- `app/Http/Controllers/GenerateWordController.php` — endpoint generate + pembersihan berkas sementara.
- `app/Http/Controllers/GeneratorPageController.php` — halaman & persistensi pengaturan/kelompok audit.
- `resources/views/word-generator.blade.php` — UI satu-halaman & wiring ke backend.
- `resources/templates/master_template_teguran.docx` — master template Word.
- `database/migrations/*app_settings*`, `*audit_reference_groups*` — penyimpanan konfigurasi yang dapat dipakai ulang.
- `tests/Unit/WordGenerator/*`, `tests/Feature/GenerateWordEndpointTest.php`, `tests/Feature/GeneratorConfigPersistenceTest.php` — pengujian aturan/endpoint/persistensi.

## Catatan lingkungan & batasan
- **Ekstensi `zip`/`gd`:** verifikasi & pengujian otomatis di lingkungan pengembangan ini dijalankan dengan `php -d extension=zip -d extension=gd`; pastikan runtime web Anda juga mengaktifkannya.
- **Paginasi 2 halaman:** DOCX adalah dokumen mengalir. Dua versi memorandum per penerima adalah target rancangan, namun **jumlah halaman/tata letak akhir wajib diverifikasi di Microsoft Word** yang digunakan (teks panjang dapat meluber; isi tidak dipotong otomatis).
- **Pratinjau:** visual HTML saja, bukan jaminan hasil DOCX identik.
- **Data & privasi:** ditujukan untuk `127.0.0.1` single-user; data penerima tidak disimpan (tanpa draft/riwayat). Kelompok audit & pengaturan tersimpan di DB lokal; hasil unduhan & berkas sementara harus diperlakukan sebagai data sensitif.
- **Belum termasuk/terbuka:** impor `.xlsx` native (tersedia tempel Excel & CSV/TSV), penomoran memorandum resmi otomatis, riwayat hasil final, aktivasi permanen `zip/gd` pada php.ini, dan pembersihan menyeluruh kode/dependensi alur lama PDF/wizard/draft (masih ada di repo namun bukan alur utama).

## Pengujian
```bash
cd laravel-app
php -d extension=zip -d extension=gd vendor/bin/phpunit
```
Mencakup aturan formatter/validasi Word, endpoint generate (termasuk 30 penerima → ZIP & kebersihan storage), persistensi konfigurasi, dan pengujian bawaan lainnya. Uji skenario penerimaan dan verifikasi visual di Microsoft Word tetap perlu dilakukan secara manual.
