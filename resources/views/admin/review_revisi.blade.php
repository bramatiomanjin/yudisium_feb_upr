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

    <!-- CSS DETAIL PENGAJUAN (IDENTITAS, AKADEMIK, DOKUMEN) -->
    <link
        rel="stylesheet"
        href="{{ asset('css/admin_detail_pengajuan.css') }}?v={{ @filemtime(public_path('css/admin_detail_pengajuan.css')) }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/admin_review_revisi.css') }}?v={{ @filemtime(public_path('css/admin_review_revisi.css')) }}"
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
                    href="/admin/pengajuan?filter=review-revisi"
                    class="admin-nav-item active"
                >
                    <span class="admin-nav-icon">
                        !
                    </span>
                    <span>
                        Revisi Siap Direview
                    </span>

                </a>


                <a
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
                        {{ Auth::user()->initial }}
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

                @include('admin.partials.sidebar_watermark')

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
                            {{ Auth::user()->initial }}
                        </div>

                        <div>

                            <strong id="topbarAdminName">{{ Auth::user()->name }}</strong>

                            <span id="topbarAdminRole">{{ Auth::user()->role === 'SUPER_ADMIN' ? 'SUPER ADMIN' : 'ADMIN' }}</span>

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
                 1. IDENTITAS (Data Mahasiswa)
            ================================================== -->
            <section class="admin-dashboard-card detail-section">

                <div class="admin-card-header detail-section-header">

                    <div>

                        <p class="detail-section-eyebrow">
                            IDENTITAS
                        </p>

                        <h2>
                            Data Mahasiswa
                        </h2>

                        <p>
                            Informasi identitas mahasiswa yang
                            mengajukan yudisium.
                        </p>

                    </div>

                </div>


                <div class="detail-data-grid">

                    <div class="detail-data-item">

                        <span>
                            Nama Lengkap
                        </span>

                        <strong id="detailStudentFullName">
                            -
                        </strong>

                    </div>


                    <div class="detail-data-item">

                        <span>
                            NIM
                        </span>

                        <strong id="detailStudentNim">
                            -
                        </strong>

                    </div>


                    <div class="detail-data-item">

                        <span>
                            Email
                        </span>

                        <strong id="detailStudentEmail">
                            -
                        </strong>

                    </div>


                    <div class="detail-data-item">

                        <span>
                            Nomor WhatsApp
                        </span>

                        <strong id="detailStudentWhatsapp">
                            -
                        </strong>

                    </div>


                    <div class="detail-data-item">

                        <span>
                            Tahun Angkatan
                        </span>

                        <strong id="detailStudentYear">
                            -
                        </strong>

                    </div>


                    <div class="detail-data-item">

                        <span>
                            Jalur Masuk
                        </span>

                        <strong id="detailStudentEntryRoute">
                            -
                        </strong>

                    </div>


                    <div class="detail-data-item">

                        <span>
                            Jurusan
                        </span>

                        <strong id="detailStudentDepartment">
                            -
                        </strong>

                    </div>

                </div>

            </section>



            <!-- =================================================
                 2. DATA AKADEMIK
            ================================================== -->
            <section class="admin-dashboard-card detail-section">

                <div class="admin-card-header detail-section-header">

                    <div>

                        <p class="detail-section-eyebrow">
                            AKADEMIK
                        </p>

                        <h2>
                            Data Akademik
                        </h2>

                        <p>
                            Informasi karya tulis dan hasil ujian
                            mahasiswa.
                        </p>

                    </div>

                </div>


                <div class="detail-data-grid">

                    <div class="detail-data-item">

                        <span>
                            Jenis Karya Tulis
                        </span>

                        <strong id="detailWorkType">
                            -
                        </strong>

                    </div>


                    <div class="detail-data-item">

                        <span>
                            Tanggal Ujian
                        </span>

                        <strong id="detailExamDate">
                            -
                        </strong>

                    </div>


                    <div class="detail-data-item detail-data-wide">

                        <span>
                            Judul Skripsi / Artikel
                        </span>

                        <strong id="detailWorkTitle">
                            -
                        </strong>

                    </div>


                    <div class="detail-data-item">

                        <span>
                            Nilai Ujian
                        </span>

                        <strong id="detailExamScore">
                            -
                        </strong>

                    </div>


                    <div class="detail-data-item">

                        <span>
                            Nilai Huruf
                        </span>

                        <strong id="detailLetterGrade">
                            -
                        </strong>

                    </div>

                </div>

            </section>



            <!-- =================================================
                 3. DOKUMEN MAHASISWA (BERKAS)
            ================================================== -->
            <section class="admin-dashboard-card detail-section">

                <div class="admin-card-header detail-section-header">

                    <div>

                        <p class="detail-section-eyebrow">
                            BERKAS
                        </p>

                        <h2>
                            Dokumen Mahasiswa
                        </h2>

                        <p>
                            Klik tombol Preview untuk melihat
                            dokumen tanpa meninggalkan halaman.
                        </p>

                    </div>


                    <span class="detail-document-count" id="detailDocumentCount">
                        0 Dokumen
                    </span>

                </div>


                <div class="detail-document-list" id="detailDocumentList">
                    <div style="padding:24px;text-align:center;color:var(--feb-muted);">
                        Memuat berkas dokumen mahasiswa...
                    </div>
                </div>

            </section>



            <!-- =================================================
                 INFO PERIKSA ITEM REVISI
            ================================================== -->

            <section class="revision-info-box">

                <div>
                    i
                </div>


                <p>
                    Berikut adalah item yang dikirim ulang oleh mahasiswa untuk direview di bawah ini.
                    Item yang sebelumnya sudah disetujui tetap terkunci dan tidak perlu diperiksa kembali.
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
        role="dialog"
        aria-modal="true"
        aria-labelledby="revisionPreviewTitle"
        aria-hidden="true"
        tabindex="-1"
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
                    aria-label="Tutup preview dokumen revisi"
                >
                    ×
                </button>

            </div>


            <div class="revision-preview-body">
                <iframe id="revisionPreviewFrame" title="Preview Dokumen" style="width: 100%; height: 100%; display: none; border: none;"></iframe>
                <div class="revision-preview-placeholder" id="revisionPreviewPlaceholder">
                    <div>FILE</div>
                    <strong id="revisionPreviewFilename">file.pdf</strong>
                    <p>Memuat dokumen...</p>
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

    <script src="{{ asset('js/yudisium_api.js') }}?v={{ @filemtime(public_path('js/yudisium_api.js')) }}"></script>
    <script src="{{ asset('js/modal_accessibility.js') }}?v={{ @filemtime(public_path('js/modal_accessibility.js')) }}"></script>
    <script src="{{ asset('js/admin.js') }}?v={{ @filemtime(public_path('js/admin.js')) }}"></script>
    <script src="{{ asset('js/admin_review_revisi.js') }}?v={{ @filemtime(public_path('js/admin_review_revisi.js')) }}"></script>


</body>

</html>
