document.addEventListener(
    "DOMContentLoaded",
    function () {

        console.log(
            "Admin verifikasi aktif"
        );


        /* =====================================
           DOCUMENT DATA
        ===================================== */

        const documents = [

            {
                key:
                    "formulir_yudisium",

                title:
                    "Formulir Pendaftaran Yudisium",

                filename:
                    "formulir_yudisium.pdf",

                size:
                    "650 KB"
            },

            {
                key:
                    "foto_3x4",

                title:
                    "Foto 3x4 Berwarna",

                filename:
                    "foto_3x4.pdf",

                size:
                    "420 KB"
            },

            {
                key:
                    "ijazah_slta",

                title:
                    "Ijazah SLTA",

                filename:
                    "ijazah_slta.pdf",

                size:
                    "830 KB"
            },

            {
                key:
                    "berita_acara",

                title:
                    "Berita Acara Ujian Skripsi / Artikel",

                filename:
                    "berita_acara.pdf",

                size:
                    "790 KB"
            },

            {
                key:
                    "rekap_nilai",

                title:
                    "Rekapitulasi Nilai Ujian",

                filename:
                    "rekap_nilai.pdf",

                size:
                    "740 KB"
            },

            {
                key:
                    "blanko_revisi",

                title:
                    "Blanko Revisi",

                filename:
                    "blanko_revisi.pdf",

                size:
                    "510 KB"
            },

            {
                key:
                    "tanda_terima",

                title:
                    "Tanda Terima Skripsi / Artikel",

                filename:
                    "tanda_terima.pdf",

                size:
                    "1.2 MB"
            },

            {
                key:
                    "pernyataan_ijazah",

                title:
                    "Surat Pernyataan Penulisan Ijazah",

                filename:
                    "pernyataan_ijazah.pdf",

                size:
                    "560 KB"
            },

            {
                key:
                    "bebas_pinjam_universitas",

                title:
                    "Surat Bebas Pinjam Perpustakaan Universitas",

                filename:
                    "bebas_pinjam_univ.pdf",

                size:
                    "690 KB"
            },

            {
                key:
                    "bebas_pinjam_fakultas",

                title:
                    "Surat Bebas Pinjam Perpustakaan Fakultas",

                filename:
                    "bebas_pinjam_fakultas.pdf",

                size:
                    "620 KB"
            },

            {
                key:
                    "khs",

                title:
                    "KHS Semester 1 s/d Terbaru",

                filename:
                    "khs.pdf",

                size:
                    "880 KB"
            },

            {
                key:
                    "transkrip",

                title:
                    "Transkrip Nilai Ujian Skripsi",

                filename:
                    "transkrip.pdf",

                size:
                    "710 KB"
            },

            {
                key:
                    "surat_tugas_pembimbing",

                title:
                    "Surat Tugas Dosen Pembimbing",

                filename:
                    "surat_tugas_pembimbing.pdf",

                size:
                    "1.4 MB"
            },

            {
                key:
                    "bebas_tunggakan",

                title:
                    "Surat Verifikasi Bebas Tunggakan",

                filename:
                    "bebas_tunggakan.pdf",

                size:
                    "530 KB"
            },

            {
                key:
                    "jurnal_manajemen",

                title:
                    "Bukti Pengisian Jurnal Manajemen",

                filename:
                    "jurnal_manajemen.pdf",

                size:
                    "680 KB"
            }

        ];


        /* =====================================
           ELEMENTS
        ===================================== */

        const documentContainer =
            document.getElementById(
                "verificationDocuments"
            );


        const submitButton =
            document.getElementById(
                "submitVerification"
            );


        const confirmation =
            document.getElementById(
                "verificationConfirmation"
            );


        const confirmationError =
            document.getElementById(
                "verificationConfirmationError"
            );


        const approvedCountElement =
            document.getElementById(
                "approvedCount"
            );


        const revisionCountElement =
            document.getElementById(
                "revisionCount"
            );


        const pendingCountElement =
            document.getElementById(
                "pendingCount"
            );


        const progressText =
            document.getElementById(
                "verificationProgressText"
            );


        const progressFill =
            document.getElementById(
                "verificationProgressFill"
            );


        const resultBadge =
            document.getElementById(
                "verificationResultBadge"
            );


        const successModal =
            document.getElementById(
                "verificationSuccessModal"
            );


        const successTitle =
            document.getElementById(
                "verificationSuccessTitle"
            );


        const successMessage =
            document.getElementById(
                "verificationSuccessMessage"
            );


        const successButton =
            document.getElementById(
                "verificationSuccessButton"
            );


        /* =====================================
           RENDER DOCUMENTS
        ===================================== */

        function renderDocuments() {

            documents.forEach(
                function (documentData) {

                    const article =
                        document.createElement(
                            "article"
                        );


                    article.className =
                        "verification-item verification-document-item";


                    article.setAttribute(
                        "data-verification-item",
                        ""
                    );


                    article.dataset.itemType =
                        "document";


                    article.dataset.itemKey =
                        documentData.key;


                    article.innerHTML = `
                        <div class="verification-document-main">

                            <div class="verification-document-icon">
                                PDF
                            </div>

                            <div class="verification-item-content">

                                <span class="verification-item-label">
                                    ${documentData.title}
                                </span>

                                <strong>
                                    ${documentData.filename}
                                </strong>

                                <small>
                                    ${documentData.size}
                                </small>

                            </div>

                        </div>


                        <div class="verification-document-tools">

                            <button
                                type="button"
                                class="verification-preview-button"
                                data-preview-title="${documentData.title}"
                                data-preview-file="${documentData.filename}"
                            >
                                Preview
                            </button>

                        </div>


                        <div class="verification-decision">

                            <button
                                type="button"
                                class="verification-choice approve"
                                data-choice="approved"
                            >
                                ✓ Disetujui
                            </button>

                            <button
                                type="button"
                                class="verification-choice revision"
                                data-choice="revision"
                            >
                                ! Revisi
                            </button>

                        </div>


                        <div class="verification-feedback">

                            <label>
                                Feedback Revisi
                            </label>

                            <textarea
                                placeholder="Jelaskan bagian dokumen yang harus diperbaiki mahasiswa..."
                            ></textarea>

                            <span class="verification-feedback-error">
                                Feedback wajib diisi untuk dokumen yang direvisi.
                            </span>

                        </div>
                    `;


                    documentContainer.appendChild(
                        article
                    );

                }
            );

        }


        renderDocuments();


        /* =====================================
           GET ITEMS
        ===================================== */

        function getVerificationItems() {

            return Array.from(
                document.querySelectorAll(
                    "[data-verification-item]"
                )
            );

        }


        /* =====================================
           DECISION LOGIC
        ===================================== */

        function setupVerificationItems() {

            const items =
                getVerificationItems();


            items.forEach(
                function (item) {

                    const buttons =
                        item.querySelectorAll(
                            ".verification-choice"
                        );


                    const feedbackBox =
                        item.querySelector(
                            ".verification-feedback"
                        );


                    const textarea =
                        feedbackBox.querySelector(
                            "textarea"
                        );


                    const error =
                        feedbackBox.querySelector(
                            ".verification-feedback-error"
                        );


                    buttons.forEach(
                        function (button) {

                            button.addEventListener(
                                "click",
                                function () {

                                    const choice =
                                        this.dataset.choice;


                                    buttons.forEach(
                                        function (
                                            otherButton
                                        ) {

                                            otherButton
                                                .classList
                                                .remove(
                                                    "selected"
                                                );

                                        }
                                    );


                                    this.classList.add(
                                        "selected"
                                    );


                                    item.dataset.decision =
                                        choice;


                                    item.classList.remove(
                                        "approved",
                                        "revision"
                                    );


                                    item.classList.add(
                                        choice ===
                                        "approved"
                                            ?
                                            "approved"
                                            :
                                            "revision"
                                    );


                                    if (
                                        choice ===
                                        "revision"
                                    ) {

                                        feedbackBox
                                            .classList
                                            .add(
                                                "active"
                                            );

                                    } else {

                                        feedbackBox
                                            .classList
                                            .remove(
                                                "active"
                                            );


                                        textarea.value =
                                            "";


                                        error
                                            .classList
                                            .remove(
                                                "active"
                                            );

                                    }


                                    updateVerificationSummary();

                                }
                            );

                        }
                    );


                    textarea.addEventListener(
                        "input",
                        function () {

                            if (
                                this.value
                                    .trim() !==
                                ""
                            ) {

                                error
                                    .classList
                                    .remove(
                                        "active"
                                    );

                            }

                        }
                    );

                }
            );

        }


        setupVerificationItems();


        /* =====================================
           SUMMARY
        ===================================== */

        function updateVerificationSummary() {

            const items =
                getVerificationItems();


            let approved =
                0;


            let revision =
                0;


            let pending =
                0;


            items.forEach(
                function (item) {

                    if (
                        item.dataset.decision ===
                        "approved"
                    ) {

                        approved++;

                    } else if (
                        item.dataset.decision ===
                        "revision"
                    ) {

                        revision++;

                    } else {

                        pending++;

                    }

                }
            );


            approvedCountElement.textContent =
                approved;


            revisionCountElement.textContent =
                revision;


            pendingCountElement.textContent =
                pending;


            const completed =
                approved +
                revision;


            const total =
                items.length;


            progressText.textContent =
                completed +
                " / " +
                total +
                " item";


            const percentage =
                total === 0
                    ?
                    0
                    :
                    (
                        completed /
                        total
                    ) * 100;


            progressFill.style.width =
                percentage +
                "%";


            resultBadge.className =
                "verification-result-badge";


            if (
                pending > 0
            ) {

                resultBadge
                    .classList
                    .add(
                        "pending"
                    );


                resultBadge.textContent =
                    "Belum Lengkap";

            } else if (
                revision > 0
            ) {

                resultBadge
                    .classList
                    .add(
                        "revision"
                    );


                resultBadge.textContent =
                    "Perlu Revisi";

            } else {

                resultBadge
                    .classList
                    .add(
                        "approved"
                    );


                resultBadge.textContent =
                    "Terverifikasi";

            }

        }


        updateVerificationSummary();


        /* =====================================
           PREVIEW
        ===================================== */

        const previewModal =
            document.getElementById(
                "verificationPreviewModal"
            );


        const previewTitle =
            document.getElementById(
                "verificationPreviewTitle"
            );


        const previewFilename =
            document.getElementById(
                "verificationPreviewFilename"
            );


        const closePreview =
            document.getElementById(
                "closeVerificationPreview"
            );


        const openNewTab =
            document.getElementById(
                "verificationOpenNewTab"
            );


        let currentPreviewFile =
            "";


        document.addEventListener(
            "click",
            function (event) {

                const previewButton =
                    event.target.closest(
                        ".verification-preview-button"
                    );


                if (
                    !previewButton
                ) {

                    return;

                }


                previewTitle.textContent =
                    previewButton.dataset
                        .previewTitle;


                previewFilename.textContent =
                    previewButton.dataset
                        .previewFile;


                currentPreviewFile =
                    previewButton.dataset
                        .previewFile;


                previewModal
                    .classList
                    .add(
                        "active"
                    );


                document.body.style.overflow =
                    "hidden";

            }
        );


        function closePreviewModal() {

            previewModal
                .classList
                .remove(
                    "active"
                );


            document.body.style.overflow =
                "";


            currentPreviewFile =
                "";

        }


        closePreview.addEventListener(
            "click",
            closePreviewModal
        );


        previewModal.addEventListener(
            "click",
            function (event) {

                if (
                    event.target ===
                    previewModal
                ) {

                    closePreviewModal();

                }

            }
        );


        document.addEventListener(
            "keydown",
            function (event) {

                if (
                    event.key ===
                    "Escape" &&
                    previewModal.classList
                        .contains(
                            "active"
                        )
                ) {

                    closePreviewModal();

                }

            }
        );


        openNewTab.addEventListener(
            "click",
            function () {

                if (
                    currentPreviewFile ===
                    ""
                ) {

                    return;

                }


                alert(
                    "File " +
                    currentPreviewFile +
                    " akan dibuka melalui URL file dari Laravel setelah integrasi backend."
                );

            }
        );


        /* =====================================
           VALIDATE BEFORE SUBMIT
        ===================================== */

        function validateVerification() {

            const items =
                getVerificationItems();


            let valid =
                true;


            let firstInvalidItem =
                null;


            items.forEach(
                function (item) {

                    const decision =
                        item.dataset.decision;


                    item.classList.remove(
                        "verification-error"
                    );


                    if (
                        !decision
                    ) {

                        valid =
                            false;


                        item.classList.add(
                            "verification-error"
                        );


                        if (
                            !firstInvalidItem
                        ) {

                            firstInvalidItem =
                                item;

                        }


                        return;

                    }


                    if (
                        decision ===
                        "revision"
                    ) {

                        const textarea =
                            item.querySelector(
                                ".verification-feedback textarea"
                            );


                        const error =
                            item.querySelector(
                                ".verification-feedback-error"
                            );


                        if (
                            textarea.value
                                .trim() ===
                            ""
                        ) {

                            valid =
                                false;


                            error.classList.add(
                                "active"
                            );


                            item.classList.add(
                                "verification-error"
                            );


                            if (
                                !firstInvalidItem
                            ) {

                                firstInvalidItem =
                                    item;

                            }

                        }

                    }

                }
            );


            if (
                firstInvalidItem
            ) {

                firstInvalidItem
                    .scrollIntoView(
                        {
                            behavior:
                                "smooth",

                            block:
                                "center"
                        }
                    );

            }


            return valid;

        }


        /* =====================================
           SUBMIT
        ===================================== */

        submitButton.addEventListener(
            "click",
            function () {

                confirmationError
                    .classList
                    .remove(
                        "active"
                    );


                if (
                    !validateVerification()
                ) {

                    return;

                }


                if (
                    !confirmation.checked
                ) {

                    confirmationError
                        .classList
                        .add(
                            "active"
                        );


                    return;

                }


                const items =
                    getVerificationItems();


                const result =
                    [];


                let revisionCount =
                    0;


                items.forEach(
                    function (item) {

                        const decision =
                            item.dataset
                                .decision;


                        const feedback =
                            item.querySelector(
                                ".verification-feedback textarea"
                            );


                        if (
                            decision ===
                            "revision"
                        ) {

                            revisionCount++;

                        }


                        result.push(
                            {
                                type:
                                    item.dataset
                                        .itemType,

                                key:
                                    item.dataset
                                        .itemKey,

                                status:
                                    decision ===
                                    "approved"
                                        ?
                                        "DISETUJUI"
                                        :
                                        "REVISI",

                                feedback:
                                    decision ===
                                    "revision"
                                        ?
                                        feedback.value
                                            .trim()
                                        :
                                        null
                            }
                        );

                    }
                );


                const finalStatus =
                    revisionCount > 0
                        ?
                        "PERLU_REVISI"
                        :
                        "TERVERIFIKASI";


                /*
                 * SIMULASI PAYLOAD FRONTEND
                 *
                 * Nanti payload ini dikirim
                 * ke Laravel.
                 */

                console.log(
                    {
                        pengajuan_id:
                            1,

                        kode_pengajuan:
                            "YDS-2026-0001",

                        final_status:
                            finalStatus,

                        verification:
                            result
                    }
                );


                if (
                    finalStatus ===
                    "PERLU_REVISI"
                ) {

                    successTitle.textContent =
                        "Revisi Dikirim";


                    successMessage.textContent =
                        "Pengajuan ditandai Perlu Revisi. Mahasiswa hanya akan dapat memperbaiki item yang ditandai revisi.";

                } else {

                    successTitle.textContent =
                        "Pengajuan Terverifikasi";


                    successMessage.textContent =
                        "Seluruh data dan dokumen telah disetujui. Pengajuan dapat dilanjutkan ke proses SK.";

                }


                successModal
                    .classList
                    .add(
                        "active"
                    );

            }
        );


        confirmation.addEventListener(
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


        successButton.addEventListener(
            "click",
            function () {

                window.location.href =
                    "pengajuan.html";

            }
        );

    }
);