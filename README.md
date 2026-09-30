# Sistem Informasi Yudisium FEB UPR

Sistem Informasi Yudisium Fakultas Ekonomi dan Bisnis Universitas Palangka Raya merupakan aplikasi berbasis web untuk membantu proses pengajuan, verifikasi, revisi, tracking, hingga proses penerbitan SK Yudisium.

Sistem ini dibangun menggunakan Laravel dan MySQL dengan dukungan antarmuka untuk Mahasiswa, Admin Akademik, dan Super Admin.

## Fitur Utama

### Mahasiswa

Mahasiswa dapat menggunakan sistem tanpa perlu membuat akun.

Fitur yang tersedia meliputi:

- Mengisi data identitas dan data akademik.
- Mengunggah dokumen persyaratan Yudisium.
- Mendapatkan Kode Pengajuan Yudisium setelah pengajuan berhasil.
- Melakukan tracking pengajuan menggunakan NIM atau Kode Pengajuan.
- Melihat status pengajuan secara berkala.
- Melihat feedback dari Admin apabila terdapat data atau dokumen yang perlu diperbaiki.
- Mengirim revisi hanya pada field atau dokumen yang diminta Admin.
- Melihat perkembangan proses SK sampai siap diambil.

### Admin Akademik

Admin Akademik digunakan oleh staf untuk memproses pengajuan mahasiswa.

Fitur utama:

- Dashboard pengajuan.
- Daftar seluruh pengajuan Yudisium.
- Pemeriksaan data mahasiswa per field.
- Pemeriksaan dokumen per dokumen.
- Pemberian feedback revisi.
- Review hasil revisi mahasiswa.
- Verifikasi pengajuan.
- Proses status SK Yudisium.
- History aktivitas pengajuan.
- Export data Yudisium ke Excel.
- Preview dokumen mahasiswa secara private.

### Super Admin

Super Admin memiliki akses tambahan untuk pengelolaan sistem.

Fitur utama:

- Mengelola akun Admin.
- Approve dan reject pendaftaran Admin.
- Mengaktifkan dan menonaktifkan Admin.
- Mengatur dokumen persyaratan.
- Melihat log aktivitas.
- Melakukan backup dokumen mahasiswa.

## Alur Status Pengajuan

Status utama pengajuan pada sistem:

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
