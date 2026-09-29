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

            revisionSubmitted:
                document.getElementById(
                    "dashboardRevisionSubmittedCount"
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

            quickRevisionSubmitted:
                document.getElementById(
                    "dashboardQuickRevisionSubmittedText"
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

            dataState:
                document.getElementById(
                    "dashboardDataState"
                ),

            retry:
                document.getElementById(
                    "dashboardRetryButton"
                ),

            tableWrapper:
                document.getElementById(
                    "dashboardSubmissionTableWrapper"
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


        function showDashboardState(state) {
            if (elements.tableWrapper) {
                elements.tableWrapper.style.display = state === "ready" ? "block" : "none";
            }

            if (!elements.dataState) {
                return;
            }

            if (state === "ready") {
                elements.dataState.style.display = "none";
                return;
            }

            const content = {
                loading: ["Memuat pengajuan...", "Mohon tunggu sebentar."],
                empty: ["Belum ada pengajuan", "Pengajuan mahasiswa akan tampil di sini setelah dikirim."],
                error: ["Pengajuan gagal dimuat", "Periksa koneksi, lalu coba muat kembali data dashboard."]
            }[state];

            elements.dataState.dataset.state = state;
            elements.dataState.style.display = "flex";
            elements.dataState.querySelector("strong").textContent = content[0];
            elements.dataState.querySelector("p").textContent = content[1];

            if (elements.retry) {
                elements.retry.style.display = state === "error" ? "inline-flex" : "none";
            }
        }


        function setStatisticsUnavailable() {
            [
                elements.total,
                elements.pending,
                elements.revision,
                elements.revisionSubmitted,
                elements.verified,
                elements.process,
                elements.ready,
                elements.sidebarRevision
            ].forEach(function (element) {
                setText(element, "—");
            });

            [
                elements.quickPending,
                elements.quickRevision,
                elements.quickRevisionSubmitted,
                elements.quickVerified,
                elements.quickProcess
            ].forEach(function (element) {
                setText(element, "Data belum tersedia");
            });
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


        function getAdminStatusLabel(status, fallback) {
            if (status === STATUS.PERLU_REVISI) {
                return "Menunggu Perbaikan Mahasiswa";
            }

            if (status === STATUS.REVISI_DIKIRIM) {
                return "Revisi Siap Direview";
            }

            return fallback;
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
                                "numeric",
                                
                            timeZone:
                                "Asia/Jakarta"

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
                                STATUS.PARAF_PIMPINAN,
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
                elements.revisionSubmitted,
                stats.revisionSubmitted
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


            setText(

                elements.sidebarRevision,

                stats.revisionSubmitted

            );


            setText(

                elements.quickPending,

                stats.pending +
                " pengajuan menunggu verifikasi"

            );


            setText(
                elements.quickRevision,
                stats.revision + " mahasiswa sedang memperbaiki"
            );


            setText(
                elements.quickRevisionSubmitted,
                stats.revisionSubmitted + " revisi menunggu review Admin"
            );


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
                        label: "Verifikasi",
                        href: "/admin/verifikasi?id=" + submission.id
                    };


                case STATUS.PERLU_REVISI:

                    return {
                        label: "Lihat Detail",
                        href: "/admin/pengajuan/" + submission.id
                    };


                case STATUS.REVISI_DIKIRIM:

                    return {
                        label: "Review Revisi",
                        href: "/admin/review-revisi?id=" + submission.id
                    };


                case STATUS.TERVERIFIKASI:

                    return {
                        label: "Proses SK",
                        href: "/admin/proses-sk?id=" + submission.id
                    };


                case STATUS.PEMBUATAN_SK:
                case STATUS.PARAF_PIMPINAN:
                case STATUS.TTD_DEKAN:

                    return {
                        label: "Lihat Proses",
                        href: "/admin/proses-sk?id=" + submission.id
                    };


                case STATUS.SK_SIAP_DIAMBIL:

                    return {
                        label: "Lihat Status",
                        href: "/admin/proses-sk?id=" + submission.id
                    };


                default:

                    return {
                        label: "Lihat Detail",
                        href: "/admin/pengajuan/" + submission.id
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
                showDashboardState("empty");
                return;

            }


            showDashboardState("ready");


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
                                    getAdminStatusLabel(submission.status, meta.label)
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

            showDashboardState("loading");
            setStatisticsUnavailable();


            try {

                const submissions =
                    await API
                        .getSubmissions();


                renderStatistics(
                    submissions
                );

                const filter = new URLSearchParams(window.location.search).get('filter');
                let filtered = submissions;
                if (filter === 'pengajuan') {
                    // show all - no filter needed
                } else if (filter === 'menunggu') {
                    filtered = submissions.filter(function(s) { return s.status === STATUS.MENUNGGU_VERIFIKASI; });
                } else if (filter === 'menunggu-revisi') {
                    filtered = submissions.filter(function(s) { return s.status === STATUS.PERLU_REVISI; });
                } else if (filter === 'review-revisi' || filter === 'revisi') {
                    filtered = submissions.filter(function(s) { return s.status === STATUS.REVISI_DIKIRIM; });
                } else if (filter === 'terverifikasi') {
                    filtered = submissions.filter(function(s) { return s.status === STATUS.TERVERIFIKASI; });
                } else if (filter === 'proses-sk') {
                    filtered = submissions.filter(function(s) { return [STATUS.TERVERIFIKASI, STATUS.PEMBUATAN_SK, STATUS.PARAF_PIMPINAN, STATUS.TTD_DEKAN].includes(s.status); });
                }

                renderTable(
                    filtered
                );

            } catch (error) {

                console.error(
                    "Gagal memuat Dashboard:",
                    error
                );


                if (elements.tableBody) {
                    elements.tableBody.innerHTML = "";
                }
                setStatisticsUnavailable();
                showDashboardState("error");

            }

        }


        elements.retry?.addEventListener("click", loadDashboard);


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

        // Highlight active sidebar link based on filter
        (function() {
            var filter = new URLSearchParams(window.location.search).get('filter');
            if (!filter) return;
            var navItems = document.querySelectorAll('.admin-nav-item');
            navItems.forEach(function(item) {
                item.classList.remove('active');
                var href = item.getAttribute('href') || '';
                if (href.includes('filter=' + filter)) {
                    item.classList.add('active');
                }
            });
        })();

    }
);
