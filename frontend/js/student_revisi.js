document.addEventListener("DOMContentLoaded", function () {

    console.log("Halaman revisi aktif");


    /* =========================================================
       DATA TRACKING
    ========================================================= */

    const kode =
        sessionStorage.getItem(
            "tracking_kode"
        );

    const nim =
        sessionStorage.getItem(
            "tracking_nim"
        );


    /*
     * Kalau halaman revisi dibuka langsung
     * tanpa proses tracking sebelumnya,
     * kembalikan mahasiswa ke halaman cek status.
     */
    if (!kode || !nim) {

        window.location.href =
            "tracking.html";

        return;

    }


    /* =========================================================
       ELEMENT
    ========================================================= */

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


    const revisionFileStatus =
        document.getElementById(
            "revisionFileStatus"
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


    /* =========================================================
       TAMPILKAN IDENTITAS
    ========================================================= */

    if (revisionKode) {

        revisionKode.textContent =
            kode;

    }


    if (revisionNim) {

        revisionNim.textContent =
            nim;

    }


    if (lockedNim) {

        lockedNim.textContent =
            nim;

    }


    /* =========================================================
       ERROR STATE
    ========================================================= */

    function setError(
        field,
        hasError
    ) {

        if (!field) {

            return;

        }


        const formGroup =
            field.closest(
                ".form-group"
            );


        field.classList.toggle(
            "form-control-error",
            hasError
        );


        if (formGroup) {

            formGroup.classList.toggle(
                "has-error",
                hasError
            );

        }

    }


    /* =========================================================
       VALIDASI TEXT
    ========================================================= */

    function validateText(field) {

        if (!field) {

            return false;

        }


        const valid =
            field.value
                .trim() !== "";


        setError(
            field,
            !valid
        );


        return valid;

    }


    /* =========================================================
       FORMAT FILE SIZE
    ========================================================= */

    function formatFileSize(bytes) {

        const kb =
            bytes / 1024;


        if (kb >= 1024) {

            return (
                kb / 1024
            ).toFixed(2) +
                " MB";

        }


        return (
            Math.round(kb) +
            " KB"
        );

    }


    /* =========================================================
       STATUS FILE
    ========================================================= */

    function updateFileStatus(
        file,
        valid = true
    ) {

        if (!revisionFileStatus) {

            return;

        }


        revisionFileStatus
            .classList
            .remove(
                "has-file"
            );


        if (!file) {

            revisionFileStatus.textContent =
                "Belum ada file dipilih";

            return;

        }


        if (!valid) {

            revisionFileStatus.textContent =
                file.name +
                " — file tidak sesuai ketentuan";

            return;

        }


        revisionFileStatus.textContent =
            "✓ " +
            file.name +
            " • " +
            formatFileSize(
                file.size
            );


        revisionFileStatus
            .classList
            .add(
                "has-file"
            );

    }


    /* =========================================================
       VALIDASI FILE
    ========================================================= */

    function validateFile(field) {

        if (!field) {

            return false;

        }


        const file =
            field.files &&
            field.files[0]
                ? field.files[0]
                : null;


        if (!file) {

            setError(
                field,
                true
            );


            updateFileStatus(
                null
            );


            return false;

        }


        const fileName =
            file.name
                .toLowerCase();


        const maxSizeMb =
            parseFloat(
                field.dataset.maxSize ||
                "1"
            );


        const maxSizeBytes =
            maxSizeMb *
            1024 *
            1024;


        const valid =
            fileName.endsWith(
                ".pdf"
            ) &&
            file.size <=
                maxSizeBytes;


        setError(
            field,
            !valid
        );


        updateFileStatus(
            file,
            valid
        );


        return valid;

    }


    /* =========================================================
       EVENT INPUT JUDUL
    ========================================================= */

    if (revisiJudul) {

        revisiJudul.addEventListener(
            "input",
            function () {

                validateText(
                    this
                );

            }
        );

    }


    /* =========================================================
       EVENT FILE
    ========================================================= */

    if (revisiRekapNilai) {

        revisiRekapNilai.addEventListener(
            "change",
            function () {

                validateFile(
                    this
                );

            }
        );

    }


    /* =========================================================
       CHECKBOX KONFIRMASI
    ========================================================= */

    if (revisionConfirmation) {

        revisionConfirmation
            .addEventListener(
                "change",
                function () {

                    if (
                        this.checked &&
                        revisionConfirmationError
                    ) {

                        revisionConfirmationError
                            .classList
                            .remove(
                                "active"
                            );

                    }

                }
            );

    }


    /* =========================================================
       SUBMIT REVISI
    ========================================================= */

    if (revisionForm) {

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


                /*
                 * Kalau ada field revisi
                 * yang belum valid.
                 */
                if (
                    !judulValid ||
                    !fileValid
                ) {

                    const firstInvalid =
                        revisionForm
                            .querySelector(
                                ".form-control-error"
                            );


                    if (firstInvalid) {

                        firstInvalid
                            .scrollIntoView({
                                behavior:
                                    "smooth",

                                block:
                                    "center"
                            });


                        firstInvalid.focus();

                    }


                    return;

                }


                /*
                 * Wajib konfirmasi.
                 */
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


                /*
                 * SIMULASI FRONTEND
                 *
                 * Nanti ketika Laravel sudah aktif,
                 * bagian ini akan diganti request
                 * POST ke backend.
                 */
                sessionStorage.setItem(
                    "revision_submitted",
                    "true"
                );


                sessionStorage.setItem(
                    "revision_submitted_at",
                    new Date()
                        .toISOString()
                );


                /*
                 * Tampilkan modal berhasil.
                 */
                if (
                    revisionSuccessModal
                ) {

                    revisionSuccessModal
                        .classList
                        .add(
                            "active"
                        );


                    document.body
                        .style
                        .overflow =
                            "hidden";

                }

            }
        );

    }


    /* =========================================================
       KEMBALI KE DETAIL STATUS
    ========================================================= */

    if (closeRevisionModal) {

        closeRevisionModal
            .addEventListener(
                "click",
                function () {

                    window.location.href =
                        "detail_tracking.html";

                }
            );

    }


    /* =========================================================
       CLICK DI LUAR MODAL
    ========================================================= */

    if (revisionSuccessModal) {

        revisionSuccessModal
            .addEventListener(
                "click",
                function (event) {

                    if (
                        event.target ===
                        revisionSuccessModal
                    ) {

                        revisionSuccessModal
                            .classList
                            .remove(
                                "active"
                            );


                        document.body
                            .style
                            .overflow =
                                "";

                    }

                }
            );

    }


    /* =========================================================
       ESC MODAL
    ========================================================= */

    document.addEventListener(
        "keydown",
        function (event) {

            if (
                event.key ===
                    "Escape" &&
                revisionSuccessModal &&
                revisionSuccessModal
                    .classList
                    .contains(
                        "active"
                    )
            ) {

                revisionSuccessModal
                    .classList
                    .remove(
                        "active"
                    );


                document.body
                    .style
                    .overflow =
                        "";

            }

        }
    );

});