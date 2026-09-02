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


        if (adminLoginForm) {

            initAdminLogin();

        }


        if (adminRegisterForm) {

            initAdminRegister();

        }


        if (
            usernameRecoveryForm ||
            passwordRecoveryForm
        ) {

            initAccountRecovery();

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


            if (hasError) {

                field.classList.add(
                    "form-control-error"
                );


                if (formGroup) {

                    formGroup.classList.add(
                        "has-error"
                    );

                }

            } else {

                field.classList.remove(
                    "form-control-error"
                );


                if (formGroup) {

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
                field.value
                    .trim();


            const pattern =
                /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


            const valid =
                pattern.test(value);


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

            button.addEventListener(
                "click",
                function () {

                    const visible =
                        input.type ===
                        "text";


                    if (visible) {

                        input.type =
                            "password";


                        openIcon
                            .classList
                            .remove(
                                "hidden"
                            );


                        closedIcon
                            .classList
                            .add(
                                "hidden"
                            );


                        button.setAttribute(
                            "aria-label",
                            "Tampilkan password"
                        );


                        button.setAttribute(
                            "title",
                            "Tampilkan password"
                        );

                    } else {

                        input.type =
                            "text";


                        openIcon
                            .classList
                            .add(
                                "hidden"
                            );


                        closedIcon
                            .classList
                            .remove(
                                "hidden"
                            );


                        button.setAttribute(
                            "aria-label",
                            "Sembunyikan password"
                        );


                        button.setAttribute(
                            "title",
                            "Sembunyikan password"
                        );

                    }

                }
            );

        }



        /* =====================================
           LOGIN PAGE
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


            const togglePassword =
                document.getElementById(
                    "togglePassword"
                );


            const eyeOpenIcon =
                document.getElementById(
                    "eyeOpenIcon"
                );


            const eyeClosedIcon =
                document.getElementById(
                    "eyeClosedIcon"
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
                        .trim() !== "";


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
                togglePassword,
                password,
                eyeOpenIcon,
                eyeClosedIcon
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


                    loading
                        .classList
                        .add(
                            "active"
                        );


                    sessionStorage.setItem(
                        "admin_logged_in",
                        "true"
                    );


                    sessionStorage.setItem(
                        "admin_username",
                        username.value
                    );


                    setTimeout(
                        function () {

                            window.location.href =
                                "dashboard.html";

                        },
                        700
                    );

                }
            );

        }



        /* =====================================
           REGISTER PAGE
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


            const passwordConfirmation =
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

                passwordConfirmation,

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
                        .length >= 2;


                setError(
                    name,
                    !valid
                );


                return valid;

            }



            function validateUsername() {

                const value =
                    username.value
                        .trim();


                const pattern =
                    /^[a-zA-Z0-9._]+$/;


                const valid =
                    value.length >= 4 &&
                    pattern.test(value);


                setError(
                    username,
                    !valid
                );


                return valid;

            }



            function validateEmail() {

                return validateEmailField(
                    email
                );

            }



            function calculatePasswordStrength(
                value
            ) {

                let score = 0;


                if (
                    value.length >= 8
                ) {

                    score++;

                }


                if (
                    /[A-Z]/.test(value)
                ) {

                    score++;

                }


                if (
                    /[a-z]/.test(value)
                ) {

                    score++;

                }


                if (
                    /\d/.test(value)
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


                return score;

            }



            function updatePasswordStrength() {

                const value =
                    password.value;


                const fill =
                    document.getElementById(
                        "passwordStrengthFill"
                    );


                const text =
                    document.getElementById(
                        "passwordStrengthText"
                    );


                const score =
                    calculatePasswordStrength(
                        value
                    );


                fill.className =
                    "password-strength-fill";


                if (
                    value.length === 0
                ) {

                    fill.style.width =
                        "0%";


                    text.textContent =
                        "Belum ada password";


                    return;

                }


                if (score <= 2) {

                    fill.style.width =
                        "33%";


                    fill.classList.add(
                        "weak"
                    );


                    text.textContent =
                        "Password lemah";

                } else if (
                    score <= 4
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



            function validatePassword() {

                const valid =
                    password.value
                        .length >= 8;


                setError(
                    password,
                    !valid
                );


                return valid;

            }



            function validateConfirmation() {

                const valid =
                    passwordConfirmation
                        .value !== "" &&
                    passwordConfirmation
                        .value ===
                    password.value;


                setError(
                    passwordConfirmation,
                    !valid
                );


                return valid;

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
                validateEmail
            );


            password.addEventListener(
                "input",
                function () {

                    validatePassword();

                    updatePasswordStrength();


                    if (
                        passwordConfirmation
                            .value !== ""
                    ) {

                        validateConfirmation();

                    }

                }
            );


            passwordConfirmation
                .addEventListener(
                    "input",
                    validateConfirmation
                );


            agreement.addEventListener(
                "change",
                function () {

                    if (
                        this.checked
                    ) {

                        agreementError
                            .classList
                            .remove(
                                "active"
                            );

                    }

                }
            );


            adminRegisterForm.addEventListener(
                "submit",
                function (event) {

                    event.preventDefault();


                    const nameValid =
                        validateName();


                    const usernameValid =
                        validateUsername();


                    const emailValid =
                        validateEmail();


                    const passwordValid =
                        validatePassword();


                    const confirmationValid =
                        validateConfirmation();


                    if (
                        !nameValid ||
                        !usernameValid ||
                        !emailValid ||
                        !passwordValid ||
                        !confirmationValid
                    ) {

                        return;

                    }


                    if (
                        !agreement.checked
                    ) {

                        agreementError
                            .classList
                            .add(
                                "active"
                            );


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


                    successModal
                        .classList
                        .add(
                            "active"
                        );

                }
            );


            registerToLogin.addEventListener(
                "click",
                function () {

                    window.location.href =
                        "login.html";

                }
            );

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


            /* ===============================
               TAB SWITCH
            =============================== */

            function showUsernameRecovery() {

                usernameTab
                    .classList
                    .add("active");


                passwordTab
                    .classList
                    .remove("active");


                usernamePanel
                    .classList
                    .add("active");


                passwordPanel
                    .classList
                    .remove("active");

            }



            function showPasswordRecovery() {

                passwordTab
                    .classList
                    .add("active");


                usernameTab
                    .classList
                    .remove("active");


                passwordPanel
                    .classList
                    .add("active");


                usernamePanel
                    .classList
                    .remove("active");

            }


            usernameTab.addEventListener(
                "click",
                showUsernameRecovery
            );


            passwordTab.addEventListener(
                "click",
                showPasswordRecovery
            );


            /* ===============================
               USERNAME EMAIL
            =============================== */

            usernameEmail.addEventListener(
                "input",
                function () {

                    validateEmailField(
                        this
                    );

                }
            );


            /* ===============================
               PASSWORD EMAIL
            =============================== */

            passwordEmail.addEventListener(
                "input",
                function () {

                    validateEmailField(
                        this
                    );

                }
            );


            /* ===============================
               USERNAME SUBMIT
            =============================== */

            usernameRecoveryForm.addEventListener(
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


                    /*
                     * Simulasi frontend.
                     *
                     * Nanti Laravel mencari user
                     * berdasarkan email dan
                     * mengirim username.
                     */

                    successTitle.textContent =
                        "Username Dikirim";


                    successMessage.textContent =
                        "Jika email terdaftar pada sistem, username akun akan dikirim ke alamat email tersebut.";


                    successModal
                        .classList
                        .add(
                            "active"
                        );

                }
            );


            /* ===============================
               PASSWORD SUBMIT
            =============================== */

            passwordRecoveryForm.addEventListener(
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


                    /*
                     * Simulasi frontend.
                     *
                     * Nanti Laravel membuat
                     * token password reset.
                     */

                    successTitle.textContent =
                        "Link Reset Dikirim";


                    successMessage.textContent =
                        "Jika email terdaftar pada sistem, tautan untuk membuat password baru akan dikirim ke email tersebut.";


                    successModal
                        .classList
                        .add(
                            "active"
                        );

                }
            );


            backToLogin.addEventListener(
                "click",
                function () {

                    window.location.href =
                        "login.html";

                }
            );

        }

    }
);