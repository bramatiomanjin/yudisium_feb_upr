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
                    href="/admin/dashboard?filter=pengajuan"
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
                    href="/admin/dashboard?filter=revisi"
                    href="/admin/pengajuan?filter=revisi"
                    class="admin-nav-item"
                    id="dashboardRevisionMenu"
                >

                    <span class="admin-nav-icon">
                        !
                    </span>

                    <span>
                        Perlu Revisi
                    </span>


                    <span
                        class="admin-nav-count"
                        id="sidebarRevisionCount"
                    >
                        0
                    </span>

                </a>


                <a
                    href="/admin/dashboard?filter=proses-sk"
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
                    href="/superadmin/log-aktivitas"
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


                @if(Auth::user()->role === 'SUPER_ADMIN')
                <a
                    href="/superadmin/kelola-admin"
                    class="admin-nav-item"
                    id="manageAdminMenu"
                >

                    <span class="admin-nav-icon">
                        ♙
                    </span>

                    <span>
                        Kelola Admin
                    </span>

                </a>
                @endif

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

                            <strong id="topbarAdminName">
                                Admin
                            </strong>

                            <span id="topbarAdminRole">
                                ADMIN
                            </span>

                        </div>

                    </div>

                </div>

            </header>



            <!-- =================================================
                 STATISTICS
            ================================================== -->

            <section class="admin-stats-grid">


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
                            SK Siap Diambil
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
                            href="/admin/dashboard?filter=pengajuan"
                            class="admin-text-link"
                        >
                            Lihat Semua
                        </a>

                    </div>


                    <div class="admin-table-wrapper">

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
                        id="dashboardEmptyState"
                        style="
                            display: none;
                            padding: 30px 20px;
                            text-align: center;
                        "
                    >

                        <strong>
                            Belum ada pengajuan
                        </strong>

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
                            href="/admin/dashboard?filter=menunggu"
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



                        <!-- REVISI -->

                        <a
                            href="/admin/dashboard?filter=revisi"
                            class="admin-quick-action"
                            id="dashboardQuickRevision"
                        >

                            <div class="admin-quick-icon warning">
                                !
                            </div>


                            <div>

                                <strong>
                                    Periksa Revisi
                                </strong>

                                <span id="dashboardQuickRevisionText">
                                    0 mahasiswa perlu revisi
                                </span>

                            </div>

                        </a>



                        <!-- TERVERIFIKASI -->

                        <a
                            href="/admin/dashboard?filter=terverifikasi"
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
                            href="/admin/dashboard?filter=proses-sk"
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
                            href="/superadmin/log-aktivitas"
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

                        <button
                            type="button"
                            class="admin-quick-action button-action"
                            id="quickExport"
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

                        </button>

                    </div>

                </div>

            </section>



            <!-- =================================================
                 SUPER ADMIN
            ================================================== -->

            <section
                class="admin-dashboard-card super-admin-section"
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
                        href="/superadmin/kelola-admin"
                        class="admin-text-link"
                    >
                        Kelola Admin
                    </a>

                </div>


                <div class="admin-super-admin-list">

                    <div class="admin-account-request">

                        <div>

                            <strong>
                                Budi Santoso
                            </strong>

                            <span>
                                budi.santoso@example.com
                            </span>

                        </div>


                        <div class="admin-account-actions">

                            <a
                                href="/superadmin/kelola-admin"
                                class="admin-small-button approve"
                            >
                                Kelola
                            </a>

                        </div>

                    </div>


                    <div class="admin-account-request">

                        <div>

                            <strong>
                                Rina Marlina
                            </strong>

                            <span>
                                rina.marlina@example.com
                            </span>

                        </div>


                        <div class="admin-account-actions">

                            <a
                                href="/superadmin/kelola-admin"
                                class="admin-small-button approve"
                            >
                                Kelola
                            </a>

                        </div>

                    </div>

                </div>

            </section>

        </main>

    </div>



    <!-- =====================================================
         EXPORT MODAL
    ====================================================== -->

    <div
        class="modal-overlay"
        id="exportModal"
    >

        <div class="modal-card">

            <div class="success-icon">
                ↓
            </div>


            <h2>
                Export Data
            </h2>


            <p>
                Fitur export akan mengunduh data pengajuan
                dalam format Excel setelah terhubung
                dengan Laravel.
            </p>


            <button
                type="button"
                class="btn btn-primary"
                id="closeExportModal"
            >
                Tutup
            </button>

        </div>

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
