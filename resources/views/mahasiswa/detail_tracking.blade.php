<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Status Pengajuan Yudisium - FEB UPR
    </title>


    <link
        rel="stylesheet"
        href="{{ asset('css/style.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/student_theme.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/student_detail_tracking.css') }}"
    >

</head>


<body>

    <main class="tracking-detail-page">

        <div class="tracking-detail-container">


            <!-- =================================================
                 TOPBAR
            ================================================== -->

            <div class="tracking-detail-topbar">

                <a
                    href="/tracking"
                    class="tracking-back-link"
                >
                    ← Kembali ke Cek Status
                </a>


                <span class="tracking-faculty-badge">
                    FEB UPR
                </span>

            </div>



            <!-- =================================================
                 HEADER
            ================================================== -->

            <header class="tracking-detail-header">

                <div>

                    <span class="tracking-system-label">
                        Sistem Yudisium FEB UPR
                    </span>


                    <h1>
                        Status Pengajuan Yudisium
                    </h1>


                    <p>
                        Lihat status terbaru, arahan dari admin,
                        dan tahapan penerbitan SK Yudisium Anda.
                    </p>

                </div>

            </header>



            <!-- =================================================
                 CURRENT STATUS
            ================================================== -->

            <section
                class="status-card"
                id="statusCard"
            >

                <div
                    class="status-icon"
                    id="statusIcon"
                >
                    …
                </div>


                <div class="status-content">

                    <span class="status-caption">
                        STATUS SAAT INI
                    </span>


                    <h2 id="currentStatus">
                        Memuat status...
                    </h2>


                    <p id="statusDescription">
                        Mohon tunggu sebentar.
                    </p>

                </div>


                <div class="status-updated">

                    <span>
                        Pembaruan terakhir
                    </span>

                    <strong id="statusUpdatedAt">
                        -
                    </strong>

                </div>

            </section>



            <!-- =================================================
                 NEXT ACTION
            ================================================== -->

            <section
                class="next-action-card"
                id="nextActionCard"
            >

                <div class="next-action-number">
                    1
                </div>


                <div class="next-action-content">

                    <span class="next-action-label">
                        YANG PERLU ANDA LAKUKAN
                    </span>


                    <h2 id="nextActionTitle">
                        Tidak ada tindakan yang diperlukan
                    </h2>


                    <p id="nextActionDescription">
                        Anda dapat memantau perkembangan
                        pengajuan melalui halaman ini.
                    </p>

                </div>

            </section>



            <!-- =================================================
                 REVISION ALERT
            ================================================== -->

            <section
                class="revision-alert"
                id="revisionAlert"
            >

                <div class="revision-alert-icon">
                    !
                </div>


                <div class="revision-alert-content">

                    <span class="revision-label">
                        TINDAKAN DIPERLUKAN
                    </span>


                    <h2>
                        Pengajuan Memerlukan Revisi
                    </h2>


                    <p>
                        Admin menemukan data atau dokumen yang
                        perlu diperbaiki. Hanya bagian yang
                        ditandai admin yang dapat Anda ubah.
                    </p>


                    <div class="revision-summary">

                        <div class="revision-summary-item">

                            <span>
                                Jumlah bagian yang perlu diperbaiki
                            </span>

                            <strong id="revisionCount">
                                -
                            </strong>

                        </div>

                    </div>


                    <a
                        href="/mahasiswa/revisi"
                        class="btn btn-danger revision-button"
                    >
                        Lihat & Perbaiki Data
                    </a>

                </div>

            </section>



            <!-- =================================================
                 INFO
            ================================================== -->

            <section class="detail-card submission-card">

                <div class="detail-card-header">

                    <div>

                        <span class="section-eyebrow">
                            RINGKASAN
                        </span>


                        <h2>
                            Informasi Pengajuan
                        </h2>


                        <p>
                            Pastikan NIM dan Kode SK Yudisium
                            sesuai dengan pengajuan Anda.
                        </p>

                    </div>

                </div>



                <div class="detail-info-grid">

                    <div class="detail-info-item detail-code-item">

                        <span>
                            Kode SK Yudisium
                        </span>

                        <strong id="detailKode">
                            -
                        </strong>

                    </div>


                    <div class="detail-info-item">

                        <span>
                            NIM
                        </span>

                        <strong id="detailNim">
                            -
                        </strong>

                    </div>


                    <div class="detail-info-item">

                        <span>
                            Nama Mahasiswa
                        </span>

                        <strong id="detailNama">
                            -
                        </strong>

                    </div>


                    <div class="detail-info-item">

                        <span>
                            Jurusan
                        </span>

                        <strong id="detailJurusan">
                            -
                        </strong>

                    </div>


                    <div class="detail-info-item">

                        <span>
                            Tanggal Pengajuan
                        </span>

                        <strong id="detailTanggal">
                            -
                        </strong>

                    </div>


                    <div class="detail-info-item">

                        <span>
                            Pembaruan Terakhir
                        </span>

                        <strong id="detailUpdated">
                            -
                        </strong>

                    </div>

                </div>

            </section>



            <!-- =================================================
                 TIMELINE
            ================================================== -->

            <section class="detail-card progress-card">

                <div class="detail-card-header">

                    <div>

                        <span class="section-eyebrow">
                            PROGRES
                        </span>


                        <h2>
                            Tahapan Penerbitan SK
                        </h2>


                        <p>
                            Tahap berwarna hijau berarti selesai.
                            Tahap dengan penanda aktif menunjukkan
                            proses yang sedang berjalan.
                        </p>

                    </div>

                </div>


                <div
                    class="tracking-timeline"
                    id="trackingTimeline"
                >
                </div>

            </section>



            <!-- =================================================
                 INFO
            ================================================== -->

            <section class="tracking-help-card">

                <div class="tracking-help-icon">
                    i
                </div>


                <div>

                    <h2>
                        Informasi Penting
                    </h2>


                    <p>
                        Status diperbarui oleh Bagian Akademik
                        FEB UPR sesuai perkembangan proses.
                        Jika ada revisi, arahan admin akan
                        tampil pada halaman ini.
                    </p>


                    <p>
                        Simpan
                        <strong>
                            Kode SK Yudisium
                        </strong>
                        Anda karena kode tersebut diperlukan
                        bersama NIM untuk membuka kembali
                        status pengajuan.
                    </p>

                </div>

            </section>



            <div class="detail-actions">

                <a
                    href="/tracking"
                    class="btn btn-secondary"
                >
                    Cek Pengajuan Lain
                </a>


                <a
                    href="/"
                    class="btn btn-primary"
                >
                    Halaman Pengajuan
                </a>

            </div>

        </div>

    </main>



    <!-- CENTRAL DATA -->

    <script src="{{ asset('js/yudisium_api.js') }}"></script>
    <script src="{{ asset('js/student_tracking.js') }}"></script>


</body>

</html>