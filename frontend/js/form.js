document.addEventListener("DOMContentLoaded", function () {

    console.log("JavaScript Yudisium aktif");


    /* =========================================
       STEP
    ========================================= */

    const step1 =
        document.getElementById("step1");

    const step2 =
        document.getElementById("step2");

    const step3 =
        document.getElementById("step3");

    const step4 =
        document.getElementById("step4");


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


    /* =========================================
       EDIT BUTTON
    ========================================= */

    const editIdentitas =
        document.getElementById("editIdentitas");

    const editAkademik =
        document.getElementById("editAkademik");

    const editDokumen =
        document.getElementById("editDokumen");


    /* =========================================
       FORM
    ========================================= */

    const formYudisium =
        document.getElementById("formYudisium");


    /* =========================================
       INPUT KHUSUS
    ========================================= */

    const nim =
        document.getElementById("nim");

    const noWhatsapp =
        document.getElementById("no_whatsapp");

    const nilaiAngka =
        document.getElementById("nilai_angka");

    const jurusan =
        document.getElementById("jurusan");


    /* =========================================
       DOKUMEN KHUSUS
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
       KONFIRMASI
    ========================================= */

    const konfirmasiData =
        document.getElementById("konfirmasiData");

    const confirmationError =
        document.getElementById("confirmationError");


    /* =========================================
       MODAL
    ========================================= */

    const successModal =
        document.getElementById("successModal");

    const closeSuccessModal =
        document.getElementById("closeSuccessModal");


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


        steps[stepNumber - 1]
            .classList
            .add("active");


        for (
            let i = 0;
            i < stepNumber;
            i++
        ) {

            indicators[i]
                .classList
                .add("active");

        }


        if (stepNumber === 3) {

            updateJurusanDocument();

        }


        if (stepNumber === 4) {

            generateReview();

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

            field
                .classList
                .add("form-control-error");


            if (formGroup) {

                formGroup
                    .classList
                    .add("has-error");

            }

        } else {

            field
                .classList
                .remove("form-control-error");


            if (formGroup) {

                formGroup
                    .classList
                    .remove("has-error");

            }

        }

    }


    /* =========================================
       VALIDASI FILE
    ========================================= */

    function validateFile(field) {

        const file =
            field.files[0];


        if (
            field.hasAttribute("required") &&
            !file
        ) {

            setError(field, true);

            return false;

        }


        if (!file) {

            setError(field, false);

            return true;

        }


        const maxSizeMb =
            parseFloat(
                field.dataset.maxSize || "1"
            );


        const maxSizeBytes =
            maxSizeMb * 1024 * 1024;


        if (
            file.size > maxSizeBytes
        ) {

            setError(field, true);

            return false;

        }


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

        if (
            field.type === "file"
        ) {

            return validateFile(field);

        }


        let valid = true;


        if (
            field.hasAttribute("required") &&
            field.value.trim() === ""
        ) {

            valid = false;

        }


        /* EMAIL */

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


        /* NIM */

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


        /* WHATSAPP */

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


        /* NILAI ANGKA */

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

            const jurusanParent =
                field.closest(
                    ".jurusan-document"
                );


            if (
                jurusanParent &&
                !jurusanParent
                    .classList
                    .contains("active")
            ) {

                return;

            }


            const fieldValid =
                validateField(field);


            if (!fieldValid) {

                sectionValid = false;


                if (!firstInvalidField) {

                    firstInvalidField =
                        field;

                }

            }

        });


        const optionalFiles =
            section.querySelectorAll(
                'input[type="file"]:not([required])'
            );


        optionalFiles.forEach(
            function (field) {

                const jurusanParent =
                    field.closest(
                        ".jurusan-document"
                    );


                if (
                    jurusanParent &&
                    !jurusanParent
                        .classList
                        .contains("active")
                ) {

                    return;

                }


                if (
                    field.files.length > 0
                ) {

                    const valid =
                        validateFile(field);


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

            }
        );


        if (firstInvalidField) {

            firstInvalidField.focus();

        }


        return sectionValid;

    }


    /* =========================================
       DOKUMEN JURUSAN
    ========================================= */

    function updateJurusanDocument() {

        dokumenManajemen
            .classList
            .remove("active");

        dokumenEkonomi
            .classList
            .remove("active");

        dokumenAkuntansi
            .classList
            .remove("active");


        jurnalManajemen
            .removeAttribute("required");

        jurnalEkonomi
            .removeAttribute("required");

        jurnalAkuntansi
            .removeAttribute("required");


        if (
            jurusan.value === "MANAJEMEN"
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
            jurusan.value === "AKUNTANSI"
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


    /* =========================================
       FORMAT TANGGAL
    ========================================= */

    function formatTanggal(dateValue) {

        if (!dateValue) {

            return "-";

        }


        const date =
            new Date(
                dateValue + "T00:00:00"
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


    /* =========================================
       GENERATE REVIEW
    ========================================= */

    function generateReview() {

        /* IDENTITAS */

        document
            .getElementById("reviewNama")
            .textContent =
                document
                    .getElementById("nama_lengkap")
                    .value;


        document
            .getElementById("reviewNim")
            .textContent =
                nim.value;


        document
            .getElementById("reviewEmail")
            .textContent =
                document
                    .getElementById("email")
                    .value;


        document
            .getElementById("reviewWhatsapp")
            .textContent =
                noWhatsapp.value;


        document
            .getElementById("reviewAngkatan")
            .textContent =
                document
                    .getElementById("tahun_angkatan")
                    .value;


        document
            .getElementById("reviewJalur")
            .textContent =
                document
                    .getElementById("jalur_masuk")
                    .value;


        document
            .getElementById("reviewJurusan")
            .textContent =
                jurusan.value;


        /* AKADEMIK */

        document
            .getElementById("reviewKaryaTulis")
            .textContent =
                document
                    .getElementById("karya_tulis")
                    .value;


        document
            .getElementById("reviewJudul")
            .textContent =
                document
                    .getElementById("judul_karya_tulis")
                    .value;


        document
            .getElementById("reviewTanggalUjian")
            .textContent =
                formatTanggal(
                    document
                        .getElementById("tanggal_ujian")
                        .value
                );


        document
            .getElementById("reviewNilaiAngka")
            .textContent =
                nilaiAngka.value;


        document
            .getElementById("reviewNilaiHuruf")
            .textContent =
                document
                    .getElementById("nilai_huruf")
                    .value;


        generateDocumentReview();

    }


    /* =========================================
       LABEL DOKUMEN
    ========================================= */

    const documentLabels = {

        form_yudisium:
            "Formulir Pendaftaran Yudisium",

        foto_3x4:
            "Foto 3×4 Berwarna",

        ijazah_slta:
            "Ijazah SLTA",

        berita_acara_ujian:
            "Berita Acara Ujian Skripsi / Artikel",

        rekap_nilai:
            "Rekapitulasi Nilai Ujian Skripsi / Artikel",

        blanko_revisi:
            "Blanko Revisi",

        tanda_terima:
            "Tanda Terima Skripsi / Artikel",

        surat_pernyataan_ijazah:
            "Surat Pernyataan untuk Proses Penulisan Ijazah",

        bebas_perpus_universitas:
            "Surat Bebas Pinjam Perpustakaan Universitas",

        bebas_perpus_fakultas:
            "Surat Bebas Pinjam Perpustakaan Fakultas",

        khs:
            "KHS Semester 1 s/d Terbaru",

        transkrip:
            "Transkrip Nilai Ujian Skripsi",

        surat_tugas_pembimbing:
            "Surat Tugas Dosen Pembimbing Skripsi",

        bebas_tunggakan:
            "Surat Verifikasi Bebas Tunggakan",

        jurnal_manajemen:
            "Bukti Pengisian Jurnal JMSO",

        jurnal_ekonomi:
            "Bukti Pengisian Jurnal Ekonomi Pembangunan",

        jurnal_akuntansi:
            "Bukti Pengisian Jurnal Akuntansi"

    };


    /* =========================================
       GENERATE DOCUMENT REVIEW
    ========================================= */

    function generateDocumentReview() {

        const documentReviewList =
            document.getElementById(
                "documentReviewList"
            );


        documentReviewList.innerHTML = "";


        const fileInputs =
            step3.querySelectorAll(
                'input[type="file"]'
            );


        fileInputs.forEach(
            function (field) {

                const jurusanParent =
                    field.closest(
                        ".jurusan-document"
                    );


                if (
                    jurusanParent &&
                    !jurusanParent
                        .classList
                        .contains("active")
                ) {

                    return;

                }


                const file =
                    field.files[0];


                if (
                    !file &&
                    !field.hasAttribute(
                        "required"
                    )
                ) {

                    const item =
                        createDocumentReviewItem(
                            documentLabels[field.id] ||
                            field.id,
                            "Tidak di-upload",
                            true
                        );


                    documentReviewList
                        .appendChild(item);


                    return;

                }


                if (!file) {

                    return;

                }


                const item =
                    createDocumentReviewItem(
                        documentLabels[field.id] ||
                        field.id,
                        file.name,
                        false
                    );


                documentReviewList
                    .appendChild(item);

            }
        );

    }


    /* =========================================
       CREATE DOCUMENT ITEM
    ========================================= */

    function createDocumentReviewItem(
        label,
        fileName,
        optional
    ) {

        const item =
            document.createElement("div");


        item.className =
            "document-review-item";


        const info =
            document.createElement("div");


        info.className =
            "document-review-info";


        const name =
            document.createElement("span");


        name.className =
            "document-review-name";


        name.textContent =
            label;


        const file =
            document.createElement("span");


        file.className =
            "document-review-file";


        file.textContent =
            fileName;


        info.appendChild(name);
        info.appendChild(file);


        const status =
            document.createElement("span");


        status.className =
            "document-status";


        if (optional) {

            status.classList.add(
                "optional"
            );


            status.textContent =
                "Opsional";

        } else {

            status.textContent =
                "Siap";

        }


        item.appendChild(info);
        item.appendChild(status);


        return item;

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
       NILAI
    ========================================= */

    nilaiAngka.addEventListener(
        "input",
        function () {

            this.value =
                this.value.replace(
                    /[^0-9,]/g,
                    ""
                );


            const parts =
                this.value.split(",");


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


    allFields.forEach(
        function (field) {

            if (
                field.id ===
                "konfirmasiData"
            ) {

                return;

            }


            if (
                field.id !== "nim" &&
                field.id !== "no_whatsapp" &&
                field.id !== "nilai_angka" &&
                field.id !== "jurusan"
            ) {

                if (
                    field.type === "file"
                ) {

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

        }
    );


    /* =========================================
       STEP NAVIGATION
    ========================================= */

    nextStep1.addEventListener(
        "click",
        function () {

            if (
                !validateSection(step1)
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
                !validateSection(step2)
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
                !validateSection(step3)
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


    /* =========================================
       EDIT DARI REVIEW
    ========================================= */

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


    /* =========================================
       CHECKBOX KONFIRMASI
    ========================================= */

    konfirmasiData.addEventListener(
        "change",
        function () {

            if (this.checked) {

                confirmationError
                    .classList
                    .remove("active");

            }

        }
    );


    /* =========================================
       SUBMIT
    ========================================= */

    formYudisium.addEventListener(
        "submit",
        function (event) {

            /*
             * Untuk sementara kita cegah
             * submit ke server karena backend
             * belum dihubungkan.
             */
            event.preventDefault();


            if (
                !konfirmasiData.checked
            ) {

                confirmationError
                    .classList
                    .add("active");


                konfirmasiData.focus();


                return;

            }


            /*
             * Validasi ulang seluruh step
             * sebelum dikirim.
             */
            if (
                !validateSection(step1)
            ) {

                showStep(1);

                return;

            }


            if (
                !validateSection(step2)
            ) {

                showStep(2);

                return;

            }


            if (
                !validateSection(step3)
            ) {

                showStep(3);

                return;

            }


            /*
             * Simulasi berhasil.
             */
            successModal
                .classList
                .add("active");


            console.log(
                "Pengajuan siap dikirim ke Laravel"
            );


            /*
             * Nanti ketika integrasi backend:
             *
             * event.preventDefault()
             * bisa kita hilangkan
             *
             * atau menggunakan fetch().
             */

        }
    );


    /* =========================================
       MODAL
    ========================================= */

    closeSuccessModal.addEventListener(
        "click",
        function () {

            successModal
                .classList
                .remove("active");

        }
    );


    successModal.addEventListener(
        "click",
        function (event) {

            if (
                event.target ===
                successModal
            ) {

                successModal
                    .classList
                    .remove("active");

            }

        }
    );


    /* =========================================
       START
    ========================================= */

    updateJurusanDocument();

    showStep(1);

});