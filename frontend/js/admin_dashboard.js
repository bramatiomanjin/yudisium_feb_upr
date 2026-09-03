document.addEventListener(
    "DOMContentLoaded",
    function () {

        "use strict";


        if (
            !window.YudisiumAPI
        ) {

            console.error(
                "YudisiumAPI tidak ditemukan."
            );

            return;

        }


        const API =
            window.YudisiumAPI;


        const STATUS =
            API.STATUS;


        const elements = {

            total:
                document.getElementById(
                    "dashboardTotalCount"
                ),

            pending:
                document.getElementById(
                    "dashboardPendingCount"
                ),

            revision:
                document.getElementById(
                    "dashboardRevisionCount"
                ),

            verified:
                document.getElementById(
                    "dashboardVerifiedCount"
                ),

            process:
                document.getElementById(
                    "dashboardProcessCount"
                ),

            ready:
                document.getElementById(
                    "dashboardPublishedCount"
                ),

            sidebarRevision:
                document.getElementById(
                    "sidebarRevisionCount"
                ),

            quickPending:
                document.getElementById(
                    "dashboardQuickPendingText"
                ),

            quickRevision:
                document.getElementById(
                    "dashboardQuickRevisionText"
                ),

            quickVerified:
                document.getElementById(
                    "dashboardQuickVerifiedText"
                ),

            quickProcess:
                document.getElementById(
                    "dashboardQuickProcessText"
                ),

            tableBody:
                document.getElementById(
                    "dashboardSubmissionTableBody"
                ),

            empty:
                document.getElementById(
                    "dashboardEmptyState"
                ),

            date:
                document.getElementById(
                    "dashboardDate"
                ),

            export:
                document.getElementById(
                    "quickExport"
                ),

            exportModal:
                document.getElementById(
                    "exportModal"
                ),

            closeExportModal:
                document.getElementById(
                    "closeExportModal"
                )

        };


        /* =====================================================
           HELPERS
        ===================================================== */

        function escapeHtml(value) {

            return String(
                value ?? ""
            )
                .replace(
                    /&/g,
                    "&amp;"
                )
                .replace(
                    /</g,
                    "&lt;"
                )
                .replace(
                    />/g,
                    "&gt;"
                )
                .replace(
                    /"/g,
                    "&quot;"
                )
                .replace(
                    /'/g,
                    "&#039;"
                );

        }


        function setText(
            element,
            value
        ) {

            if (
                element
            ) {

                element.textContent =
                    value;

            }

        }


        /* =====================================================
           DATE
        ===================================================== */

        function renderDate() {

            if (
                !elements.date
            ) {

                return;

            }


            elements.date.textContent =
                new Date()
                    .toLocaleDateString(
                        "id-ID",
                        {

                            weekday:
                                "long",

                            day:
                                "2-digit",

                            month:
                                "long",

                            year:
                                "numeric"

                        }
                    );

        }


        /* =====================================================
           STATISTICS
        ===================================================== */

        function calculateStatistics(
            submissions
        ) {

            return {

                total:
                    submissions.length,


                pending:
                    submissions.filter(
                        function (item) {

                            return (
                                item.status ===
                                STATUS.MENUNGGU_VERIFIKASI
                            );

                        }
                    ).length,


                revision:
                    submissions.filter(
                        function (item) {

                            return (
                                item.status ===
                                STATUS.PERLU_REVISI
                            );

                        }
                    ).length,


                revisionSubmitted:
                    submissions.filter(
                        function (item) {

                            return (
                                item.status ===
                                STATUS.REVISI_DIKIRIM
                            );

                        }
                    ).length,


                verified:
                    submissions.filter(
                        function (item) {

                            return (
                                item.status ===
                                STATUS.TERVERIFIKASI
                            );

                        }
                    ).length,


                process:
                    submissions.filter(
                        function (item) {

                            return [

                                STATUS.PEMBUATAN_SK,
                                STATUS.TTD_WAKIL_DEKAN,
                                STATUS.TTD_DEKAN

                            ].includes(
                                item.status
                            );

                        }
                    ).length,


                ready:
                    submissions.filter(
                        function (item) {

                            return (
                                item.status ===
                                STATUS.SK_SIAP_DIAMBIL
                            );

                        }
                    ).length

            };

        }


        function renderStatistics(
            submissions
        ) {

            const stats =
                calculateStatistics(
                    submissions
                );


            setText(
                elements.total,
                stats.total
            );


            setText(
                elements.pending,
                stats.pending
            );


            setText(
                elements.revision,
                stats.revision
            );


            setText(
                elements.verified,
                stats.verified
            );


            setText(
                elements.process,
                stats.process
            );


            setText(
                elements.ready,
                stats.ready
            );


            /*
             * Sidebar "Perlu Revisi" menunjukkan
             * seluruh pekerjaan revisi yang masih aktif:
             *
             * - mahasiswa belum mengirim revisi
             * - mahasiswa sudah mengirim dan menunggu review
             */
            setText(

                elements.sidebarRevision,

                stats.revision +
                stats.revisionSubmitted

            );


            setText(

                elements.quickPending,

                stats.pending +
                " pengajuan menunggu verifikasi"

            );


            if (
                elements.quickRevision
            ) {

                if (
                    stats.revisionSubmitted >
                    0
                ) {

                    elements.quickRevision
                        .textContent =

                        stats.revision +
                        " perlu revisi • " +

                        stats.revisionSubmitted +
                        " menunggu review";

                } else {

                    elements.quickRevision
                        .textContent =

                        stats.revision +
                        " mahasiswa perlu revisi";

                }

            }


            setText(

                elements.quickVerified,

                stats.verified +
                " pengajuan siap diproses"

            );


            setText(

                elements.quickProcess,

                stats.process +
                " pengajuan dalam proses SK"

            );

        }


        /* =====================================================
           ACTION
        ===================================================== */

        function getAction(
            submission
        ) {

            switch (
                submission.status
            ) {

                case STATUS.MENUNGGU_VERIFIKASI:

                    return {

                        label:
                            "Verifikasi",

                        href:
                            "verifikasi.html?id=" +
                            submission.id

                    };


                case STATUS.PERLU_REVISI:

                    return {

                        label:
                            "Lihat Detail",

                        href:
                            "detail_pengajuan.html?id=" +
                            submission.id

                    };


                case STATUS.REVISI_DIKIRIM:

                    return {

                        label:
                            "Review Revisi",

                        href:
                            "review_revisi.html?id=" +
                            submission.id

                    };


                case STATUS.TERVERIFIKASI:

                    return {

                        label:
                            "Proses SK",

                        href:
                            "proses_sk.html?id=" +
                            submission.id

                    };


                case STATUS.PEMBUATAN_SK:

                case STATUS.TTD_WAKIL_DEKAN:

                case STATUS.TTD_DEKAN:

                    return {

                        label:
                            "Lihat Proses",

                        href:
                            "proses_sk.html?id=" +
                            submission.id

                    };


                case STATUS.SK_SIAP_DIAMBIL:

                    return {

                        label:
                            "Lihat Status",

                        href:
                            "proses_sk.html?id=" +
                            submission.id

                    };


                default:

                    return {

                        label:
                            "Lihat Detail",

                        href:
                            "detail_pengajuan.html?id=" +
                            submission.id

                    };

            }

        }


        /* =====================================================
           TABLE
        ===================================================== */

        function renderTable(
            submissions
        ) {

            if (
                !elements.tableBody
            ) {

                return;

            }


            elements.tableBody.innerHTML =
                "";


            if (
                submissions.length ===
                0
            ) {

                if (
                    elements.empty
                ) {

                    elements.empty.style.display =
                        "block";


                    const title =
                        elements.empty.querySelector(
                            "strong"
                        );


                    if (
                        title
                    ) {

                        title.textContent =
                            "Belum ada pengajuan";

                    }

                }


                return;

            }


            if (
                elements.empty
            ) {

                elements.empty.style.display =
                    "none";

            }


            const latest =
                submissions
                    .slice()
                    .sort(
                        function (
                            a,
                            b
                        ) {

                            return (
                                Number(
                                    b.id
                                ) -
                                Number(
                                    a.id
                                )
                            );

                        }
                    )
                    .slice(
                        0,
                        5
                    );


            latest.forEach(
                function (submission) {

                    const meta =
                        API.getStatusMeta(
                            submission.status
                        );


                    const action =
                        getAction(
                            submission
                        );


                    const row =
                        document.createElement(
                            "tr"
                        );


                    row.innerHTML =
                        `
                        <td>

                            <strong>
                                ${escapeHtml(
                                    submission.name
                                )}
                            </strong>

                        </td>


                        <td>
                            ${escapeHtml(
                                submission.nim
                            )}
                        </td>


                        <td>
                            ${escapeHtml(
                                submission.department
                            )}
                        </td>


                        <td>

                            <span
                                class="admin-status-badge ${escapeHtml(
                                    meta.className
                                )}"
                            >
                                ${escapeHtml(
                                    meta.label
                                )}
                            </span>

                        </td>


                        <td>

                            <a
                                href="${escapeHtml(
                                    action.href
                                )}"
                                class="admin-table-link"
                            >
                                ${escapeHtml(
                                    action.label
                                )}
                            </a>

                        </td>
                        `;


                    elements.tableBody
                        .appendChild(
                            row
                        );

                }
            );

        }


        /* =====================================================
           DASHBOARD LOAD
        ===================================================== */

        async function loadDashboard() {

            renderDate();


            try {

                const submissions =
                    await API
                        .getSubmissions();


                renderStatistics(
                    submissions
                );


                renderTable(
                    submissions
                );

            } catch (error) {

                console.error(
                    "Gagal memuat Dashboard:",
                    error
                );


                renderStatistics(
                    []
                );


                renderTable(
                    []
                );

            }

        }


        /* =====================================================
           EXPORT EXCEL
        ===================================================== */

        if (
            elements.export
        ) {

            elements.export
                .addEventListener(
                    "click",
                    async function () {

                        /*
                         * Saat Laravel belum tersambung,
                         * gunakan modal informasi yang
                         * sudah ada pada desain.
                         */
                        if (
                            !API.config
                                .backendConnected
                        ) {

                            elements.exportModal
                                ?.classList
                                .add(
                                    "active"
                                );


                            return;

                        }


                        try {

                            elements.export.disabled =
                                true;


                            const blob =
                                await API
                                    .exportExcel();


                            const url =
                                URL.createObjectURL(
                                    blob
                                );


                            const link =
                                document.createElement(
                                    "a"
                                );


                            link.href =
                                url;


                            link.download =
                                "data_yudisium_feb_upr.xlsx";


                            document.body
                                .appendChild(
                                    link
                                );


                            link.click();


                            link.remove();


                            URL.revokeObjectURL(
                                url
                            );

                        } catch (error) {

                            console.error(
                                "Export Excel gagal:",
                                error
                            );


                            elements.exportModal
                                ?.classList
                                .add(
                                    "active"
                                );

                        } finally {

                            elements.export.disabled =
                                false;

                        }

                    }
                );

        }


        if (
            elements.closeExportModal
        ) {

            elements.closeExportModal
                .addEventListener(
                    "click",
                    function () {

                        elements.exportModal
                            ?.classList
                            .remove(
                                "active"
                            );

                    }
                );

        }


        loadDashboard();

    }
);