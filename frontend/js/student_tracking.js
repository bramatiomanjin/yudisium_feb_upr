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
         * tracking.html
         */
        if (trackingForm) {

            initTrackingSearch();

        }


        /*
         * detail_tracking.html
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
               ERROR STATE
            ===================================== */

            function setError(
                field,
                hasError
            ) {

                if (!field) {

                    return;

                }


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
               SUBMIT TRACKING
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


                    if (trackingLoading) {

                        trackingLoading
                            .classList
                            .add(
                                "active"
                            );

                    }


                    /*
                     * Data simulasi frontend.
                     *
                     * Nantinya validasi NIM + kode
                     * dilakukan Laravel/database.
                     */
                    sessionStorage.setItem(
                        "tracking_nim",
                        trackingNim.value
                    );


                    sessionStorage.setItem(
                        "tracking_kode",
                        kodePengajuan.value
                    );


                    /*
                     * Kalau mahasiswa melakukan
                     * pencarian baru, status revisi
                     * simulasi lama dibersihkan.
                     */
                    sessionStorage.removeItem(
                        "revision_submitted"
                    );


                    sessionStorage.removeItem(
                        "revision_submitted_at"
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
           HALAMAN DETAIL TRACKING
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
             * Tidak boleh membuka detail
             * tanpa NIM + Kode SK.
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
               STATUS SIMULASI
            ================================================= */

            let currentStatus =
                "TTD_WAKIL_DEKAN";


            /*
             * Kalau mahasiswa sudah
             * mengirim revisi.
             */
            const revisionSubmitted =
                sessionStorage.getItem(
                    "revision_submitted"
                ) === "true";


            if (revisionSubmitted) {

                currentStatus =
                    "REVISI_DIKIRIM";

            } else if (
                kode.includes(
                    "REVISI"
                )
            ) {

                currentStatus =
                    "PERLU_REVISI";

            } else if (
                kode.includes(
                    "TERBIT"
                )
            ) {

                currentStatus =
                    "SK_TERBIT";

            } else if (
                kode.includes(
                    "DEKAN"
                )
            ) {

                currentStatus =
                    "TTD_DEKAN";

            } else if (
                kode.includes(
                    "BUATSK"
                )
            ) {

                currentStatus =
                    "PEMBUATAN_SK";

            } else if (
                kode.includes(
                    "VERIFIKASI"
                )
            ) {

                currentStatus =
                    "VERIFIKASI_ADMIN";

            } else if (
                kode.includes(
                    "VERIFIED"
                )
            ) {

                currentStatus =
                    "TERVERIFIKASI";

            } else if (
                kode.includes(
                    "DIAJUKAN"
                )
            ) {

                currentStatus =
                    "DIAJUKAN";

            }


            /*
             * Nantinya object ini berasal
             * dari response API Laravel.
             */
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
                    revisionSubmitted
                        ? "Revisi baru saja dikirim"
                        : "02 September 2026, 09.30",

                status:
                    currentStatus

            };


            renderDetail(
                mockData
            );

        }



        /* =====================================================
           RENDER DETAIL
        ===================================================== */

        function renderDetail(data) {

            setText(
                "detailKode",
                data.kodePengajuan
            );


            setText(
                "detailNim",
                data.nim
            );


            setText(
                "detailNama",
                data.nama
            );


            setText(
                "detailJurusan",
                data.jurusan
            );


            setText(
                "detailTanggal",
                data.tanggalPengajuan
            );


            setText(
                "detailUpdated",
                data.updatedAt
            );


            setText(
                "statusUpdatedAt",
                data.updatedAt
            );


            renderCurrentStatus(
                data.status
            );


            renderTimeline(
                data.status
            );


            renderRevision(
                data.status
            );


            renderNextAction(
                data.status
            );

        }



        /* =====================================================
           HELPER SET TEXT
        ===================================================== */

        function setText(
            id,
            value
        ) {

            const element =
                document.getElementById(
                    id
                );


            if (element) {

                element.textContent =
                    value;

            }

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


            if (
                !currentStatus ||
                !statusDescription ||
                !statusIcon ||
                !statusCard
            ) {

                return;

            }


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
                        "Terdapat data atau dokumen yang harus diperbaiki sesuai catatan admin.";


                    statusIcon.textContent =
                        "!";


                    statusCard
                        .classList
                        .add(
                            "status-warning"
                        );

                    break;



                case "REVISI_DIKIRIM":

                    currentStatus.textContent =
                        "Revisi Telah Dikirim";


                    statusDescription.textContent =
                        "Perbaikan telah diterima dan sekarang menunggu pemeriksaan ulang admin.";


                    statusIcon.textContent =
                        "✓";


                    statusCard
                        .classList
                        .add(
                            "status-progress"
                        );

                    break;



                case "TERVERIFIKASI":

                    currentStatus.textContent =
                        "Data Terverifikasi";


                    statusDescription.textContent =
                        "Data dan dokumen telah dinyatakan lengkap dan benar.";


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
                        "Seluruh proses penerbitan SK Yudisium telah selesai.";


                    statusIcon.textContent =
                        "✓";


                    statusCard
                        .classList
                        .add(
                            "status-success"
                        );

                    break;



                default:

                    currentStatus.textContent =
                        "Status Pengajuan";


                    statusDescription.textContent =
                        "Status pengajuan sedang diperbarui.";


                    statusIcon.textContent =
                        "i";


                    statusCard
                        .classList
                        .add(
                            "status-progress"
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


            if (!timeline) {

                return;

            }


            timeline.innerHTML =
                "";


            const steps = [

                {
                    key:
                        "DIAJUKAN",

                    title:
                        "Pengajuan Diterima",

                    description:
                        "Pengajuan berhasil dikirim ke sistem."
                },


                {
                    key:
                        "VERIFIKASI_ADMIN",

                    title:
                        "Verifikasi Admin",

                    description:
                        "Data dan dokumen diperiksa oleh Bagian Akademik."
                },


                {
                    key:
                        "TERVERIFIKASI",

                    title:
                        "Data Terverifikasi",

                    description:
                        "Seluruh persyaratan telah dinyatakan benar."
                },


                {
                    key:
                        "PEMBUATAN_SK",

                    title:
                        "Pembuatan SK",

                    description:
                        "Dokumen SK Yudisium mulai diproses."
                },


                {
                    key:
                        "TTD_WAKIL_DEKAN",

                    title:
                        "Tanda Tangan Wakil Dekan",

                    description:
                        "SK diajukan untuk tanda tangan Wakil Dekan."
                },


                {
                    key:
                        "TTD_DEKAN",

                    title:
                        "Tanda Tangan Dekan",

                    description:
                        "SK diajukan untuk tanda tangan Dekan."
                },


                {
                    key:
                        "SK_TERBIT",

                    title:
                        "SK Terbit",

                    description:
                        "Proses penerbitan SK Yudisium selesai."
                }

            ];


            const statusOrder = {

                DIAJUKAN:
                    0,

                VERIFIKASI_ADMIN:
                    1,

                PERLU_REVISI:
                    1,

                REVISI_DIKIRIM:
                    1,

                TERVERIFIKASI:
                    2,

                PEMBUATAN_SK:
                    3,

                TTD_WAKIL_DEKAN:
                    4,

                TTD_DEKAN:
                    5,

                SK_TERBIT:
                    6

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


                    /*
                     * Tahap sudah selesai.
                     */
                    if (
                        index <
                        currentIndex
                    ) {

                        item.classList.add(
                            "completed"
                        );

                    }


                    /*
                     * Tahap aktif normal.
                     */
                    if (
                        index ===
                            currentIndex &&
                        currentStatus !==
                            "PERLU_REVISI"
                    ) {

                        item.classList.add(
                            "current"
                        );

                    }


                    /*
                     * Tahap verifikasi
                     * sedang membutuhkan revisi.
                     */
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
                        index <
                        currentIndex
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

                    } else if (
                        currentStatus ===
                            "REVISI_DIKIRIM" &&
                        index === 1
                    ) {

                        marker.textContent =
                            "✓";

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
                        (
                            currentStatus ===
                                "REVISI_DIKIRIM" &&
                            index === 1
                        )
                            ? "Revisi Dikirim"
                            : step.title;


                    const description =
                        document.createElement(
                            "p"
                        );


                    description.textContent =
                        (
                            currentStatus ===
                                "REVISI_DIKIRIM" &&
                            index === 1
                        )
                            ? "Perbaikan telah dikirim dan menunggu verifikasi ulang admin."
                            : step.description;


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
           NEXT ACTION
        ===================================================== */

        function renderNextAction(
            status
        ) {

            const title =
                document.getElementById(
                    "nextActionTitle"
                );


            const description =
                document.getElementById(
                    "nextActionDescription"
                );


            const card =
                document.getElementById(
                    "nextActionCard"
                );


            if (
                !title ||
                !description ||
                !card
            ) {

                return;

            }


            card.classList.remove(
                "needs-action"
            );


            switch (status) {


                case "PERLU_REVISI":

                    title.textContent =
                        "Perbaiki data yang ditandai admin";


                    description.textContent =
                        "Buka halaman revisi, ikuti catatan admin, lalu kirim kembali perbaikan Anda.";


                    card.classList.add(
                        "needs-action"
                    );

                    break;



                case "REVISI_DIKIRIM":

                    title.textContent =
                        "Revisi sudah berhasil dikirim";


                    description.textContent =
                        "Perbaikan sedang menunggu pemeriksaan ulang admin. Anda tidak perlu mengirim ulang revisi.";

                    break;



                case "SK_TERBIT":

                    title.textContent =
                        "Proses telah selesai";


                    description.textContent =
                        "SK Yudisium telah terbit. Ikuti informasi Bagian Akademik mengenai akses atau pengambilan dokumen.";

                    break;



                case "DIAJUKAN":

                    title.textContent =
                        "Tunggu pemeriksaan admin";


                    description.textContent =
                        "Pengajuan sudah masuk ke sistem. Belum ada tindakan yang perlu Anda lakukan.";

                    break;



                case "VERIFIKASI_ADMIN":

                    title.textContent =
                        "Pengajuan sedang diperiksa";


                    description.textContent =
                        "Admin sedang memeriksa data dan dokumen Anda. Pantau halaman ini untuk melihat hasil verifikasi.";

                    break;



                case "TERVERIFIKASI":

                    title.textContent =
                        "Tidak ada tindakan yang diperlukan";


                    description.textContent =
                        "Data sudah terverifikasi. Proses berikutnya akan dilakukan oleh Bagian Akademik.";

                    break;



                case "PEMBUATAN_SK":

                    title.textContent =
                        "SK sedang dipersiapkan";


                    description.textContent =
                        "Anda cukup menunggu proses pembuatan SK selesai.";

                    break;



                case "TTD_WAKIL_DEKAN":

                    title.textContent =
                        "Menunggu tanda tangan Wakil Dekan";


                    description.textContent =
                        "Tidak ada tindakan yang perlu dilakukan. SK sedang diproses untuk tanda tangan Wakil Dekan.";

                    break;



                case "TTD_DEKAN":

                    title.textContent =
                        "Menunggu tanda tangan Dekan";


                    description.textContent =
                        "Tidak ada tindakan yang perlu dilakukan. SK sedang diproses untuk tanda tangan Dekan.";

                    break;



                default:

                    title.textContent =
                        "Tidak ada tindakan yang diperlukan";


                    description.textContent =
                        "Pengajuan sedang diproses. Anda cukup memantau pembaruan status pada halaman ini.";

                    break;

            }

        }



        /* =====================================================
           REVISION ALERT
        ===================================================== */

        function renderRevision(
            status
        ) {

            const revisionAlert =
                document.getElementById(
                    "revisionAlert"
                );


            if (!revisionAlert) {

                return;

            }


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