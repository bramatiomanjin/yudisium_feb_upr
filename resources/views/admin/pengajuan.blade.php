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
        Daftar Pengajuan - Yudisium FEB UPR
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
                    class="admin-nav-item"
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
                    class="admin-nav-item {{ request('filter') ? '' : 'active' }}"
                >

                    <span class="admin-nav-icon">
                        ☷
                    </span>

                    <span>
                        Pengajuan
                    </span>

                </a>


                <a
                    href="/admin/pengajuan?filter=revisi"
                    class="admin-nav-item {{ request('filter') == 'revisi' ? 'active' : '' }}"
                >
                    <span class="admin-nav-icon">
                        !
                    </span>
                    <span>
                        Perlu Revisi
                    </span>

                    <span
                        class="admin-nav-count"
                        id="revisionSidebarCount"
                    >
                        0
                    </span>

                </a>


                <a
                    href="/admin/pengajuan?filter=proses-sk"
                    class="admin-nav-item {{ request('filter') == 'proses-sk' ? 'active' : '' }}"
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


                <div class="admin-nav-divider"></div>


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

            
                @if(Auth::user()->role === 'SUPER_ADMIN')
                <a
                    href="/admin/pengaturan-dokumen"
                    class="admin-nav-item"
                >
                    <span class="admin-nav-icon">⚙️</span>
                    <span>Pengaturan Dokumen</span>
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

                    <button
                        type="submit"
                        class="admin-logout-button"
                    >
                        Keluar
                    </button>
                </form>

            </div>

        </aside>



        <!-- =====================================================
             MAIN
        ====================================================== -->

        <main class="admin-main">


            <!-- TOPBAR -->

            <header class="admin-topbar">

                <div>

                    <p class="admin-page-eyebrow">
                        PENGELOLAAN YUDISIUM
                    </p>

                    <h1>
                        Daftar Pengajuan
                    </h1>

                    <p class="admin-page-description">
                        Kelola dan verifikasi pengajuan yudisium mahasiswa.
                    </p>

                </div>


                <div class="admin-profile-chip">

                    <div class="admin-profile-avatar">
                        A
                    </div>

                    <div>

                        <strong id="topbarAdminName">{{ Auth::user()->name }}</strong>

                        <span id="topbarAdminRole">{{ Auth::user()->role === 'SUPER_ADMIN' ? 'SUPER ADMIN' : 'ADMIN' }}</span>

                    </div>

                </div>

            </header>



            <!-- =================================================
                 MINI STATS
            ================================================== -->

            <section class="submission-mini-stats">

                <div class="submission-mini-card">

                    <span>
                        Total
                    </span>

                    <strong id="submissionStatTotal">
                        0
                    </strong>

                </div>


                <div class="submission-mini-card">

                    <span>
                        Menunggu
                    </span>

                    <strong id="submissionStatPending">
                        0
                    </strong>

                </div>


                <div class="submission-mini-card revision">

                    <span>
                        Revisi
                    </span>

                    <strong id="submissionStatRevision">
                        0
                    </strong>

                </div>


                <div class="submission-mini-card verified">

                    <span>
                        Terverifikasi
                    </span>

                    <strong id="submissionStatVerified">
                        0
                    </strong>

                </div>

            </section>



            <!-- =================================================
                 FILTER
            ================================================== -->

            <section class="admin-dashboard-card">

                <div class="admin-card-header">

                    <div>

                        <h2>
                            Cari & Filter
                        </h2>

                        <p>
                            Tentukan kriteria, kemudian klik Terapkan Filter.
                        </p>

                    </div>

                </div>


                <div class="submission-filter-grid">


                    <!-- SEARCH -->

                    <div class="submission-filter-group submission-search">

                        <label for="submissionSearch">
                            Cari Mahasiswa
                        </label>

                        <input
                            type="text"
                            id="submissionSearch"
                            placeholder="Nama, NIM, atau Kode SK Yudisium"
                        >

                    </div>


                    <!-- BULAN -->
                    <div class="submission-filter-group">
                        <label for="submissionMonth">Bulan</label>
                        <select id="submissionMonth">
                            <option value="">Semua Bulan</option>
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>

                    <!-- TAHUN -->
                    <div class="submission-filter-group">
                        <label for="submissionYear">Tahun</label>
                        <select id="submissionYear">
                            <option value="">Semua Tahun</option>
                            <!-- You can render dynamic years if needed, but static for now up to a range -->
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                            <option value="2027">2027</option>
                        </select>
                    </div>

                    <!-- JURUSAN -->
                    <div class="submission-filter-group">

                        <label for="submissionDepartment">
                            Jurusan
                        </label>

                        <select id="submissionDepartment">

                            <option value="">
                                Semua Jurusan
                            </option>

                            <option value="manajemen">
                                Manajemen
                            </option>

                            <option value="akuntansi">
                                Akuntansi
                            </option>

                            <option value="ekonomi pembangunan">
                                Ekonomi Pembangunan
                            </option>

                        </select>

                    </div>


                    <!-- STATUS -->

                    <div class="submission-filter-group">

                        <label for="submissionStatus">
                            Status
                        </label>

                        <select id="submissionStatus">

                            <option value="">
                                Semua Status
                            </option>

                            <option value="menunggu verifikasi">
                                Menunggu Verifikasi
                            </option>

                            <option value="perlu revisi">
                                Perlu Revisi
                            </option>

                            <option value="revisi dikirim">
                                Revisi Dikirim
                            </option>

                            <option value="terverifikasi">
                                Terverifikasi
                            </option>

                            <option value="pembuatan sk">
                                Pembuatan SK
                            </option>

                            <option value="ttd wakil dekan">
                                Paraf Pimpinan
                            </option>

                            <option value="ttd dekan">
                                TTD Dekan
                            </option>

                            <option value="sk_siap_diambil">
                                SK Selesai
                            </option>

                        </select>

                    </div>

                </div>


                <div class="submission-filter-actions">

                    <button
                        type="button"
                        class="admin-primary-button"
                        id="applySubmissionFilter"
                    >
                        Terapkan Filter
                    </button>


                    <button
                        type="button"
                        class="admin-secondary-button"
                        id="resetSubmissionFilter"
                    >
                        Reset Filter
                    </button>

                </div>


                <div
                    class="active-filter-box"
                    id="activeFilterBox"
                >

                    <div>

                        <span class="active-filter-label">
                            Filter aktif:
                        </span>

                        <span id="activeFilterText">
                            Semua pengajuan
                        </span>

                    </div>

                    <span class="active-filter-success">
                        ✓ Diterapkan
                    </span>

                </div>

            </section>



            <!-- =================================================
                 TABLE
            ================================================== -->

            <section class="admin-dashboard-card submission-table-card">

                <div class="admin-card-header">

                    <div>

                        <h2>
                            Data Pengajuan
                        </h2>

                        <p id="submissionResultCount">
                            Menampilkan 0 pengajuan
                        </p>

                    </div>


                    <a
                        href="{{ route('admin.export-yudisium') }}"
                        class="admin-secondary-button"
                        id="submissionExportButton"
                    >
                        Export Excel
                    </a>

                </div>


                <div class="admin-table-wrapper">

                    <table class="admin-table submission-table">

                        <thead>

                            <tr>

                                <th class="bulk-action-th" style="display: none; width: 40px; text-align: center;">
                                    <input type="checkbox" id="selectAllSubmissions">
                                </th>

                                <th>
                                    Kode
                                </th>

                                <th>
                                    Mahasiswa
                                </th>

                                <th>
                                    Jurusan
                                </th>

                                <th>
                                    Tanggal
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
                            PENTING:
                            Tidak ada dummy hardcode di sini.

                            Semua data berasal dari:
                            ../js/yudisium_api.js
                        -->

                        <tbody id="centralSubmissionTableBody">
                        </tbody>

                    </table>

                </div>


                <!-- EMPTY STATE -->

                <div
                    class="submission-empty-state"
                    id="submissionEmptyState"
                    style="display: none;"
                >

                    <strong>
                        Tidak ada pengajuan ditemukan
                    </strong>

                    <span>
                        Coba ubah kata kunci atau filter pencarian.
                    </span>

                </div>


                <!-- PAGINATION -->
                <div class="admin-pagination" id="submissionPaginationContainer" style="display: none;">
                    <span id="submissionPaginationInfo">Halaman 1 dari 1</span>
                    <div id="submissionPaginationControls">
                        <button type="button" disabled>Sebelumnya</button>
                        <button type="button" class="active">1</button>
                        <button type="button" disabled>Berikutnya</button>
                    </div>
                </div>

            </section>

        </main>

    </div>



    <!-- =====================================================
         BULK ACTION BAR & MODAL
    ====================================================== -->

    <div class="bulk-action-bar" id="bulkActionBar" style="display: none;">
        <div class="bulk-action-info">
            <span id="bulkSelectedCount">0</span> pengajuan dipilih
        </div>
        <div class="bulk-action-buttons">
            <button class="admin-primary-button" id="btnBulkLanjut">Lanjutkan ke progres selanjutnya</button>
            <button class="admin-secondary-button" id="btnBulkLoncat" style="display: none;">Loncat ke progres berikutnya</button>
        </div>
    </div>

    <div class="modal-overlay" id="bulkLoncatModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1100; align-items: center; justify-content: center; background: rgba(5, 27, 19, 0.58); backdrop-filter: blur(4px);">
        <div class="modal-card">
            <h3 style="margin: 0 0 16px 0; font-size: 1.25rem;">Pilih Status Tujuan</h3>
            <p style="margin-bottom: 24px; color: #5f6368; font-size: 0.95rem;">
                Pilih status untuk diterapkan ke semua pengajuan yang dipilih:
            </p>
            <select class="admin-input" id="bulkTargetStatus" style="width: 100%; margin-bottom: 24px;">
            </select>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button class="admin-secondary-button" id="btnCancelBulkLoncat">Batal</button>
                <button class="admin-primary-button" id="btnConfirmBulkLoncat">Terapkan Status</button>
            </div>
        </div>
    </div>

    <!-- =====================================================
         SCRIPT
    ====================================================== -->

    <script src="{{ asset('js/yudisium_api.js') }}"></script>

    <script src="{{ asset('js/admin.js') }}"></script>

    <script src="{{ asset('js/admin_pengajuan.js') }}"></script>


</body>

</html>
