document.addEventListener(
    "DOMContentLoaded",
    function () {

        console.log(
            "Admin frontend aktif"
        );


        /* =====================================
           PAGE DETECTION
        ===================================== */

        const adminLoginForm =
            document.getElementById(
                "adminLoginForm"
            );


        const adminRegisterForm =
            document.getElementById(
                "adminRegisterForm"
            );


        const usernameRecoveryForm =
            document.getElementById(
                "usernameRecoveryForm"
            );


        const passwordRecoveryForm =
            document.getElementById(
                "passwordRecoveryForm"
            );


        const adminLayout =
            document.querySelector(
                ".admin-layout"
            );


        const submissionTableBody =
            document.getElementById(
                "submissionTableBody"
            );


        const isDashboard =
            document.getElementById(
                "quickExport"
            ) !== null;


        if (
            adminLoginForm
        ) {

            initAdminLogin();

        }


        if (
            adminRegisterForm
        ) {

            initAdminRegister();

        }


        if (
            usernameRecoveryForm ||
            passwordRecoveryForm
        ) {

            initAccountRecovery();

        }


        if (
            adminLayout
        ) {

            setAdminIdentity();

            setupLogout();

        }


        if (
            isDashboard
        ) {

            initAdminDashboard();

        }


        if (
            submissionTableBody
        ) {

            initSubmissionPage();

        }


        /* =====================================
           GLOBAL HELPER
        ===================================== */

        function setError(
            field,
            hasError
        ) {

            const formGroup =
                field.closest(
                    ".form-group"
                );


            if (
                hasError
            ) {

                field.classList.add(
                    "form-control-error"
                );


                if (
                    formGroup
                ) {

                    formGroup.classList.add(
                        "has-error"
                    );

                }

            } else {

                field.classList.remove(
                    "form-control-error"
                );


                if (
                    formGroup
                ) {

                    formGroup.classList.remove(
                        "has-error"
                    );

                }

            }

        }



        function validateEmailField(
            field
        ) {

            const value =
                field.value.trim();


            const pattern =
                /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


            const valid =
                pattern.test(
                    value
                );


            setError(
                field,
                !valid
            );


            return valid;

        }



        function setupPasswordToggle(
            button,
            input,
            openIcon,
            closedIcon
        ) {

            if (
                !button ||
                !input ||
                !openIcon ||
                !closedIcon
            ) {

                return;

            }


            button.addEventListener(
                "click",
                function () {

                    const visible =
                        input.type ===
                        "text";


                    if (
                        visible
                    ) {

                        input.type =
                            "password";


                        openIcon.classList.remove(
                            "hidden"
                        );


                        closedIcon.classList.add(
                            "hidden"
                        );


                        button.setAttribute(
                            "aria-label",
                            "Tampilkan password"
                        );

                    } else {

                        input.type =
                            "text";


                        openIcon.classList.add(
                            "hidden"
                        );


                        closedIcon.classList.remove(
                            "hidden"
                        );


                        button.setAttribute(
                            "aria-label",
                            "Sembunyikan password"
                        );

                    }

                }
            );

        }



        function getAdminSession() {

            const username =
                sessionStorage.getItem(
                    "admin_username"
                ) || "admin";


            const role =
                sessionStorage.getItem(
                    "admin_role"
                ) || "ADMIN";


            return {

                username:
                    username,

                role:
                    role

            };

        }



        function setAdminIdentity() {

            const session =
                getAdminSession();


            const displayName =
                session.username
                    .charAt(
                        0
                    )
                    .toUpperCase() +
                session.username
                    .slice(
                        1
                    );


            const sidebarName =
                document.getElementById(
                    "sidebarAdminName"
                );


            const sidebarRole =
                document.getElementById(
                    "sidebarAdminRole"
                );


            const topbarName =
                document.getElementById(
                    "topbarAdminName"
                );


            const topbarRole =
                document.getElementById(
                    "topbarAdminRole"
                );


            if (
                sidebarName
            ) {

                sidebarName.textContent =
                    displayName;

            }


            if (
                sidebarRole
            ) {

                sidebarRole.textContent =
                    session.role;

            }


            if (
                topbarName
            ) {

                topbarName.textContent =
                    displayName;

            }


            if (
                topbarRole
            ) {

                topbarRole.textContent =
                    session.role;

            }


            const manageAdminMenu =
                document.getElementById(
                    "manageAdminMenu"
                );


            if (
                manageAdminMenu
            ) {

                if (
                    session.role ===
                    "SUPER_ADMIN"
                ) {

                    manageAdminMenu.style.display =
                        "flex";


                    manageAdminMenu.classList.add(
                        "active-role"
                    );

                } else {

                    manageAdminMenu.style.display =
                        "none";


                    manageAdminMenu.classList.remove(
                        "active-role"
                    );

                }

            }


            return session;

        }



        function setupLogout() {

            const logoutButton =
                document.getElementById(
                    "adminLogout"
                );


            if (
                !logoutButton
            ) {

                return;

            }


            /*
             * Supaya tidak terpasang
             * listener dua kali.
             */

            if (
                logoutButton.dataset
                    .logoutInitialized ===
                "true"
            ) {

                return;

            }


            logoutButton.dataset
                .logoutInitialized =
                "true";


            logoutButton.addEventListener(
                "click",
                function () {

                    sessionStorage.removeItem(
                        "admin_logged_in"
                    );


                    sessionStorage.removeItem(
                        "admin_username"
                    );


                    sessionStorage.removeItem(
                        "admin_role"
                    );


                    window.location.href =
                        "login.html";

                }
            );

        }


        /* =====================================
           LOGIN
        ===================================== */

        function initAdminLogin() {

            const username =
                document.getElementById(
                    "admin_username"
                );


            const password =
                document.getElementById(
                    "admin_password"
                );


            const loading =
                document.getElementById(
                    "adminLoginLoading"
                );


            function validateField(
                field
            ) {

                const valid =
                    field.value
                        .trim() !==
                    "";


                setError(
                    field,
                    !valid
                );


                return valid;

            }


            username.addEventListener(
                "input",
                function () {

                    validateField(
                        this
                    );

                }
            );


            password.addEventListener(
                "input",
                function () {

                    validateField(
                        this
                    );

                }
            );


            setupPasswordToggle(
                document.getElementById(
                    "togglePassword"
                ),
                password,
                document.getElementById(
                    "eyeOpenIcon"
                ),
                document.getElementById(
                    "eyeClosedIcon"
                )
            );


            adminLoginForm.addEventListener(
                "submit",
                function (event) {

                    event.preventDefault();


                    const usernameValid =
                        validateField(
                            username
                        );


                    const passwordValid =
                        validateField(
                            password
                        );


                    if (
                        !usernameValid ||
                        !passwordValid
                    ) {

                        return;

                    }


                    if (
                        loading
                    ) {

                        loading.classList.add(
                            "active"
                        );

                    }


                    const role =
                        username.value
                            .trim()
                            .toLowerCase() ===
                        "superadmin"
                            ?
                            "SUPER_ADMIN"
                            :
                            "ADMIN";


                    sessionStorage.setItem(
                        "admin_logged_in",
                        "true"
                    );


                    sessionStorage.setItem(
                        "admin_username",
                        username.value.trim()
                    );


                    sessionStorage.setItem(
                        "admin_role",
                        role
                    );


                    setTimeout(
                        function () {

                            window.location.href =
                                "dashboard.html";

                        },
                        500
                    );

                }
            );

        }


        /* =====================================
           REGISTER
        ===================================== */

        function initAdminRegister() {

            const name =
                document.getElementById(
                    "register_name"
                );


            const username =
                document.getElementById(
                    "register_username"
                );


            const email =
                document.getElementById(
                    "register_email"
                );


            const password =
                document.getElementById(
                    "register_password"
                );


            const confirmationPassword =
                document.getElementById(
                    "register_password_confirmation"
                );


            const agreement =
                document.getElementById(
                    "registerAgreement"
                );


            const agreementError =
                document.getElementById(
                    "registerAgreementError"
                );


            const successModal =
                document.getElementById(
                    "registerSuccessModal"
                );


            const registerToLogin =
                document.getElementById(
                    "registerToLogin"
                );


            setupPasswordToggle(
                document.getElementById(
                    "toggleRegisterPassword"
                ),
                password,
                document.getElementById(
                    "registerEyeOpen"
                ),
                document.getElementById(
                    "registerEyeClosed"
                )
            );


            setupPasswordToggle(
                document.getElementById(
                    "toggleConfirmPassword"
                ),
                confirmationPassword,
                document.getElementById(
                    "confirmEyeOpen"
                ),
                document.getElementById(
                    "confirmEyeClosed"
                )
            );


            function validateName() {

                const valid =
                    name.value
                        .trim()
                        .length >=
                    2;


                setError(
                    name,
                    !valid
                );


                return valid;

            }



            function validateUsername() {

                const value =
                    username.value.trim();


                const pattern =
                    /^[a-zA-Z0-9._]+$/;


                const valid =
                    value.length >=
                    4 &&
                    pattern.test(
                        value
                    );


                setError(
                    username,
                    !valid
                );


                return valid;

            }



            function validatePassword() {

                const valid =
                    password.value
                        .length >=
                    8;


                setError(
                    password,
                    !valid
                );


                return valid;

            }



            function validateConfirmation() {

                const valid =
                    confirmationPassword.value !==
                        "" &&
                    confirmationPassword.value ===
                        password.value;


                setError(
                    confirmationPassword,
                    !valid
                );


                return valid;

            }



            function updatePasswordStrength() {

                const fill =
                    document.getElementById(
                        "passwordStrengthFill"
                    );


                const text =
                    document.getElementById(
                        "passwordStrengthText"
                    );


                if (
                    !fill ||
                    !text
                ) {

                    return;

                }


                const value =
                    password.value;


                let score =
                    0;


                if (
                    value.length >=
                    8
                ) {

                    score++;

                }


                if (
                    /[A-Z]/.test(
                        value
                    )
                ) {

                    score++;

                }


                if (
                    /[a-z]/.test(
                        value
                    )
                ) {

                    score++;

                }


                if (
                    /\d/.test(
                        value
                    )
                ) {

                    score++;

                }


                if (
                    /[^A-Za-z0-9]/.test(
                        value
                    )
                ) {

                    score++;

                }


                fill.className =
                    "password-strength-fill";


                if (
                    value.length ===
                    0
                ) {

                    fill.style.width =
                        "0%";


                    text.textContent =
                        "Belum ada password";


                } else if (
                    score <=
                    2
                ) {

                    fill.style.width =
                        "33%";


                    fill.classList.add(
                        "weak"
                    );


                    text.textContent =
                        "Password lemah";


                } else if (
                    score <=
                    4
                ) {

                    fill.style.width =
                        "66%";


                    fill.classList.add(
                        "medium"
                    );


                    text.textContent =
                        "Password cukup";


                } else {

                    fill.style.width =
                        "100%";


                    fill.classList.add(
                        "strong"
                    );


                    text.textContent =
                        "Password kuat";

                }

            }


            name.addEventListener(
                "input",
                validateName
            );


            username.addEventListener(
                "input",
                function () {

                    this.value =
                        this.value.replace(
                            /\s/g,
                            ""
                        );


                    validateUsername();

                }
            );


            email.addEventListener(
                "input",
                function () {

                    validateEmailField(
                        this
                    );

                }
            );


            password.addEventListener(
                "input",
                function () {

                    validatePassword();

                    updatePasswordStrength();


                    if (
                        confirmationPassword.value !==
                        ""
                    ) {

                        validateConfirmation();

                    }

                }
            );


            confirmationPassword
                .addEventListener(
                    "input",
                    validateConfirmation
                );


            if (
                agreement
            ) {

                agreement.addEventListener(
                    "change",
                    function () {

                        if (
                            this.checked &&
                            agreementError
                        ) {

                            agreementError
                                .classList
                                .remove(
                                    "active"
                                );

                        }

                    }
                );

            }


            adminRegisterForm.addEventListener(
                "submit",
                function (event) {

                    event.preventDefault();


                    const valid =
                        validateName() &&
                        validateUsername() &&
                        validateEmailField(
                            email
                        ) &&
                        validatePassword() &&
                        validateConfirmation();


                    if (
                        !valid
                    ) {

                        return;

                    }


                    if (
                        agreement &&
                        !agreement.checked
                    ) {

                        if (
                            agreementError
                        ) {

                            agreementError
                                .classList
                                .add(
                                    "active"
                                );

                        }


                        return;

                    }


                    console.log(
                        {
                            name:
                                name.value,

                            username:
                                username.value,

                            email:
                                email.value,

                            role:
                                "ADMIN",

                            status:
                                "PENDING"
                        }
                    );


                    if (
                        successModal
                    ) {

                        successModal
                            .classList
                            .add(
                                "active"
                            );

                    }

                }
            );


            if (
                registerToLogin
            ) {

                registerToLogin.addEventListener(
                    "click",
                    function () {

                        window.location.href =
                            "login.html";

                    }
                );

            }

        }


        /* =====================================
           ACCOUNT RECOVERY
        ===================================== */

        function initAccountRecovery() {

            const usernameTab =
                document.getElementById(
                    "usernameTab"
                );


            const passwordTab =
                document.getElementById(
                    "passwordTab"
                );


            const usernamePanel =
                document.getElementById(
                    "usernameRecoveryPanel"
                );


            const passwordPanel =
                document.getElementById(
                    "passwordRecoveryPanel"
                );


            const usernameEmail =
                document.getElementById(
                    "recovery_username_email"
                );


            const passwordEmail =
                document.getElementById(
                    "recovery_password_email"
                );


            const successModal =
                document.getElementById(
                    "recoverySuccessModal"
                );


            const successTitle =
                document.getElementById(
                    "recoverySuccessTitle"
                );


            const successMessage =
                document.getElementById(
                    "recoverySuccessMessage"
                );


            const backToLogin =
                document.getElementById(
                    "recoveryBackToLogin"
                );


            if (
                usernameTab
            ) {

                usernameTab.addEventListener(
                    "click",
                    function () {

                        usernameTab
                            .classList
                            .add(
                                "active"
                            );


                        passwordTab
                            .classList
                            .remove(
                                "active"
                            );


                        usernamePanel
                            .classList
                            .add(
                                "active"
                            );


                        passwordPanel
                            .classList
                            .remove(
                                "active"
                            );

                    }
                );

            }


            if (
                passwordTab
            ) {

                passwordTab.addEventListener(
                    "click",
                    function () {

                        passwordTab
                            .classList
                            .add(
                                "active"
                            );


                        usernameTab
                            .classList
                            .remove(
                                "active"
                            );


                        passwordPanel
                            .classList
                            .add(
                                "active"
                            );


                        usernamePanel
                            .classList
                            .remove(
                                "active"
                            );

                    }
                );

            }


            if (
                usernameRecoveryForm
            ) {

                usernameRecoveryForm
                    .addEventListener(
                        "submit",
                        function (event) {

                            event.preventDefault();


                            if (
                                !validateEmailField(
                                    usernameEmail
                                )
                            ) {

                                return;

                            }


                            successTitle.textContent =
                                "Username Dikirim";


                            successMessage.textContent =
                                "Jika email terdaftar, username akan dikirim ke alamat email tersebut.";


                            successModal
                                .classList
                                .add(
                                    "active"
                                );

                        }
                    );

            }


            if (
                passwordRecoveryForm
            ) {

                passwordRecoveryForm
                    .addEventListener(
                        "submit",
                        function (event) {

                            event.preventDefault();


                            if (
                                !validateEmailField(
                                    passwordEmail
                                )
                            ) {

                                return;

                            }


                            successTitle.textContent =
                                "Link Reset Dikirim";


                            successMessage.textContent =
                                "Jika email terdaftar, tautan reset password akan dikirim ke email tersebut.";


                            successModal
                                .classList
                                .add(
                                    "active"
                                );

                        }
                    );

            }


            if (
                backToLogin
            ) {

                backToLogin.addEventListener(
                    "click",
                    function () {

                        window.location.href =
                            "login.html";

                    }
                );

            }

        }


        /* =====================================
           DASHBOARD
        ===================================== */

        function initAdminDashboard() {

            const session =
                setAdminIdentity();


            const superAdminSection =
                document.getElementById(
                    "superAdminDashboardSection"
                );


            if (
                superAdminSection
            ) {

                if (
                    session.role ===
                    "SUPER_ADMIN"
                ) {

                    superAdminSection.classList.add(
                        "active"
                    );

                } else {

                    superAdminSection.classList.remove(
                        "active"
                    );

                }

            }


            /* =================================
               SIDEBAR NAVIGATION
            ================================= */

            const revisionMenu =
                document.getElementById(
                    "dashboardRevisionMenu"
                );


            const skMenu =
                document.getElementById(
                    "dashboardSkMenu"
                );


            const quickRevision =
                document.getElementById(
                    "dashboardQuickRevision"
                );


            const quickSk =
                document.getElementById(
                    "dashboardQuickSk"
                );


            function goToRevision() {

                window.location.assign(
                    "pengajuan.html?filter=revisi"
                );

            }


            function goToSkProcess() {

                window.location.assign(
                    "pengajuan.html?filter=proses-sk"
                );

            }


            if (
                revisionMenu
            ) {

                revisionMenu.addEventListener(
                    "click",
                    function (event) {

                        event.preventDefault();

                        event.stopPropagation();

                        goToRevision();

                    }
                );

            }


            if (
                skMenu
            ) {

                skMenu.addEventListener(
                    "click",
                    function (event) {

                        event.preventDefault();

                        event.stopPropagation();

                        goToSkProcess();

                    }
                );

            }


            if (
                quickRevision
            ) {

                quickRevision.addEventListener(
                    "click",
                    function (event) {

                        event.preventDefault();

                        goToRevision();

                    }
                );

            }


            if (
                quickSk
            ) {

                quickSk.addEventListener(
                    "click",
                    function (event) {

                        event.preventDefault();

                        goToSkProcess();

                    }
                );

            }


            /* =================================
               EXPORT
            ================================= */

            const quickExport =
                document.getElementById(
                    "quickExport"
                );


            const exportModal =
                document.getElementById(
                    "exportModal"
                );


            const closeExportModal =
                document.getElementById(
                    "closeExportModal"
                );


            if (
                quickExport &&
                exportModal
            ) {

                quickExport.addEventListener(
                    "click",
                    function () {

                        exportModal
                            .classList
                            .add(
                                "active"
                            );

                    }
                );

            }


            if (
                closeExportModal &&
                exportModal
            ) {

                closeExportModal
                    .addEventListener(
                        "click",
                        function () {

                            exportModal
                                .classList
                                .remove(
                                    "active"
                                );

                        }
                    );

            }

        }


        /* =====================================
           SUBMISSION LIST
        ===================================== */

        function initSubmissionPage() {

            const searchInput =
                document.getElementById(
                    "submissionSearch"
                );


            const departmentFilter =
                document.getElementById(
                    "submissionDepartment"
                );


            const statusFilter =
                document.getElementById(
                    "submissionStatus"
                );


            const applyButton =
                document.getElementById(
                    "applySubmissionFilter"
                );


            const resetButton =
                document.getElementById(
                    "resetSubmissionFilter"
                );


            const resultCount =
                document.getElementById(
                    "submissionResultCount"
                );


            const emptyState =
                document.getElementById(
                    "submissionEmptyState"
                );


            const activeFilterBox =
                document.getElementById(
                    "activeFilterBox"
                );


            const activeFilterText =
                document.getElementById(
                    "activeFilterText"
                );


            const exportButton =
                document.getElementById(
                    "submissionExportButton"
                );


            const exportModal =
                document.getElementById(
                    "submissionExportModal"
                );


            const closeExportModal =
                document.getElementById(
                    "closeSubmissionExportModal"
                );


            const rows =
                Array.from(
                    submissionTableBody.querySelectorAll(
                        "tr"
                    )
                );


            let appliedSearch =
                "";


            let appliedDepartment =
                "";


            let appliedStatus =
                "";


            let appliedProcessSK =
                false;


            function getDepartmentLabel(
                value
            ) {

                const labels = {

                    manajemen:
                        "Manajemen",

                    akuntansi:
                        "Akuntansi",

                    "ekonomi pembangunan":
                        "Ekonomi Pembangunan"

                };


                return (
                    labels[value] ||
                    ""
                );

            }



            function getStatusLabel(
                value
            ) {

                const labels = {

                    "menunggu verifikasi":
                        "Menunggu Verifikasi",

                    "perlu revisi":
                        "Perlu Revisi",

                    "revisi dikirim":
                        "Revisi Dikirim",

                    terverifikasi:
                        "Terverifikasi",

                    "pembuatan sk":
                        "Pembuatan SK",

                    "ttd wakil dekan":
                        "TTD Wakil Dekan",

                    "ttd dekan":
                        "TTD Dekan",

                    "sk terbit":
                        "SK Terbit"

                };


                return (
                    labels[value] ||
                    ""
                );

            }



            function isSKProcessStatus(
                status
            ) {

                return [

                    "pembuatan sk",
                    "ttd wakil dekan",
                    "ttd dekan"

                ].includes(
                    status
                );

            }



            function updateActiveFilterText() {

                const parts =
                    [];


                if (
                    appliedSearch !==
                    ""
                ) {

                    parts.push(
                        'Pencarian "' +
                        appliedSearch +
                        '"'
                    );

                }


                if (
                    appliedDepartment !==
                    ""
                ) {

                    parts.push(
                        "Jurusan " +
                        getDepartmentLabel(
                            appliedDepartment
                        )
                    );

                }


                if (
                    appliedStatus !==
                    ""
                ) {

                    parts.push(
                        "Status " +
                        getStatusLabel(
                            appliedStatus
                        )
                    );

                }


                if (
                    appliedProcessSK
                ) {

                    parts.push(
                        "Status Proses SK"
                    );

                }


                activeFilterText.textContent =
                    parts.length ===
                    0
                        ?
                        "Semua pengajuan"
                        :
                        parts.join(
                            " • "
                        );


                activeFilterBox.classList.add(
                    "active"
                );

            }



            function filterSubmissions() {

                let visibleCount =
                    0;


                rows.forEach(
                    function (row) {

                        const name =
                            (
                                row.dataset.name ||
                                ""
                            )
                                .toLowerCase();


                        const nim =
                            (
                                row.dataset.nim ||
                                ""
                            )
                                .toLowerCase();


                        const code =
                            (
                                row.dataset.code ||
                                ""
                            )
                                .toLowerCase();


                        const department =
                            (
                                row.dataset.department ||
                                ""
                            )
                                .toLowerCase();


                        const status =
                            (
                                row.dataset.status ||
                                ""
                            )
                                .toLowerCase();


                        const search =
                            appliedSearch
                                .toLowerCase();


                        const matchSearch =
                            search ===
                                "" ||
                            name.includes(
                                search
                            ) ||
                            nim.includes(
                                search
                            ) ||
                            code.includes(
                                search
                            );


                        const matchDepartment =
                            appliedDepartment ===
                                "" ||
                            department ===
                                appliedDepartment;


                        const matchStatus =
                            appliedStatus ===
                                "" ||
                            status ===
                                appliedStatus;


                        const matchProcess =
                            !appliedProcessSK ||
                            isSKProcessStatus(
                                status
                            );


                        const visible =
                            matchSearch &&
                            matchDepartment &&
                            matchStatus &&
                            matchProcess;


                        row.style.display =
                            visible
                                ?
                                ""
                                :
                                "none";


                        if (
                            visible
                        ) {

                            visibleCount++;

                        }

                    }
                );


                if (
                    resultCount
                ) {

                    resultCount.textContent =
                        "Menampilkan " +
                        visibleCount +
                        " dari " +
                        rows.length +
                        " pengajuan";

                }


                if (
                    emptyState
                ) {

                    emptyState.style.display =
                        visibleCount ===
                        0
                            ?
                            "flex"
                            :
                            "none";

                }


                updateActiveFilterText();

            }



            function applyFilter() {

                appliedSearch =
                    searchInput.value
                        .trim();


                appliedDepartment =
                    departmentFilter.value;


                appliedStatus =
                    statusFilter.value;


                appliedProcessSK =
                    false;


                filterSubmissions();

            }



            function resetFilter() {

                searchInput.value =
                    "";


                departmentFilter.value =
                    "";


                statusFilter.value =
                    "";


                appliedSearch =
                    "";


                appliedDepartment =
                    "";


                appliedStatus =
                    "";


                appliedProcessSK =
                    false;


                filterSubmissions();

            }


            if (
                applyButton
            ) {

                applyButton.addEventListener(
                    "click",
                    applyFilter
                );

            }


            if (
                resetButton
            ) {

                resetButton.addEventListener(
                    "click",
                    resetFilter
                );

            }


            if (
                searchInput
            ) {

                searchInput.addEventListener(
                    "keydown",
                    function (event) {

                        if (
                            event.key ===
                            "Enter"
                        ) {

                            event.preventDefault();

                            applyFilter();

                        }

                    }
                );

            }


            /* =================================
               QUERY PARAMETER
            ================================= */

            const params =
                new URLSearchParams(
                    window.location.search
                );


            const pageFilter =
                params.get(
                    "filter"
                );


            if (
                pageFilter ===
                "revisi"
            ) {

                statusFilter.value =
                    "perlu revisi";


                appliedStatus =
                    "perlu revisi";


            } else if (
                pageFilter ===
                "menunggu"
            ) {

                statusFilter.value =
                    "menunggu verifikasi";


                appliedStatus =
                    "menunggu verifikasi";


            } else if (
                pageFilter ===
                "terverifikasi"
            ) {

                statusFilter.value =
                    "terverifikasi";


                appliedStatus =
                    "terverifikasi";


            } else if (
                pageFilter ===
                "proses-sk"
            ) {

                statusFilter.value =
                    "";


                appliedStatus =
                    "";


                appliedProcessSK =
                    true;

            }


            filterSubmissions();


            /* =================================
               EXPORT
            ================================= */

            if (
                exportButton &&
                exportModal
            ) {

                exportButton.addEventListener(
                    "click",
                    function () {

                        exportModal.classList.add(
                            "active"
                        );

                    }
                );

            }


            if (
                closeExportModal &&
                exportModal
            ) {

                closeExportModal.addEventListener(
                    "click",
                    function () {

                        exportModal.classList.remove(
                            "active"
                        );

                    }
                );

            }

        }

    }
);