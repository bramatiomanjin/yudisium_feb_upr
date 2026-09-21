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
        Pengaturan Dokumen - Yudisium FEB UPR
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

            
    <a href="/admin/pengaturan-dokumen" class="admin-nav-item active">
        <span class="admin-nav-icon">⚙️</span>
        <span>Pengaturan Dokumen</span>
    </a>
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

    <div class="admin-content-padding" style="padding: 24px;">
        <div style="margin-bottom: 24px;">
            <h2 style="margin:0; font-size: 1.5rem; color: var(--admin-text-primary);">Pengaturan Dokumen</h2>
            <p style="margin: 4px 0 0 0; color: var(--admin-text-secondary);">Atur visibilitas (hide/unhide) dokumen pada form pengajuan mahasiswa.</p>
        </div>

        @if(session('success'))
            <div style="background-color: #d1fae5; color: #065f46; border: 1px solid #34d399; padding: 16px; margin-bottom: 24px; border-radius: 8px;">
                {{ session('success') }}
            </div>
        @endif

        <section class="admin-card">
            <form action="{{ route('admin.pengaturan-dokumen.update') }}" method="POST">
                @csrf
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 80px; text-align: center;">Tampilkan</th>
                                <th>Kode</th>
                                <th>Nama Dokumen</th>
                                <th>Jurusan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dokumen as $doc)
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">
                                    <input type="checkbox" name="active_docs[]" value="{{ $doc->id }}" {{ $doc->is_active ? 'checked' : '' }} style="transform: scale(1.5); cursor: pointer;">
                                </td>
                                <td><strong>{{ $doc->kode }}</strong></td>
                                <td>{{ $doc->nama_dokumen }}</td>
                                <td>
                                    @if($doc->jurusan)
                                        <span style="background-color: #e5e7eb; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; color: #374151;">{{ $doc->jurusan }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div style="margin-top: 24px; display: flex; justify-content: flex-end;">
                    <button type="submit" class="admin-primary-button">Simpan Pengaturan</button>
                </div>
            </form>
        </section>
    </div>
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
