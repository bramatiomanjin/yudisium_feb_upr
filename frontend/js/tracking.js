document.addEventListener(
    "DOMContentLoaded",
    function () {

        console.log(
            "Tracking Yudisium aktif"
        );


        /* =====================================
           ELEMENT
        ===================================== */

        const trackingForm =
            document.getElementById(
                "trackingForm"
            );


        const trackingNim =
            document.getElementById(
                "tracking_nim"
            );


        const kodePengajuan =
            document.getElementById(
                "kode_pengajuan"
            );


        const trackingLoading =
            document.getElementById(
                "trackingLoading"
            );


        /* =====================================
           SET ERROR
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

                field
                    .classList
                    .add(
                        "form-control-error"
                    );


                if (formGroup) {

                    formGroup
                        .classList
                        .add(
                            "has-error"
                        );

                }

            } else {

                field
                    .classList
                    .remove(
                        "form-control-error"
                    );


                if (formGroup) {

                    formGroup
                        .classList
                        .remove(
                            "has-error"
                        );

                }

            }

        }


        /* =====================================
           VALIDASI NIM
        ===================================== */

        function validateNim() {

            const nim =
                trackingNim
                    .value
                    .trim();


            let valid = true;


            if (nim === "") {

                valid = false;

            }


            if (
                nim !== "" &&
                !/^\d+$/.test(nim)
            ) {

                valid = false;

            }


            setError(
                trackingNim,
                !valid
            );


            return valid;

        }


        /* =====================================
           VALIDASI KODE
        ===================================== */

        function validateKode() {

            const kode =
                kodePengajuan
                    .value
                    .trim();


            let valid = true;


            if (kode === "") {

                valid = false;

            }


            setError(
                kodePengajuan,
                !valid
            );


            return valid;

        }


        /* =====================================
           NIM HANYA ANGKA
        ===================================== */

        trackingNim.addEventListener(
            "input",
            function () {

                this.value =
                    this.value.replace(
                        /\D/g,
                        ""
                    );


                validateNim();

            }
        );


        /* =====================================
           KODE PENGAJUAN
        ===================================== */

        kodePengajuan.addEventListener(
            "input",
            function () {

                /*
                 * Kode otomatis dibuat uppercase.
                 */
                this.value =
                    this.value
                        .toUpperCase();


                validateKode();

            }
        );


        /* =====================================
           SUBMIT
        ===================================== */

        trackingForm.addEventListener(
            "submit",
            function (event) {

                event.preventDefault();


                const nimValid =
                    validateNim();


                const kodeValid =
                    validateKode();


                if (
                    !nimValid ||
                    !kodeValid
                ) {

                    return;

                }


                /*
                 * Simulasi pencarian backend.
                 */
                trackingLoading
                    .classList
                    .add("active");


                /*
                 * Data disimpan sementara
                 * hanya untuk testing frontend.
                 *
                 * Nanti diganti oleh Laravel.
                 */
                sessionStorage.setItem(
                    "tracking_nim",
                    trackingNim.value
                );


                sessionStorage.setItem(
                    "tracking_kode",
                    kodePengajuan.value
                );


                setTimeout(
                    function () {

                        window.location.href =
                            "detail_tracking.html";

                    },
                    800
                );

            }
        );

    }
);