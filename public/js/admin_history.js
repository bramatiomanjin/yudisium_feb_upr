document.addEventListener(
    "DOMContentLoaded",
    async function () {

        "use strict";


        if (!window.YudisiumAPI) {

            console.error(
                "YudisiumAPI tidak ditemukan."
            );

            return;
        }


        const API =
            window.YudisiumAPI;


        /* =====================================================
           CURRENT ADMIN
           =====================================================
           Jangan lagi menggunakan sessionStorage.

           Role dibaca dari Blade karena halaman sudah
           menampilkan Auth::user()->role.

           Authorization data tetap ditangani backend.
        ===================================================== */

        const sidebarRoleElement =
            document.getElementById(
                "sidebarAdminRole"
            );


        const topbarRoleElement =
            document.getElementById(
                "topbarAdminRole"
            );


        const currentRole =
            String(
                sidebarRoleElement
                    ?.textContent
                ||
                topbarRoleElement
                    ?.textContent
                ||
                "ADMIN"
            )
                .trim()
                .toUpperCase();


        const isSuperAdmin =
            currentRole ===
            "SUPER_ADMIN";


        /* =====================================================
           ELEMENTS
        ===================================================== */

        const pageTitle =
            document.getElementById(
                "historyPageTitle"
            );


        const pageDescription =
            document.getElementById(
                "historyPageDescription"
            );


        const roleTitle =
            document.getElementById(
                "historyRoleTitle"
            );


        const roleDescription =
            document.getElementById(
                "historyRoleDescription"
            );


        const adminFilterGroup =
            document.getElementById(
                "historyAdminFilterGroup"
            );


        const adminFilter =
            document.getElementById(
                "historyAdminFilter"
            );


        const adminColumnHeader =
            document.getElementById(
                "historyAdminColumn"
            );


        const searchInput =
            document.getElementById(
                "historySearch"
            );


        const actionFilter =
            document.getElementById(
                "historyActionFilter"
            );

        const monthFilter =
            document.getElementById(
                "historyMonth"
            );

        const yearFilter =
            document.getElementById(
                "historyYear"
            );


        const applyFilterButton =
            document.getElementById(
                "applyHistoryFilter"
            );


        const resetFilterButton =
            document.getElementById(
                "resetHistoryFilter"
            );


        const activeFilterBox =
            document.getElementById(
                "historyActiveFilter"
            );


        const activeFilterText =
            document.getElementById(
                "historyActiveFilterText"
            );


        const tableBody =
            document.getElementById(
                "historyTableBody"
            );


        const emptyState =
            document.getElementById(
                "historyEmpty"
            );


        const resultText =
            document.getElementById(
                "historyResultText"
            );


        const totalCount =
            document.getElementById(
                "historyTotalCount"
            );


        const verificationCount =
            document.getElementById(
                "historyVerificationCount"
            );


        const revisionCount =
            document.getElementById(
                "historyRevisionCount"
            );


        const skCount =
            document.getElementById(
                "historySkCount"
            );


        const sidebarRevisionCount =
            document.getElementById(
                "historySidebarRevisionCount"
            );


        let allActivities =
            [];

        let historyCurrentPage = 1;
        const historyItemsPerPage = 10;
        let historyFilteredActivities = [];


        let appliedSearch =
            "";


        let appliedAdmin =
            "";


        let appliedAction =
            "";


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


        function getActionLabel(
            activity
        ) {

            if (
                activity.actionLabel
            ) {

                return activity
                    .actionLabel;
            }


            const labels = {

                VERIFICATION:
                    "Verifikasi Pengajuan",

                REVISION:
                    "Revisi Pengajuan",

                UPDATE_SK_STATUS:
                    "Proses SK"
            };


            return (
                labels[
                    activity.action
                ]
                ||
                "Aktivitas"
            );
        }


        function getActionCategory(
            action
        ) {

            if (
                action ===
                "VERIFICATION"
            ) {

                return "verification";
            }


            if (
                action ===
                "REVISION"
            ) {

                return "revision";
            }


            return "process";
        }


        function getStatusLabel(
            status
        ) {

            if (!status) {

                return "-";
            }


            if (
                typeof API
                    .getStatusLabel ===
                "function"
            ) {

                return API
                    .getStatusLabel(
                        status
                    );
            }


            return String(
                status
            )
                .replace(
                    /_/g,
                    " "
                )
                .replace(
                    /\b\w/g,
                    function (letter) {

                        return letter
                            .toUpperCase();
                    }
                );
        }


        function formatDate(
            value
        ) {

            if (!value) {

                return "-";
            }


            const date =
                new Date(
                    value
                );


            if (
                Number.isNaN(
                    date.getTime()
                )
            ) {

                return value;
            }


            return date
                .toLocaleString(
                    "id-ID",
                    {
                        timeZone:
                            "Asia/Jakarta",

                        day:
                            "2-digit",

                        month:
                            "short",

                        year:
                            "numeric",

                        hour:
                            "2-digit",

                        minute:
                            "2-digit"
                    }
                );
        }


        /* =====================================================
           ROLE UI
        ===================================================== */

        function setupRoleUI() {

            if (
                isSuperAdmin
            ) {

                if (pageTitle) {

                    pageTitle
                        .textContent =
                        "History Seluruh Admin";
                }


                if (
                    pageDescription
                ) {

                    pageDescription
                        .textContent =
                        "Lihat aktivitas administrasi seluruh akun Admin FEB UPR.";
                }


                if (roleTitle) {

                    roleTitle
                        .textContent =
                        "Akses Super Admin";
                }


                if (
                    roleDescription
                ) {

                    roleDescription
                        .textContent =
                        "Super Admin dapat melihat aktivitas seluruh admin pada sistem.";
                }


                if (
                    adminFilterGroup
                ) {

                    adminFilterGroup
                        .style
                        .display =
                        "flex";
                }


                if (
                    adminColumnHeader
                ) {

                    adminColumnHeader
                        .style
                        .display =
                        "";
                }

            } else {

                if (pageTitle) {

                    pageTitle
                        .textContent =
                        "History Aktivitas Saya";
                }


                if (
                    pageDescription
                ) {

                    pageDescription
                        .textContent =
                        "Riwayat aktivitas yang dilakukan menggunakan akun Anda.";
                }


                if (roleTitle) {

                    roleTitle
                        .textContent =
                        "History akun Anda";
                }


                if (
                    roleDescription
                ) {

                    roleDescription
                        .textContent =
                        "Admin hanya dapat melihat aktivitas yang dilakukan menggunakan akun sendiri.";
                }


                if (
                    adminFilterGroup
                ) {

                    adminFilterGroup
                        .style
                        .display =
                        "none";
                }


                if (
                    adminColumnHeader
                ) {

                    adminColumnHeader
                        .style
                        .display =
                        "none";
                }
            }
        }


        /* =====================================================
           ROLE ACCESS
           =====================================================
           Backend sudah memfilter berdasarkan Auth::user().
           Frontend TIDAK memfilter ulang berdasarkan username.
        ===================================================== */

        function getRoleActivities() {

            return [
                ...allActivities
            ];
        }


        /* =====================================================
           ADMIN FILTER
        ===================================================== */

        function populateAdminFilter() {

            if (
                !isSuperAdmin ||
                !adminFilter
            ) {

                return;
            }


            adminFilter
                .innerHTML =
                `
                    <option value="">
                        Semua Admin
                    </option>
                `;


            const admins =
                new Map();


            allActivities
                .forEach(
                    function (
                        activity
                    ) {

                        const key =
                            activity
                                .adminUsername
                            ||
                            activity
                                .adminName
                            ||
                            "";


                        if (!key) {

                            return;
                        }


                        const label =
                            activity
                                .adminName
                            ||
                            activity
                                .adminUsername
                            ||
                            key;


                        admins.set(
                            key,
                            label
                        );
                    }
                );


            [
                ...admins
                    .entries()
            ]
                .sort(
                    function (
                        a,
                        b
                    ) {

                        return String(
                            a[1]
                        )
                            .localeCompare(
                                String(
                                    b[1]
                                ),
                                "id"
                            );
                    }
                )
                .forEach(
                    function (
                        [
                            value,
                            label
                        ]
                    ) {

                        const option =
                            document
                                .createElement(
                                    "option"
                                );


                        option.value =
                            value;


                        option.textContent =
                            label;


                        adminFilter
                            .appendChild(
                                option
                            );
                    }
                );
        }


        /* =====================================================
           FILTER
        ===================================================== */

        function getFilteredActivities() {

            const roleActivities =
                getRoleActivities();


            const search =
                appliedSearch
                    .trim()
                    .toLowerCase();


            return roleActivities
                .filter(
                    function (
                        activity
                    ) {

                        const searchable = [

                            activity
                                .studentName,

                            activity
                                .nim,

                            activity
                                .submissionCode,

                            activity
                                .adminUsername,

                            activity
                                .adminName,

                            activity
                                .department

                        ]
                            .map(
                                function (
                                    value
                                ) {

                                    return String(
                                        value ||
                                        ""
                                    )
                                        .toLowerCase();
                                }
                            );


                        const matchSearch =
                            !search
                            ||
                            searchable
                                .some(
                                    function (
                                        value
                                    ) {

                                        return value
                                            .includes(
                                                search
                                            );
                                    }
                                );


                        const matchAdmin =
                            !isSuperAdmin
                            ||
                            !appliedAdmin
                            ||
                            activity
                                .adminUsername ===
                                appliedAdmin
                            ||
                            activity
                                .adminName ===
                                appliedAdmin;


                        const matchAction =
                            !appliedAction
                            ||
                            activity
                                .action ===
                                appliedAction;

                        let matchMonth = true;
                        if (monthFilter && monthFilter.value) {
                            const selectedMonth = parseInt(monthFilter.value, 10);
                            const date = new Date(activity.createdAt);
                            matchMonth = (date.getMonth() + 1) === selectedMonth;
                        }

                        let matchYear = true;
                        if (yearFilter && yearFilter.value) {
                            const selectedYear = parseInt(yearFilter.value, 10);
                            const date = new Date(activity.createdAt);
                            matchYear = date.getFullYear() === selectedYear;
                        }

                        return (
                            matchSearch
                            &&
                            matchAdmin
                            &&
                            matchAction
                            &&
                            matchMonth
                            &&
                            matchYear
                        );
                    }
                );
        }


        /* =====================================================
           FILTER LABEL
        ===================================================== */

        function updateActiveFilter() {

            if (
                !activeFilterText ||
                !activeFilterBox
            ) {

                return;
            }


            const parts =
                [];


            if (
                appliedSearch
            ) {

                parts.push(
                    'Pencarian "' +
                    appliedSearch +
                    '"'
                );
            }


            if (
                isSuperAdmin &&
                appliedAdmin
            ) {

                const selected =
                    adminFilter
                        ?.selectedOptions[
                            0
                        ];


                parts.push(
                    "Admin " +
                    (
                        selected
                            ?.textContent
                            ?.trim()
                        ||
                        appliedAdmin
                    )
                );
            }


            if (
                appliedAction
            ) {

                const labels = {

                    VERIFICATION:
                        "Verifikasi",

                    REVISION:
                        "Revisi",

                    UPDATE_SK_STATUS:
                        "Proses SK"
                };


                parts.push(
                    "Aktivitas " +
                    (
                        labels[
                            appliedAction
                        ]
                        ||
                        appliedAction
                    )
                );
            }


            activeFilterText
                .textContent =
                parts.length
                    ? parts.join(
                        " • "
                    )
                    : "Semua aktivitas";


            activeFilterBox
                .classList
                .add(
                    "active"
                );
        }


        /* =====================================================
           SUMMARY
        ===================================================== */

        function updateSummary(
            activities
        ) {

            const verification =
                activities
                    .filter(
                        function (
                            item
                        ) {

                            return (
                                item.action ===
                                "VERIFICATION"
                            );
                        }
                    )
                    .length;


            const revision =
                activities
                    .filter(
                        function (
                            item
                        ) {

                            return (
                                item.action ===
                                "REVISION"
                            );
                        }
                    )
                    .length;


            const process =
                activities
                    .filter(
                        function (
                            item
                        ) {

                            return (
                                item.action ===
                                "UPDATE_SK_STATUS"
                            );
                        }
                    )
                    .length;


            if (totalCount) {

                totalCount
                    .textContent =
                    activities.length;
            }


            if (
                verificationCount
            ) {

                verificationCount
                    .textContent =
                    verification;
            }


            if (
                revisionCount
            ) {

                revisionCount
                    .textContent =
                    revision;
            }


            if (skCount) {

                skCount
                    .textContent =
                    process;
            }
        }


        /* =====================================================
           SIDEBAR REVISION COUNT
        ===================================================== */

        async function updateSidebarCount() {

            if (
                !sidebarRevisionCount
            ) {

                return;
            }


            try {

                const submissions =
                    await API
                        .getSubmissions();


                const count =
                    submissions
                        .filter(
                            function (
                                submission
                            ) {

                                return [

                                    API.STATUS
                                        .PERLU_REVISI,

                                    API.STATUS
                                        .REVISI_DIKIRIM

                                ]
                                    .includes(
                                        submission
                                            .status
                                    );
                            }
                        )
                        .length;


                sidebarRevisionCount
                    .textContent =
                    count;

            } catch (
                error
            ) {

                console.error(
                    "Gagal menghitung revisi:",
                    error
                );


                sidebarRevisionCount
                    .textContent =
                    "0";
            }
        }


        /* =====================================================
           RENDER HISTORY
        ===================================================== */

        function renderHistory() {

            if (!tableBody) {

                return;
            }


            const roleActivities =
                getRoleActivities();


            historyFilteredActivities = getFilteredActivities();
            const totalItems = historyFilteredActivities.length;
            const totalPages = Math.ceil(totalItems / historyItemsPerPage);

            if (historyCurrentPage < 1) historyCurrentPage = 1;
            if (historyCurrentPage > totalPages && totalPages > 0) historyCurrentPage = totalPages;

            const startIndex = (historyCurrentPage - 1) * historyItemsPerPage;
            const endIndex = startIndex + historyItemsPerPage;
            const paginatedData = historyFilteredActivities.slice(startIndex, endIndex);

            tableBody
                .innerHTML =
                "";


            if (resultText) {

                resultText
                    .textContent =
                    "Menampilkan " +
                    paginatedData.length +
                    " dari " +
                    roleActivities.length +
                    " aktivitas";
            }


            updateSummary(
                historyFilteredActivities
            );


            updateActiveFilter();


            if (
                totalItems ===
                0
            ) {

                if (emptyState) {

                    emptyState
                        .style
                        .display =
                        "flex";
                }

                updateHistoryPagination(0);
                return;
            }


            if (emptyState) {

                emptyState
                    .style
                    .display =
                    "none";
            }

            paginatedData
                .forEach(
                    function (
                        activity
                    ) {

                        const row =
                            document
                                .createElement(
                                    "tr"
                                );


                        const category =
                            getActionCategory(
                                activity
                                    .action
                            );


                        const adminCell =
                            isSuperAdmin
                                ? `
                                    <td class="history-admin-cell">

                                        <strong>
                                            ${escapeHtml(
                                                activity.adminName
                                                ||
                                                "-"
                                            )}
                                        </strong>

                                        <span>
                                            ${escapeHtml(
                                                activity.adminRole
                                                ||
                                                "ADMIN"
                                            )}
                                        </span>

                                        <small>
                                            ${escapeHtml(
                                                activity.adminUsername
                                                ||
                                                ""
                                            )}
                                        </small>

                                    </td>
                                `
                                : "";


                        const detailUrl =
                            activity
                                .submissionId
                                ? (
                                    "/admin/pengajuan/" +
                                    encodeURIComponent(
                                        activity
                                            .submissionId
                                    ) +
                                    "?from=history"
                                )
                                : "#";


                        row.innerHTML =
                            `
                                <td>

                                    <div class="history-time">

                                        <strong>
                                            ${escapeHtml(
                                                formatDate(
                                                    activity
                                                        .createdAt
                                                )
                                            )}
                                        </strong>

                                    </div>

                                </td>


                                ${adminCell}


                                <td>

                                    <div class="history-student">

                                        <strong>
                                            ${escapeHtml(
                                                activity.studentName
                                                ||
                                                "-"
                                            )}
                                        </strong>

                                        <span>
                                            ${escapeHtml(
                                                activity.nim
                                                ||
                                                "-"
                                            )}
                                        </span>

                                        <small>
                                            ${escapeHtml(
                                                activity.submissionCode
                                                ||
                                                "-"
                                            )}
                                        </small>

                                    </div>

                                </td>


                                <td>

                                    <span
                                        class="history-action-badge ${escapeHtml(
                                            category
                                        )}"
                                    >
                                        ${escapeHtml(
                                            getActionLabel(
                                                activity
                                            )
                                        )}
                                    </span>

                                </td>


                                <td>

                                    <div class="history-change">

                                        <span>
                                            ${escapeHtml(
                                                getStatusLabel(
                                                    activity
                                                        .previousStatus
                                                )
                                            )}
                                        </span>

                                        <b>
                                            →
                                        </b>

                                        <strong>
                                            ${escapeHtml(
                                                getStatusLabel(
                                                    activity
                                                        .newStatus
                                                )
                                            )}
                                        </strong>

                                    </div>

                                </td>


                                <td>

                                    <a
                                        href="${escapeHtml(
                                            detailUrl
                                        )}"
                                        class="history-detail-button"
                                    >
                                        Lihat Pengajuan
                                    </a>

                                </td>
                            `;


                        tableBody
                            .appendChild(
                                row
                            );
                    }
                );

            updateHistoryPagination(totalPages);
        }


        /* =====================================================
           FILTER EVENTS
        ===================================================== */

        applyFilterButton
            ?.addEventListener(
                "click",
                function () {

                    appliedSearch =
                        searchInput
                            ?.value
                            .trim()
                        ||
                        "";


                    appliedAction =
                        actionFilter
                            ?.value
                        ||
                        "";


                    appliedAdmin =
                        isSuperAdmin
                            ? (
                                adminFilter
                                    ?.value
                                ||
                                ""
                            )
                            : "";

                    historyCurrentPage = 1;
                    renderHistory();
                }
            );


        resetFilterButton
            ?.addEventListener(
                "click",
                function () {

                    if (
                        searchInput
                    ) {

                        searchInput.value =
                            "";
                    }


                    if (
                        actionFilter
                    ) {

                        actionFilter.value =
                            "";
                    }


                    if (
                        adminFilter
                    ) {

                        adminFilter.value =
                            "";
                    }

                    if (monthFilter) monthFilter.value = "";
                    if (yearFilter) yearFilter.value = "";


                    appliedSearch =
                        "";


                    appliedAction =
                        "";


                    appliedAdmin =
                        "";

                    historyCurrentPage = 1;
                    renderHistory();
                }
            );


        searchInput
            ?.addEventListener(
                "keydown",
                function (
                    event
                ) {

                    if (
                        event.key ===
                        "Enter"
                    ) {

                        event
                            .preventDefault();


                        applyFilterButton
                            ?.click();
                    }
                }
            );


        /* =====================================================
           PAGINATION
        ===================================================== */

        function updateHistoryPagination(totalPages) {
            const container = document.getElementById("historyPaginationContainer");
            const info = document.getElementById("historyPaginationInfo");
            const controls = document.getElementById("historyPaginationControls");

            if (!container || !info || !controls) return;

            if (totalPages <= 1) {
                container.style.display = "none";
                return;
            }

            container.style.display = "flex";
            info.textContent = "Halaman " + historyCurrentPage + " dari " + totalPages;
            controls.innerHTML = "";

            const prevBtn = document.createElement("button");
            prevBtn.type = "button";
            prevBtn.textContent = "Sebelumnya";
            if (historyCurrentPage === 1) {
                prevBtn.disabled = true;
            } else {
                prevBtn.addEventListener("click", function() {
                    historyCurrentPage--;
                    renderHistory();
                });
            }
            controls.appendChild(prevBtn);

            let startPage = Math.max(1, historyCurrentPage - 2);
            let endPage = Math.min(totalPages, historyCurrentPage + 2);
            
            if (historyCurrentPage <= 3) endPage = Math.min(totalPages, 5);
            if (historyCurrentPage >= totalPages - 2) startPage = Math.max(1, totalPages - 4);

            for (let i = startPage; i <= endPage; i++) {
                const btn = document.createElement("button");
                btn.type = "button";
                btn.textContent = i;
                if (i === historyCurrentPage) {
                    btn.classList.add("active");
                } else {
                    btn.addEventListener("click", function() {
                        historyCurrentPage = i;
                        renderHistory();
                    });
                }
                controls.appendChild(btn);
            }

            const nextBtn = document.createElement("button");
            nextBtn.type = "button";
            nextBtn.textContent = "Berikutnya";
            if (historyCurrentPage === totalPages) {
                nextBtn.disabled = true;
            } else {
                nextBtn.addEventListener("click", function() {
                    historyCurrentPage++;
                    renderHistory();
                });
            }
            controls.appendChild(nextBtn);
        }

        /* =====================================================
           LOAD HISTORY
        ===================================================== */

        async function loadHistory() {

            setupRoleUI();


            try {

                /*
                 * Backend menentukan data yang boleh
                 * dilihat berdasarkan Auth::user().
                 *
                 * Tidak mengirim admin_username lagi.
                 */
                allActivities =
                    await API
                        .getHistory();


                if (
                    !Array.isArray(
                        allActivities
                    )
                ) {

                    allActivities =
                        [];
                }


                populateAdminFilter();


                renderHistory();


                await updateSidebarCount();

            } catch (
                error
            ) {

                console.error(
                    "Gagal memuat History:",
                    error
                );


                allActivities =
                    [];


                populateAdminFilter();


                renderHistory();


                if (
                    sidebarRevisionCount
                ) {

                    sidebarRevisionCount
                        .textContent =
                        "0";
                }
            }
        }


        await loadHistory();
    }
);