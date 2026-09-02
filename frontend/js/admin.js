document.addEventListener(
    "DOMContentLoaded",
    function () {

        console.log(
            "Admin frontend aktif"
        );


        /* =====================================
           LOGIN PAGE
        ===================================== */

        const adminLoginForm =
            document.getElementById(
                "adminLoginForm"
            );


        if (adminLoginForm) {

            initAdminLogin();

        }


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


            const loading =
                document.getElementById(
                    "adminLoginLoading"
                );


            /* ===============================
               SET ERROR
            =============================== */

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


            /* ===============================
               VALIDASI
            =============================== */

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


            /* ===============================
               SHOW PASSWORD
            =============================== */

            togglePassword.addEventListener(
                "click",
                function () {

                    if (
                        password.type ===
                        "password"
                    ) {

                        password.type =
                            "text";

                        togglePassword.textContent =
                            "Sembunyikan";

                    } else {

                        password.type =
                            "password";

                        togglePassword.textContent =
                            "Lihat";

                    }

                }
            );


            /* ===============================
               LOGIN SUBMIT
            =============================== */

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


                    /*
                     * Simulasi login frontend.
                     *
                     * Nanti Laravel akan:
                     * - cek username
                     * - cek password
                     * - buat session auth
                     */

                    loading
                        .classList
                        .add("active");


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

    }
);