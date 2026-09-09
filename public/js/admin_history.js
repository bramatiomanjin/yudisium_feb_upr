document.addEventListener(
    "DOMContentLoaded",
    async function () {

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


        /* =====================================================
           SESSION ADMIN
        ===================================================== */

        const currentUsername =
            sessionStorage.getItem(
                "admin_username"
            ) || "";


        const currentRole =
            sessionStorage.getItem(
                "admin_role"
            ) || "ADMIN";


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


        function getActionLabel(activity) {

            if (
                activity.actionLabel
            ) {

                return activity.actionLabel;

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
                ] ||
                "Aktivitas"
            );

        }


        function getActionCategory(action) {

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


        function getStatusLabel(status) {

            if (!status) {

                return "-";

            }


            return API
                .getStatusLabel(
                    status
                );

        }


        function formatDate(value) {

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


            return date
                .toLocaleString(
                    "id-ID",
                    {
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

                pageTitle.textContent =
                    "History Seluruh Admin";


                pageDescription.textContent =
                    "Lihat aktivitas administrasi seluruh akun Admin FEB UPR.";


                roleTitle.textContent =
                    "Akses Super Admin";


                roleDescription.textContent =
                    "Super Admin dapat melihat aktivitas seluruh admin pada sistem.";


                if (
                    adminFilterGroup
                ) {

                    adminFilterGroup.style.display =
                        "flex";

                }


                if (
                    adminColumnHeader
                ) {

                    adminColumnHeader.style.display =
                        "";

                }

            } else {

                pageTitle.textContent =
                    "History Aktivitas Saya";


                pageDescription.textContent =
                    "Riwayat aktivitas yang dilakukan menggunakan akun Anda.";


                roleTitle.textContent =
                    "History akun Anda";


                roleDescription.textContent =
                    "Admin hanya dapat melihat aktivitas yang dilakukan menggunakan akun sendiri.";


                if (
                    adminFilterGroup
                ) {

                    adminFilterGroup.style.display =
                        "none";

                }


                if (
                    adminColumnHeader
                ) {

                    adminColumnHeader.style.display =
                        "none";

                }

            }

        }


        /* =====================================================
           ROLE ACCESS
        ===================================================== */

        function getRoleActivities() {

            if (
                isSuperAdmin
            ) {

                return [
                    ...allActivities
                ];

            }


            return allActivities.filter(
                function (activity) {

                    return (
                        activity.adminUsername ===
                        currentUsername
                    );

                }
            );

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


            adminFilter.innerHTML =
                `
                <option value="">
                    Semua Admin
                </option>
                `;


            const usernames =
                [
                    ...new Set(
                        allActivities
                            .map(
                                function (activity) {

                                    return activity
                                        .adminUsername;

                                }
                            )
                            .filter(Boolean)
                    )
                ]
                    .sort();


            usernames.forEach(
                function (username) {

                    const option =
                        document.createElement(
                            "option"
                        );


                    option.value =
                        username;


                    option.textContent =
                        username;


                    adminFilter.appendChild(
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


            return roleActivities.filter(
                function (activity) {

                    const searchable = [

                        activity.studentName,
                        activity.nim,
                        activity.submissionCode,
                        activity.adminUsername,
                        activity.department

                    ]
                        .map(
                            function (value) {

                                return String(
                                    value || ""
                                )
                                    .toLowerCase();

                            }
                        );


                    const matchSearch =
                        !search ||
                        searchable.some(
                            function (value) {

                                return value.includes(
                                    search
                                );

                            }
                        );


                    const matchAdmin =
                        !isSuperAdmin ||
                        !appliedAdmin ||
                        activity.adminUsername ===
                            appliedAdmin;


                    const matchAction =
                        !appliedAction ||
                        activity.action ===
                            appliedAction;


                    return (
                        matchSearch &&
                        matchAdmin &&
                        matchAction
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

                parts.push(
                    "Admin " +
                    appliedAdmin
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
                        ] ||
                        appliedAction
                    )
                );

            }


            activeFilterText.textContent =
                parts.length
                    ? parts.join(
                        " • "
                    )
                    : "Semua aktivitas";


            activeFilterBox.classList.add(
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
                activities.filter(
                    function (item) {

                        return (
                            item.action ===
                            "VERIFICATION"
                        );

                    }
                ).length;


            const revision =
                activities.filter(
                    function (item) {

                        return (
                            item.action ===
                            "REVISION"
                        );

                    }
                ).length;


            const process =
                activities.filter(
                    function (item) {

                        return (
                            item.action ===
                            "UPDATE_SK_STATUS"
                        );

                    }
                ).length;


            totalCount.textContent =
                activities.length;


            verificationCount.textContent =
                verification;


            revisionCount.textContent =
                revision;


            skCount.textContent =
                process;

        }


        /* =====================================================
           SIDEBAR COUNT
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
                    submissions.filter(
                        function (submission) {

                            return [

                                API.STATUS
                                    .PERLU_REVISI,

                                API.STATUS
                                    .REVISI_DIKIRIM

                            ].includes(
                                submission.status
                            );

                        }
                    ).length;


                sidebarRevisionCount
                    .textContent =
                    count;

            } catch (error) {

                sidebarRevisionCount
                    .textContent =
                    "0";

            }

        }


        /* =====================================================
           RENDER
        ===================================================== */

        function renderHistory() {

            const roleActivities =
                getRoleActivities();


            const activities =
                getFilteredActivities();


            tableBody.innerHTML =
                "";


            resultText.textContent =
                "Menampilkan " +
                activities.length +
                " dari " +
                roleActivities.length +
                " aktivitas";


            updateSummary(
                activities
            );


            updateActiveFilter();


            if (
                activities.length ===
                0
            ) {

                emptyState.style.display =
                    "flex";


                return;

            }


            emptyState.style.display =
                "none";


            activities.forEach(
                function (activity) {

                    const row =
                        document.createElement(
                            "tr"
                        );


                    const category =
                        getActionCategory(
                            activity.action
                        );


                    const adminCell =
                        isSuperAdmin
                            ? `
                            <td class="history-admin-cell">

                                <strong>
                                    ${escapeHtml(
                                        activity.adminUsername ||
                                        "-"
                                    )}
                                </strong>

                                <span>
                                    ${escapeHtml(
                                        activity.adminRole ||
                                        "ADMIN"
                                    )}
                                </span>

                            </td>
                            `
                            : "";


                    row.innerHTML =
                        `
                        <td>

                            <div class="history-time">

                                <strong>
                                    ${escapeHtml(
                                        formatDate(
                                            activity.createdAt
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
                                        activity.studentName ||
                                        "-"
                                    )}
                                </strong>

                                <span>
                                    ${escapeHtml(
                                        activity.nim ||
                                        "-"
                                    )}
                                </span>

                                <small>
                                    ${escapeHtml(
                                        activity.submissionCode ||
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
                                            activity.previousStatus
                                        )
                                    )}
                                </span>

                                <b>
                                    →
                                </b>

                                <strong>
                                    ${escapeHtml(
                                        getStatusLabel(
                                            activity.newStatus
                                        )
                                    )}
                                </strong>

                            </div>

                        </td>


                        <td>

                            <a
                                href="/superadmin/log-aktivitas?id=${encodeURIComponent(
                                    activity.id
                                )}"
                                class="history-detail-button"
                            >
                                Lihat Detail
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

        applyFilterButton
            ?.addEventListener(
                "click",
                function () {

                    appliedSearch =
                        searchInput
                            ?.value
                            .trim() ||
                        "";


                    appliedAction =
                        actionFilter
                            ?.value ||
                        "";


                    appliedAdmin =
                        isSuperAdmin
                            ? (
                                adminFilter
                                    ?.value ||
                                ""
                            )
                            : "";


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


                    appliedSearch =
                        "";

                    appliedAction =
                        "";

                    appliedAdmin =
                        "";


                    renderHistory();

                }
            );


        searchInput
            ?.addEventListener(
                "keydown",
                function (event) {

                    if (
                        event.key ===
                        "Enter"
                    ) {

                        event.preventDefault();


                        applyFilterButton
                            ?.click();

                    }

                }
            );


        /* =====================================================
           LOAD
        ===================================================== */

        async function loadHistory() {

            setupRoleUI();


            try {

                /*
                 * Frontend tetap melakukan role guard.
                 * Backend nantinya WAJIB tetap menerapkan
                 * authorization sendiri.
                 */

                const params =
                    !isSuperAdmin &&
                    currentUsername
                        ? {
                            admin_username:
                                currentUsername
                        }
                        : {};


                allActivities =
                    await API
                        .getHistory(
                            params
                        );


                populateAdminFilter();


                renderHistory();


                await updateSidebarCount();

            } catch (error) {

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