<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        Dashboard Admin - Yudisium FEB UPR
    </title>


    <link
        rel="stylesheet"
        href="{{ asset('css/style.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/admin.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/admin_theme.css') }}"
    >

</head>


<body>

    <div class="admin-layout">


        <!-- =====================================================
             SIDEBAR
        ====================================================== -->

        <aside class="admin-sidebar">

            <div class="admin-sidebar-brand">

                <div class="admin-sidebar-logo">
                    FEB
                </div>

                <div>

                    <strong>
                        Yudisium FEB
                    </strong>

                    <span>
                        Admin Panel
                    </span>

                </div>

            </div>


            <nav class="admin-sidebar-nav">


                <a
                    href="/admin/dashboard"
                    class="admin-nav-item active"
                >

                    <span class="admin-nav-icon">
                        ▦
                    </span>

                    <span>
                        Dashboard
                    </span>

                </a>


                <a
                    href="/admin/pengajuan"
                    class="admin-nav-item"
                >

                    <span class="admin-nav-icon">
                        ☷
                    </span>

                    <span>
                        Pengajuan
                    </span>

                </a>


                <a
                    href="/admin/pengajuan?filter=review-revisi"
                    class="admin-nav-item"
                    id="dashboardRevisionMenu"
                >

                    <span class="admin-nav-icon">
                        !
                    </span>

                    <span>
                        Revisi Siap Direview
                    </span>


                    <span
                        class="admin-nav-count"
                        id="sidebarRevisionCount"
                    >
                        0
                    </span>

                </a>


                <a
                    href="/admin/pengajuan?filter=proses-sk"
                    class="admin-nav-item"
                    id="dashboardSkMenu"
                >

                    <span class="admin-nav-icon">
                        ◷
                    </span>

                    <span>
                        Proses SK
                    </span>

                </a>


                <a
                    href="/admin/history"
                    class="admin-nav-item"
                >

                    <span class="admin-nav-icon">
                        ↺
                    </span>

                    <span>
                        History
                    </span>

                </a>
                <div class="admin-nav-divider">
                </div>

                @include('admin.partials.super_admin_navigation')

