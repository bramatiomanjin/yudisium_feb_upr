document.addEventListener("DOMContentLoaded", function () {
    "use strict";

    if (!window.YudisiumAPI) {
        console.error("YudisiumAPI tidak ditemukan.");
        return;
    }

    const API = window.YudisiumAPI;

    const form = document.getElementById("formYudisium");

    if (!form) {
        return;
    }

    const confirmation = document.getElementById("konfirmasiData");
    const confirmationError = document.getElementById("confirmationError");
    const submitButton = document.getElementById("submitPengajuan");

    const successModal = document.getElementById("successModal");
    const generatedCode = document.getElementById("generatedSubmissionCode");
    const copyFeedback = document.getElementById("copyCodeFeedback");
    const goToTracking = document.getElementById("goToTracking");


    /* =========================================================
       SHOW STEP
    ========================================================= */

    function showStepForField(field) {
        const step =
            field?.closest(".form-step") ||
            field?.closest("[id^='step']");

        if (!step) {
            return;
        }

        const match = step.id.match(/step(\d+)/);

        if (!match) {
            return;
        }

        const number = Number(match[1]);

        document
            .querySelectorAll(".form-step")
            .forEach(function (item) {
                item.classList.remove("active");
            });

        step.classList.add("active");

        for (let i = 1; i <= 4; i++) {
            const indicator = document.getElementById(
                "indicatorStep" + i
            );

            if (!indicator) {
                continue;
            }

            indicator.classList.toggle(
                "active",
                i <= number
            );
        }

        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    }


    /* =========================================================
       ERROR STATE
    ========================================================= */

    function setFieldError(field, hasError) {
        if (!field) {
            return;
        }

        const group = field.closest(".form-group");

        field.classList.toggle(
            "form-control-error",
            hasError
        );

        group?.classList.toggle(
            "has-error",
            hasError
        );
    }


    /* =========================================================
       FILE VALIDATION
    ========================================================= */

    function getExtension(filename) {
        const parts = String(filename || "")
            .toLowerCase()
            .split(".");

        return parts.length > 1
            ? parts.pop()
            : "";
    }


    function validateFileInput(input) {
        /*
         * File optional dan kosong.
         */
        if (
            !input.required &&
            (
                !input.files ||
                !input.files.length
            )
        ) {
            setFieldError(input, false);
            return true;
        }

        const file = input.files?.[0];

        if (!file) {
            setFieldError(input, true);
            return false;
        }

        const maxSizeMb = Number(
            input.dataset.maxSize || 0
        );

        if (
            maxSizeMb > 0 &&
            file.size > maxSizeMb * 1024 * 1024
        ) {
            setFieldError(input, true);
            return false;
        }

        const accept = String(
            input.getAttribute("accept") || ""
        ).toLowerCase();

        const extension = getExtension(file.name);

        if (accept && extension) {
            const acceptsPdf = accept.includes(".pdf");
            const acceptsDoc = accept.includes(".doc");
            const acceptsDocx = accept.includes(".docx");
            const acceptsJpg = accept.includes(".jpg") || accept.includes(".jpeg");
            const acceptsPng = accept.includes(".png");

            const extensionAllowed =
                (extension === "pdf" && acceptsPdf) ||
                (extension === "doc" && acceptsDoc) ||
                (extension === "docx" && acceptsDocx) ||
                ((extension === "jpg" || extension === "jpeg") && acceptsJpg) ||
                (extension === "png" && acceptsPng);

            if (!extensionAllowed) {
                setFieldError(input, true);
                return false;
            }
        }

        setFieldError(input, false);
        return true;
    }


    /* =========================================================
       FORM VALIDATION
    ========================================================= */

    function validateForm() {
        let firstInvalid = null;
        let valid = true;

        const fields = Array.from(
            form.querySelectorAll(
                "input, select, textarea"
            )
        );

        fields.forEach(function (field) {
            /*
             * Input dari dokumen jurusan yang sedang
             * disembunyikan tidak perlu divalidasi.
             */
            const hiddenContainer =
                field.closest(
                    '[style*="display: none"]'
                );

            if (hiddenContainer) {
                return;
            }

            let fieldValid = true;

            if (field.type === "file") {
                fieldValid =
                    validateFileInput(field);
            } else if (field.required) {
                fieldValid =
                    field.checkValidity();
            }

            if (!fieldValid) {
                valid = false;

                setFieldError(
                    field,
                    true
                );

                if (!firstInvalid) {
                    firstInvalid = field;
                }
            } else if (
                field.type !== "file"
            ) {
                setFieldError(
                    field,
                    false
                );
            }
        });

        if (!confirmation?.checked) {
            valid = false;

            confirmationError
                ?.classList
                .add("active");

            if (!firstInvalid) {
                firstInvalid = confirmation;
            }
        } else {
            confirmationError
                ?.classList
                .remove("active");
        }

        if (firstInvalid) {
            showStepForField(
                firstInvalid
            );

            firstInvalid.focus?.();
        }

        return valid;
    }


    /* =========================================================
       FORM DATA
    ========================================================= */

    function buildFormData() {
        const formData =
            new FormData(form);

        /*
         * Checkbox konfirmasi hanya untuk UI.
         */
        formData.delete(
            "konfirmasiData"
        );

        return formData;
    }


    /* =========================================================
       SUBMIT PENGAJUAN
    ========================================================= */

    form.addEventListener(
        "submit",
        async function (event) {
            event.preventDefault();

            event.stopImmediatePropagation();

            if (!validateForm()) {
                return;
            }

            /*
             * Pastikan backend aktif.
             */
            if (
                !API.config
                    .backendConnected
            ) {
                alert(
                    "Sistem pengajuan belum terhubung ke server. Data belum dikirim."
                );

                return;
            }

            try {
                /*
                 * Disable tombol saat proses.
                 */
                if (submitButton) {
                    submitButton.disabled = true;

                    submitButton.dataset.originalText =
                        submitButton.textContent;

                    submitButton.textContent =
                        "Mengirim Pengajuan...";
                }

                /*
                 * Kirim pengajuan ke backend Laravel.
                 */
                const response =
                    await API.submitApplication(
                        buildFormData()
                    );

                /*
                 * Backend wajib mengembalikan kode pengajuan.
                 */
                if (
                    !response ||
                    !response.code
                ) {
                    throw new Error(
                        "Backend tidak mengembalikan Kode SK Yudisium."
                    );
                }

                const submissionCode =
                    response.code;

                const studentNim =
                    response.nim ||
                    document
                        .getElementById("nim")
                        ?.value
                        .trim() ||
                    "";

                /*
                 * Tampilkan kode pada modal sukses.
                 */
                if (generatedCode) {
                    generatedCode.textContent =
                        submissionCode;
                }

                /*
                 * Simpan sementara agar bisa membantu
                 * proses tracking berikutnya.
                 */
                sessionStorage.setItem(
                    "last_submission_code",
                    submissionCode
                );

                sessionStorage.setItem(
                    "last_submission_nim",
                    studentNim
                );

                if (response.id) {
                    sessionStorage.setItem(
                        "last_submission_id",
                        String(response.id)
                    );
                }

                /*
                 * Informasi untuk mahasiswa.
                 */
                if (copyFeedback) {
                    copyFeedback.textContent =
                        "Simpan Kode SK Yudisium ini. Kode dan NIM diperlukan untuk melakukan tracking pengajuan.";
                }

                /*
                 * Tampilkan modal sukses.
                 */
                if (successModal) {
                    successModal.classList.add(
                        "active"
                    );

                    document.body.style.overflow =
                        "hidden";
                } else {
                    /*
                     * Fallback jika modal ternyata tidak ada.
                     */
                    alert(
                        "Pengajuan berhasil dikirim. Kode SK Yudisium Anda: " +
                        submissionCode
                    );
                }

                console.log(
                    "Pengajuan berhasil dikirim ke backend:",
                    response
                );

            } catch (error) {
                console.error(
                    "Pengajuan gagal:",
                    error
                );

                alert(
                    error?.message ||
                    "Pengajuan gagal dikirim. Silakan coba kembali."
                );

            } finally {
                if (submitButton) {
                    submitButton.disabled =
                        false;

                    submitButton.textContent =
                        submitButton.dataset
                            .originalText ||
                        "Kirim Pengajuan";
                }
            }
        },
        true
    );


    /* =========================================================
       TOMBOL CEK STATUS DARI MODAL SUKSES
    ========================================================= */

    goToTracking
        ?.addEventListener(
            "click",
            function (event) {
                event.preventDefault();

                event.stopImmediatePropagation();

                const code =
                    generatedCode
                        ?.textContent
                        .trim();

                const studentNim =
                    document
                        .getElementById("nim")
                        ?.value
                        .trim();

                /*
                 * Simpan ulang untuk memastikan
                 * data tracking tersedia.
                 */
                if (
                    code &&
                    code !== "-"
                ) {
                    sessionStorage.setItem(
                        "last_submission_code",
                        code
                    );
                }

                if (studentNim) {
                    sessionStorage.setItem(
                        "last_submission_nim",
                        studentNim
                    );
                }

                /*
                 * Mahasiswa tetap masuk melalui halaman
                 * NIM + Kode SK, bukan langsung detail.
                 */
                window.location.href =
                    "/tracking";
            },
            true
        );


    console.log(
        "Student Pengajuan API Bridge aktif."
    );
});