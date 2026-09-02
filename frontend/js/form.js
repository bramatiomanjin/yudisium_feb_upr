document.addEventListener("DOMContentLoaded", function () {

    console.log("JavaScript Yudisium aktif");


    /* =========================================
       STEP
    ========================================= */

    const step1 = document.getElementById("step1");
    const step2 = document.getElementById("step2");
    const step3 = document.getElementById("step3");
    const step4 = document.getElementById("step4");


    const steps = [
        step1,
        step2,
        step3,
        step4
    ];


    /* =========================================
       INDICATOR
    ========================================= */

    const indicators = [
        document.getElementById("indicatorStep1"),
        document.getElementById("indicatorStep2"),
        document.getElementById("indicatorStep3"),
        document.getElementById("indicatorStep4")
    ];


    /* =========================================
       BUTTON
    ========================================= */

    const nextStep1 = document.getElementById("nextStep1");
    const backStep2 = document.getElementById("backStep2");
    const nextStep2 = document.getElementById("nextStep2");

    const backStep3 = document.getElementById("backStep3");
    const nextStep3 = document.getElementById("nextStep3");

    const backStep4 = document.getElementById("backStep4");


    /* =========================================
       INPUT KHUSUS
    ========================================= */

    const nim = document.getElementById("nim");

    const noWhatsapp =
        document.getElementById("no_whatsapp");

    const nilaiAngka =
        document.getElementById("nilai_angka");

    const jurusan =
        document.getElementById("jurusan");


    /* =========================================
       DOKUMEN KHUSUS JURUSAN
    ========================================= */

    const dokumenManajemen =
        document.getElementById("dokumenManajemen");

    const dokumenEkonomi =
        document.getElementById("dokumenEkonomi");

    const dokumenAkuntansi =
        document.getElementById("dokumenAkuntansi");


    const jurnalManajemen =
        document.getElementById("jurnal_manajemen");

    const jurnalEkonomi =
        document.getElementById("jurnal_ekonomi");

    const jurnalAkuntansi =
        document.getElementById("jurnal_akuntansi");


    /* =========================================
       SHOW STEP
    ========================================= */

    function showStep(stepNumber) {

        steps.forEach(function (step) {
            step.classList.remove("active");
        });


        indicators.forEach(function (indicator) {
            indicator.classList.remove("active");
        });


        steps[stepNumber - 1].classList.add("active");


        for (let i = 0; i < stepNumber; i++) {

            indicators[i].classList.add("active");

        }


        if (stepNumber === 3) {

            updateJurusanDocument();

        }


        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });

    }


    /* =========================================
       SET ERROR
    ========================================= */

    function setError(field, hasError) {

        const formGroup =
            field.closest(".form-group");


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


    /* =========================================
       VALIDASI FILE
    ========================================= */

    function validateFile(field) {

        const file = field.files[0];


        /*
         * Jika required tetapi belum ada file.
         */
        if (
            field.hasAttribute("required") &&
            !file
        ) {

            setError(field, true);

            return false;

        }


        /*
         * Optional dan tidak ada file.
         */
        if (!file) {

            setError(field, false);

            return true;

        }


        /*
         * Ukuran file.
         */
        const maxSizeMb =
            parseFloat(
                field.dataset.maxSize || "1"
            );


        const maxSizeBytes =
            maxSizeMb * 1024 * 1024;


        if (file.size > maxSizeBytes) {

            setError(field, true);

            return false;

        }


        /*
         * Validasi extension.
         */
        const fileName =
            file.name.toLowerCase();


        const accept =
            field
                .getAttribute("accept")
                ?.toLowerCase() || "";


        let extensionValid = false;


        if (
            accept.includes(".pdf") &&
            fileName.endsWith(".pdf")
        ) {

            extensionValid = true;

        }


        if (
            accept.includes(".doc") &&
            fileName.endsWith(".doc")
        ) {

            extensionValid = true;

        }


        if (
            accept.includes(".docx") &&
            fileName.endsWith(".docx")
        ) {

            extensionValid = true;

        }


        if (!extensionValid) {

            setError(field, true);

            return false;

        }


        setError(field, false);

        return true;

    }


    /* =========================================
       VALIDASI FIELD
    ========================================= */

    function validateField(field) {

        /*
         * FILE INPUT
         */
        if (field.type === "file") {

            return validateFile(field);

        }


        let valid = true;


        /*
         * REQUIRED
         */
        if (
            field.hasAttribute("required") &&
            field.value.trim() === ""
        ) {

            valid = false;

        }


        /*
         * EMAIL
         */
        if (
            field.type === "email" &&
            field.value.trim() !== ""
        ) {

            const emailPattern =
                /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


            if (
                !emailPattern.test(
                    field.value.trim()
                )
            ) {

                valid = false;

            }

        }


        /*
         * NIM
         */
        if (
            field.id === "nim" &&
            field.value.trim() !== ""
        ) {

            if (
                !/^\d+$/.test(
                    field.value.trim()
                )
            ) {

                valid = false;

            }

        }


        /*
         * WHATSAPP
         */
        if (
            field.id === "no_whatsapp" &&
            field.value.trim() !== ""
        ) {

            if (
                !/^\d+$/.test(
                    field.value.trim()
                )
            ) {

                valid = false;

            }

        }


        /*
         * NILAI ANGKA
         *
         * Format yang diterima:
         * 80
         * 80,0
         * 80,00
         */
        if (
            field.id === "nilai_angka" &&
            field.value.trim() !== ""
        ) {

            const nilaiPattern =
                /^\d{1,3}(,\d{1,2})?$/;


            if (
                !nilaiPattern.test(
                    field.value.trim()
                )
            ) {

                valid = false;

            } else {

                const nilai =
                    parseFloat(
                        field.value
                            .replace(",", ".")
                    );


                if (
                    nilai < 0 ||
                    nilai > 100
                ) {

                    valid = false;

                }

            }

        }


        setError(field, !valid);


        return valid;

    }


    /* =========================================
       VALIDASI SECTION
    ========================================= */

    function validateSection(section) {

        const fields =
            section.querySelectorAll(
                "input[required], select[required], textarea[required]"
            );


        let sectionValid = true;

        let firstInvalidField = null;


        fields.forEach(function (field) {

            /*
             * Abaikan field tersembunyi.
             */
            if (
                field.closest(".jurusan-document") &&
                !field
                    .closest(".jurusan-document")
                    .classList.contains("active")
            ) {

                return;

            }


            const fieldValid =
                validateField(field);


            if (!fieldValid) {

                sectionValid = false;


                if (!firstInvalidField) {

                    firstInvalidField = field;

                }

            }

        });


        /*
         * Validasi file optional yang diisi.
         */
        const optionalFiles =
            section.querySelectorAll(
                'input[type="file"]:not([required])'
            );


        optionalFiles.forEach(function (field) {

            if (
                field.closest(".jurusan-document") &&
                !field
                    .closest(".jurusan-document")
                    .classList.contains("active")
            ) {

                return;

            }


            if (field.files.length > 0) {

                const valid =
                    validateFile(field);


                if (!valid) {

                    sectionValid = false;


                    if (!firstInvalidField) {

                        firstInvalidField = field;

                    }

                }

            }

        });


        if (firstInvalidField) {

            firstInvalidField.focus();

        }


        return sectionValid;

    }


    /* =========================================
       DOKUMEN BERDASARKAN JURUSAN
    ========================================= */

    function updateJurusanDocument() {

        dokumenManajemen.classList.remove(
            "active"
        );

        dokumenEkonomi.classList.remove(
            "active"
        );

        dokumenAkuntansi.classList.remove(
            "active"
        );


        jurnalManajemen.removeAttribute(
            "required"
        );

        jurnalEkonomi.removeAttribute(
            "required"
        );

        jurnalAkuntansi.removeAttribute(
            "required"
        );


        if (
            jurusan.value === "MANAJEMEN"
        ) {

            dokumenManajemen.classList.add(
                "active"
            );

            jurnalManajemen.setAttribute(
                "required",
                ""
            );

        }


        if (
            jurusan.value ===
            "EKONOMI PEMBANGUNAN"
        ) {

            dokumenEkonomi.classList.add(
                "active"
            );

            jurnalEkonomi.setAttribute(
                "required",
                ""
            );

        }


        if (
            jurusan.value === "AKUNTANSI"
        ) {

            dokumenAkuntansi.classList.add(
                "active"
            );

            jurnalAkuntansi.setAttribute(
                "required",
                ""
            );

        }

    }


    /* =========================================
       NIM
    ========================================= */

    nim.addEventListener(
        "input",
        function () {

            this.value =
                this.value.replace(
                    /\D/g,
                    ""
                );


            validateField(this);

        }
    );


    /* =========================================
       WHATSAPP
    ========================================= */

    noWhatsapp.addEventListener(
        "input",
        function () {

            this.value =
                this.value.replace(
                    /\D/g,
                    ""
                );


            validateField(this);

        }
    );


    /* =========================================
       NILAI ANGKA
    ========================================= */

    nilaiAngka.addEventListener(
        "input",
        function () {

            /*
             * Hanya angka dan koma.
             */
            this.value =
                this.value.replace(
                    /[^0-9,]/g,
                    ""
                );


            /*
             * Hanya satu koma.
             */
            const parts =
                this.value.split(",");


            if (parts.length > 2) {

                this.value =
                    parts[0] +
                    "," +
                    parts
                        .slice(1)
                        .join("");

            }


            validateField(this);

        }
    );


    /* =========================================
       JURUSAN
    ========================================= */

    jurusan.addEventListener(
        "change",
        function () {

            validateField(this);

            updateJurusanDocument();

        }
    );


    /* =========================================
       FIELD UMUM
    ========================================= */

    const allFields =
        document.querySelectorAll(
            "input, select, textarea"
        );


    allFields.forEach(function (field) {

        if (
            field.id !== "nim" &&
            field.id !== "no_whatsapp" &&
            field.id !== "nilai_angka" &&
            field.id !== "jurusan"
        ) {

            if (field.type === "file") {

                field.addEventListener(
                    "change",
                    function () {

                        validateFile(field);

                    }
                );

            } else {

                field.addEventListener(
                    "input",
                    function () {

                        validateField(field);

                    }
                );


                field.addEventListener(
                    "change",
                    function () {

                        validateField(field);

                    }
                );

            }

        }

    });


    /* =========================================
       STEP 1 -> STEP 2
    ========================================= */

    nextStep1.addEventListener(
        "click",
        function () {

            if (!validateSection(step1)) {

                return;

            }


            showStep(2);

        }
    );


    /* =========================================
       STEP 2 -> STEP 1
    ========================================= */

    backStep2.addEventListener(
        "click",
        function () {

            showStep(1);

        }
    );


    /* =========================================
       STEP 2 -> STEP 3
    ========================================= */

    nextStep2.addEventListener(
        "click",
        function () {

            if (!validateSection(step2)) {

                return;

            }


            showStep(3);

        }
    );


    /* =========================================
       STEP 3 -> STEP 2
    ========================================= */

    backStep3.addEventListener(
        "click",
        function () {

            showStep(2);

        }
    );


    /* =========================================
       STEP 3 -> STEP 4
    ========================================= */

    nextStep3.addEventListener(
        "click",
        function () {

            if (!validateSection(step3)) {

                return;

            }


            showStep(4);

        }
    );


    /* =========================================
       STEP 4 -> STEP 3
    ========================================= */

    backStep4.addEventListener(
        "click",
        function () {

            showStep(3);

        }
    );


    /* =========================================
       START
    ========================================= */

    updateJurusanDocument();

    showStep(1);

});