</nav>


            <div class="admin-sidebar-footer">

                <div class="admin-user-mini">

                    <div class="admin-user-avatar">
                        A
                    </div>

                    <div>

                        <strong id="sidebarAdminName">
                            {{ Auth::user()->name }}
                        </strong>

                        <span id="sidebarAdminRole">
                            {{ Auth::user()->role }}
                        </span>

                    </div>

                </div>


                <form action="/admin/logout" method="POST" style="width: 100%;">
                    @csrf
                    <button type="submit" class="admin-logout-button">
                        Keluar
                    </button>
                </form>

            </div>

        </aside>



        <!-- =====================================================
             MAIN
        ====================================================== -->

        <main class="admin-main">


            <!-- =================================================
                 TOPBAR
            ================================================== -->

            <header class="admin-topbar">

                <div>

                    <p class="admin-page-eyebrow">
                        SISTEM YUDISIUM FEB UPR
                    </p>

                    <h1>
                        Dashboard
                    </h1>

                </div>


                <div class="admin-topbar-actions">

                    <span
                        class="admin-date"
                        id="dashboardDate"
                    >
                        -
                    </span>


                    <div class="admin-profile-chip">

                        <div class="admin-profile-avatar">
                            A
                        </div>

                        <div>

                            <strong id="topbarAdminName">{{ Auth::user()->name }}</strong>

                            <span id="topbarAdminRole">{{ Auth::user()->role === 'SUPER_ADMIN' ? 'SUPER ADMIN' : 'ADMIN' }}</span>

                        </div>

                    </div>

                </div>

            </header>



            <!-- =================================================
                 STATISTICS
            ================================================== -->

            <section class="admin-stats-grid" style="align-items: flex-start;">


                <!-- TOTAL -->

                <article class="admin-stat-card">

                    <div class="admin-stat-header">

                        <span>
                            Total Pengajuan
                        </span>

                        <span class="admin-stat-icon">
                            ☷
                        </span>

                    </div>


                    <strong
                        class="admin-stat-value"
                        id="dashboardTotalCount"
                    >
                        0
                    </strong>


                    <p>
                        Seluruh pengajuan yudisium
                    </p>

                </article>



                <!-- MENUNGGU -->

                <article class="admin-stat-card">

                    <div class="admin-stat-header">

                        <span>
                            Menunggu Verifikasi
                        </span>

                        <span class="admin-stat-icon">
                            ◷
                        </span>

                    </div>


                    <strong
                        class="admin-stat-value"
                        id="dashboardPendingCount"
                    >
                        0
                    </strong>


                    <p>
                        Perlu diperiksa admin
                    </p>

                </article>



                <!-- REVISI -->

                <article class="admin-stat-card warning">

                    <div class="admin-stat-header">

                        <span>
                            Perlu Revisi
                        </span>

                        <span class="admin-stat-icon">
                            !
                        </span>

                    </div>


                    <strong
                        class="admin-stat-value"
                        id="dashboardRevisionCount"
                    >
                        0
                    </strong>


                    <p>
                        Menunggu perbaikan mahasiswa
                    </p>

                </article>


                <!-- REVISI DIKIRIM -->

                <article class="admin-stat-card warning">

                    <div class="admin-stat-header">

                        <span>
                            Revisi Siap Direview
                        </span>

                        <span class="admin-stat-icon">
                            ✓
                        </span>

                    </div>


                    <strong
                        class="admin-stat-value"
                        id="dashboardRevisionSubmittedCount"
                    >
                        0
                    </strong>


                    <p>
                        Perlu ditindaklanjuti Admin
                    </p>

                </article>



                <!-- TERVERIFIKASI -->

                <article class="admin-stat-card success">

                    <div class="admin-stat-header">

                        <span>
                            Terverifikasi
                        </span>

                        <span class="admin-stat-icon">
                            ✓
                        </span>

                    </div>


                    <strong
                        class="admin-stat-value"
                        id="dashboardVerifiedCount"
                    >
                        0
                    </strong>


                    <p>
                        Data dinyatakan lengkap
                    </p>

                </article>



                <!-- PROSES SK -->

                <article class="admin-stat-card">

                    <div class="admin-stat-header">

                        <span>
                            Proses SK
                        </span>

                        <span class="admin-stat-icon">
                            ✎
                        </span>

                    </div>


                    <strong
                        class="admin-stat-value"
                        id="dashboardProcessCount"
                    >
                        0
                    </strong>


                    <p>
                        Sedang dalam proses penerbitan
                    </p>

                </article>



                <!-- SK SIAP DIAMBIL -->

                <article class="admin-stat-card success">

                    <div class="admin-stat-header">

                        <span>
                            SK Selesai
                        </span>

                        <span class="admin-stat-icon">
                            ✓
                        </span>

                    </div>


                    <strong
                        class="admin-stat-value"
                        id="dashboardPublishedCount"
                    >
                        0
                    </strong>


                    <p>
                        SK selesai diproses dan siap diambil mahasiswa
                    </p>

                </article>

                <!-- STATUS PENDAFTARAN -->
                <article class="admin-stat-card" style="background: {{ $isPengajuanOpen ? '#f4faf7' : '#fff4f4' }}; border-color: {{ $isPengajuanOpen ? '#c8d7d0' : '#f5d1d1' }};">
                    <div class="admin-stat-header">
                        <span style="color: {{ $isPengajuanOpen ? '#0a684c' : '#c93b3b' }};">
                            Status Pendaftaran
                        </span>
                        <span class="admin-stat-icon">
                            ⚙
                        </span>
                    </div>

                    <strong class="admin-stat-value" style="font-size: 1.5rem; color: {{ $isPengajuanOpen ? '#0a684c' : '#c93b3b' }}; margin-top: 12px; margin-bottom: 8px; display: block;" id="pengajuanStatusText">
                        {{ $isPengajuanOpen ? 'DIBUKA' : 'DITUTUP' }}
                    </strong>

                    <p>
                        Mahasiswa {{ $isPengajuanOpen ? 'bisa' : 'tidak bisa' }} mengakses form pengajuan.
                    </p>

                    <button type="button" class="admin-secondary-button" id="togglePengajuanBtn" style="margin-top: 12px; width: 100%; justify-content: center; border-color: {{ $isPengajuanOpen ? '#c93b3b' : '#0a684c' }}; color: {{ $isPengajuanOpen ? '#c93b3b' : '#0a684c' }};">
                        {{ $isPengajuanOpen ? 'Tutup Pendaftaran' : 'Buka Pendaftaran' }}
                    </button>
                </article>

                <!-- PENGUMUMAN POSTER -->
                <article class="admin-stat-card" style="display: flex; flex-direction: column;">
                    <div class="admin-stat-header">
                        <span>
                            Poster Pengumuman
                        </span>
                        <span class="admin-stat-icon" style="background: #e9f2ee; color: #0a684c; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 8px;">
                            📢
                        </span>
                    </div>
                    
                    @if(count($pengumumanImages) > 0)
                        <div id="pengumuman-sortable-grid" style="margin-top: 12px; margin-bottom: 8px; display: flex; flex-wrap: wrap; gap: 8px; padding-bottom: 8px;">
                            @foreach($pengumumanImages as $index => $img)
                            <div class="pengumuman-sortable-item" data-index="{{ $index }}" style="flex-shrink: 0; width: 80px; height: 100px; border: 1px solid #e1e8e4; border-radius: 8px; overflow: hidden; position: relative; cursor: grab;">
                                <img src="{{ asset($img) }}" alt="Pengumuman" style="width: 100%; height: 100%; object-fit: cover; pointer-events: none;">
                                <form action="{{ route('admin.hapus-satu-pengumuman') }}" method="POST" onsubmit="return confirm('Hapus gambar ini?');" style="position: absolute; top: 4px; right: 4px;">
                                    @csrf
                                    <input type="hidden" name="index" value="{{ $index }}">
                                    <button type="submit" style="background: white; border: none; width: 20px; height: 20px; border-radius: 50%; color: #c93b3b; font-size: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">✕</button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                        <p style="font-size: 0.8rem; color: #4a5c53; margin-bottom: 12px;">{{ count($pengumumanImages) }} poster tayang. (Seret gambar untuk mengubah urutan)</p>
                        
                        <div style="display: flex; gap: 8px; margin-top: auto;">
                            <!-- Form Tambah -->
                            <form action="{{ route('admin.tambah-pengumuman') }}" method="POST" enctype="multipart/form-data" style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                                @csrf
                                <input type="file" name="pengumuman_images[]" accept="image/*" multiple required style="font-size: 0.75rem; width: 100%;" id="tambahPosterInput">
                                <button type="submit" class="admin-primary-button" style="width: 100%; justify-content: center; padding: 6px; font-size: 0.8rem;">Tambah</button>
                            </form>
                            <!-- Form Ganti Semua -->
                            <form action="{{ route('admin.upload-pengumuman') }}" method="POST" enctype="multipart/form-data" style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                                @csrf
                                <input type="file" name="pengumuman_images[]" accept="image/*" multiple required style="font-size: 0.75rem; width: 100%;">
                                <button type="submit" class="admin-secondary-button" style="width: 100%; justify-content: center; padding: 6px; font-size: 0.8rem; border-color: #e1e8e4;">Ganti Semua</button>
                            </form>
                        </div>
                        <form action="{{ route('admin.hapus-pengumuman') }}" method="POST" style="margin-top: 8px;" onsubmit="return confirm('Yakin ingin menghapus seluruh poster pengumuman ini?');">
                            @csrf
                            <button type="submit" class="admin-secondary-button" style="width: 100%; justify-content: center; color: #c93b3b; border-color: #f5d1d1; padding: 6px; font-size: 0.8rem;">Hapus Semua</button>
                        </form>
                        
                        <!-- SortableJS -->
                        <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                var el = document.getElementById('pengumuman-sortable-grid');
                                if (el) {
                                    new Sortable(el, {
                                        animation: 150,
                                        ghostClass: 'sortable-ghost',
                                        onEnd: function (evt) {
                                            var items = el.querySelectorAll('.pengumuman-sortable-item');
                                            var newOrder = Array.from(items).map(item => item.getAttribute('data-index'));
                                            
                                            fetch('{{ route('admin.reorder-pengumuman') }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify({ order: newOrder })
                                            }).then(res => res.json()).then(data => {
                                                if (data.success) {
                                                    // optionally reload to update indices in forms
                                                    window.location.reload();
                                                }
                                            });
                                        }
                                    });
                                }
                            });
                        </script>
                        <style>
                            .sortable-ghost { opacity: 0.4; }
                        </style>
                    @else
                        <strong class="admin-stat-value" style="font-size: 1.2rem; color: #6b7d73; margin-top: 12px; margin-bottom: 8px; display: block;">
                            Belum Ada Poster
                        </strong>
                        <p style="font-size: 0.8rem; color: #4a5c53;">Upload hingga 10 gambar (JPG/PNG) untuk ditampilkan sebagai popup di halaman mahasiswa.</p>
                        <form action="{{ route('admin.upload-pengumuman') }}" method="POST" enctype="multipart/form-data" style="margin-top: auto; display: flex; flex-direction: column; gap: 8px;">
                            @csrf
                            <input type="file" name="pengumuman_images[]" accept="image/*" multiple required style="font-size: 0.8rem; max-width: 100%;">
                            <button type="submit" class="admin-primary-button" style="width: 100%; justify-content: center; padding: 6px; font-size: 0.9rem;">Upload</button>
                        </form>
                    @endif
                </article>

            </section>



            <!-- =================================================
                 DASHBOARD GRID
            ================================================== -->

            <section class="admin-dashboard-grid">


                <!-- =============================================
                     PENGAJUAN TERBARU
                ============================================== -->

                <div class="admin-dashboard-card admin-dashboard-large">

                    <div class="admin-card-header">

                        <div>

                            <h2>
                                Pengajuan Terbaru
                            </h2>

                            <p>
                                Data pengajuan mahasiswa terbaru
                                dari sistem.
                            </p>

                        </div>


                        <a
                            href="/admin/pengajuan?filter=semua"
                            class="admin-text-link"
                        >
                            Lihat Semua
                        </a>

                    </div>


                    <div class="admin-table-wrapper" id="dashboardSubmissionTableWrapper" style="display: none;">

                        <table class="admin-table">

                            <thead>

                                <tr>

                                    <th>
                                        Mahasiswa
                                    </th>

                                    <th>
                                        NIM
                                    </th>

                                    <th>
                                        Jurusan
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th>
                                        Aksi
                                    </th>

                                </tr>

                            </thead>


                            <!--
                                Tidak ada dummy hardcode.
                                Data dibuat admin_dashboard.js
                            -->

                            <tbody id="dashboardSubmissionTableBody">
                            </tbody>

                        </table>

                    </div>


                    <div
                        class="admin-data-state"
                        id="dashboardDataState"
                        data-state="loading"
                        role="status"
                    >
                        <strong>Memuat pengajuan...</strong>
                        <p>Mohon tunggu sebentar.</p>
                        <button type="button" class="admin-secondary-button" id="dashboardRetryButton">
                            Coba Lagi
                        </button>
                    </div>

                </div>



                <!-- =============================================
                     QUICK ACTION
                ============================================== -->

                <div class="admin-dashboard-card">

                    <div class="admin-card-header">

                        <div>

                            <h2>
                                Aksi Cepat
                            </h2>

                            <p>
                                Menu yang sering digunakan.
                            </p>

                        </div>

                    </div>


                    <div class="admin-quick-actions">


                        <!-- MENUNGGU -->

                        <a
                            href="/admin/pengajuan?filter=menunggu"
                            class="admin-quick-action"
                        >

                            <div class="admin-quick-icon">
                                ◷
                            </div>


                            <div>

                                <strong>
                                    Verifikasi Pengajuan
                                </strong>

                                <span id="dashboardQuickPendingText">
                                    0 pengajuan menunggu
                                </span>

                            </div>

                        </a>



                        <!-- MENUNGGU PERBAIKAN -->

                        <a
                            href="/admin/pengajuan?filter=menunggu-revisi"
                            class="admin-quick-action"
                        >

                            <div class="admin-quick-icon warning">
                                !
                            </div>


                            <div>

                                <strong>
                                    Menunggu Perbaikan Mahasiswa
                                </strong>

                                <span id="dashboardQuickRevisionText">
                                    0 mahasiswa sedang memperbaiki
                                </span>

                            </div>

                        </a>


                        <!-- REVISI SIAP DIREVIEW -->

                        <a
                            href="/admin/pengajuan?filter=review-revisi"
                            class="admin-quick-action"
                            id="dashboardQuickRevision"
                        >

                            <div class="admin-quick-icon warning">
                                ✓
                            </div>


                            <div>

                                <strong>
                                    Revisi Siap Direview
                                </strong>

                                <span id="dashboardQuickRevisionSubmittedText">
                                    0 revisi menunggu review Admin
                                </span>

                            </div>

                        </a>



                        <!-- TERVERIFIKASI -->

                        <a
                            href="/admin/pengajuan?filter=terverifikasi"
                            class="admin-quick-action"
                        >

                            <div class="admin-quick-icon success">
                                ✓
                            </div>


                            <div>

                                <strong>
                                    Data Terverifikasi
                                </strong>

                                <span id="dashboardQuickVerifiedText">
                                    0 pengajuan siap diproses
                                </span>

                            </div>

                        </a>



                        <!-- PROSES SK -->

                        <a
                            href="/admin/pengajuan?filter=proses-sk"
                            class="admin-quick-action"
                            id="dashboardQuickSk"
                        >

                            <div class="admin-quick-icon">
                                ◷
                            </div>


                            <div>

                                <strong>
                                    Proses SK
                                </strong>

                                <span id="dashboardQuickProcessText">
                                    0 pengajuan dalam proses SK
                                </span>

                            </div>

                        </a>



                        <!-- HISTORY -->

                        <a
                            href="/admin/history"
                            class="admin-quick-action"
                        >

                            <div class="admin-quick-icon">
                                ↺
                            </div>


                            <div>

                                <strong>
                                    History Aktivitas
                                </strong>

                                <span>
                                    Lihat aktivitas administrasi
                                </span>

                            </div>

                        </a>



                        <!-- EXPORT -->

                        <a
                            href="{{ route('admin.export-yudisium') }}"
                            class="admin-quick-action"
                        >

                            <div class="admin-quick-icon">
                                ↓
                            </div>

                            <div>

                                <strong>
                                    Export Excel
                                </strong>

                                <span>
                                    Unduh data pengajuan
                                </span>

                            </div>

                        </a>

                    </div>

                </div>

            </section>



            <!-- =================================================
                 SUPER ADMIN
            ================================================== -->

            @if(Auth::user()->role === 'SUPER_ADMIN')
            <section
                class="admin-dashboard-card super-admin-section active"
                id="superAdminDashboardSection"
            >

                <div class="admin-card-header">

                    <div>

                        <h2>
                            Permintaan Akun Admin
                        </h2>

                        <p>
                            Akun baru yang menunggu persetujuan
                            Super Admin.
                        </p>

                    </div>


                    <a
                        href="{{ route('superadmin.kelola-admin') }}"
                        class="admin-text-link"
                    >
                        Kelola Admin
                    </a>

                </div>


                <div class="admin-super-admin-list">

                    @forelse($pendingAdmins as $pendingAdmin)
                        <div class="admin-account-request">
                            <div>
                                <strong>{{ $pendingAdmin->name }}</strong>
                                <span>{{ $pendingAdmin->email }}</span>
                            </div>

                            <div class="admin-account-actions">
                                <a
                                    href="{{ route('superadmin.kelola-admin') }}"
                                    class="admin-small-button approve"
                                >
                                    Tinjau Akun
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="admin-data-state compact" data-state="empty">
                            <strong>Tidak ada permintaan akun baru</strong>
                            <p>Semua permintaan akun Admin sudah ditangani.</p>
                        </div>
                    @endforelse

                </div>

            </section>
            @endif

        </main>

    </div>



    <!-- =====================================================
         SCRIPT

         1. yudisium_api.js = service API frontend
         2. admin.js = login/sidebar/admin global
         3. admin_dashboard.js = renderer dashboard
    ====================================================== -->

    <script src="{{ asset('js/yudisium_api.js') }}"></script>
    
    <script src="{{ asset('js/admin.js') }}"></script>
    
    <script src="{{ asset('js/admin_dashboard.js') }}"></script>


</body>

</html>
