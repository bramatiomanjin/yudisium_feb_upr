# Sistem Informasi SK Yudisium FEB UPR

Sistem berbasis web untuk digitalisasi pengajuan dan verifikasi berkas Surat Keputusan (SK) Yudisium di Fakultas Ekonomi dan Bisnis, Universitas Palangka Raya (UPR). Repositori ini berisi struktur inti *backend* yang dibangun menggunakan framework Laravel.

## 🚀 Fitur Utama

**👨‍🎓 Modul Mahasiswa (Publik)**
* **Pengajuan Terpadu:** Pengisian form identitas, data akademik, dan unggah dokumen persyaratan (PDF) dalam satu pintu dengan proteksi transaksi *database*.
* **Tracking Status:** Pengecekan status pengajuan secara *real-time* menggunakan NIM dan Kode Pengajuan tanpa perlu memiliki akun.
* **Sistem Revisi Pintar:** Mahasiswa hanya perlu (dan hanya bisa) memperbaiki *field* data atau dokumen spesifik yang ditolak oleh Admin. Data yang sudah disetujui akan otomatis terkunci.

**🧑‍💼 Modul Admin Akademik**
* **Validasi Mikro:** Pemeriksaan berkas dan data teks dilakukan secara detail per-kolom (Field-by-Field) dan per-dokumen (Document-by-Document).
* **Feedback Revisi:** Pemberian catatan penolakan spesifik pada data yang salah agar mahasiswa tahu persis apa yang harus diperbaiki.
* **Automasi Status:** Sistem secara cerdas menyimpulkan status akhir pengajuan (*Terverifikasi*, *Verifikasi Admin*, atau *Perlu Revisi*) berdasarkan kalkulasi hasil validasi seluruh baris data.
* **Keamanan Dokumen Private:** File PDF mahasiswa disimpan di direktori internal (`storage/app/private`) dan hanya bisa dirender oleh Admin yang memiliki sesi *login* valid.

**👑 Modul Super Admin**
* **Manajemen Pengguna:** Sistem CRUD untuk mengelola otorisasi penambahan dan penghapusan akun Admin (staf akademik).
* **Log Aktivitas (Audit Trail):** Perekaman riwayat aktivitas validasi yang transparan (melacak identitas Admin yang memverifikasi data, waktu eksekusi, serta keputusan yang diambil).

## 🛠️ Tech Stack

* **Framework Backend:** Laravel
* **Database:** MySQL
* **Environment Server:** Laragon (PHP, Node.js, Composer)

## 🗄️ Struktur Database Inti

Sistem ini didukung oleh arsitektur *relational database* dengan 8 tabel utama untuk menjaga integritas data pengajuan dan riwayat revisi:
1. `users` - Autentikasi dan otorisasi tingkatan Admin/Super Admin.
2. `mahasiswa` - Entitas biodata dan riwayat identitas akademik mahasiswa.
3. `pengajuan_yudisium` - Entitas *header* pendaftaran SK Yudisium.
4. `jenis_dokumen` - Master data untuk persyaratan dokumen (Ijazah, KHS, dll).
5. `pengajuan_dokumen` - Penyimpanan referensi *path* file PDF dan status validasi lampiran.
6. `validasi_field` - Tabel *tracking* log status persetujuan untuk masing-masing baris inputan teks mahasiswa.
7. `riwayat_revisi` - Tabel pencatatan nilai historis (perubahan nilai lama ke nilai baru) setiap kali mahasiswa mensubmit ulang perbaikan data.
8. `riwayat_status` - (Opsional/Mendatang) Pencatatan pergerakan status makro pengajuan.

## 💻 Panduan Instalasi Lokal

1. **Clone repositori**
```bash
git clone https://github.com/bramatiomanjin/yudisium_feb_upr.git
cd yudisium-feb

```

2. **Install dependensi backend**
```bash
composer install

```


3. **Konfigurasi Environment**
Salin file `.env.example` menjadi `.env`, lalu atur konfigurasi koneksi *database* MySQL (misal: `DB_DATABASE=yudisium_feb`).
```bash
copy .env.example .env
php artisan key:generate

```


4. **Migrasi dan Seeding Database**
Jalankan perintah ini untuk membangun seluruh relasi tabel dan memasukkan data *dummy* awal (termasuk persyaratan dokumen dan akun *Super Admin* default).
```bash
php artisan migrate --seed

```


5. **Jalankan Aplikasi**
Akses melalui *virtual host* Laragon (contoh: `[http://yudisium-feb.test](http://yudisium-feb.test)`) atau jalankan server lokal bawaan Laravel:
```bash
php artisan serve

```



---

*Dikembangkan oleh Ciko Christian untuk proyek Sistem Informasi Fakultas Ekonomi dan Bisnis Universitas Palangka Raya.*