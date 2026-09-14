document.addEventListener(
    "DOMContentLoaded",
    async function () {

        "use strict";

        /* =========================================================
           CEK API
        ========================================================= */

        if (!window.YudisiumAPI) {
            console.error("YudisiumAPI tidak ditemukan.");
            return;
        }

        const API = window.YudisiumAPI;


        /* =========================================================
           AMBIL ID PENGAJUAN DARI URL
        ========================================================= */

        const params =
            new URLSearchParams(
                window.location.search
            );

        const id =
            params.get("id");


        if (!id) {
            window.location.href =
                "/admin/pengajuan";

            return;
        }


        /* =========================================================
           HELPER
        ========================================================= */

        function setText(selector, value) {

            const element =
                document.querySelector(selector);

            if (!element) {
                return;
            }

            const finalValue =
                value !== undefined &&
                value !== null &&
                String(value).trim() !== ""
                    ? value
                    : "-";

            element.textContent =
                finalValue;
        }


        function formatDate(value) {

            if (!value) {
                return "-";
            }

            const date =
                new Date(value);

            if (
                Number.isNaN(
                    date.getTime()
                )
            ) {
                return value;
            }

            return new Intl.DateTimeFormat(
                "id-ID",
                {
                    timeZone: "Asia/Jakarta",
                    day: "2-digit",
                    month: "long",
                    year: "numeric"
                }
            ).format(date);
        }


        function formatScore(value) {

            if (
                value === undefined ||
                value === null ||
                value === ""
            ) {
                return "-";
            }

            const number =
                Number(value);

            if (
                Number.isNaN(number)
            ) {
                return value;
            }

            return number
                .toFixed(2)
                .replace(".", ",");
        }


        function formatFieldValue(
            submission,
            key
        ) {

            let value =
                API.getFieldValue(
                    submission,
                    key
                );

            if (
                key === "tanggal_ujian"
            ) {
                return formatDate(value);
            }

            if (
                key === "nilai_angka"
            ) {
                return formatScore(value);
            }

            return (
                value !== undefined &&
                value !== null &&
                String(value).trim() !== ""
            )
                ? value
                : "-";
        }


        /* =========================================================
           LOAD DATA PENGAJUAN
        ========================================================= */

        let submission = null;
        let documents = [];

        try {

            submission =
                await API.getSubmission(id);


            if (!submission) {

                alert(
                    "Data pengajuan tidak ditemukan."
                );

                window.location.href =
                    "/admin/pengajuan";

                return;
            }


            /*
             * Halaman verifikasi hanya untuk
             * pengajuan yang masih menunggu verifikasi.
             */
            if (
                submission.status !==
                API.STATUS.MENUNGGU_VERIFIKASI
            ) {

                window.location.href =
                    "/admin/pengajuan/" +
                    submission.id;

                return;
            }


            try {

                documents =
                    await API.getDocuments(
                        submission.id
                    );

                if (
                    !Array.isArray(documents)
                ) {
                    documents = [];
                }

            } catch (documentError) {

                console.error(
                    "Gagal mengambil dokumen:",
                    documentError
                );

                documents = [];
            }

        } catch (error) {

            console.error(
                "Gagal mengambil data pengajuan:",
                error
            );

            alert(
                error?.message ||
                "Data pengajuan gagal dimuat."
            );

            return;
        }


        /* =========================================================
           HEADER
        ========================================================= */

        setText(
            ".verification-header h1",
            submission.code
        );


        setText(
            ".verification-student-avatar",
            API.getInitials(
                submission.name
            )
        );


        setText(
            ".verification-student-info h2",
            submission.name
        );


        setText(
            ".verification-student-info p",
            "NIM " +
                (submission.nim || "-") +
                " • " +
                (submission.department || "-") +
                " • Angkatan " +
                (submission.year || "-")
        );


        /* =========================================================
           ISI DATA MAHASISWA + AKADEMIK
        ========================================================= */

        const fieldItems =
            Array.from(
                document.querySelectorAll(
                    '[data-verification-item][data-item-type="field"]'
                )
            );


        fieldItems.forEach(
            function (item) {

                const key =
                    item.dataset.itemKey;

                const valueElement =
                    item.querySelector(
                        ".verification-item-content strong"
                    );


                if (
                    !key ||
                    !valueElement
                ) {
                    return;
                }


                valueElement.textContent =
                    formatFieldValue(
                        submission,
                        key
                    );

            }
        );


        /* =========================================================
           RENDER DOKUMEN
        ========================================================= */

        const documentContainer =
            document.getElementById(
                "verificationDocuments"
            );


        if (documentContainer) {

            documentContainer.innerHTML =
                "";


            if (
                documents.length === 0
            ) {

                const empty =
                    document.createElement(
                        "div"
                    );

                empty.className =
                    "verification-document-empty";

                empty.innerHTML = `
                    <strong>
                        Belum ada dokumen yang dapat ditampilkan.
                    </strong>

                    <span>
                        Dokumen pengajuan belum tersedia atau gagal dimuat.
                    </span>
                `;

                documentContainer
                    .appendChild(
                        empty
                    );

            } else {

                documents.forEach(
                    function (doc) {

                        const item =
                            document.createElement(
                                "article"
                            );


                        item.className =
                            "verification-item verification-document-item";


                        item.dataset.verificationItem =
                            "";

                        item.dataset.itemType =
                            "document";

                        item.dataset.itemKey =
                            doc.key ||
                            doc.id ||
                            "";


                        const title =
                            doc.title ||
                            doc.label ||
                            doc.nama_dokumen ||
                            "Dokumen";


                        const filename =
                            doc.filename ||
                            doc.nama_file_asli ||
                            "-";


                        const previewUrl =
                            doc.url ||
                            doc.file_url ||
                            null;


                        item.innerHTML = `
                                                        <div class="verification-document-main">

                                <div class="verification-document-icon">
                                    FILE
                                </div>

                                <div class="verification-document-info">
                                    <strong>${title}</strong>
                                    <span>${filename}</span>
                                </div>

                                ${previewUrl ? `
                                    <button
                                        type="button"
                                        class="verification-preview-button"
                                        data-preview-url="${previewUrl}"
                                        data-preview-title="${title}"
                                        data-preview-filename="${filename}"
                                    >
                                        Preview
                                    </button>
                                ` : ""}

                            </div>

                                <div class="verification-document-info">

                                    <strong>
                                        ${title}
                                    </strong>

                                    <span>
                                        ${filename}
                                    </span>

                                </div>

                                ${
                                    previewUrl
                                        ? `
                                            <a
                                                href="${previewUrl}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="verification-preview-button"
                                            >
                                                Preview
                                            </a>
                                        `
                                        : ""
                                }

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
                                    placeholder="Jelaskan bagian yang harus diperbaiki mahasiswa..."
                                ></textarea>

                                <span class="verification-feedback-error">
                                    Feedback wajib diisi.
                                </span>

                            </div>
                        `;


                        documentContainer
                            .appendChild(
                                item
                            );

                    }
                );

            }

        }


        /* =========================================================
           AMBIL SEMUA ITEM VERIFIKASI
        ========================================================= */

        const items =
            Array.from(
                document.querySelectorAll(
                    "[data-verification-item]"
                )
            );


        const confirmation =
            document.getElementById(
                "verificationConfirmation"
            );


        const confirmationError =
            document.getElementById(
                "verificationConfirmationError"
            );


        const submitButton =
            document.getElementById(
                "submitVerification"
            );


        const progressText =
            document.getElementById(
                "verificationProgressText"
            );


        const progressFill =
            document.getElementById(
                "verificationProgressFill"
            );


        const approvedCount =
            document.getElementById(
                "approvedCount"
            );


        const revisionCount =
            document.getElementById(
                "revisionCount"
            );


        const pendingCount =
            document.getElementById(
                "pendingCount"
            );


        const resultBadge =
            document.getElementById(
                "verificationResultBadge"
            );


        const studentDataStatus =
            document.getElementById(
                "studentDataStatus"
            );


        /* =========================================================
           UPDATE PROGRESS
        ========================================================= */

        function updateProgress() {

            let approved = 0;
            let revision = 0;
            let pending = 0;


            items.forEach(
                function (item) {

                    const decision =
                        item.dataset.decision;


                    if (
                        decision === "approved"
                    ) {

                        approved++;

                    } else if (
                        decision === "revision"
                    ) {

                        revision++;

                    } else {

                        pending++;

                    }

                }
            );


            const total =
                items.length;

            const completed =
                approved +
                revision;


            if (approvedCount) {
                approvedCount.textContent =
                    approved;
            }


            if (revisionCount) {
                revisionCount.textContent =
                    revision;
            }


            if (pendingCount) {
                pendingCount.textContent =
                    pending;
            }


            if (progressText) {
                progressText.textContent =
                    completed +
                    " / " +
                    total +
                    " item";
            }


            if (progressFill) {

                const percentage =
                    total > 0
                        ? (
                            completed /
                            total
                        ) * 100
                        : 0;

                progressFill.style.width =
                    percentage + "%";
            }


            if (resultBadge) {

                resultBadge.classList.remove(
                    "pending"
                );

                resultBadge.classList.remove(
                    "approved"
                );

                resultBadge.classList.remove(
                    "revision"
                );


                if (
                    pending > 0
                ) {

                    resultBadge.textContent =
                        "Belum Lengkap";

                    resultBadge.classList.add(
                        "pending"
                    );

                } else if (
                    revision > 0
                ) {

                    resultBadge.textContent =
                        "Perlu Revisi";

                    resultBadge.classList.add(
                        "revision"
                    );

                } else {

                    resultBadge.textContent =
                        "Terverifikasi";

                    resultBadge.classList.add(
                        "approved"
                    );

                }

            }


            /*
             * Status bagian data mahasiswa.
             */
            if (studentDataStatus) {

                const studentItems =
                    items.filter(
                        function (item) {

                            return (
                                item.dataset.itemType ===
                                "field"
                            );

                        }
                    );


                const studentCompleted =
                    studentItems.every(
                        function (item) {

                            return Boolean(
                                item.dataset.decision
                            );

                        }
                    );


                studentDataStatus.textContent =
                    studentCompleted
                        ? "Selesai"
                        : "Belum selesai";

            }

        }


        /* =========================================================
           PILIH DISETUJUI / REVISI
        ========================================================= */

        items.forEach(
            function (item) {

                const buttons =
                    item.querySelectorAll(
                        ".verification-choice"
                    );


                const feedback =
                    item.querySelector(
                        ".verification-feedback"
                    );


                const textarea =
                    feedback
                        ?.querySelector(
                            "textarea"
                        );


                buttons.forEach(
                    function (button) {

                        button.addEventListener(
                            "click",
                            function () {

                                buttons.forEach(
                                    function (other) {

                                        other
                                            .classList
                                            .remove(
                                                "active"
                                            );

                                    }
                                );


                                button.classList.add(
                                    "active"
                                );


                                item.dataset.decision =
                                    button.dataset.choice;


                                if (
                                    button.dataset.choice ===
                                    "revision"
                                ) {

                                    feedback
                                        ?.classList
                                        .add(
                                            "active"
                                        );

                                } else {

                                    feedback
                                        ?.classList
                                        .remove(
                                            "active"
                                        );


                                    if (textarea) {
                                        textarea.value =
                                            "";
                                    }

                                }


                                updateProgress();

                            }
                        );

                    }
                );

            }
        );


        /* =========================================================
           KONFIRMASI
        ========================================================= */

        confirmation
            ?.addEventListener(
                "change",
                function () {

                    if (
                        confirmation.checked
                    ) {

                        confirmationError
                            ?.classList
                            .remove(
                                "active"
                            );

                    }

                }
            );


        /* =========================================================
           SIMPAN HASIL VERIFIKASI
        ========================================================= */

        if (submitButton) {

            submitButton.addEventListener(
                "click",
                async function () {

                    const result =
                        [];


                    let valid =
                        true;


                    items.forEach(
                        function (item) {

                            const decision =
                                item.dataset.decision;


                            const textarea =
                                item.querySelector(
                                    ".verification-feedback textarea"
                                );


                            const feedbackError =
                                item.querySelector(
                                    ".verification-feedback-error"
                                );


                            if (!decision) {

                                valid =
                                    false;

                                return;

                            }


                            if (
                                decision ===
                                "revision" &&
                                (
                                    !textarea ||
                                    textarea.value
                                        .trim() ===
                                        ""
                                )
                            ) {

                                valid =
                                    false;


                                feedbackError
                                    ?.classList
                                    .add(
                                        "active"
                                    );


                                return;

                            }


                            feedbackError
                                ?.classList
                                .remove(
                                    "active"
                                );


                            result.push({

                                type:
                                    item.dataset.itemType,

                                key:
                                    item.dataset.itemKey,

                                decision:
                                    decision,

                                feedback:
                                    decision ===
                                    "revision"
                                        ? textarea
                                            ?.value
                                            .trim()
                                        : null

                            });

                        }
                    );


                    if (
                        !confirmation
                            ?.checked
                    ) {

                        valid =
                            false;


                        confirmationError
                            ?.classList
                            .add(
                                "active"
                            );

                    } else {

                        confirmationError
                            ?.classList
                            .remove(
                                "active"
                            );

                    }


                    if (!valid) {

                        alert(
                            "Lengkapi seluruh keputusan verifikasi dan feedback revisi terlebih dahulu."
                        );

                        return;

                    }


                    try {

                        submitButton.disabled =
                            true;


                        submitButton.textContent =
                            "Menyimpan...";


                        await API.verifySubmission(
                            submission.id,
                            {
                                items:
                                    result
                            }
                        );


                        window.location.href =
                            "/admin/pengajuan/" +
                            submission.id;

                    } catch (error) {

                        console.error(
                            "Gagal menyimpan verifikasi:",
                            error
                        );


                        alert(
                            error?.message ||
                            "Hasil verifikasi gagal disimpan."
                        );

                    } finally {

                        submitButton.disabled =
                            false;


                        submitButton.textContent =
                            "Simpan Hasil Verifikasi";

                    }

                }
            );

        }


        /* =========================================================
           NILAI AWAL PROGRESS
        ========================================================= */

        updateProgress();


        console.log(
            "Halaman verifikasi siap:",
            submission
        );

    
        const modal = document.getElementById("verificationPreviewModal");
        const closeBtn = document.getElementById("closeVerificationPreview");
        const iframe = document.getElementById("verificationPreviewFrame");
        const placeholder = document.getElementById("verificationPreviewPlaceholder");
        const titleEl = document.getElementById("verificationPreviewTitle");
        const filenameEl = document.getElementById("verificationPreviewFilename");
        const openNewTab = document.getElementById("verificationOpenNewTab");
        let currentUrl = "";

        document.addEventListener("click", function(e) {
            if (e.target.classList.contains("verification-preview-button")) {
                currentUrl = e.target.dataset.previewUrl;
                const title = e.target.dataset.previewTitle;
                const filename = e.target.dataset.previewFilename;

                if (titleEl) titleEl.textContent = title;
                if (filenameEl) filenameEl.textContent = filename;

                if (iframe && placeholder) {
                    placeholder.style.display = "none";
                    iframe.style.display = "block";
                    iframe.src = currentUrl;
                }

                if (modal) modal.classList.add("active");
            }
        });

        if (closeBtn && modal) {
            closeBtn.addEventListener("click", function() {
                modal.classList.remove("active");
                if (iframe) iframe.src = "";
            });
        }

        if (openNewTab) {
            openNewTab.addEventListener("click", function() {
                if (currentUrl) window.open(currentUrl, "_blank");
            });
        }

    }
);