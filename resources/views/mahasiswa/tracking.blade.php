<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Cek Status Yudisium - FEB UPR
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
        href="{{ asset('css/student_tracking.css') }}"
    >

</head>


<body>

    <main class="student-tracking-page">

        <div class="student-tracking-shell">


            <!-- =================================================
                 HERO
            ================================================== -->

            <header class="tracking-hero">

                <div class="tracking-hero-copy">

                    <span class="student-system-label">
                        Sistem Yudisium FEB UPR
                    </span>


                    <h1>
                        Cek Status Pengajuan
                    </h1>


                    <p>
                        Masukkan NIM dan Kode SK Yudisium untuk melihat
                        status proses, feedback admin, dan tindakan yang
                        perlu dilakukan.
                    </p>

                </div>


                <a
                    href="/"
                    class="tracking-back-link"
                >
                    ← Form Pengajuan
                </a>

            </header>



            <!-- =================================================
                 CONTENT
            ================================================== -->

            <section
                class="tracking-layout"
                aria-label="Pencarian status yudisium"
            >


                <!-- SEARCH CARD -->

                <div class="tracking-card tracking-search-card">

                    <div class="tracking-card-heading">

                        <span class="tracking-eyebrow">
                            AKSES PENGAJUAN
                        </span>


                        <h2>
                            Masuk dengan data pengajuan
                        </h2>


                        <p>
                            Gunakan data yang sama dengan saat pengajuan.
                            Tidak diperlukan akun mahasiswa.
                        </p>

                    </div>



                    <form
                        id="trackingForm"
                        novalidate
                    >
                        @csrf


                        <!-- NIM -->

                        <div class="form-group tracking-form-group">

                            <label for="tracking_nim">

                                NIM

                                <span class="required">
                                    *
                                </span>

                            </label>


                            <input
                                type="text"
                                id="tracking_nim"
                                name="nim"
                                placeholder="Contoh: 2301110001"
                                inputmode="numeric"
                                autocomplete="off"
                                maxlength="20"
                                required
                            >


                            <small>
                                Masukkan NIM tanpa spasi,
                                titik, atau tanda lainnya.
                            </small>


                            <div
                                class="error-message"
                                id="trackingNimError"
                            >
                                NIM wajib diisi dan hanya boleh berisi angka.
                            </div>

                        </div>



                        <!-- KODE SK -->

                        <div class="form-group tracking-form-group">

                            <label for="kode_pengajuan">

                                Kode SK Yudisium

                                <span class="required">
                                    *
                                </span>

                            </label>


                            <input
                                type="text"
                                id="kode_pengajuan"
                                name="kode_pengajuan"
                                placeholder="Contoh: YDS-2026-0001"
                                autocomplete="off"
                                maxlength="30"
                                spellcheck="false"
                                required
                            >


                            <small>
                                Kode ini diberikan setelah
                                pengajuan berhasil dikirim.
                            </small>


                            <div
                                class="error-message"
                                id="trackingKodeError"
                            >
                                Kode SK Yudisium wajib diisi.
                            </div>

                        </div>



                        <!-- LOOKUP ERROR -->

                        <div
                            id="trackingLookupError"
                            style="
                                display: none;
                                margin-bottom: 20px;
                                padding: 14px 16px;
                                border: 1px solid #e3b9b5;
                                border-radius: 12px;
                                background: #fff4f3;
                                color: #a52d28;
                                font-size: 0.9rem;
                                font-weight: 700;
                                line-height: 1.5;
                            "
                        >
                            NIM dan Kode SK Yudisium tidak cocok
                            dengan data pengajuan yang tersedia.
                        </div>



                        <!-- SECURITY NOTE -->

                        <div
                            class="tracking-security-note"
                            role="note"
                        >

                            <div class="tracking-security-icon">
                                i
                            </div>


                            <p>
                                NIM dan Kode SK Yudisium digunakan
                                untuk membuka data pengajuan Anda.
                                Jangan membagikan kode kepada orang lain.
                            </p>

                        </div>



                        <button
                            type="submit"
                            class="btn btn-primary tracking-submit"
                        >
                            Cek Status Pengajuan
                        </button>

                    </form>

                </div>



                <!-- HELP -->

                <aside class="tracking-side-card">

                    <div class="tracking-side-icon">
                        ?
                    </div>


                    <span class="tracking-eyebrow">
                        BANTUAN
                    </span>


                    <h2>
                        Belum memiliki Kode SK Yudisium?
                    </h2>


                    <p>
                        Kode diberikan setelah formulir pengajuan
                        berhasil melewati validasi dan dikirim.
                    </p>


                    <ol class="tracking-help-steps">

                        <li>

                            <span>
                                1
                            </span>


                            <div>

                                <strong>
                                    Isi formulir pengajuan
                                </strong>

                                <p>
                                    Lengkapi identitas,
                                    akademik, dan dokumen.
                                </p>

                            </div>

                        </li>


                        <li>

                            <span>
                                2
                            </span>


                            <div>

                                <strong>
                                    Simpan kode yang diberikan
                                </strong>

                                <p>
                                    Kode akan muncul setelah
                                    pengajuan berhasil.
                                </p>

                            </div>

                        </li>


                        <li>

                            <span>
                                3
                            </span>


                            <div>

                                <strong>
                                    Cek status kapan saja
                                </strong>

                                <p>
                                    Gunakan NIM dan kode tersebut
                                    di halaman ini.
                                </p>

                            </div>

                        </li>

                    </ol>


                    <a
                        href="/"
                        class="tracking-secondary-link"
                    >
                        Ajukan Yudisium
                    </a>

                </aside>

            </section>



            <footer class="tracking-footer-note">

                <strong>
                    FEB UPR
                </strong>

                <span>
                    •
                </span>

                <span>
                    Sistem Pengajuan dan Tracking Yudisium
                </span>

            </footer>

        </div>

    </main>



    <!-- =====================================================
         LOADING
    ====================================================== -->

    <div
        class="modal-overlay"
        id="trackingLoading"
        aria-live="polite"
    >

        <div class="modal-card tracking-loading-card">

            <div
                class="loading-spinner"
                aria-hidden="true"
            >
            </div>


            <h2>
                Mencari Pengajuan
            </h2>


            <p>
                Mohon tunggu sebentar...
            </p>

        </div>

    </div>



    <!-- =====================================================
         CENTRAL DATA HARUS DIMUAT DULU
    ====================================================== -->

    <script src="{{ asset('js/yudisium_api.js') }}"></script>
    <script src="{{ asset('js/student_tracking.js') }}"></script>


</body>

</html>