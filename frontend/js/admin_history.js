document.addEventListener(
    "DOMContentLoaded",
    function () {

        console.log(
            "Admin history aktif"
        );


        /* =====================================
           STORAGE KEY
        ===================================== */

        const STORAGE_KEY =
            "yudisium_admin_activity";


        /* =====================================
           SESSION
        ===================================== */

        const currentUsername =
            sessionStorage.getItem(
                "admin_username"
            ) || "admin";


        const currentRole =
            sessionStorage.getItem(
                "admin_role"
            ) || "ADMIN";


        const isSuperAdmin =
            currentRole ===
            "SUPER_ADMIN";


        /* =====================================
           ELEMENTS
        ===================================== */

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


        /* =====================================
           MOCK DATA
        ===================================== */

        const mockActivities = [

            {
                id:
                    1001,

                admin_username:
                    "adminfeb",

                admin_role:
                    "ADMIN",

                pengajuan_id:
                    1,

                kode_pengajuan:
                    "YDS-2026-0001",

                nim:
                    "2301110001",

                mahasiswa:
                    "Andi Saputra",

                jurusan:
                    "Manajemen",

                action:
                    "VERIFICATION",

                action_label:
                    "Verifikasi Pengajuan",

                old_status:
                    "VERIFIKASI_ADMIN",

                new_status:
                    "TERVERIFIKASI",

                note:
                    "Seluruh data dan dokumen mahasiswa telah disetujui.",

                created_at:
                    "2 Sep 2026, 08.15",

                timestamp:
                    1788308100000
            },


            {
                id:
                    1002,

                admin_username:
                    "adminfeb",

                admin_role:
                    "ADMIN",

                pengajuan_id:
                    2,

                kode_pengajuan:
                    "YDS-2026-0002",

                nim:
                    "2301120002",

                mahasiswa:
                    "Citra Lestari",

                jurusan:
                    "Akuntansi",

                action:
                    "REVISION",

                action_label:
                    "Permintaan Revisi",

                old_status:
                    "VERIFIKASI_ADMIN",

                new_status:
                    "PERLU_REVISI",

                note:
                    "Judul karya tulis dan dokumen rekap nilai perlu diperbaiki.",

                created_at:
                    "2 Sep 2026, 08.45",

                timestamp:
                    1788309900000
            },


            {
                id:
                    1003,

                admin_username:
                    "ciko_admin",

                admin_role:
                    "ADMIN",

                pengajuan_id:
                    3,

                kode_pengajuan:
                    "YDS-2026-0003",

                nim:
                    "2301130003",

                mahasiswa:
                    "Deni Pratama",

                jurusan:
                    "Ekonomi Pembangunan",

                action:
                    "UPDATE_SK_STATUS",

                action_label:
                    "Update Status SK",

                old_status:
                    "TERVERIFIKASI",

                new_status:
                    "PEMBUATAN_SK",

                note:
                    "Dokumen siap masuk tahap pembuatan SK.",

                created_at:
                    "2 Sep 2026, 09.10",

                timestamp:
                    1788311400000
            },


            {
                id:
                    1004,

                admin_username:
                    "ciko_admin",

                admin_role:
                    "ADMIN",

                pengajuan_id:
                    4,

                kode_pengajuan:
                    "YDS-2026-0004",

                nim:
                    "2301110004",

                mahasiswa:
                    "Eva Natalia",

                jurusan:
                    "Manajemen",

                action:
                    "UPDATE_SK_STATUS",

                action_label:
                    "Update Status SK",

                old_status:
                    "PEMBUATAN_SK",

                new_status:
                    "TTD_WAKIL_DEKAN",

                note:
                    null,

                created_at:
                    "2 Sep 2026, 09.25",

                timestamp:
                    1788312300000
            },


            {
                id:
                    1005,

                admin_username:
                    "superadmin",

                admin_role:
                    "SUPER_ADMIN",

                pengajuan_id:
                    5,

                kode_pengajuan:
                    "YDS-2026-0005",

                nim:
                    "2301120005",

                mahasiswa:
                    "Fajar Rahman",

                jurusan:
                    "Akuntansi",

                action:
                    "REVISION",

                action_label:
                    "Review Revisi",

                old_status:
                    "REVISI_DIKIRIM",

                new_status:
                    "TERVERIFIKASI",

                note:
                    "Seluruh perbaikan mahasiswa telah diterima.",

                created_at:
                    "2 Sep 2026, 09.50",

                timestamp:
                    1788313800000
            },


            {
                id:
                    1006,

                admin_username:
                    "superadmin",

                admin_role:
                    "SUPER_ADMIN",

                pengajuan_id:
                    6,

                kode_pengajuan:
                    "YDS-2026-0006",

                nim:
                    "2301110006",

                mahasiswa:
                    "Grace Amelia",

                jurusan:
                    "Manajemen",

                action:
                    "UPDATE_SK_STATUS",

                action_label:
                    "Update Status SK",

                old_status:
                    "TTD_WAKIL_DEKAN",

                new_status:
                    "TTD_DEKAN",

                note:
                    "Dokumen diteruskan ke Dekan.",

                created_at:
                    "2 Sep 2026, 10.05",

                timestamp:
                    1788314700000
            }

        ];


        /* =====================================
           STORAGE
        ===================================== */

        function loadStoredActivities() {

            try {

                const saved =
                    localStorage.getItem(
                        STORAGE_KEY
                    );


                if (
                    !saved
                ) {

                    return [];

                }


                const parsed =
                    JSON.parse(
                        saved
                    );


                return Array.isArray(
                    parsed
                )
                    ?
                    parsed
                    :
                    [];

            } catch (
                error
            ) {

                console.error(
                    "Gagal membaca history:",
                    error
                );


                return [];

            }

        }



        function getAllActivities() {

            const storedActivities =
                loadStoredActivities();


            /*
             * Gabungkan mock dengan aktivitas
             * yang dibuat dari halaman Proses SK.
             */

            const combined =
                [
                    ...storedActivities,
                    ...mockActivities
                ];


            /*
             * Hilangkan duplikat ID.
             */

            const map =
                new Map();


            combined.forEach(
                function (activity) {

                    const key =
                        String(
                            activity.id
                        );


                    if (
                        !map.has(
                            key
                        )
                    ) {

                        map.set(
                            key,
                            activity
                        );

                    }

                }
            );


            return Array.from(
                map.values()
            ).sort(
                function (
                    a,
                    b
                ) {

                    return (
                        Number(
                            b.timestamp ||
                            b.id
                        ) -
                        Number(
                            a.timestamp ||
                            a.id
                        )
                    );

                }
            );

        }


        const allActivities =
            getAllActivities();


        /* =====================================
           ROLE UI
        ===================================== */

        function setupRoleUI() {

            if (
                isSuperAdmin
            ) {

                pageTitle.textContent =
                    "History Seluruh Admin";


                pageDescription.textContent =
                    "Lihat aktivitas Anda dan seluruh admin yang menggunakan sistem.";


                roleTitle.textContent =
                    "Akses Super Admin";


                roleDescription.textContent =
                    "Super Admin dapat melihat history miliknya sendiri maupun aktivitas seluruh admin.";


                adminFilterGroup.style.display =
                    "flex";


                adminColumnHeader.style.display =
                    "";


            } else {

                pageTitle.textContent =
                    "History Aktivitas Saya";


                pageDescription.textContent =
                    "Riwayat aktivitas yang dilakukan menggunakan akun Anda.";


                roleTitle.textContent =
                    "History akun Anda";


                roleDescription.textContent =
                    "Admin hanya dapat melihat aktivitas yang dilakukan menggunakan akun sendiri.";


                adminFilterGroup.style.display =
                    "none";


                adminColumnHeader.style.display =
                    "none";

            }

        }


        setupRoleUI();


        /* =====================================
           ADMIN FILTER OPTIONS
        ===================================== */

        function populateAdminFilter() {

            if (
                !isSuperAdmin
            ) {

                return;

            }


            const usernames =
                [
                    ...new Set(
                        allActivities.map(
                            function (
                                activity
                            ) {

                                return (
                                    activity
                                        .admin_username
                                );

                            }
                        )
                    )
                ].sort();


            usernames.forEach(
                function (
                    username
                ) {

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


        populateAdminFilter();


        /* =====================================
           BASE ROLE ACCESS
        ===================================== */

        function getRoleAccessibleActivities() {

            if (
                isSuperAdmin
            ) {

                return [
                    ...allActivities
                ];

            }


            return allActivities.filter(
                function (
                    activity
                ) {

                    return (
                        activity
                            .admin_username ===
                        currentUsername
                    );

                }
            );

        }


        const roleActivities =
            getRoleAccessibleActivities();


        /* =====================================
           APPLIED FILTER
        ===================================== */

        let appliedSearch =
            "";


        let appliedAdmin =
            "";


        let appliedAction =
            "";


        /* =====================================
           LABEL
        ===================================== */

        function getActionLabel(
            activity
        ) {

            if (
                activity.action_label
            ) {

                return (
                    activity.action_label
                );

            }


            const labels = {

                VERIFICATION:
                    "Verifikasi Pengajuan",

                REVISION:
                    "Revisi Pengajuan",

                UPDATE_SK_STATUS:
                    "Update Status SK"

            };


            return (
                labels[
                    activity.action
                ] ||
                activity.action
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



        function formatStatus(
            status
        ) {

            if (
                !status
            ) {

                return "-";

            }


            const labels = {

                DIAJUKAN:
                    "Diajukan",

                VERIFIKASI_ADMIN:
                    "Verifikasi Admin",

                PERLU_REVISI:
                    "Perlu Revisi",

                REVISI_DIKIRIM:
                    "Revisi Dikirim",

                TERVERIFIKASI:
                    "Terverifikasi",

                PEMBUATAN_SK:
                    "Pembuatan SK",

                TTD_WAKIL_DEKAN:
                    "TTD Wakil Dekan",

                TTD_DEKAN:
                    "TTD Dekan",

                SK_TERBIT:
                    "SK Terbit"

            };


            return (
                labels[
                    status
                ] ||
                status
            );

        }


        /* =====================================
           FILTER
        ===================================== */

        function getFilteredActivities() {

            const search =
                appliedSearch
                    .toLowerCase();


            return roleActivities.filter(
                function (
                    activity
                ) {

                    const student =
                        String(
                            activity.mahasiswa ||
                            ""
                        ).toLowerCase();


                    const nim =
                        String(
                            activity.nim ||
                            ""
                        ).toLowerCase();


                    const code =
                        String(
                            activity.kode_pengajuan ||
                            ""
                        ).toLowerCase();


                    const username =
                        String(
                            activity.admin_username ||
                            ""
                        ).toLowerCase();


                    const matchSearch =
                        search === "" ||
                        student.includes(
                            search
                        ) ||
                        nim.includes(
                            search
                        ) ||
                        code.includes(
                            search
                        ) ||
                        username.includes(
                            search
                        );


                    const matchAdmin =
                        !isSuperAdmin ||
                        appliedAdmin === "" ||
                        activity.admin_username ===
                        appliedAdmin;


                    const matchAction =
                        appliedAction === "" ||
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


        /* =====================================
           ACTIVE FILTER
        ===================================== */

        function updateActiveFilter() {

            const parts =
                [];


            if (
                appliedSearch !== ""
            ) {

                parts.push(
                    'Pencarian "' +
                    appliedSearch +
                    '"'
                );

            }


            if (
                isSuperAdmin &&
                appliedAdmin !== ""
            ) {

                parts.push(
                    "Admin " +
                    appliedAdmin
                );

            }


            if (
                appliedAction !== ""
            ) {

                const labelMap = {

                    VERIFICATION:
                        "Verifikasi",

                    REVISION:
                        "Revisi",

                    UPDATE_SK_STATUS:
                        "Proses SK"

                };


                parts.push(
                    "Aktivitas " +
                    labelMap[
                        appliedAction
                    ]
                );

            }


            activeFilterText.textContent =
                parts.length > 0
                    ?
                    parts.join(
                        " • "
                    )
                    :
                    "Semua aktivitas";


            activeFilterBox
                .classList
                .add(
                    "active"
                );

        }


        /* =====================================
           SUMMARY
        ===================================== */

        function updateSummary(
            activities
        ) {

            const verification =
                activities.filter(
                    function (
                        activity
                    ) {

                        return (
                            activity.action ===
                            "VERIFICATION"
                        );

                    }
                ).length;


            const revision =
                activities.filter(
                    function (
                        activity
                    ) {

                        return (
                            activity.action ===
                            "REVISION"
                        );

                    }
                ).length;


            const process =
                activities.filter(
                    function (
                        activity
                    ) {

                        return (
                            activity.action ===
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


        /* =====================================
           RENDER
        ===================================== */

        function renderHistory() {

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
                function (
                    activity
                ) {

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
                            ?
                            `
                            <td class="history-admin-cell">

                                <strong>
                                    ${activity.admin_username}
                                </strong>

                                <span>
                                    ${activity.admin_role || "ADMIN"}
                                </span>

                            </td>
                            `
                            :
                            "";


                    row.innerHTML = `
                        <td>

                            <div class="history-time">

                                <strong>
                                    ${activity.created_at || "-"}
                                </strong>

                            </div>

                        </td>


                        ${adminCell}


                        <td>

                            <div class="history-student">

                                <strong>
                                    ${activity.mahasiswa || "-"}
                                </strong>

                                <span>
                                    ${activity.nim || "-"}
                                </span>

                                <small>
                                    ${activity.kode_pengajuan || "-"}
                                </small>

                            </div>

                        </td>


                        <td>

                            <span
                                class="history-action-badge ${category}"
                            >
                                ${getActionLabel(
                                    activity
                                )}
                            </span>

                        </td>


                        <td>

                            <div class="history-change">

                                <span>
                                    ${formatStatus(
                                        activity.old_status
                                    )}
                                </span>

                                <b>
                                    →
                                </b>

                                <strong>
                                    ${formatStatus(
                                        activity.new_status
                                    )}
                                </strong>

                            </div>

                        </td>


                        <td>

                            <a
                                href="detail_history.html?id=${activity.id}"
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


        /* =====================================
           APPLY FILTER
        ===================================== */

        applyFilterButton.addEventListener(
            "click",
            function () {

                appliedSearch =
                    searchInput.value
                        .trim();


                appliedAction =
                    actionFilter.value;


                if (
                    isSuperAdmin
                ) {

                    appliedAdmin =
                        adminFilter.value;

                } else {

                    appliedAdmin =
                        "";

                }


                renderHistory();

            }
        );


        /* =====================================
           ENTER SEARCH
        ===================================== */

        searchInput.addEventListener(
            "keydown",
            function (
                event
            ) {

                if (
                    event.key ===
                    "Enter"
                ) {

                    event.preventDefault();


                    applyFilterButton
                        .click();

                }

            }
        );


        /* =====================================
           RESET
        ===================================== */

        resetFilterButton.addEventListener(
            "click",
            function () {

                searchInput.value =
                    "";


                actionFilter.value =
                    "";


                if (
                    isSuperAdmin
                ) {

                    adminFilter.value =
                        "";

                }


                appliedSearch =
                    "";


                appliedAdmin =
                    "";


                appliedAction =
                    "";


                renderHistory();

            }
        );


        /* =====================================
           INITIAL RENDER
        ===================================== */

        renderHistory();

    }
);