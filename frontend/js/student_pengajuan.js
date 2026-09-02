document.addEventListener("DOMContentLoaded", function () {

    console.log("JavaScript Yudisium aktif");


    /* =========================================================
       ELEMENT REFERENCES
    ========================================================= */

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


    const indicators = [
        document.getElementById("indicatorStep1"),
        document.getElementById("indicatorStep2"),
        document.getElementById("indicatorStep3"),
        document.getElementById("indicatorStep4")
    ];


    const nextStep1 =
        document.getElementById("nextStep1");

    const backStep2 =
        document.getElementById("backStep2");

    const nextStep2 =
        document.getElementById("nextStep2");

    const backStep3 =
        document.getElementById("backStep3");

    const nextStep3 =
        document.getElementById("nextStep3");

    const backStep4 =
        document.getElementById("backStep4");


    const editIdentitas =
        document.getElementById("editIdentitas");

    const editAkademik =
        document.getElementById("editAkademik");

    const editDokumen =
        document.getElementById("editDokumen");


    const formYudisium =
        document.getElementById("formYudisium");


    /* =========================================================
       FIELD UTAMA
    ========================================================= */

    const namaLengkap =
        document.getElementById("nama_lengkap");

    const nim =
        document.getElementById("nim");

    const email =
        document.getElementById("email");

    const noWhatsapp =
        document.getElementById("no_whatsapp");

    const tahunAngkatan =
        document.getElementById("tahun_angkatan");

    const jalurMasuk =
        document.getElementById("jalur_masuk");

    const jurusan =
        document.getElementById("jurusan");


    const karyaTulis =
        document.getElementById("karya_tulis");

    const judulKaryaTulis =
        document.getElementById("judul_karya_tulis");

    const tanggalUjian =
        document.getElementById("tanggal_ujian");

    const nilaiAngka =
        document.getElementById("nilai_angka");

    const nilaiHuruf =
        document.getElementById("nilai_huruf");


    /* =========================================================
       DOKUMEN KHUSUS JURUSAN
    ========================================================= */

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


    /* =========================================================
       KONFIRMASI
    ========================================================= */

    const konfirmasiData =
        document.getElementById("konfirmasiData");

    const confirmationError =
        document.getElementById("confirmationError");


    /* =========================================================
       MODAL SUKSES
    ========================================================= */

    const successModal =
        document.getElementById("successModal");

    const closeSuccessModal =
        document.getElementById("closeSuccessModal");

    const generatedSubmissionCode =
        document.getElementById("generatedSubmissionCode");

    const copySubmissionCode =
        document.getElementById("copySubmissionCode");

    const copyCodeFeedback =
        document.getElementById("copyCodeFeedback");

    const goToTracking =
        document.getElementById("goToTracking");


    /* =========================================================
       PROGRESS DOKUMEN
    ========================================================= */

    const documentProgressText =
        document.getElementById("documentProgressText");

    const documentProgressValue =
        document.getElementById("documentProgressValue");


    /* =========================================================
       REVIEW
    ========================================================= */

    const reviewNama =
        document.getElementById("reviewNama");

    const reviewNim =
        document.getElementById("reviewNim");

    const reviewEmail =
        document.getElementById("reviewEmail");

    const reviewWhatsapp =
        document.getElementById("reviewWhatsapp");

    const reviewAngkatan =
        document.getElementById("reviewAngkatan");

    const reviewJalur =
        document.getElementById("reviewJalur");

    const reviewJurusan =
        document.getElementById("reviewJurusan");


    const reviewKaryaTulis =
        document.getElementById("reviewKaryaTulis");

    const reviewTanggalUjian =
        document.getElementById("reviewTanggalUjian");

    const reviewNilaiAngka =
        document.getElementById("reviewNilaiAngka");

    const reviewNilaiHuruf =
        document.getElementById("reviewNilaiHuruf");

    const reviewJudul =
        document.getElementById("reviewJudul");


    const documentReviewList =
        document.getElementById("documentReviewList");


    /* =========================================================
       HELPER — SHOW STEP
    ========================================================= */

    function showStep(stepNumber) {

        steps.forEach(function (step) {

            if (step) {
                step.classList.remove("active");
            }

        });


        indicators.forEach(function (indicator) {

            if (indicator) {
                indicator.classList.remove("active");
            }

        });


        const activeStep =
            steps[stepNumber - 1];


        if (activeStep) {

            activeStep.classList.add("active");

        }


        for (
            let index = 0;
            index < stepNumber;
            index++
        ) {

            if (indicators[index]) {

                indicators[index]
                    .classList
                    .add("active");

            }

        }


        if (stepNumber === 3) {

            updateJurusanDocument();
            refreshUploadUI();

        }


        if (stepNumber === 4) {

            generateReview();

        }


        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });

    }


    /* =========================================================
       HELPER — ERROR STATE
    ========================================================= */

    function setError(
        field,
        hasError
    ) {

        if (!field) {
            return;
        }


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


    /* =========================================================
       VALIDASI FILE
    ========================================================= */

    function validateFile(field) {

        if (!field) {
            return true;
        }


        const file =
            field.files &&
            field.files.length > 0
                ? field.files[0]
                : null;


        if (
            field.hasAttribute("required") &&
            !file
        ) {

            setError(
                field,
                true
            );

            return false;

        }


        if (!file) {

            setError(
                field,
                false
            );

            return true;

        }


        const maxSizeMb =
            parseFloat(
                field.dataset.maxSize || "1"
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


        const fileName =
            file.name.toLowerCase();


        const accept =
            (
                field.getAttribute(
                    "accept"
                ) || ""
            ).toLowerCase();


        let extensionValid = false;


        if (
            accept.includes(".pdf") &&
            fileName.endsWith(".pdf")
        ) {

            extensionValid = true;

        }


        if (
            accept.includes(".docx") &&
            fileName.endsWith(".docx")
        ) {

            extensionValid = true;

        }


        if (
            accept.includes(".doc") &&
            !fileName.endsWith(".docx") &&
            fileName.endsWith(".doc")
        ) {

            extensionValid = true;

        }


        if (!extensionValid) {

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


    /* =========================================================
       VALIDASI FIELD
    ========================================================= */

    function validateField(field) {

        if (!field) {
            return true;
        }


        if (
            field.type === "file"
        ) {

            return validateFile(field);

        }


        let valid = true;


        const value =
            field.value.trim();


        if (
            field.hasAttribute("required") &&
            value === ""
        ) {

            valid = false;

        }


        /* EMAIL */

        if (
            field.type === "email" &&
            value !== ""
        ) {

            const emailPattern =
                /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


            if (
                !emailPattern.test(value)
            ) {

                valid = false;

            }

        }


        /* NIM */

        if (
            field.id === "nim" &&
            value !== ""
        ) {

            if (
                !/^\d+$/.test(value)
            ) {

                valid = false;

            }

        }


        /* WHATSAPP */

        if (
            field.id ===
            "no_whatsapp" &&
            value !== ""
        ) {

            if (
                !/^\d+$/.test(value)
            ) {

                valid = false;

            }

        }


        /* TAHUN ANGKATAN */

        if (
            field.id ===
            "tahun_angkatan" &&
            value !== ""
        ) {

            const year =
                Number(value);


            if (
                Number.isNaN(year) ||
                year < 2000 ||
                year > 2100
            ) {

                valid = false;

            }

        }


        /* NILAI ANGKA */

        if (
            field.id ===
            "nilai_angka" &&
            value !== ""
        ) {

            const nilaiPattern =
                /^\d{1,3}(,\d{1,2})?$/;


            if (
                !nilaiPattern.test(value)
            ) {

                valid = false;

            } else {

                const nilai =
                    parseFloat(
                        value.replace(
                            ",",
                            "."
                        )
                    );


                if (
                    nilai < 0 ||
                    nilai > 100
                ) {

                    valid = false;

                }

            }

        }


        setError(
            field,
            !valid
        );


        return valid;

    }


    /* =========================================================
       VALIDASI SECTION
    ========================================================= */

    function validateSection(section) {

        if (!section) {
            return true;
        }


        const requiredFields =
            section.querySelectorAll(
                "input[required], select[required], textarea[required]"
            );


        let sectionValid = true;

        let firstInvalidField =
            null;


        requiredFields.forEach(
            function (field) {

                const jurusanContainer =
                    field.closest(
                        ".jurusan-document"
                    );


                if (
                    jurusanContainer &&
                    !jurusanContainer
                        .classList
                        .contains("active")
                ) {

                    return;

                }


                const valid =
                    validateField(field);


                if (!valid) {

                    sectionValid = false;


                    if (
                        !firstInvalidField
                    ) {

                        firstInvalidField =
                            field;

                    }

                }

            }
        );


        const optionalFiles =
            section.querySelectorAll(
                'input[type="file"]:not([required])'
            );


        optionalFiles.forEach(
            function (field) {

                const jurusanContainer =
                    field.closest(
                        ".jurusan-document"
                    );


                if (
                    jurusanContainer &&
                    !jurusanContainer
                        .classList
                        .contains("active")
                ) {

                    return;

                }


                if (
                    field.files.length >
                    0
                ) {

                    const valid =
                        validateFile(field);


                    if (!valid) {

                        sectionValid =
                            false;


                        if (
                            !firstInvalidField
                        ) {

                            firstInvalidField =
                                field;

                        }

                    }

                }

            }
        );


        if (firstInvalidField) {

            firstInvalidField.focus();


            firstInvalidField
                .scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });

        }


        return sectionValid;

    }


    /* =========================================================
       DOKUMEN KHUSUS JURUSAN
    ========================================================= */

    function updateJurusanDocument() {

        const specialGroups = [
            dokumenManajemen,
            dokumenEkonomi,
            dokumenAkuntansi
        ];


        specialGroups.forEach(
            function (group) {

                if (group) {

                    group.classList.remove(
                        "active"
                    );

                }

            }
        );


        const specialInputs = [
            jurnalManajemen,
            jurnalEkonomi,
            jurnalAkuntansi
        ];


        specialInputs.forEach(
            function (field) {

                if (field) {

                    field.removeAttribute(
                        "required"
                    );

                }

            }
        );


        if (
            jurusan.value ===
            "MANAJEMEN"
        ) {

            dokumenManajemen
                .classList
                .add("active");


            jurnalManajemen
                .setAttribute(
                    "required",
                    ""
                );

        }


        if (
            jurusan.value ===
            "EKONOMI PEMBANGUNAN"
        ) {

            dokumenEkonomi
                .classList
                .add("active");


            jurnalEkonomi
                .setAttribute(
                    "required",
                    ""
                );

        }


        if (
            jurusan.value ===
            "AKUNTANSI"
        ) {

            dokumenAkuntansi
                .classList
                .add("active");


            jurnalAkuntansi
                .setAttribute(
                    "required",
                    ""
                );

        }

    }


    /* =========================================================
       FORMAT TANGGAL
    ========================================================= */

    function formatTanggal(
        dateValue
    ) {

        if (!dateValue) {

            return "-";

        }


        const date =
            new Date(
                dateValue +
                "T00:00:00"
            );


        return date.toLocaleDateString(
            "id-ID",
            {
                day: "2-digit",
                month: "long",
                year: "numeric"
            }
        );

    }


    /* =========================================================
       FORMAT FILE SIZE
    ========================================================= */

    function formatFileSize(
        bytes
    ) {

        if (
            bytes === 0 ||
            !bytes
        ) {

            return "0 KB";

        }


        const mb =
            bytes /
            (
                1024 *
                1024
            );


        if (mb >= 1) {

            return (
                mb.toFixed(2) +
                " MB"
            );

        }


        const kb =
            bytes /
            1024;


        return (
            Math.round(kb) +
            " KB"
        );

    }


    /* =========================================================
       FILE INPUT YANG TERLIHAT
    ========================================================= */

    function getVisibleFileInputs() {

        if (!step3) {
            return [];
        }


        return Array
            .from(
                step3.querySelectorAll(
                    'input[type="file"]'
                )
            )
            .filter(
                function (field) {

                    const jurusanContainer =
                        field.closest(
                            ".jurusan-document"
                        );


                    if (
                        jurusanContainer &&
                        !jurusanContainer
                            .classList
                            .contains("active")
                    ) {

                        return false;

                    }


                    return true;

                }
            );

    }


    /* =========================================================
       VISUAL FILE SELECTED
    ========================================================= */

    function updateFileVisual(field) {

        if (!field) {
            return;
        }


        const group =
            field.closest(
                ".file-group"
            );


        if (!group) {
            return;
        }


        let selectedInfo =
            group.querySelector(
                ".upload-selected-file"
            );


        if (!selectedInfo) {

            selectedInfo =
                document.createElement(
                    "div"
                );


            selectedInfo.className =
                "upload-selected-file is-empty";


            group.appendChild(
                selectedInfo
            );

        }


        const file =
            field.files &&
            field.files.length > 0
                ? field.files[0]
                : null;


        if (file) {

            selectedInfo
                .classList
                .remove(
                    "is-empty"
                );


            selectedInfo.textContent =
                file.name +
                " • " +
                formatFileSize(
                    file.size
                );


            group.classList.add(
                "has-file"
            );

        } else {

            selectedInfo
                .classList
                .add(
                    "is-empty"
                );


            selectedInfo.textContent =
                "Belum ada file dipilih";


            group.classList.remove(
                "has-file"
            );

        }

    }


    /* =========================================================
       DOCUMENT PROGRESS
    ========================================================= */

    function updateDocumentProgress() {

        const visibleInputs =
            getVisibleFileInputs();


        const requiredInputs =
            visibleInputs.filter(
                function (field) {

                    return field
                        .hasAttribute(
                            "required"
                        );

                }
            );


        const selectedRequired =
            requiredInputs.filter(
                function (field) {

                    return (
                        field.files &&
                        field.files.length >
                        0
                    );

                }
            ).length;


        const totalRequired =
            requiredInputs.length;


        const percentage =
            totalRequired > 0
                ? Math.round(
                    (
                        selectedRequired /
                        totalRequired
                    ) *
                    100
                )
                : 0;


        if (
            documentProgressText
        ) {

            documentProgressText
                .textContent =
                    selectedRequired +
                    " dari " +
                    totalRequired +
                    " dokumen wajib dipilih";

        }


        if (
            documentProgressValue
        ) {

            documentProgressValue
                .style
                .width =
                    percentage +
                    "%";

        }

    }


    /* =========================================================
       REFRESH UPLOAD UI
    ========================================================= */

    function refreshUploadUI() {

        if (!step3) {
            return;
        }


        step3
            .querySelectorAll(
                'input[type="file"]'
            )
            .forEach(
                function (field) {

                    updateFileVisual(
                        field
                    );

                }
            );


        updateDocumentProgress();

    }


    /* =========================================================
       GENERATE REVIEW
    ========================================================= */

    function generateReview() {

        reviewNama.textContent =
            namaLengkap.value || "-";

        reviewNim.textContent =
            nim.value || "-";

        reviewEmail.textContent =
            email.value || "-";

        reviewWhatsapp.textContent =
            noWhatsapp.value || "-";

        reviewAngkatan.textContent =
            tahunAngkatan.value || "-";

        reviewJalur.textContent =
            jalurMasuk.value || "-";

        reviewJurusan.textContent =
            jurusan.value || "-";


        reviewKaryaTulis.textContent =
            karyaTulis.value || "-";

        reviewTanggalUjian.textContent =
            formatTanggal(
                tanggalUjian.value
            );

        reviewNilaiAngka.textContent =
            nilaiAngka.value || "-";

        reviewNilaiHuruf.textContent =
            nilaiHuruf.value || "-";

        reviewJudul.textContent =
            judulKaryaTulis.value || "-";


        generateDocumentReview();

    }


    /* =========================================================
       REVIEW DOKUMEN
    ========================================================= */

    function generateDocumentReview() {

        if (!documentReviewList) {
            return;
        }


        documentReviewList.innerHTML =
            "";


        const visibleInputs =
            getVisibleFileInputs();


        visibleInputs.forEach(
            function (field) {

                const group =
                    field.closest(
                        ".file-group"
                    );


                const label =
                    group
                        ?.querySelector(
                            "label"
                        )
                        ?.textContent
                        .replace(
                            "*",
                            ""
                        )
                        .trim() ||
                    field.name;


                const file =
                    field.files &&
                    field.files.length > 0
                        ? field.files[0]
                        : null;


                const item =
                    document.createElement(
                        "div"
                    );


                item.className =
                    "document-review-item";


                const info =
                    document.createElement(
                        "div"
                    );


                info.className =
                    "document-review-info";


                const name =
                    document.createElement(
                        "span"
                    );


                name.className =
                    "document-review-name";


                name.textContent =
                    label;


                const fileInfo =
                    document.createElement(
                        "span"
                    );


                fileInfo.className =
                    "document-review-file";


                fileInfo.textContent =
                    file
                        ? (
                            file.name +
                            " • " +
                            formatFileSize(
                                file.size
                            )
                        )
                        : "Belum ada file";


                const status =
                    document.createElement(
                        "span"
                    );


                status.className =
                    "document-status";


                if (file) {

                    status.textContent =
                        "Siap";

                } else {

                    status.textContent =
                        "Opsional";

                    status.classList.add(
                        "optional"
                    );

                }


                info.appendChild(
                    name
                );

                info.appendChild(
                    fileInfo
                );


                item.appendChild(
                    info
                );

                item.appendChild(
                    status
                );


                documentReviewList
                    .appendChild(
                        item
                    );

            }
        );

    }


    /* =========================================================
       GENERATE KODE SIMULASI
    ========================================================= */

    function createSimulationCode() {

        const now =
            new Date();


        const year =
            now.getFullYear();


        const suffix =
            String(
                Date.now()
            ).slice(
                -6
            );


        return (
            "YDS-" +
            year +
            "-" +
            suffix
        );

    }


    /* =========================================================
       COPY KODE
    ========================================================= */

    async function copyCode() {

        const code =
            generatedSubmissionCode
                .textContent
                .trim();


        if (!code) {
            return;
        }


        try {

            await navigator
                .clipboard
                .writeText(
                    code
                );


            copyCodeFeedback
                .textContent =
                    "Kode berhasil disalin ke clipboard.";


            copySubmissionCode
                .textContent =
                    "Tersalin ✓";


            setTimeout(
                function () {

                    copySubmissionCode
                        .textContent =
                            "Salin Kode";

                },
                1800
            );

        } catch (error) {

            copyCodeFeedback
                .textContent =
                    "Gagal menyalin otomatis. Silakan salin kode secara manual.";

        }

    }


    /* =========================================================
       INPUT NIM
    ========================================================= */

    nim.addEventListener(
        "input",
        function () {

            this.value =
                this.value.replace(
                    /\D/g,
                    ""
                );


            validateField(
                this
            );

        }
    );


    /* =========================================================
       INPUT WHATSAPP
    ========================================================= */

    noWhatsapp.addEventListener(
        "input",
        function () {

            this.value =
                this.value.replace(
                    /\D/g,
                    ""
                );


            validateField(
                this
            );

        }
    );


    /* =========================================================
       INPUT NILAI
    ========================================================= */

    nilaiAngka.addEventListener(
        "input",
        function () {

            this.value =
                this.value.replace(
                    /[^0-9,]/g,
                    ""
                );


            const parts =
                this.value.split(
                    ","
                );


            if (
                parts.length > 2
            ) {

                this.value =
                    parts[0] +
                    "," +
                    parts
                        .slice(1)
                        .join("");

            }


            validateField(
                this
            );

        }
    );


    /* =========================================================
       JURUSAN
    ========================================================= */

    jurusan.addEventListener(
        "change",
        function () {

            validateField(
                this
            );


            updateJurusanDocument();

            refreshUploadUI();

        }
    );


    /* =========================================================
       EVENT FIELD UMUM
    ========================================================= */

    const allFields =
        document.querySelectorAll(
            "input, select, textarea"
        );


    allFields.forEach(
        function (field) {

            if (
                field.id ===
                "konfirmasiData"
            ) {

                return;

            }


            if (
                field.id === "nim" ||
                field.id ===
                    "no_whatsapp" ||
                field.id ===
                    "nilai_angka" ||
                field.id === "jurusan"
            ) {

                return;

            }


            if (
                field.type === "file"
            ) {

                field.addEventListener(
                    "change",
                    function () {

                        validateFile(
                            field
                        );


                        updateFileVisual(
                            field
                        );


                        updateDocumentProgress();

                    }
                );

            } else {

                field.addEventListener(
                    "input",
                    function () {

                        validateField(
                            field
                        );

                    }
                );


                field.addEventListener(
                    "change",
                    function () {

                        validateField(
                            field
                        );

                    }
                );

            }

        }
    );


    /* =========================================================
       NAVIGASI STEP
    ========================================================= */

    nextStep1.addEventListener(
        "click",
        function () {

            if (
                !validateSection(
                    step1
                )
            ) {

                return;

            }


            showStep(2);

        }
    );


    backStep2.addEventListener(
        "click",
        function () {

            showStep(1);

        }
    );


    nextStep2.addEventListener(
        "click",
        function () {

            if (
                !validateSection(
                    step2
                )
            ) {

                return;

            }


            showStep(3);

        }
    );


    backStep3.addEventListener(
        "click",
        function () {

            showStep(2);

        }
    );


    nextStep3.addEventListener(
        "click",
        function () {

            if (
                !validateSection(
                    step3
                )
            ) {

                return;

            }


            showStep(4);

        }
    );


    backStep4.addEventListener(
        "click",
        function () {

            showStep(3);

        }
    );


    /* =========================================================
       EDIT DARI REVIEW
    ========================================================= */

    editIdentitas.addEventListener(
        "click",
        function () {

            showStep(1);

        }
    );


    editAkademik.addEventListener(
        "click",
        function () {

            showStep(2);

        }
    );


    editDokumen.addEventListener(
        "click",
        function () {

            showStep(3);

        }
    );


    /* =========================================================
       CHECKBOX KONFIRMASI
    ========================================================= */

    konfirmasiData.addEventListener(
        "change",
        function () {

            if (
                this.checked
            ) {

                confirmationError
                    .classList
                    .remove(
                        "active"
                    );

            }

        }
    );


    /* =========================================================
       SUBMIT
    ========================================================= */

    formYudisium.addEventListener(
        "submit",
        function (event) {

            /*
             * Masih prototype frontend.
             * Pengiriman ke Laravel belum dilakukan.
             */
            event.preventDefault();


            if (
                !konfirmasiData.checked
            ) {

                confirmationError
                    .classList
                    .add(
                        "active"
                    );


                konfirmasiData.focus();


                return;

            }


            /*
             * Validasi ulang semua step.
             */

            if (
                !validateSection(
                    step1
                )
            ) {

                showStep(1);

                return;

            }


            if (
                !validateSection(
                    step2
                )
            ) {

                showStep(2);

                return;

            }


            if (
                !validateSection(
                    step3
                )
            ) {

                showStep(3);

                return;

            }


            /*
             * Simulasi kode.
             * Nanti diganti kode dari response Laravel.
             */

            const simulationCode =
                createSimulationCode();


            generatedSubmissionCode
                .textContent =
                    simulationCode;


            /*
             * Simpan sementara untuk simulasi tracking.
             */

            sessionStorage.setItem(
                "last_submission_code",
                simulationCode
            );


            sessionStorage.setItem(
                "last_submission_nim",
                nim.value
            );


            sessionStorage.setItem(
                "tracking_kode",
                simulationCode
            );


            sessionStorage.setItem(
                "tracking_nim",
                nim.value
            );


            copyCodeFeedback
                .textContent =
                    "Simpan kode ini. Kode akan diperlukan bersama NIM untuk mengecek status pengajuan.";


            successModal
                .classList
                .add(
                    "active"
                );


            document.body.style.overflow =
                "hidden";


            console.log(
                "Pengajuan berhasil melewati validasi frontend."
            );

        }
    );


    /* =========================================================
       COPY SUBMISSION CODE
    ========================================================= */

    copySubmissionCode.addEventListener(
        "click",
        copyCode
    );


    /* =========================================================
       KE TRACKING
    ========================================================= */

    goToTracking.addEventListener(
        "click",
        function () {

            const code =
                generatedSubmissionCode
                    .textContent
                    .trim();


            sessionStorage.setItem(
                "tracking_nim",
                nim.value
            );


            sessionStorage.setItem(
                "tracking_kode",
                code
            );


            window.location.href =
                "detail_tracking.html";

        }
    );


    /* =========================================================
       CLOSE MODAL
    ========================================================= */

    function closeModal() {

        successModal
            .classList
            .remove(
                "active"
            );


        document.body.style.overflow =
            "";

    }


    closeSuccessModal.addEventListener(
        "click",
        closeModal
    );


    successModal.addEventListener(
        "click",
        function (event) {

            if (
                event.target ===
                successModal
            ) {

                closeModal();

            }

        }
    );


    document.addEventListener(
        "keydown",
        function (event) {

            if (
                event.key ===
                    "Escape" &&
                successModal
                    .classList
                    .contains(
                        "active"
                    )
            ) {

                closeModal();

            }

        }
    );


    /* =========================================================
       START
    ========================================================= */

    updateJurusanDocument();

    refreshUploadUI();

    showStep(1);

});