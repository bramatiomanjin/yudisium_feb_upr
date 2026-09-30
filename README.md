# Sistem Informasi Yudisium FEB UPR

Sistem Informasi Yudisium Fakultas Ekonomi dan Bisnis Universitas Palangka Raya merupakan aplikasi berbasis web yang digunakan untuk mendukung proses pengajuan, verifikasi, revisi, tracking, hingga proses penerbitan Surat Keputusan (SK) Yudisium.

Sistem ini dibangun menggunakan Laravel dan MySQL serta menyediakan antarmuka untuk tiga jenis pengguna utama, yaitu Mahasiswa, Admin Akademik, dan Super Admin.

---

## Fitur Utama

### Mahasiswa

Mahasiswa dapat menggunakan sistem tanpa perlu membuat akun.

Fitur yang tersedia antara lain:

- Mengisi data identitas mahasiswa.
- Mengisi data akademik.
- Mengunggah dokumen persyaratan Yudisium.
- Mendapatkan Kode Pengajuan Yudisium setelah pengajuan berhasil.
- Melakukan tracking pengajuan menggunakan NIM atau Kode Pengajuan Yudisium.
- Melihat status pengajuan.
- Melihat feedback dari Admin jika terdapat data atau dokumen yang perlu diperbaiki.
- Mengirim revisi hanya pada data atau dokumen yang diminta Admin.
- Melihat perkembangan proses SK hingga siap diambil.

---

### Admin Akademik

Admin Akademik digunakan oleh staf untuk melakukan pemeriksaan dan pemrosesan pengajuan mahasiswa.

Fitur utama meliputi:

- Dashboard Admin.
- Melihat daftar seluruh pengajuan Yudisium.
- Melihat detail pengajuan mahasiswa.
- Memeriksa data mahasiswa per field.
- Memeriksa dokumen mahasiswa satu per satu.
- Memberikan feedback revisi.
- Memproses permintaan revisi mahasiswa.
- Melakukan review hasil revisi.
- Melakukan verifikasi pengajuan.
- Memproses status SK Yudisium.
- Melihat history aktivitas pengajuan.
- Melakukan export data Yudisium ke Excel.
- Melihat dokumen mahasiswa melalui private document preview.

---

### Super Admin

Super Admin memiliki seluruh akses Admin Akademik serta fitur tambahan untuk pengelolaan sistem.

Fitur utama meliputi:

- Mengelola akun Admin.
- Menyetujui pendaftaran Admin baru.
- Menolak pendaftaran Admin.
- Mengaktifkan akun Admin.
- Menonaktifkan akun Admin.
- Mengelola pengaturan dokumen persyaratan.
- Melihat log aktivitas sistem.
- Melakukan backup dokumen mahasiswa.
- Mengakses seluruh fitur operasional Admin Akademik.

---

## Alur Status Pengajuan

Status utama pengajuan Yudisium pada sistem adalah:

```text
MENUNGGU_VERIFIKASI
        ↓
PERLU_REVISI
        ↓
REVISI_DIKIRIM
        ↓
TERVERIFIKASI
        ↓
PEMBUATAN_SK
        ↓
PARAF_PIMPINAN
        ↓
TTD_DEKAN
        ↓
SK_SIAP_DIAMBIL
