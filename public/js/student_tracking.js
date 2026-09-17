document.addEventListener(
    "DOMContentLoaded",
    function () {

        "use strict";

        if (!window.YudisiumAPI) {
            console.error("YudisiumAPI tidak ditemukan.");
            return;
        }

        const API =
            window.YudisiumAPI;

        const trackingForm =
            document.getElementById(
                "trackingForm"
            );

        const timeline =
            document.getElementById(
                "trackingTimeline"
            );

        if (trackingForm) {
            initSearch();
        }

        if (timeline) {
            initDetail();
        }


        /* =====================================================
           SEARCH
        ===================================================== */

        function initSearch() {

            const nim =
                document.getElementById(
                    "tracking_nim"
                );

            const code =
                document.getElementById(
                    "kode_pengajuan"
                );

            const errorBox =
                document.getElementById(
                    "trackingLookupError"
                );

            trackingForm.addEventListener(
                "submit",
                async function (event) {

                    event.preventDefault();

                    if (
                        !nim.value.trim()
                    ) {
                        return;
                    }

                    try {

                        if (errorBox) {
                            errorBox.style.display =
                                "none";
                        }

                        const submission =
                            await API.findSubmission(
                                nim.value,
                                code.value
                            );

                        if (!submission) {

                            if (errorBox) {

                                errorBox.style.display =
                                    "block";

                                errorBox.textContent =
                                    API.config.backendConnected
                                        ? "Data pengajuan tidak ditemukan."
                                        : "Sistem tracking belum terhubung ke backend.";
                            }

                            return;
                        }

                        sessionStorage.setItem(
                            "tracking_nim",
                            submission.nim
                        );

                        sessionStorage.setItem(
                            "tracking_kode",
                            submission.code
                        );

                        sessionStorage.setItem(
                            "tracking_submission_id",
                            String(
                                submission.id
                            )
                        );

                        window.location.href =
                            "/detail_tracking";

                    } catch (error) {

                        console.error(
                            "Tracking error:",
                            error
                        );

                        if (errorBox) {

                            errorBox.style.display =
                                "block";

                            errorBox.textContent =
                                error.message || "Sistem belum dapat mengakses data pengajuan.";
                        }
                    }
                }
            );
        }


        /* =====================================================
           DETAIL TRACKING
        ===================================================== */

        async function initDetail() {

            const urlParams =
                new URLSearchParams(
                    window.location.search
                );

            const queryNim =
                urlParams.get("nim");

            const queryKode =
                urlParams.get("kode") ||
                urlParams.get("kode_sk") ||
                urlParams.get("kode_pengajuan");

            if (queryNim) {
                sessionStorage.setItem(
                    "tracking_nim",
                    queryNim.trim()
                );
                if (queryKode) {
                    sessionStorage.setItem(
                        "tracking_kode",
                        queryKode.trim()
                    );
                }
            }

            const nim =
                sessionStorage.getItem(
                    "tracking_nim"
                );

            const code =
                sessionStorage.getItem(
                    "tracking_kode"
                );

            if (
                !nim
            ) {

                window.location.href =
                    "/tracking";

                return;
            }

            try {

                /*
                 * Pertama cari pengajuan berdasarkan
                 * NIM + kode seperti biasa.
                 */
                let submission =
                    await API.findSubmission(
                        nim,
                        code
                    );

                if (!submission) {

                    window.location.href =
                        "/tracking";

                    return;
                }


                /*
                 * =================================================
                 * PENTING
                 *
                 * Endpoint tracking mungkin hanya mengirim
                 * data ringkas.
                 *
                 * Kita ambil detail submission lagi agar
                 * mendapatkan revisionCount dari:
                 *
                 * GET /submissions/{id}
                 * =================================================
                 */

                if (submission.id) {

                    try {

                        const detail =
                            await API.getSubmission(
                                submission.id,
                                code
                            );

                        if (detail) {

                            /*
                             * Gabungkan data.
                             * Detail menimpa property yang sama
                             * karena datanya lebih lengkap.
                             */
                            submission = {
                                ...submission,
                                ...detail
                            };
                        }

                    } catch (detailError) {

                        /*
                         * Tracking tetap ditampilkan meskipun
                         * request detail gagal.
                         */
                        console.error(
                            "Gagal mengambil detail submission:",
                            detailError
                        );
                    }
                }


                /* =================================================
                   DATA PENGAJUAN
                ================================================= */

                setText(
                    "detailKode",
                    submission.code
                );

                setText(
                    "detailNim",
                    submission.nim
                );

                setText(
                    "detailNama",
                    submission.name
                );

                setText(
                    "detailJurusan",
                    submission.department
                );

                setText(
    "detailTanggal",
    formatDateTime(
        submission.submittedAt
    )
);

setText(
    "detailUpdated",
    formatDateTime(
        submission.updatedAt ||
        submission.submittedAt
    )
);

setText(
    "statusUpdatedAt",
    formatDateTime(
        submission.updatedAt ||
        submission.submittedAt
    )
);

function formatDateTime(value) {

    if (!value) {
        return "-";
    }

    const date =
        new Date(value);

    if (
        Number.isNaN(
            date.getTime()
        )
    ) {
        return value;
    }

    return new Intl.DateTimeFormat(
        "id-ID",
        {
            timeZone:
                "Asia/Jakarta",

            day:
                "2-digit",

            month:
                "long",

            year:
                "numeric",

            hour:
                "2-digit",

            minute:
                "2-digit",

            hour12:
                false
        }
    )
        .format(date)
        .replace(
            ".",
            ":"
        ) +
        " WIB";
}


                /* =================================================
                   STATUS + TIMELINE
                ================================================= */

                renderStatus(
                    submission
                );

                renderTimeline(
                    submission.status
                );


                /* =================================================
                   REVISI
                ================================================= */

                if (
                    submission.status ===
                    API.STATUS.PERLU_REVISI
                ) {

                    const revisionAlert =
                        document.getElementById(
                            "revisionAlert"
                        );

                    revisionAlert
                        ?.classList
                        .add(
                            "active"
                        );


                    /*
                     * Jumlah field + dokumen yang statusnya REVISI.
                     */
                    const revisionCount =
                        Number(
                            submission.revisionCount ??
                            0
                        );


                    const revisionCountElement =
                        document.getElementById(
                            "revisionCount"
                        );

                    if (
                        revisionCountElement
                    ) {

                        revisionCountElement
                            .textContent =
                            revisionCount;
                    }


                    /*
                     * Tombol pada Blade sebelumnya:
                     *
                     * /mahasiswa/revisi
                     *
                     * Sekarang diarahkan ke route Laravel:
                     *
                     * /revisi/{kode_pengajuan}
                     */
                    const revisionButton =
                        document.querySelector(
                            ".revision-button"
                        );

                    if (
                        revisionButton &&
                        submission.code
                    ) {

                        revisionButton.href =
                            "/revisi/" +
                            encodeURIComponent(
                                submission.code
                            );
                    }
                }

                console.log(
                    "Tracking detail:",
                    submission
                );

            } catch (error) {

                console.error(
                    "Gagal memuat detail tracking:",
                    error
                );

                window.location.href =
                    "/tracking";
            }
        }


        /* =====================================================
           HELPER TEXT
        ===================================================== */

        function setText(
            id,
            value
        ) {

            const element =
                document.getElementById(
                    id
                );

            if (!element) {
                return;
            }

            if (
                value === undefined ||
                value === null ||
                String(value).trim() === ""
            ) {

                element.textContent =
                    "-";

                return;
            }

            element.textContent =
                value;
        }


        /* =====================================================
           STATUS
        ===================================================== */

        function renderStatus(
            submission
        ) {

            const STATUS =
                API.STATUS;

            let title =
                "Pengajuan Diproses";

            let description =
                "Pengajuan sedang diproses oleh FEB UPR.";

            let icon =
                "•";


            switch (
                submission.status
            ) {

                case STATUS.MENUNGGU_VERIFIKASI:

                    title =
                        "Menunggu Verifikasi";

                    description =
                        "Pengajuan menunggu pemeriksaan Bagian Akademik.";

                    icon =
                        "⌕";

                    break;


                case STATUS.PERLU_REVISI:

                    title =
                        "Perlu Revisi";

                    description =
                        "Terdapat data atau dokumen yang perlu diperbaiki.";

                    icon =
                        "!";

                    break;


                case STATUS.REVISI_DIKIRIM:

                    title =
                        "Revisi Telah Dikirim";

                    description =
                        "Revisi menunggu pemeriksaan ulang Admin.";

                    icon =
                        "✓";

                    break;


                case STATUS.TERVERIFIKASI:

                    title =
                        "Data Terverifikasi";

                    description =
                        "Pengajuan dinyatakan lengkap dan benar.";

                    icon =
                        "✓";

                    break;


                case STATUS.PEMBUATAN_SK:

                    title =
                        "Pembuatan SK";

                    description =
                        "SK Yudisium sedang diproses.";

                    icon =
                        "⌛";

                    break;


                case STATUS.TTD_WAKIL_DEKAN:

                    title =
                        "Tanda Tangan Wakil Dekan";

                    description =
                        "SK sedang melalui proses tanda tangan Wakil Dekan.";

                    icon =
                        "✎";

                    break;


                case STATUS.TTD_DEKAN:

                    title =
                        "Tanda Tangan Dekan";

                    description =
                        "SK sedang melalui proses tanda tangan Dekan.";

                    icon =
                        "✎";

                    break;


                case STATUS.SK_SIAP_DIAMBIL:

                    title =
                        "SK Selesai";

                    description =
                        "SK Yudisium telah selesai diproses. Silakan mengambil SK di Bagian Akademik FEB UPR.";

                    icon =
                        "✓";

                    break;
            }


            setText(
                "currentStatus",
                title
            );

            setText(
                "statusDescription",
                description
            );

            setText(
                "statusIcon",
                icon
            );


            const nextTitle =
                document.getElementById(
                    "nextActionTitle"
                );

            const nextDescription =
                document.getElementById(
                    "nextActionDescription"
                );


            if (
                !nextTitle ||
                !nextDescription
            ) {
                return;
            }


            if (
                submission.status ===
                STATUS.SK_SIAP_DIAMBIL
            ) {

                nextTitle.textContent =
                    "Ambil SK Yudisium di Fakultas";

                nextDescription.textContent =
                    "Silakan datang ke Bagian Akademik FEB UPR untuk mengambil SK Yudisium yang telah selesai.";

            } else if (
                submission.status ===
                STATUS.PERLU_REVISI
            ) {

                nextTitle.textContent =
                    "Perbaiki data atau dokumen";

                nextDescription.textContent =
                    "Buka halaman revisi dan ikuti feedback Admin.";

            } else {

                nextTitle.textContent =
                    "Tidak ada tindakan yang diperlukan";

                nextDescription.textContent =
                    "Silakan pantau perkembangan pengajuan melalui halaman ini.";
            }
        }


        /* =====================================================
           TIMELINE
        ===================================================== */

        function renderTimeline(
            status
        ) {

            if (!timeline) {
                return;
            }

            const STATUS =
                API.STATUS;

            const steps = [

                {
                    key:
                        STATUS.MENUNGGU_VERIFIKASI,

                    title:
                        "Pengajuan Diterima"
                },

                {
                    key:
                        STATUS.TERVERIFIKASI,

                    title:
                        "Data Terverifikasi"
                },

                {
                    key:
                        STATUS.PEMBUATAN_SK,

                    title:
                        "Pembuatan SK"
                },

                {
                    key:
                        STATUS.TTD_WAKIL_DEKAN,

                    title:
                        "Paraf Pimpinan"
                },

                {
                    key:
                        STATUS.TTD_DEKAN,

                    title:
                        "TTD Dekan"
                },

                {
                    key:
                        STATUS.SK_SIAP_DIAMBIL,

                    title:
                        "SK Selesai"
                }

            ];


            const order = {

                [STATUS.MENUNGGU_VERIFIKASI]:
                    0,

                [STATUS.PERLU_REVISI]:
                    0,

                [STATUS.REVISI_DIKIRIM]:
                    0,

                [STATUS.TERVERIFIKASI]:
                    1,

                [STATUS.PEMBUATAN_SK]:
                    2,

                [STATUS.TTD_WAKIL_DEKAN]:
                    3,

                [STATUS.TTD_DEKAN]:
                    4,

                [STATUS.SK_SIAP_DIAMBIL]:
                    5
            };


            const index =
                order[status] ?? 0;


            timeline.innerHTML =
                "";


            steps.forEach(
                function (
                    step,
                    i
                ) {

                    const item =
                        document.createElement(
                            "div"
                        );

                    item.className =
                        "timeline-item";


                    if (
                        i < index
                    ) {

                        item.classList.add(
                            "completed"
                        );
                    }


                    if (
                        i === index
                    ) {

                        item.classList.add(
                            "current"
                        );
                    }


                    item.innerHTML = `
                        <div class="timeline-marker">
                            ${
                                i < index
                                    ? "✓"
                                    : i + 1
                            }
                        </div>

                        <div class="timeline-content">
                            <h3>
                                ${step.title}
                            </h3>
                        </div>
                    `;


                    timeline.appendChild(
                        item
                    );
                }
            );
        }

    }
);