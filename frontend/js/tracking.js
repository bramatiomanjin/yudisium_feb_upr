document.addEventListener(
    "DOMContentLoaded",
    function () {

        console.log(
            "Tracking Yudisium aktif"
        );


        /* =====================================================
           CEK HALAMAN
        ===================================================== */

        const trackingForm =
            document.getElementById(
                "trackingForm"
            );


        const trackingTimeline =
            document.getElementById(
                "trackingTimeline"
            );


        /*
         * Jika trackingForm tersedia,
         * berarti sedang berada di tracking.html
         */
        if (trackingForm) {

            initTrackingSearch();

        }


        /*
         * Jika trackingTimeline tersedia,
         * berarti sedang berada di detail_tracking.html
         */
        if (trackingTimeline) {

            initTrackingDetail();

        }



        /* =====================================================
           HALAMAN PENCARIAN
        ===================================================== */

        function initTrackingSearch() {

            const trackingNim =
                document.getElementById(
                    "tracking_nim"
                );


            const kodePengajuan =
                document.getElementById(
                    "kode_pengajuan"
                );


            const trackingLoading =
                document.getElementById(
                    "trackingLoading"
                );


            /* =====================================
               ERROR
            ===================================== */

            function setError(
                field,
                hasError
            ) {

                const formGroup =
                    field.closest(
                        ".form-group"
                    );


                if (hasError) {

                    field
                        .classList
                        .add(
                            "form-control-error"
                        );


                    if (formGroup) {

                        formGroup
                            .classList
                            .add(
                                "has-error"
                            );

                    }

                } else {

                    field
                        .classList
                        .remove(
                            "form-control-error"
                        );


                    if (formGroup) {

                        formGroup
                            .classList
                            .remove(
                                "has-error"
                            );

                    }

                }

            }



            /* =====================================
               VALIDASI NIM
            ===================================== */

            function validateNim() {

                const nim =
                    trackingNim
                        .value
                        .trim();


                let valid = true;


                if (nim === "") {

                    valid = false;

                }


                if (
                    nim !== "" &&
                    !/^\d+$/.test(nim)
                ) {

                    valid = false;

                }


                setError(
                    trackingNim,
                    !valid
                );


                return valid;

            }



            /* =====================================
               VALIDASI KODE
            ===================================== */

            function validateKode() {

                const kode =
                    kodePengajuan
                        .value
                        .trim();


                let valid = true;


                if (kode === "") {

                    valid = false;

                }


                setError(
                    kodePengajuan,
                    !valid
                );


                return valid;

            }



            /* =====================================
               INPUT NIM
            ===================================== */

            trackingNim.addEventListener(
                "input",
                function () {

                    this.value =
                        this.value.replace(
                            /\D/g,
                            ""
                        );


                    validateNim();

                }
            );



            /* =====================================
               INPUT KODE
            ===================================== */

            kodePengajuan.addEventListener(
                "input",
                function () {

                    this.value =
                        this.value
                            .toUpperCase();


                    validateKode();

                }
            );



            /* =====================================
               SUBMIT
            ===================================== */

            trackingForm.addEventListener(
                "submit",
                function (event) {

                    event.preventDefault();


                    const nimValid =
                        validateNim();


                    const kodeValid =
                        validateKode();


                    if (
                        !nimValid ||
                        !kodeValid
                    ) {

                        return;

                    }


                    trackingLoading
                        .classList
                        .add("active");


                    /*
                     * Penyimpanan sementara
                     * untuk simulasi frontend.
                     *
                     * Nanti data berasal
                     * dari Laravel.
                     */
                    sessionStorage.setItem(
                        "tracking_nim",
                        trackingNim.value
                    );


                    sessionStorage.setItem(
                        "tracking_kode",
                        kodePengajuan.value
                    );


                    setTimeout(
                        function () {

                            window.location.href =
                                "detail_tracking.html";

                        },
                        700
                    );

                }
            );

        }



        /* =====================================================
           HALAMAN DETAIL
        ===================================================== */

        function initTrackingDetail() {

            const kode =
                sessionStorage.getItem(
                    "tracking_kode"
                );


            const nim =
                sessionStorage.getItem(
                    "tracking_nim"
                );


            /*
             * Jika user membuka detail secara langsung,
             * arahkan ke halaman pencarian.
             */
            if (
                !kode ||
                !nim
            ) {

                window.location.href =
                    "tracking.html";


                return;

            }


            /* =================================================
               DATA SIMULASI
            ================================================= */

            /*
             * Untuk testing frontend:
             *
             * jika kode mengandung "REVISI"
             * maka status PERLU_REVISI.
             *
             * selain itu:
             * TTD_WAKIL_DEKAN.
             *
             * Nanti seluruh bagian ini diganti
             * data database Laravel.
             */

            let currentStatus =
                "TTD_WAKIL_DEKAN";


            if (
                kode.includes(
                    "REVISI"
                )
            ) {

                currentStatus =
                    "PERLU_REVISI";

            }


            const mockData = {

                kodePengajuan:
                    kode,

                nim:
                    nim,

                nama:
                    "Mahasiswa FEB UPR",

                jurusan:
                    "Manajemen",

                tanggalPengajuan:
                    "02 September 2026",

                updatedAt:
                    "02 September 2026, 09.30",

                status:
                    currentStatus

            };


            renderDetail(mockData);

        }



        /* =====================================================
           RENDER DETAIL
        ===================================================== */

        function renderDetail(data) {

            document
                .getElementById(
                    "detailKode"
                )
                .textContent =
                    data.kodePengajuan;


            document
                .getElementById(
                    "detailNim"
                )
                .textContent =
                    data.nim;


            document
                .getElementById(
                    "detailNama"
                )
                .textContent =
                    data.nama;


            document
                .getElementById(
                    "detailJurusan"
                )
                .textContent =
                    data.jurusan;


            document
                .getElementById(
                    "detailTanggal"
                )
                .textContent =
                    data.tanggalPengajuan;


            document
                .getElementById(
                    "detailUpdated"
                )
                .textContent =
                    data.updatedAt;


            renderCurrentStatus(
                data.status
            );


            renderTimeline(
                data.status
            );


            renderRevision(
                data.status
            );

        }



        /* =====================================================
           CURRENT STATUS
        ===================================================== */

        function renderCurrentStatus(
            status
        ) {

            const currentStatus =
                document.getElementById(
                    "currentStatus"
                );


            const statusDescription =
                document.getElementById(
                    "statusDescription"
                );


            const statusIcon =
                document.getElementById(
                    "statusIcon"
                );


            const statusCard =
                document.getElementById(
                    "statusCard"
                );


            statusCard.classList.remove(
                "status-warning",
                "status-success",
                "status-progress"
            );


            switch (status) {


                case "DIAJUKAN":

                    currentStatus.textContent =
                        "Pengajuan Diterima";

                    statusDescription.textContent =
                        "Pengajuan telah diterima dan menunggu pemeriksaan admin.";

                    statusIcon.textContent =
                        "✓";

                    statusCard
                        .classList
                        .add(
                            "status-progress"
                        );

                    break;



                case "VERIFIKASI_ADMIN":

                    currentStatus.textContent =
                        "Sedang Diverifikasi Admin";

                    statusDescription.textContent =
                        "Bagian Akademik sedang memeriksa data dan dokumen pengajuan.";

                    statusIcon.textContent =
                        "⌕";

                    statusCard
                        .classList
                        .add(
                            "status-progress"
                        );

                    break;



                case "PERLU_REVISI":

                    currentStatus.textContent =
                        "Perlu Revisi";

                    statusDescription.textContent =
                        "Terdapat data atau dokumen yang harus diperbaiki.";

                    statusIcon.textContent =
                        "!";

                    statusCard
                        .classList
                        .add(
                            "status-warning"
                        );

                    break;



                case "TERVERIFIKASI":

                    currentStatus.textContent =
                        "Data Terverifikasi";

                    statusDescription.textContent =
                        "Data telah dinyatakan lengkap dan benar.";

                    statusIcon.textContent =
                        "✓";

                    statusCard
                        .classList
                        .add(
                            "status-success"
                        );

                    break;



                case "PEMBUATAN_SK":

                    currentStatus.textContent =
                        "Pembuatan SK";

                    statusDescription.textContent =
                        "SK Yudisium sedang dipersiapkan oleh Bagian Akademik.";

                    statusIcon.textContent =
                        "⌛";

                    statusCard
                        .classList
                        .add(
                            "status-progress"
                        );

                    break;



                case "TTD_WAKIL_DEKAN":

                    currentStatus.textContent =
                        "Proses Tanda Tangan Wakil Dekan";

                    statusDescription.textContent =
                        "SK sedang dalam proses tanda tangan Wakil Dekan.";

                    statusIcon.textContent =
                        "✎";

                    statusCard
                        .classList
                        .add(
                            "status-progress"
                        );

                    break;



                case "TTD_DEKAN":

                    currentStatus.textContent =
                        "Proses Tanda Tangan Dekan";

                    statusDescription.textContent =
                        "SK sedang dalam proses tanda tangan Dekan.";

                    statusIcon.textContent =
                        "✎";

                    statusCard
                        .classList
                        .add(
                            "status-progress"
                        );

                    break;



                case "SK_TERBIT":

                    currentStatus.textContent =
                        "SK Yudisium Telah Terbit";

                    statusDescription.textContent =
                        "Seluruh proses penerbitan SK telah selesai.";

                    statusIcon.textContent =
                        "✓";

                    statusCard
                        .classList
                        .add(
                            "status-success"
                        );

                    break;

            }

        }



        /* =====================================================
           TIMELINE
        ===================================================== */

        function renderTimeline(
            currentStatus
        ) {

            const timeline =
                document.getElementById(
                    "trackingTimeline"
                );


            timeline.innerHTML = "";


            const steps = [

                {
                    key: "DIAJUKAN",
                    title: "Pengajuan Diterima",
                    description:
                        "Pengajuan berhasil dikirim ke sistem."
                },

                {
                    key: "VERIFIKASI_ADMIN",
                    title: "Verifikasi Admin",
                    description:
                        "Data dan dokumen diperiksa oleh Bagian Akademik."
                },

                {
                    key: "TERVERIFIKASI",
                    title: "Data Terverifikasi",
                    description:
                        "Seluruh persyaratan telah dinyatakan benar."
                },

                {
                    key: "PEMBUATAN_SK",
                    title: "Pembuatan SK",
                    description:
                        "Dokumen SK Yudisium mulai diproses."
                },

                {
                    key: "TTD_WAKIL_DEKAN",
                    title: "Tanda Tangan Wakil Dekan",
                    description:
                        "SK diajukan untuk tanda tangan Wakil Dekan."
                },

                {
                    key: "TTD_DEKAN",
                    title: "Tanda Tangan Dekan",
                    description:
                        "SK diajukan untuk tanda tangan Dekan."
                },

                {
                    key: "SK_TERBIT",
                    title: "SK Terbit",
                    description:
                        "Proses penerbitan SK Yudisium selesai."
                }

            ];


            const statusOrder = {

                DIAJUKAN: 0,

                VERIFIKASI_ADMIN: 1,

                PERLU_REVISI: 1,

                REVISI_DIKIRIM: 1,

                TERVERIFIKASI: 2,

                PEMBUATAN_SK: 3,

                TTD_WAKIL_DEKAN: 4,

                TTD_DEKAN: 5,

                SK_TERBIT: 6

            };


            const currentIndex =
                statusOrder[
                    currentStatus
                ] ?? 0;


            steps.forEach(
                function (
                    step,
                    index
                ) {

                    const item =
                        document.createElement(
                            "div"
                        );


                    item.className =
                        "timeline-item";


                    if (
                        index < currentIndex
                    ) {

                        item.classList.add(
                            "completed"
                        );

                    }


                    if (
                        index === currentIndex &&
                        currentStatus !==
                            "PERLU_REVISI"
                    ) {

                        item.classList.add(
                            "current"
                        );

                    }


                    if (
                        currentStatus ===
                            "PERLU_REVISI" &&
                        index === 1
                    ) {

                        item.classList.add(
                            "revision"
                        );

                    }


                    const marker =
                        document.createElement(
                            "div"
                        );


                    marker.className =
                        "timeline-marker";


                    if (
                        index < currentIndex
                    ) {

                        marker.textContent =
                            "✓";

                    } else if (
                        currentStatus ===
                            "PERLU_REVISI" &&
                        index === 1
                    ) {

                        marker.textContent =
                            "!";

                    } else {

                        marker.textContent =
                            index + 1;

                    }


                    const content =
                        document.createElement(
                            "div"
                        );


                    content.className =
                        "timeline-content";


                    const title =
                        document.createElement(
                            "h3"
                        );


                    title.textContent =
                        step.title;


                    const description =
                        document.createElement(
                            "p"
                        );


                    description.textContent =
                        step.description;


                    content.appendChild(
                        title
                    );


                    content.appendChild(
                        description
                    );


                    item.appendChild(
                        marker
                    );


                    item.appendChild(
                        content
                    );


                    timeline.appendChild(
                        item
                    );

                }
            );

        }



        /* =====================================================
           REVISION
        ===================================================== */

        function renderRevision(
            status
        ) {

            const revisionAlert =
                document.getElementById(
                    "revisionAlert"
                );


            if (
                status ===
                "PERLU_REVISI"
            ) {

                revisionAlert
                    .classList
                    .add(
                        "active"
                    );

            } else {

                revisionAlert
                    .classList
                    .remove(
                        "active"
                    );

            }

        }

    }
);