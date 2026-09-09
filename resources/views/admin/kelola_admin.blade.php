<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Kelola Admin - Yudisium FEB UPR
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
        href="{{ asset('css/admin_kelola_admin.css') }}"
    >
    <link rel="stylesheet" href="{{ asset('css/admin_theme.css') }}">
</head>

<body>

    <div class="admin-layout">

        <!-- =====================================
             SIDEBAR
        ====================================== -->
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
                    href="/admin/dashboard"
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
                >
                    <span class="admin-nav-icon">
                        !
                    </span>

                    <span>
                        Perlu Revisi
                    </span>

                    <span class="admin-nav-count">
                        3
                    </span>
                </a>


                <a
                    href="/admin/dashboard?filter=proses-sk"
                    href="/admin/pengajuan?filter=proses-sk"
                    class="admin-nav-item"
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


                <div class="admin-nav-divider"></div>


                @if(Auth::user()->role === 'SUPER_ADMIN')
                <a
                    href="/superadmin/kelola-admin"
                    class="admin-nav-item active"
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
                        SA
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


        <!-- =====================================
             MAIN
        ====================================== -->
        <main class="admin-main manage-admin-main">

            <!-- HEADER -->
            <header class="manage-admin-header">

                <div>

                    <p class="admin-page-eyebrow">
                        SUPER ADMIN
                    </p>

                    <h1>
                        Kelola Admin
                    </h1>

                    <p>
                        Kelola permintaan akun dan status admin sistem.
                    </p>

                </div>


                <div class="admin-profile-chip">

                    <div class="admin-profile-avatar">
                        SA
                    </div>

                    <div>

                        <strong id="topbarAdminName">
                            {{ Auth::user()->name }}
                        </strong>

                        <span id="topbarAdminRole">
                            {{ Auth::user()->role }}
                        </span>

                    </div>

                </div>

            </header>


            <!-- =====================================
                 ACCESS DENIED
            ====================================== -->
            @if(Auth::user()->role !== 'SUPER_ADMIN')
            <section
                class="manage-admin-access-denied"
                id="manageAdminAccessDenied"
            >

                <div class="manage-admin-denied-icon">
                    !
                </div>

                <h2>
                    Akses Ditolak
                </h2>

                <p>
                    Halaman Kelola Admin hanya dapat diakses oleh Super Admin.
                </p>

                <a href="/admin/dashboard">
                    Kembali ke Dashboard
                </a>

            </section>
            @endif


            <!-- =====================================
                 CONTENT
            ====================================== -->
            @if(Auth::user()->role === 'SUPER_ADMIN')
            <div id="manageAdminContent">

                <!-- SUMMARY -->
                <section class="manage-admin-summary-grid">

                    <div class="manage-admin-summary-card pending">

                        <span>
                            Menunggu Persetujuan
                        </span>

                        <strong id="pendingAdminCount">
                            {{ $counts->pending }}
                        </strong>

                    </div>


                    <div class="manage-admin-summary-card active">

                        <span>
                            Admin Aktif
                        </span>

                        <strong id="activeAdminCount">
                            {{ $counts->active }}
                        </strong>

                    </div>


                    <div class="manage-admin-summary-card inactive">

                        <span>
                            Admin Nonaktif
                        </span>

                        <strong id="inactiveAdminCount">
                            {{ $counts->inactive }}
                        </strong>

                    </div>


                    <div class="manage-admin-summary-card">

                        <span>
                            Total Admin
                        </span>

                        <strong id="totalAdminCount">
                            {{ $counts->total }}
                        </strong>

                    </div>

                </section>


                <!-- INFO -->
                <section class="manage-admin-info">

                    <div>
                        i
                    </div>

                    <div>

                        <strong>
                            Super Admin mengelola akses akun
                        </strong>

                        <p>
                            Akun baru dari halaman registrasi selalu dibuat
                            sebagai ADMIN berstatus PENDING. Super Admin dapat
                            menyetujui, menolak, menonaktifkan, atau
                            mengaktifkan kembali akun tersebut.
                        </p>

                    </div>

                </section>


                <!-- =================================
                     SEARCH
                ================================== -->
                <section class="manage-admin-card">

                    <div class="manage-admin-card-header">

                        <div>

                            <h2>
                                Cari Admin
                            </h2>

                            <p>
                                Cari berdasarkan nama, username, atau email.
                            </p>

                        </div>

                    </div>


                    <div class="manage-admin-search-row">

                        <input
                            type="text"
                            id="manageAdminSearch"
                            placeholder="Cari nama, username, atau email..."
                        >


                        <button
                            type="button"
                            class="manage-admin-primary-button"
                            id="applyAdminSearch"
                        >
                            Cari
                        </button>


                        <button
                            type="button"
                            class="manage-admin-secondary-button"
                            id="resetAdminSearch"
                        >
                            Reset
                        </button>

                    </div>


                    <div
                        class="manage-admin-filter-info"
                        id="manageAdminFilterInfo"
                    >

                        <strong>
                            Filter aktif:
                        </strong>

                        <span id="manageAdminFilterText">
                            Semua admin
                        </span>

                    </div>

                </section>


                <!-- =================================
                     PENDING
                ================================== -->
                <section class="manage-admin-card">

                    <div class="manage-admin-card-header">

                        <div>

                            <h2>
                                Permintaan Akun Admin
                            </h2>

                            <p>
                                Akun baru yang menunggu persetujuan.
                            </p>

                        </div>


                        <span
                            class="manage-admin-section-count pending"
                            id="pendingSectionCount"
                        >
                            {{ $counts->pending }} Pending
                        </span>

                    </div>


                    <div
                        class="manage-admin-list"
                        id="pendingAdminList"
                    >
                        @foreach($admins->where('status', 'PENDING') as $admin)
                        <div class="manage-admin-item">
                            <div class="manage-admin-item-info">
                                <div class="manage-admin-item-avatar">{{ strtoupper(substr($admin->name, 0, 1)) }}</div>
                                <div>
                                    <strong>{{ $admin->name }}</strong>
                                    <span>{{ $admin->email }}</span>
                                </div>
                            </div>
                            <div class="manage-admin-item-actions">
                                <form action="/superadmin/approve-admin/{{ $admin->id }}" method="POST" style="display:inline">
                                    @csrf
                                    <button type="submit" class="manage-admin-primary-button" style="padding: 6px 16px; font-size: 13px;">Setujui</button>
                                </form>
                                <form action="/superadmin/reject-admin/{{ $admin->id }}" method="POST" style="display:inline">
                                    @csrf
                                    <button type="submit" class="manage-admin-secondary-button" style="padding: 6px 16px; font-size: 13px;">Tolak</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($admins->where('status', 'PENDING')->isEmpty())
                    <div
                        class="manage-admin-empty"
                        id="pendingAdminEmpty"
                    >

                        <strong>
                            Tidak ada permintaan akun
                        </strong>

                        <p>
                            Semua permintaan akun sudah diproses.
                        </p>

                    </div>
                    @endif

                </section>


                <!-- =================================
                     ACTIVE
                ================================== -->
                <section class="manage-admin-card">

                    <div class="manage-admin-card-header">

                        <div>

                            <h2>
                                Admin Aktif
                            </h2>

                            <p>
                                Akun admin yang saat ini dapat masuk ke sistem.
                            </p>

                        </div>


                        <span
                            class="manage-admin-section-count active"
                            id="activeSectionCount"
                        >
                            {{ $counts->active }} Aktif
                        </span>

                    </div>


                    <div
                        class="manage-admin-list"
                        id="activeAdminList"
                    >
                        @foreach($admins->where('status', 'ACTIVE') as $admin)
                        <div class="manage-admin-item">
                            <div class="manage-admin-item-info">
                                <div class="manage-admin-item-avatar">{{ strtoupper(substr($admin->name, 0, 1)) }}</div>
                                <div>
                                    <strong>{{ $admin->name }}</strong>
                                    <span>{{ $admin->email }} — {{ $admin->role }}</span>
                                </div>
                            </div>
                            <div class="manage-admin-item-meta">
                                <span class="manage-admin-badge active">Aktif</span>
                                @if($admin->id !== Auth::id())
                                <form action="/superadmin/hapus-admin/{{ $admin->id }}" method="POST" style="display:inline">
                                    @csrf
                                    <button type="submit" class="manage-admin-secondary-button" style="padding: 6px 16px; font-size: 13px;" onclick="return confirm('Yakin ingin menonaktifkan admin ini?')">Nonaktifkan</button>
                                </form>
                                @else
                                <span style="color: var(--feb-muted, #777); font-size: 12px;">(Anda)</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($admins->where('status', 'ACTIVE')->isEmpty())
                    <div
                        class="manage-admin-empty"
                        id="activeAdminEmpty"
                    >

                        <strong>
                            Tidak ada admin aktif
                        </strong>

                    </div>
                    @endif

                </section>


                <!-- =================================
                     INACTIVE
                ================================== -->
                <section class="manage-admin-card">

                    <div class="manage-admin-card-header">

                        <div>

                            <h2>
                                Admin Nonaktif
                            </h2>

                            <p>
                                Akun yang ditolak atau sedang dinonaktifkan.
                            </p>

                        </div>


                        <span
                            class="manage-admin-section-count inactive"
                            id="inactiveSectionCount"
                        >
                            {{ $counts->inactive }} Nonaktif
                        </span>

                    </div>


                    <div
                        class="manage-admin-list"
                        id="inactiveAdminList"
                    >
                        @foreach($admins->where('status', 'INACTIVE') as $admin)
                        <div class="manage-admin-item">
                            <div class="manage-admin-item-info">
                                <div class="manage-admin-item-avatar">{{ strtoupper(substr($admin->name, 0, 1)) }}</div>
                                <div>
                                    <strong>{{ $admin->name }}</strong>
                                    <span>{{ $admin->email }} — {{ $admin->role }}</span>
                                </div>
                            </div>
                            <div class="manage-admin-item-meta">
                                <span class="manage-admin-badge inactive">Nonaktif</span>
                                <form action="/superadmin/activate-admin/{{ $admin->id }}" method="POST" style="display:inline">
                                    @csrf
                                    <button type="submit" class="manage-admin-primary-button" style="padding: 6px 16px; font-size: 13px;">Aktifkan Kembali</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($admins->where('status', 'INACTIVE')->isEmpty())
                    <div
                        class="manage-admin-empty"
                        id="inactiveAdminEmpty"
                    >

                        <strong>
                            Tidak ada admin nonaktif
                        </strong>

                    </div>
                    @endif

                </section>

            </div>
            @endif

        </main>

    </div>


    <!-- =====================================
         CONFIRM ACTION MODAL
    ====================================== -->
    <div
        class="manage-admin-modal-overlay"
        id="adminActionModal"
    >

        <div class="manage-admin-modal">

            <div
                class="manage-admin-modal-icon"
                id="adminActionModalIcon"
            >
                ?
            </div>


            <h2 id="adminActionModalTitle">
                Konfirmasi
            </h2>


            <p id="adminActionModalMessage">
                Apakah Anda yakin?
            </p>


            <div class="manage-admin-modal-account">

                <strong id="adminActionAccountName">
                    Admin
                </strong>

                <span id="adminActionAccountUsername">
                    username
                </span>

            </div>


            <div class="manage-admin-modal-actions">

                <button
                    type="button"
                    class="manage-admin-modal-cancel"
                    id="cancelAdminAction"
                >
                    Batal
                </button>


                <button
                    type="button"
                    class="manage-admin-modal-confirm"
                    id="confirmAdminAction"
                >
                    Ya, Lanjutkan
                </button>

            </div>

        </div>

    </div>


    <!-- =====================================
         SUCCESS MODAL
    ====================================== -->
    <div
        class="modal-overlay"
        id="adminActionSuccessModal"
    >

        <div class="modal-card">

            <div class="success-icon">
                ✓
            </div>

            <h2 id="adminActionSuccessTitle">
                Berhasil
            </h2>

            <p id="adminActionSuccessMessage">
                Status admin berhasil diperbarui.
            </p>

            <button
                type="button"
                class="btn btn-primary"
                id="closeAdminActionSuccess"
            >
                Tutup
            </button>

        </div>

    </div>


   
    <script src="{{ asset('js/admin.js') }}"></script>
    <script src="{{ asset('js/admin_kelola_admin.js') }}"></script>

</body>

</html>
