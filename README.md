# Yudisium FEB UPR

Frontend Sistem Pendaftaran dan Tracking SK Yudisium Fakultas Ekonomi dan Bisnis Universitas Palangka Raya.

## Status Proyek

Frontend sudah berada pada tahap **frontend freeze candidate** dan disiapkan untuk integrasi backend Laravel + MySQL.

Backend saat ini belum terhubung. Konfigurasi koneksi frontend berada di:

```text
frontend/js/yudisium_api.js
```

Secara default:

```js
backendConnected: false
```

Ubah menjadi `true` ketika endpoint Laravel sudah siap.

## Alur Mahasiswa

1. Mahasiswa mengisi formulir pengajuan Yudisium.
2. Data dan dokumen dikirim ke backend.
3. Backend membuat **Kode SK Yudisium**.
4. Mahasiswa melakukan tracking menggunakan **NIM + Kode SK Yudisium**.
5. Jika perlu revisi, mahasiswa mengirim perbaikan melalui halaman revisi.
6. Setelah seluruh proses selesai, status akhir adalah **SK Siap Diambil**.
7. Mahasiswa mengambil SK di Bagian Akademik FEB UPR.

Frontend tidak membuat Kode SK Yudisium sendiri. Kode harus berasal dari backend/database.

## Alur Admin

1. Admin melihat daftar pengajuan.
2. Admin memeriksa data dan dokumen mahasiswa.
3. Admin melakukan verifikasi atau meminta revisi.
4. Revisi yang dikirim mahasiswa direview kembali.
5. Pengajuan yang terverifikasi masuk ke proses SK.
6. Tahapan SK:
   - Terverifikasi
   - Pembuatan SK
   - TTD Wakil Dekan
   - TTD Dekan
   - SK Siap Diambil
7. Aktivitas administrasi dicatat dalam History.

## Status Canonical

```text
menunggu_verifikasi
perlu_revisi
revisi_dikirim
terverifikasi
pembuatan_sk
ttd_wakil_dekan
ttd_dekan
sk_siap_diambil
```

`sk_terbit` hanya boleh digunakan sebagai compatibility alias untuk data lama dan bukan status utama pada UI.

## Output Administrasi

- Export Excel digunakan sebagai data sumber operasional untuk penyusunan SK.
- Status akhir mahasiswa adalah **SK Siap Diambil**.
- Distribusi SK digital/PDF belum dikonfirmasi sebagai kebutuhan sistem, sehingga frontend tidak mengasumsikan adanya download PDF SK.
- Upload PDF tetap digunakan untuk dokumen persyaratan mahasiswa.

## Struktur Utama

```text
frontend/
├── admin/
├── mahasiswa/
├── assets/
├── css/
└── js/
```

### JavaScript utama

```text
yudisium_api.js
admin.js
admin_dashboard.js
admin_pengajuan.js
admin_detail.js
admin_verifikasi.js
admin_review_revisi.js
admin_proses_sk.js
admin_history.js
admin_detail_history.js
student_pengajuan.js
student_pengajuan_api_bridge.js
student_tracking.js
student_revisi.js
```

## API Frontend

`frontend/js/yudisium_api.js` menjadi boundary utama antara UI dan Laravel.

Method yang tersedia:

```text
getSubmissions()
getSubmission()
submitApplication()
findSubmission()
getDocuments()
getVerificationResult()
verifySubmission()
getRevisionSubmission()
submitRevision()
reviewRevision()
updateSkStatus()
exportExcel()
getHistory()
getHistoryDetail()
```

## Rencana Endpoint Laravel

Endpoint dapat disesuaikan saat backend dibangun. Kontrak frontend saat ini mengarah pada pola berikut:

```text
GET    /api/submissions
POST   /api/submissions
GET    /api/submissions/{id}
POST   /api/tracking
GET    /api/submissions/{id}/documents
GET    /api/submissions/{id}/verification
POST   /api/submissions/{id}/verify
GET    /api/submissions/{id}/revision
POST   /api/submissions/{id}/revision
POST   /api/submissions/{id}/revision/review
PATCH  /api/submissions/{id}/sk-status
GET    /api/submissions/export-excel
GET    /api/history
GET    /api/history/{id}
```

## Authentication Admin

Authentication dan pengelolaan akun admin pada tahap frontend masih menggunakan penyimpanan browser untuk kebutuhan prototype UI.

Saat integrasi backend, bagian ini wajib diganti dengan authentication dan authorization Laravel/database. Jangan menggunakan localStorage sebagai mekanisme authentication produksi.

Hak akses yang direncanakan:

- **Admin**: menjalankan workflow pengajuan, revisi, verifikasi, proses SK, serta melihat history sendiri.
- **Super Admin**: memiliki kemampuan operasional Admin dan dapat mengelola akun serta melihat history seluruh admin.

## Menjalankan Frontend

Frontend dapat dibuka melalui web server lokal sederhana.

Contoh dengan VS Code Live Server atau server statis lain, lalu buka:

```text
frontend/index.html
```

Jangan mengandalkan `file://` untuk pengujian final karena integrasi API dan beberapa behavior browser lebih konsisten melalui HTTP lokal.

## Catatan Integrasi Laravel

Saat backend siap:

1. Atur `backendConnected: true` di `frontend/js/yudisium_api.js`.
2. Sesuaikan `baseUrl` jika API tidak berada pada `/api`.
3. Pastikan response backend menyediakan field yang dipakai normalizer API.
4. Pindahkan authentication Admin ke backend.
5. Terapkan authorization di server; pembatasan role di frontend hanya untuk UX, bukan keamanan.
6. Validasi seluruh file upload di server.
7. Generate Kode SK Yudisium hanya di backend.

## Catatan UI/UX

Identitas visual menggunakan nuansa FEB UPR:

- hijau institusional sebagai warna utama,
- aksen emas akademik,
- tipografi dan ukuran kontrol yang nyaman untuk staf administrasi,
- layout formal dan profesional,
- responsive untuk desktop, tablet, dan mobile.

Desain halaman utama dianggap selesai dan sebaiknya tidak dirombak saat integrasi backend kecuali ada perubahan kebutuhan dari pihak FEB UPR.
