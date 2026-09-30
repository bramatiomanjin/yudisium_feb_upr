@if(strtoupper(trim((string) Auth::user()->role)) === 'SUPER_ADMIN')
    <a
        href="{{ route('admin.backup-dokumen') }}"
        class="admin-nav-item {{ request()->routeIs('admin.backup-dokumen') ? 'active' : '' }}"
    >
        <span class="admin-nav-icon">↓</span>
        <span>Backup Dokumen</span>
    </a>

    <a
        href="{{ route('superadmin.kelola-admin') }}"
        class="admin-nav-item {{ request()->routeIs('superadmin.kelola-admin') ? 'active' : '' }}"
        id="manageAdminMenu"
    >
        <span class="admin-nav-icon">♙</span>
        <span>Kelola Admin</span>
    </a>

    <a
        href="{{ route('admin.pengaturan-dokumen') }}"
        class="admin-nav-item {{ request()->routeIs('admin.pengaturan-dokumen*') ? 'active' : '' }}"
    >
        <span class="admin-nav-icon">⚙️</span>
        <span>Pengaturan Dokumen</span>
    </a>
@endif
