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
        Review Revisi - Yudisium FEB UPR
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
        href="{{ asset('css/admin_review_revisi.css') }}"
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
                    class="admin-nav-item active"
                >
                    <span class="admin-nav-icon">
                        !
                    </span>

                    <span>
                        Perlu Revisi
                    </span>

                    <span
                        class="admin-nav-count"
                        id="reviewSidebarRevisionCount"
                    >
                        0
                    </span>
                </a>


                <a
                    href="/admin/dashboard?filter=proses-sk"
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

        <main class="admin-main revision-review-main">


            <!-- BACK -->

            <div class="revision-review-back">

                <a
                    href="/admin/dashboard"
                    id="reviewBackLink"
                >
                    ← Kembali ke Pengajuan
                </a>

            </div>



            <!-- =================================================
                 HEADER
            ================================================== -->

            <header class="revision-review-header">

                <div>

                    <p class="admin-page-eyebrow">
                        REVIEW REVISI
                    </p>


                    <h1 id="reviewSubmissionCode">
                        -
                    </h1>


                    <p>
                        Periksa kembali hanya item yang telah
                        diperbaiki oleh mahasiswa.
                    </p>

                </div>


                <div class="revision-review-header-actions">

                    <span class="admin-status-badge revision-submitted">
                        Revisi Dikirim
                    </span>


                    <div class="admin-profile-chip">

                        <div class="admin-profile-avatar">
                            A
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

                </div>

            </header>



            <!-- =================================================
                 STUDENT
            ================================================== -->

            <section class="revision-student-card">

                <div
                    class="revision-student-avatar"
                    id="reviewStudentAvatar"
                >
                    -
                </div>


                <div>

                    <h2 id="reviewStudentName">
                        -
                    </h2>


                    <p id="reviewStudentSummary">
                        -
                    </p>

                </div>


                <div class="revision-round">

                    <span>
                        Revisi ke
                    </span>

                    <strong id="reviewRevisionRound">
                        1
                    </strong>

                </div>

            </section>



            <!-- =================================================
                 INFO
            ================================================== -->

            <section class="revision-info-box">

                <div>
                    i
                </div>


                <p>
                    Halaman ini hanya menampilkan item yang
                    dikirim ulang oleh mahasiswa. Item yang
                    sebelumnya sudah disetujui tetap terkunci
                    dan tidak perlu diperiksa kembali.
                </p>

            </section>



            <!-- =================================================
                 DYNAMIC ITEMS
            ================================================== -->

            <div id="reviewRevisionItemsContainer">
            </div>



            <!-- =================================================
                 RESULT
            ================================================== -->

            <section class="revision-result-card">

                <div class="revision-result-header">

                    <div>

                        <h2>
                            Hasil Review Revisi
                        </h2>

                        <p>
                            Keputusan berdasarkan item revisi mahasiswa.
                        </p>

                    </div>


                    <span
                        class="revision-result-status pending"
                        id="revisionResultStatus"
                    >
                        Belum Lengkap
                    </span>

                </div>



                <div class="revision-result-summary">

                    <div>

                        <span>
                            Disetujui
                        </span>

                        <strong id="revisionApprovedCount">
                            0
                        </strong>

                    </div>


                    <div>

                        <span>
                            Revisi Lagi
                        </span>

                        <strong id="revisionAgainCount">
                            0
                        </strong>

                    </div>


                    <div>

                        <span>
                            Belum Diperiksa
                        </span>

                        <strong id="revisionPendingCount">
                            0
                        </strong>

                    </div>

                </div>



                <label class="revision-confirmation">

                    <input
                        type="checkbox"
                        id="revisionReviewConfirmation"
                    >

                    <span>
                        Saya telah memeriksa seluruh item revisi mahasiswa.
                    </span>

                </label>


                <span
                    class="revision-confirmation-error"
                    id="revisionConfirmationError"
                >
                    Centang konfirmasi sebelum menyimpan.
                </span>


                <button
                    type="button"
                    class="revision-submit-button"
                    id="submitRevisionReview"
                >
                    Simpan Hasil Review
                </button>

            </section>

        </main>

    </div>



    <!-- =====================================================
         PREVIEW MODAL
    ====================================================== -->

    <div
        class="revision-preview-overlay"
        id="revisionPreviewModal"
    >

        <div class="revision-preview-modal">

            <div class="revision-preview-header">

                <div>

                    <span>
                        PREVIEW DOKUMEN
                    </span>

                    <h2 id="revisionPreviewTitle">
                        Dokumen
                    </h2>

                </div>


                <button
                    type="button"
                    id="closeRevisionPreview"
                >
                    ×
                </button>

            </div>


            <div class="revision-preview-body">

                <div class="revision-preview-placeholder">

                    <div>
                        FILE
                    </div>


                    <strong id="revisionPreviewFilename">
                        file.pdf
                    </strong>


                    <p>
                        Preview file asli akan menggunakan
                        URL yang diberikan backend Laravel.
                    </p>

                </div>

            </div>


            <div class="revision-preview-footer">

                <span>
                    Preview file tersedia ketika file dapat diakses dari server.
                </span>


                <button
                    type="button"
                    class="admin-secondary-button"
                    id="revisionOpenNewTab"
                >
                    Buka di Tab Baru
                </button>

            </div>

        </div>

    </div>



    <!-- =====================================================
         SUCCESS MODAL
    ====================================================== -->

    <div
        class="modal-overlay"
        id="revisionReviewSuccessModal"
    >

        <div class="modal-card">

            <div class="success-icon">
                ✓
            </div>


            <h2 id="revisionReviewSuccessTitle">
                Review Disimpan
            </h2>


            <p id="revisionReviewSuccessMessage">
                Hasil review revisi berhasil disimpan.
            </p>


            <button
                type="button"
                class="btn btn-primary"
                id="revisionReviewSuccessButton"
            >
                Kembali ke Pengajuan
            </button>

        </div>

    </div>



    <!-- =====================================================
         SCRIPT
    ====================================================== -->

<script src="{{ asset('js/yudisium_api.js') }}"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
    <script src="{{ asset('js/admin_review_revisi.js') }}"></script>


</body>

</html>
