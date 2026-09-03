document.addEventListener(
    "DOMContentLoaded",
    function () {

        "use strict";


        if (!window.YudisiumAPI) {
            return;
        }


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
                        !nim.value.trim() ||
                        !code.value.trim()
                    ) {

                        return;

                    }


                    try {

                        const submission =
                            await window.YudisiumAPI
                                .findSubmission(
                                    nim.value,
                                    code.value
                                );


                        if (!submission) {

                            if (errorBox) {

                                errorBox.style.display =
                                    "block";

                                errorBox.textContent =
                                    window.YudisiumAPI
                                        .config
                                        .backendConnected
                                        ? "NIM dan Kode SK Yudisium tidak ditemukan."
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
                            "detail_tracking.html";

                    } catch (error) {

                        if (errorBox) {

                            errorBox.style.display =
                                "block";

                            errorBox.textContent =
                                "Sistem belum dapat mengakses data pengajuan.";

                        }

                    }

                }
            );

        }


        /* =====================================================
           DETAIL
        ===================================================== */

        async function initDetail() {

            const nim =
                sessionStorage.getItem(
                    "tracking_nim"
                );


            const code =
                sessionStorage.getItem(
                    "tracking_kode"
                );


            if (
                !nim ||
                !code
            ) {

                window.location.href =
                    "tracking.html";

                return;

            }


            const submission =
                await window.YudisiumAPI
                    .findSubmission(
                        nim,
                        code
                    );


            if (!submission) {

                window.location.href =
                    "tracking.html";

                return;

            }


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
                submission.submittedAt
            );

            setText(
                "detailUpdated",
                submission.updatedAt ||
                submission.submittedAt
            );

            setText(
                "statusUpdatedAt",
                submission.updatedAt ||
                submission.submittedAt
            );


            renderStatus(
                submission
            );


            renderTimeline(
                submission.status
            );


            if (
                submission.status ===
                window.YudisiumAPI
                    .STATUS
                    .PERLU_REVISI
            ) {

                const alert =
                    document.getElementById(
                        "revisionAlert"
                    );


                alert?.classList.add(
                    "active"
                );


                setText(
                    "revisionCount",
                    submission.revisionCount ||
                    "-"
                );

            }

        }


        function setText(
            id,
            value
        ) {

            const element =
                document.getElementById(id);


            if (element) {

                element.textContent =
                    value || "-";

            }

        }


        function renderStatus(
            submission
        ) {

            const STATUS =
                window.YudisiumAPI.STATUS;


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
                        "SK Siap Diambil";

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


        function renderTimeline(
            status
        ) {

            const STATUS =
                window.YudisiumAPI.STATUS;


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
                        "TTD Wakil Dekan"
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
                        "SK Siap Diambil"
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
                order[status] || 0;


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


                    if (i < index) {

                        item.classList.add(
                            "completed"
                        );

                    }


                    if (i === index) {

                        item.classList.add(
                            "current"
                        );

                    }


                    item.innerHTML =
                        `
                        <div class="timeline-marker">
                            ${i < index ? "✓" : i + 1}
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