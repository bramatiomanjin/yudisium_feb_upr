document.addEventListener(
    "DOMContentLoaded",
    function () {

        console.log(
            "Halaman revisi aktif"
        );


        /* =====================================
           SESSION DATA
        ===================================== */

        const kode =
            sessionStorage.getItem(
                "tracking_kode"
            );


        const nim =
            sessionStorage.getItem(
                "tracking_nim"
            );


        /*
         * Kalau halaman dibuka langsung
         * tanpa data tracking.
         */
        if (
            !kode ||
            !nim
        ) {

            window.location.href =
                "tracking.html";

            return;

        }


        /* =====================================
           ELEMENT
        ===================================== */

        const revisionForm =
            document.getElementById(
                "revisionForm"
            );


        const revisionKode =
            document.getElementById(
                "revisionKode"
            );


        const revisionNim =
            document.getElementById(
                "revisionNim"
            );


        const lockedNim =
            document.getElementById(
                "lockedNim"
            );


        const revisiJudul =
            document.getElementById(
                "revisi_judul"
            );


        const revisiRekapNilai =
            document.getElementById(
                "revisi_rekap_nilai"
            );


        const revisionConfirmation =
            document.getElementById(
                "revisionConfirmation"
            );


        const revisionConfirmationError =
            document.getElementById(
                "revisionConfirmationError"
            );


        const revisionSuccessModal =
            document.getElementById(
                "revisionSuccessModal"
            );


        const closeRevisionModal =
            document.getElementById(
                "closeRevisionModal"
            );


        /* =====================================
           DISPLAY DATA
        ===================================== */

        revisionKode.textContent =
            kode;


        revisionNim.textContent =
            nim;


        lockedNim.textContent =
            nim;


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
           VALIDASI TEXT
        ===================================== */

        function validateText(
            field
        ) {

            const value =
                field.value.trim();


            const valid =
                value !== "";


            setError(
                field,
                !valid
            );


            return valid;

        }


        /* =====================================
           VALIDASI FILE
        ===================================== */

        function validateFile(
            field
        ) {

            const file =
                field.files[0];


            if (!file) {

                setError(
                    field,
                    true
                );

                return false;

            }


            const fileName =
                file.name
                    .toLowerCase();


            /*
             * PDF only.
             */
            if (
                !fileName.endsWith(
                    ".pdf"
                )
            ) {

                setError(
                    field,
                    true
                );

                return false;

            }


            /*
             * Ukuran maksimal.
             */
            const maxSizeMb =
                parseFloat(
                    field.dataset
                        .maxSize || "1"
                );


            const maxSizeBytes =
                maxSizeMb *
                1024 *
                1024;


            if (
                file.size >
                maxSizeBytes
            ) {

                setError(
                    field,
                    true
                );

                return false;

            }


            setError(
                field,
                false
            );


            return true;

        }


        /* =====================================
           JUDUL
        ===================================== */

        revisiJudul.addEventListener(
            "input",
            function () {

                validateText(
                    this
                );

            }
        );


        /* =====================================
           FILE
        ===================================== */

        revisiRekapNilai.addEventListener(
            "change",
            function () {

                validateFile(
                    this
                );

            }
        );


        /* =====================================
           CHECKBOX
        ===================================== */

        revisionConfirmation
            .addEventListener(
                "change",
                function () {

                    if (
                        this.checked
                    ) {

                        revisionConfirmationError
                            .classList
                            .remove(
                                "active"
                            );

                    }

                }
            );


        /* =====================================
           SUBMIT
        ===================================== */

        revisionForm.addEventListener(
            "submit",
            function (event) {

                event.preventDefault();


                const judulValid =
                    validateText(
                        revisiJudul
                    );


                const fileValid =
                    validateFile(
                        revisiRekapNilai
                    );


                if (
                    !judulValid ||
                    !fileValid
                ) {

                    return;

                }


                if (
                    !revisionConfirmation
                        .checked
                ) {

                    revisionConfirmationError
                        .classList
                        .add(
                            "active"
                        );


                    revisionConfirmation
                        .focus();


                    return;

                }


                /*
                 * Simulasi data revisi.
                 *
                 * Nanti Laravel akan:
                 *
                 * UPDATE judul_karya_tulis
                 *
                 * UPDATE pengajuan_dokumen
                 *
                 * INSERT riwayat_revisi
                 *
                 * UPDATE validasi_field menjadi PENDING
                 *
                 * UPDATE status pengajuan menjadi
                 * REVISI_DIKIRIM
                 */


                console.log(
                    "Revisi judul:",
                    revisiJudul.value
                );


                console.log(
                    "Revisi dokumen:",
                    revisiRekapNilai
                        .files[0]
                        .name
                );


                revisionSuccessModal
                    .classList
                    .add(
                        "active"
                    );

            }
        );


        /* =====================================
           CLOSE SUCCESS
        ===================================== */

        closeRevisionModal.addEventListener(
            "click",
            function () {

                /*
                 * Untuk simulasi frontend.
                 */
                sessionStorage.setItem(
                    "revision_submitted",
                    "true"
                );


                window.location.href =
                    "detail_tracking.html";

            }
        );

    }
);