document.addEventListener(
    "DOMContentLoaded",
    function () {

        console.log(
            "Kelola Admin aktif"
        );


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

        const content =
            document.getElementById(
                "manageAdminContent"
            );


        const accessDenied =
            document.getElementById(
                "manageAdminAccessDenied"
            );


        const pendingList =
            document.getElementById(
                "pendingAdminList"
            );


        const activeList =
            document.getElementById(
                "activeAdminList"
            );


        const inactiveList =
            document.getElementById(
                "inactiveAdminList"
            );


        const pendingEmpty =
            document.getElementById(
                "pendingAdminEmpty"
            );


        const activeEmpty =
            document.getElementById(
                "activeAdminEmpty"
            );


        const inactiveEmpty =
            document.getElementById(
                "inactiveAdminEmpty"
            );


        const pendingCount =
            document.getElementById(
                "pendingAdminCount"
            );


        const activeCount =
            document.getElementById(
                "activeAdminCount"
            );


        const inactiveCount =
            document.getElementById(
                "inactiveAdminCount"
            );


        const totalCount =
            document.getElementById(
                "totalAdminCount"
            );


        const pendingSectionCount =
            document.getElementById(
                "pendingSectionCount"
            );


        const activeSectionCount =
            document.getElementById(
                "activeSectionCount"
            );


        const inactiveSectionCount =
            document.getElementById(
                "inactiveSectionCount"
            );


        const pendingNavCount =
            document.getElementById(
                "pendingAdminNavCount"
            );


        const searchInput =
            document.getElementById(
                "manageAdminSearch"
            );


        const searchButton =
            document.getElementById(
                "applyAdminSearch"
            );


        const resetButton =
            document.getElementById(
                "resetAdminSearch"
            );


        const filterInfo =
            document.getElementById(
                "manageAdminFilterInfo"
            );


        const filterText =
            document.getElementById(
                "manageAdminFilterText"
            );


        /* =====================================
           MODAL ELEMENT
        ===================================== */

        const actionModal =
            document.getElementById(
                "adminActionModal"
            );


        const actionModalIcon =
            document.getElementById(
                "adminActionModalIcon"
            );


        const actionModalTitle =
            document.getElementById(
                "adminActionModalTitle"
            );


        const actionModalMessage =
            document.getElementById(
                "adminActionModalMessage"
            );


        const actionAccountName =
            document.getElementById(
                "adminActionAccountName"
            );


        const actionAccountUsername =
            document.getElementById(
                "adminActionAccountUsername"
            );


        const cancelAction =
            document.getElementById(
                "cancelAdminAction"
            );


        const confirmAction =
            document.getElementById(
                "confirmAdminAction"
            );


        const successModal =
            document.getElementById(
                "adminActionSuccessModal"
            );


        const successTitle =
            document.getElementById(
                "adminActionSuccessTitle"
            );


        const successMessage =
            document.getElementById(
                "adminActionSuccessMessage"
            );


        const closeSuccess =
            document.getElementById(
                "closeAdminActionSuccess"
            );


        /* =====================================
           ACCESS
        ===================================== */

        if (
            !isSuperAdmin
        ) {

            content.style.display =
                "none";


            accessDenied.style.display =
                "flex";


            return;

        }


        content.style.display =
            "block";


        accessDenied.style.display =
            "none";


        /* =====================================
           STORAGE
        ===================================== */

        const STORAGE_KEY =
            "yudisium_admin_accounts";


        /* =====================================
           DEFAULT DATA
        ===================================== */

        const defaultAdmins = [

            {
                id:
                    1,

                name:
                    "Budi Santoso",

                username:
                    "budi.santoso",

                email:
                    "budi.santoso@example.com",

                role:
                    "ADMIN",

                status:
                    "PENDING",

                created_at:
                    "1 Sep 2026"
            },


            {
                id:
                    2,

                name:
                    "Rina Marlina",

                username:
                    "rina.marlina",

                email:
                    "rina.marlina@example.com",

                role:
                    "ADMIN",

                status:
                    "PENDING",

                created_at:
                    "2 Sep 2026"
            },


            {
                id:
                    3,

                name:
                    "Admin FEB",

                username:
                    "adminfeb",

                email:
                    "adminfeb@example.com",

                role:
                    "ADMIN",

                status:
                    "ACTIVE",

                created_at:
                    "20 Agu 2026"
            },


            {
                id:
                    4,

                name:
                    "Ciko Admin",

                username:
                    "ciko_admin",

                email:
                    "ciko@example.com",

                role:
                    "ADMIN",

                status:
                    "ACTIVE",

                created_at:
                    "22 Agu 2026"
            },


            {
                id:
                    5,

                name:
                    "Dewi Akademik",

                username:
                    "dewi.akademik",

                email:
                    "dewi.akademik@example.com",

                role:
                    "ADMIN",

                status:
                    "INACTIVE",

                created_at:
                    "10 Agu 2026"
            }

        ];


        /* =====================================
           LOAD
        ===================================== */

        function loadAdmins() {

            try {

                const saved =
                    localStorage.getItem(
                        STORAGE_KEY
                    );


                if (
                    saved
                ) {

                    const parsed =
                        JSON.parse(
                            saved
                        );


                    if (
                        Array.isArray(
                            parsed
                        )
                    ) {

                        return parsed;

                    }

                }

            } catch (
                error
            ) {

                console.error(
                    "Gagal membaca data admin",
                    error
                );

            }


            localStorage.setItem(
                STORAGE_KEY,
                JSON.stringify(
                    defaultAdmins
                )
            );


            return [
                ...defaultAdmins
            ];

        }


        let admins =
            loadAdmins();


        /* =====================================
           SAVE
        ===================================== */

        function saveAdmins() {

            localStorage.setItem(
                STORAGE_KEY,
                JSON.stringify(
                    admins
                )
            );

        }


        /* =====================================
           SEARCH STATE
        ===================================== */

        let appliedSearch =
            "";


        /* =====================================
           HELPER
        ===================================== */

        function getInitials(
            name
        ) {

            return String(
                name
            )
                .split(" ")
                .filter(
                    function (part) {

                        return (
                            part !==
                            ""
                        );

                    }
                )
                .slice(
                    0,
                    2
                )
                .map(
                    function (part) {

                        return (
                            part.charAt(
                                0
                            )
                        );

                    }
                )
                .join("")
                .toUpperCase();

        }



        function getFilteredAdmins() {

            if (
                appliedSearch ===
                ""
            ) {

                return [
                    ...admins
                ];

            }


            const search =
                appliedSearch
                    .toLowerCase();


            return admins.filter(
                function (admin) {

                    return (
                        admin.name
                            .toLowerCase()
                            .includes(
                                search
                            ) ||
                        admin.username
                            .toLowerCase()
                            .includes(
                                search
                            ) ||
                        admin.email
                            .toLowerCase()
                            .includes(
                                search
                            )
                    );

                }
            );

        }


        /* =====================================
           ADMIN CARD
        ===================================== */

        function buildAdminCard(
            admin
        ) {

            const article =
                document.createElement(
                    "article"
                );


            article.className =
                "manage-admin-item";


            const avatar =
                getInitials(
                    admin.name
                );


            let actionButtons =
                "";


            if (
                admin.status ===
                "PENDING"
            ) {

                actionButtons = `
                    <button
                        type="button"
                        class="manage-admin-action approve"
                        data-admin-action="approve"
                        data-admin-id="${admin.id}"
                    >
                        Setujui
                    </button>

                    <button
                        type="button"
                        class="manage-admin-action reject"
                        data-admin-action="reject"
                        data-admin-id="${admin.id}"
                    >
                        Tolak
                    </button>
                `;

            } else if (
                admin.status ===
                "ACTIVE"
            ) {

                actionButtons = `
                    <button
                        type="button"
                        class="manage-admin-action deactivate"
                        data-admin-action="deactivate"
                        data-admin-id="${admin.id}"
                    >
                        Nonaktifkan
                    </button>
                `;

            } else {

                actionButtons = `
                    <button
                        type="button"
                        class="manage-admin-action activate"
                        data-admin-action="activate"
                        data-admin-id="${admin.id}"
                    >
                        Aktifkan Kembali
                    </button>
                `;

            }


            article.innerHTML = `
                <div class="manage-admin-user">

                    <div class="manage-admin-avatar">
                        ${avatar}
                    </div>

                    <div>

                        <strong>
                            ${admin.name}
                        </strong>

                        <span>
                            @${admin.username}
                        </span>

                        <small>
                            ${admin.email}
                        </small>

                    </div>

                </div>


                <div class="manage-admin-meta">

                    <div>

                        <span>
                            Role
                        </span>

                        <strong>
                            ${admin.role}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Terdaftar
                        </span>

                        <strong>
                            ${admin.created_at}
                        </strong>

                    </div>

                </div>


                <div class="manage-admin-status">

                    <span
                        class="manage-admin-status-badge ${admin.status.toLowerCase()}"
                    >
                        ${admin.status}
                    </span>

                </div>


                <div class="manage-admin-item-actions">
                    ${actionButtons}
                </div>
            `;


            return article;

        }


        /* =====================================
           RENDER
        ===================================== */

        function renderAdmins() {

            const filtered =
                getFilteredAdmins();


            const pending =
                filtered.filter(
                    function (admin) {

                        return (
                            admin.status ===
                            "PENDING"
                        );

                    }
                );


            const active =
                filtered.filter(
                    function (admin) {

                        return (
                            admin.status ===
                            "ACTIVE"
                        );

                    }
                );


            const inactive =
                filtered.filter(
                    function (admin) {

                        return (
                            admin.status ===
                            "INACTIVE"
                        );

                    }
                );


            pendingList.innerHTML =
                "";


            activeList.innerHTML =
                "";


            inactiveList.innerHTML =
                "";


            pending.forEach(
                function (admin) {

                    pendingList.appendChild(
                        buildAdminCard(
                            admin
                        )
                    );

                }
            );


            active.forEach(
                function (admin) {

                    activeList.appendChild(
                        buildAdminCard(
                            admin
                        )
                    );

                }
            );


            inactive.forEach(
                function (admin) {

                    inactiveList.appendChild(
                        buildAdminCard(
                            admin
                        )
                    );

                }
            );


            pendingEmpty.style.display =
                pending.length ===
                0
                    ?
                    "flex"
                    :
                    "none";


            activeEmpty.style.display =
                active.length ===
                0
                    ?
                    "flex"
                    :
                    "none";


            inactiveEmpty.style.display =
                inactive.length ===
                0
                    ?
                    "flex"
                    :
                    "none";


            /* SUMMARY GLOBAL */

            const globalPending =
                admins.filter(
                    function (admin) {

                        return (
                            admin.status ===
                            "PENDING"
                        );

                    }
                );


            const globalActive =
                admins.filter(
                    function (admin) {

                        return (
                            admin.status ===
                            "ACTIVE"
                        );

                    }
                );


            const globalInactive =
                admins.filter(
                    function (admin) {

                        return (
                            admin.status ===
                            "INACTIVE"
                        );

                    }
                );


            pendingCount.textContent =
                globalPending.length;


            activeCount.textContent =
                globalActive.length;


            inactiveCount.textContent =
                globalInactive.length;


            totalCount.textContent =
                admins.length;


            pendingSectionCount.textContent =
                pending.length +
                " Pending";


            activeSectionCount.textContent =
                active.length +
                " Aktif";


            inactiveSectionCount.textContent =
                inactive.length +
                " Nonaktif";


            pendingNavCount.textContent =
                globalPending.length;


            if (
                globalPending.length ===
                0
            ) {

                pendingNavCount.style.display =
                    "none";

            } else {

                pendingNavCount.style.display =
                    "";

            }


            filterInfo.classList.add(
                "active"
            );


            if (
                appliedSearch ===
                ""
            ) {

                filterText.textContent =
                    "Semua admin";

            } else {

                filterText.textContent =
                    'Pencarian "' +
                    appliedSearch +
                    '"';

            }

        }


        /* =====================================
           SEARCH
        ===================================== */

        function applySearch() {

            appliedSearch =
                searchInput.value
                    .trim();


            renderAdmins();

        }


        searchButton.addEventListener(
            "click",
            applySearch
        );


        searchInput.addEventListener(
            "keydown",
            function (event) {

                if (
                    event.key ===
                    "Enter"
                ) {

                    event.preventDefault();

                    applySearch();

                }

            }
        );


        resetButton.addEventListener(
            "click",
            function () {

                searchInput.value =
                    "";


                appliedSearch =
                    "";


                renderAdmins();

            }
        );


        /* =====================================
           ACTION MODAL STATE
        ===================================== */

        let selectedAdminId =
            null;


        let selectedAction =
            null;


        function getAdminById(
            id
        ) {

            return admins.find(
                function (admin) {

                    return (
                        String(
                            admin.id
                        ) ===
                        String(
                            id
                        )
                    );

                }
            );

        }


        /* =====================================
           OPEN ACTION
        ===================================== */

        document.addEventListener(
            "click",
            function (event) {

                const button =
                    event.target.closest(
                        "[data-admin-action]"
                    );


                if (
                    !button
                ) {

                    return;

                }


                const admin =
                    getAdminById(
                        button.dataset.adminId
                    );


                if (
                    !admin
                ) {

                    return;

                }


                selectedAdminId =
                    admin.id;


                selectedAction =
                    button.dataset.adminAction;


                actionAccountName.textContent =
                    admin.name;


                actionAccountUsername.textContent =
                    "@" +
                    admin.username;


                actionModalIcon.className =
                    "manage-admin-modal-icon";


                if (
                    selectedAction ===
                    "approve"
                ) {

                    actionModalIcon.textContent =
                        "✓";


                    actionModalIcon.classList.add(
                        "approve"
                    );


                    actionModalTitle.textContent =
                        "Setujui Akun Admin";


                    actionModalMessage.textContent =
                        "Akun akan diaktifkan dan dapat digunakan untuk login ke sistem.";


                    confirmAction.textContent =
                        "Ya, Setujui";


                    confirmAction.className =
                        "manage-admin-modal-confirm approve";

                } else if (
                    selectedAction ===
                    "reject"
                ) {

                    actionModalIcon.textContent =
                        "×";


                    actionModalIcon.classList.add(
                        "danger"
                    );


                    actionModalTitle.textContent =
                        "Tolak Permintaan Akun";


                    actionModalMessage.textContent =
                        "Akun akan dipindahkan ke status nonaktif dan tidak dapat digunakan untuk login.";


                    confirmAction.textContent =
                        "Ya, Tolak";


                    confirmAction.className =
                        "manage-admin-modal-confirm danger";

                } else if (
                    selectedAction ===
                    "deactivate"
                ) {

                    actionModalIcon.textContent =
                        "!";


                    actionModalIcon.classList.add(
                        "danger"
                    );


                    actionModalTitle.textContent =
                        "Nonaktifkan Admin";


                    actionModalMessage.textContent =
                        "Admin tidak akan dapat login selama akun berstatus nonaktif.";


                    confirmAction.textContent =
                        "Ya, Nonaktifkan";


                    confirmAction.className =
                        "manage-admin-modal-confirm danger";

                } else if (
                    selectedAction ===
                    "activate"
                ) {

                    actionModalIcon.textContent =
                        "✓";


                    actionModalIcon.classList.add(
                        "approve"
                    );


                    actionModalTitle.textContent =
                        "Aktifkan Kembali Admin";


                    actionModalMessage.textContent =
                        "Akun admin akan kembali aktif dan dapat digunakan untuk login.";


                    confirmAction.textContent =
                        "Ya, Aktifkan";


                    confirmAction.className =
                        "manage-admin-modal-confirm approve";

                }


                actionModal.classList.add(
                    "active"
                );

            }
        );


        /* =====================================
           CANCEL ACTION
        ===================================== */

        function closeActionModal() {

            actionModal.classList.remove(
                "active"
            );


            selectedAdminId =
                null;


            selectedAction =
                null;

        }


        cancelAction.addEventListener(
            "click",
            closeActionModal
        );


        actionModal.addEventListener(
            "click",
            function (event) {

                if (
                    event.target ===
                    actionModal
                ) {

                    closeActionModal();

                }

            }
        );


        /* =====================================
           LOG ADMIN MANAGEMENT
        ===================================== */

        function saveManagementHistory(
            targetAdmin,
            action,
            oldStatus,
            newStatus
        ) {

            const key =
                "yudisium_admin_management_history";


            let history =
                [];


            try {

                const saved =
                    localStorage.getItem(
                        key
                    );


                if (
                    saved
                ) {

                    history =
                        JSON.parse(
                            saved
                        );

                }

            } catch (
                error
            ) {

                history =
                    [];

            }


            history.unshift(
                {
                    id:
                        Date.now(),

                    super_admin:
                        currentUsername,

                    target_admin:
                        targetAdmin.username,

                    target_name:
                        targetAdmin.name,

                    action:
                        action,

                    old_status:
                        oldStatus,

                    new_status:
                        newStatus,

                    timestamp:
                        Date.now(),

                    created_at:
                        new Intl
                            .DateTimeFormat(
                                "id-ID",
                                {
                                    dateStyle:
                                        "medium",

                                    timeStyle:
                                        "short"
                                }
                            )
                            .format(
                                new Date()
                            )
                }
            );


            localStorage.setItem(
                key,
                JSON.stringify(
                    history
                )
            );

        }


        /* =====================================
           CONFIRM ACTION
        ===================================== */

        confirmAction.addEventListener(
            "click",
            function () {

                const admin =
                    getAdminById(
                        selectedAdminId
                    );


                if (
                    !admin ||
                    !selectedAction
                ) {

                    return;

                }


                const oldStatus =
                    admin.status;


                let newStatus =
                    oldStatus;


                let actionLabel =
                    "";


                if (
                    selectedAction ===
                    "approve"
                ) {

                    newStatus =
                        "ACTIVE";


                    actionLabel =
                        "APPROVE_ADMIN";

                } else if (
                    selectedAction ===
                    "reject"
                ) {

                    newStatus =
                        "INACTIVE";


                    actionLabel =
                        "REJECT_ADMIN";

                } else if (
                    selectedAction ===
                    "deactivate"
                ) {

                    newStatus =
                        "INACTIVE";


                    actionLabel =
                        "DEACTIVATE_ADMIN";

                } else if (
                    selectedAction ===
                    "activate"
                ) {

                    newStatus =
                        "ACTIVE";


                    actionLabel =
                        "ACTIVATE_ADMIN";

                }


                admin.status =
                    newStatus;


                saveAdmins();


                saveManagementHistory(
                    admin,
                    actionLabel,
                    oldStatus,
                    newStatus
                );


                actionModal.classList.remove(
                    "active"
                );


                renderAdmins();


                if (
                    selectedAction ===
                    "approve"
                ) {

                    successTitle.textContent =
                        "Admin Disetujui";


                    successMessage.textContent =
                        "Akun " +
                        admin.username +
                        " sekarang aktif.";

                } else if (
                    selectedAction ===
                    "reject"
                ) {

                    successTitle.textContent =
                        "Permintaan Ditolak";


                    successMessage.textContent =
                        "Akun " +
                        admin.username +
                        " telah dinonaktifkan.";

                } else if (
                    selectedAction ===
                    "deactivate"
                ) {

                    successTitle.textContent =
                        "Admin Dinonaktifkan";


                    successMessage.textContent =
                        "Akun " +
                        admin.username +
                        " tidak dapat login untuk sementara.";

                } else {

                    successTitle.textContent =
                        "Admin Diaktifkan";


                    successMessage.textContent =
                        "Akun " +
                        admin.username +
                        " kembali aktif.";

                }


                successModal.classList.add(
                    "active"
                );


                selectedAdminId =
                    null;


                selectedAction =
                    null;

            }
        );


        /* =====================================
           SUCCESS
        ===================================== */

        closeSuccess.addEventListener(
            "click",
            function () {

                successModal.classList.remove(
                    "active"
                );

            }
        );


        /* =====================================
           INIT
        ===================================== */

        renderAdmins();

    }
);