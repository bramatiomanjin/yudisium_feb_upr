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


        /* =====================================================
           ELEMENT
        ===================================================== */

        const tableBody =
            document.getElementById(
                "centralSubmissionTableBody"
            );


        const emptyState =
            document.getElementById(
                "submissionEmptyState"
            );


        const searchInput =
            document.getElementById(
                "submissionSearch"
            );


        const departmentFilter =
            document.getElementById(
                "departmentFilter"
            );


        const statusFilter =
            document.getElementById(
                "statusFilter"
            );


        const resetButton =
            document.getElementById(
                "resetSubmissionFilter"
            );


        const resultCount =
            document.getElementById(
                "submissionResultCount"
            );


        const statTotal =
            document.getElementById(
                "submissionStatTotal"
            );


        const statPending =
            document.getElementById(
                "submissionStatPending"
            );


        const statRevision =
            document.getElementById(
                "submissionStatRevision"
            );


        const statVerified =
            document.getElementById(
                "submissionStatVerified"
            );


        const sidebarRevisionCount =
            document.getElementById(
                "revisionSidebarCount"
            );


        /*
         * Kompatibel jika nama ID filter dari HTML lama berbeda.
         */
        const fallbackSearch =
            searchInput ||
            document.querySelector(
                'input[type="search"]'
            );


        const fallbackDepartment =
            departmentFilter ||
            document.querySelector(
                '[data-filter="department"]'
            );


        const fallbackStatus =
            statusFilter ||
            document.querySelector(
                '[data-filter="status"]'
            );


        let submissions =
            [];


        /* =====================================================
           HELPERS
        ===================================================== */

        function escapeHtml(value) {

            return String(
                value ??
                ""
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


        function normalizeText(value) {

            return String(
                value ||
                ""
            )
                .trim()
                .toLowerCase();

        }


        /* =====================================================
           ACTION
        ===================================================== */

        function getAction(
            submission
        ) {

            const STATUS =
                window.YudisiumAPI
                    .STATUS;


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
                            "Detail",

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
                            "Detail",

                        href:
                            "detail_pengajuan.html?id=" +
                            submission.id

                    };

            }

        }


        /* =====================================================
           STATISTICS
        ===================================================== */

        function renderStatistics() {

            const STATUS =
                window.YudisiumAPI
                    .STATUS;


            const pending =
                submissions.filter(
                    function (item) {

                        return (
                            item.status ===
                            STATUS.MENUNGGU_VERIFIKASI
                        );

                    }
                ).length;


            const revision =
                submissions.filter(
                    function (item) {

                        return (
                            item.status ===
                                STATUS.PERLU_REVISI ||
                            item.status ===
                                STATUS.REVISI_DIKIRIM
                        );

                    }
                ).length;


            const verified =
                submissions.filter(
                    function (item) {

                        return (
                            item.status ===
                            STATUS.TERVERIFIKASI
                        );

                    }
                ).length;


            if (
                statTotal
            ) {

                statTotal.textContent =
                    submissions.length;

            }


            if (
                statPending
            ) {

                statPending.textContent =
                    pending;

            }


            if (
                statRevision
            ) {

                statRevision.textContent =
                    revision;

            }


            if (
                statVerified
            ) {

                statVerified.textContent =
                    verified;

            }


            if (
                sidebarRevisionCount
            ) {

                sidebarRevisionCount.textContent =
                    revision;

            }

        }


        /* =====================================================
           QUERY FILTER
        ===================================================== */

        function applyQueryFilter() {

            const params =
                new URLSearchParams(
                    window.location.search
                );


            const filter =
                params.get(
                    "filter"
                );


            if (
                !filter ||
                !fallbackStatus
            ) {

                return;

            }


            const STATUS =
                window.YudisiumAPI
                    .STATUS;


            const map = {

                menunggu:
                    STATUS.MENUNGGU_VERIFIKASI,

                revisi:
                    "revision-group",

                terverifikasi:
                    STATUS.TERVERIFIKASI,

                "proses-sk":
                    "process-group"

            };


            const requested =
                map[
                    filter
                ];


            if (
                !requested
            ) {

                return;

            }


            /*
             * Bila dropdown tidak punya option group khusus,
             * simpan filter lewat dataset.
             */
            fallbackStatus.dataset
                .externalFilter =
                requested;

        }


        /* =====================================================
           FILTER DATA
        ===================================================== */

        function getFilteredData() {

            let result =
                submissions.slice();


            const search =
                normalizeText(
                    fallbackSearch
                        ?.value
                );


            const department =
                normalizeText(
                    fallbackDepartment
                        ?.value
                );


            const selectedStatus =
                normalizeText(
                    fallbackStatus
                        ?.value
                );


            const externalStatus =
                fallbackStatus
                    ?.dataset
                    .externalFilter ||
                "";


            if (
                search
            ) {

                result =
                    result.filter(
                        function (item) {

                            return [

                                item.name,
                                item.nim,
                                item.code,
                                item.department

                            ]
                                .some(
                                    function (value) {

                                        return normalizeText(
                                            value
                                        )
                                            .includes(
                                                search
                                            );

                                    }
                                );

                        }
                    );

            }


            if (
                department &&
                department !==
                    "semua" &&
                department !==
                    "all"
            ) {

                result =
                    result.filter(
                        function (item) {

                            return (
                                normalizeText(
                                    item.department
                                ) ===
                                department
                            );

                        }
                    );

            }


            const STATUS =
                window.YudisiumAPI
                    .STATUS;


            let statusTarget =
                externalStatus ||
                selectedStatus;


            if (
                statusTarget &&
                statusTarget !==
                    "semua" &&
                statusTarget !==
                    "all"
            ) {

                result =
                    result.filter(
                        function (item) {

                            if (
                                statusTarget ===
                                "revision-group"
                            ) {

                                return [

                                    STATUS.PERLU_REVISI,
                                    STATUS.REVISI_DIKIRIM

                                ].includes(
                                    item.status
                                );

                            }


                            if (
                                statusTarget ===
                                "process-group"
                            ) {

                                return [

                                    STATUS.TERVERIFIKASI,
                                    STATUS.PEMBUATAN_SK,
                                    STATUS.TTD_WAKIL_DEKAN,
                                    STATUS.TTD_DEKAN,
                                    STATUS.SK_SIAP_DIAMBIL

                                ].includes(
                                    item.status
                                );

                            }


                            const normalizedTarget =
                                window.YudisiumAPI
                                    .normalizeStatus(
                                        statusTarget
                                    );


                            return (
                                item.status ===
                                normalizedTarget
                            );

                        }
                    );

            }


            return result;

        }


        /* =====================================================
           RENDER
        ===================================================== */

        function renderTable() {

            if (
                !tableBody
            ) {

                return;

            }


            const data =
                getFilteredData();


            tableBody.innerHTML =
                "";


            if (
                resultCount
            ) {

                resultCount.textContent =
                    data.length +
                    " pengajuan";

            }


            if (
                data.length ===
                0
            ) {

                if (
                    emptyState
                ) {

                    emptyState.style.display =
                        "block";

                }


                return;

            }


            if (
                emptyState
            ) {

                emptyState.style.display =
                    "none";

            }


            data.forEach(
                function (submission) {

                    const meta =
                        window.YudisiumAPI
                            .getStatusMeta(
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


                    row.dataset.name =
                        normalizeText(
                            submission.name
                        );


                    row.dataset.nim =
                        submission.nim;


                    row.dataset.code =
                        submission.code;


                    row.dataset.department =
                        submission.department;


                    row.dataset.status =
                        submission.status;


                    row.innerHTML =
                        `
                        <td>
                            <div class="admin-student-cell">

                                <div class="admin-student-avatar">
                                    ${escapeHtml(
                                        window.YudisiumAPI
                                            .getInitials(
                                                submission.name
                                            )
                                    )}
                                </div>

                                <div>

                                    <strong>
                                        ${escapeHtml(
                                            submission.name
                                        )}
                                    </strong>

                                    <span>
                                        ${escapeHtml(
                                            submission.code
                                        )}
                                    </span>

                                </div>

                            </div>
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
                            ${escapeHtml(
                                submission.submittedAt ||
                                "-"
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


                    tableBody.appendChild(
                        row
                    );

                }
            );

        }


        /* =====================================================
           EVENTS
        ===================================================== */

        [
            fallbackSearch,
            fallbackDepartment,
            fallbackStatus
        ]
            .filter(
                Boolean
            )
            .forEach(
                function (element) {

                    element.addEventListener(
                        element.tagName ===
                            "INPUT"
                            ?
                            "input"
                            :
                            "change",
                        function () {

                            if (
                                element ===
                                fallbackStatus
                            ) {

                                delete fallbackStatus
                                    .dataset
                                    .externalFilter;

                            }


                            renderTable();

                        }
                    );

                }
            );


        if (
            resetButton
        ) {

            resetButton.addEventListener(
                "click",
                function () {

                    if (
                        fallbackSearch
                    ) {

                        fallbackSearch.value =
                            "";

                    }


                    if (
                        fallbackDepartment
                    ) {

                        fallbackDepartment.value =
                            "";

                    }


                    if (
                        fallbackStatus
                    ) {

                        fallbackStatus.value =
                            "";


                        delete fallbackStatus
                            .dataset
                            .externalFilter;

                    }


                    renderTable();

                }
            );

        }


        /* =====================================================
           LOAD
        ===================================================== */

        async function loadSubmissions() {

            try {

                submissions =
                    await window.YudisiumAPI
                        .getSubmissions();


                renderStatistics();


                applyQueryFilter();


                renderTable();

            } catch (error) {

                console.error(
                    "Gagal memuat pengajuan:",
                    error
                );


                submissions =
                    [];


                renderStatistics();


                renderTable();

            }

        }


        loadSubmissions();

    }
);