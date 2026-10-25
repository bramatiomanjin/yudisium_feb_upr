document.addEventListener(
    "DOMContentLoaded",
    async function () {

        "use strict";


        if (!window.YudisiumAPI) {
            return;
        }

        function escapeHtml(value) {
            const element = document.createElement("div");
            element.textContent = value ?? "";

            return element.innerHTML;
        }


        function safeDocumentUrl(value) {
            if (!value) {
                return "";
            }

            try {
                const url = new URL(String(value), window.location.origin);

                return url.origin === window.location.origin &&
                    ["http:", "https:"].includes(url.protocol)
                    ? url.href
                    : "";
            } catch (error) {
                return "";
            }
        }


        function formatDate(value) {
            if (!value) return "-";

            if (typeof value === "string") {
                const match = value.match(/^(\d{4})-(\d{2})-(\d{2})/);
                if (match) {
                    const [, year, month, day] = match;
                    const date = new Date(Date.UTC(+year, +month - 1, +day));
                    return new Intl.DateTimeFormat("id-ID", {
                        timeZone: "UTC",
                        day: "numeric",
                        month: "long",
                        year: "numeric"
                    }).format(date);
                }
            }

            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return value;

            return new Intl.DateTimeFormat("id-ID", {
                timeZone: "Asia/Jakarta",
                day: "numeric",
                month: "long",
                year: "numeric"
            }).format(date);
        }


        function setText(id, value) {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value || "-";
            }
        }


        const params =
            new URLSearchParams(
                window.location.search
            );


        const id =
            params.get("id");


        if (!id) {

            window.location.href =
                "/admin/dashboard";

            return;

        }


        const submission =
            await window.YudisiumAPI
                .getSubmission(id);


        if (!submission) {

            window.location.href =
                "/admin/dashboard";

            return;

        }


        if (
            submission.status !==
            window.YudisiumAPI
                .STATUS
                .REVISI_DIKIRIM
        ) {

            window.location.href =
                "/admin/pengajuan/" +
                submission.id;

            return;

        }


        const backLink = document.getElementById("reviewBackLink");
        if (backLink) {
            backLink.href = "/admin/pengajuan?filter=review-revisi";
        }

        setText("reviewSubmissionCode", submission.code);
        setText("reviewStudentName", submission.name);
        setText(
            "reviewStudentAvatar",
            window.YudisiumAPI.getInitials(submission.name)
        );
        setText(
            "reviewStudentSummary",
            "NIM " + submission.nim + " • " + submission.department
        );

        /* =====================================================
           IDENTITAS MAHASISWA
        ===================================================== */
        setText("detailStudentFullName", submission.name);
        setText("detailStudentNim", submission.nim);
        setText("detailStudentEmail", submission.email);
        setText("detailStudentWhatsapp", submission.whatsapp);
        setText("detailStudentYear", submission.year);
        setText("detailStudentEntryRoute", submission.entryRoute);
        setText("detailStudentDepartment", submission.department);

        /* =====================================================
           DATA AKADEMIK
        ===================================================== */
        setText("detailWorkType", submission.workType);
        setText("detailExamDate", formatDate(submission.examDate));
        setText("detailWorkTitle", submission.title);
        setText("detailExamScore", submission.examScore);
        setText("detailLetterGrade", submission.letterGrade);

        /* =====================================================
           DOKUMEN MAHASISWA
        ===================================================== */
        try {
            const documents =
                await window.YudisiumAPI.getDocuments(submission.id);

            const docList =
                document.getElementById("detailDocumentList");

            const docCount =
                document.getElementById("detailDocumentCount");

            if (docCount) {
                docCount.textContent =
                    (documents ? documents.length : 0) + " Dokumen";
            }

            if (docList) {
                docList.innerHTML = "";

                if (!documents || documents.length === 0) {
                    docList.innerHTML = `
                        <div style="padding:24px;text-align:center;color:var(--feb-muted);">
                            Dokumen mahasiswa belum tersedia dari backend.
                        </div>
                    `;
                } else {
                    documents.forEach(function (documentData) {
                        const previewUrl =
                            safeDocumentUrl(documentData.url);

                        const item =
                            document.createElement("div");

                        item.className =
                            "detail-document-item";

                        item.innerHTML = `
                            <div class="detail-document-info">
                                <div class="detail-document-icon">
                                    FILE
                                </div>
                                <div>
                                    <strong>
                                        ${escapeHtml(documentData.title || documentData.label || "-")}
                                    </strong>
                                    <span>
                                        ${escapeHtml(documentData.filename || "-")}
                                    </span>
                                </div>
                            </div>
                            <div class="detail-document-actions">
                                ${
                                    previewUrl
                                        ? `
                                            <button
                                                type="button"
                                                class="admin-detail-button preview-document-button"
                                                data-preview-url="${escapeHtml(previewUrl)}"
                                                data-preview-title="${escapeHtml(documentData.title || documentData.label || "Dokumen")}"
                                                data-preview-filename="${escapeHtml(documentData.filename || "dokumen.pdf")}"
                                            >
                                                Preview
                                            </button>
                                        `
                                        : `
                                            <span class="admin-status-badge pending">
                                                File belum tersedia
                                            </span>
                                        `
                                }
                            </div>
                        `;

                        docList.appendChild(item);
                    });
                }
            }
        } catch (docErr) {
            console.error("Gagal memuat dokumen mahasiswa:", docErr);
        }

        /* =====================================================
           DATA REVISI MAHASISWA
        ===================================================== */
        const revision =
            await window.YudisiumAPI
                .getRevisionSubmission(
                    submission.id
                );

        const roundEl =
            document.getElementById("reviewRevisionRound");

        if (roundEl) {
            roundEl.textContent =
                revision?.round || submission.revisionCount || "1";
        }

        const container =
            document.getElementById(
                "reviewRevisionItemsContainer"
            );

        if (
            !revision ||
            !Array.isArray(
                revision.items
            ) ||
            revision.items.length === 0
        ) {
            if (container) {
                container.innerHTML = `
                    <div class="revision-info-box">
                        Tidak ada item revisi yang perlu diperiksa atau data belum tersedia dari backend.
                    </div>
                `;
            }
            return;
        }

        container.innerHTML =
            "";


        revision.items.forEach(
            function (
                item,
                index
            ) {

                const card =
                    document.createElement(
                        "section"
                    );


                card.className =
                    "revision-review-section";


                const previewUrl =
                    safeDocumentUrl(
                        item.newFile?.url
                    );


                card.innerHTML =
                    `
                    <article
                        class="revision-review-item"
                        data-review-item
                        data-item-key="${escapeHtml(item.key)}"
                        data-item-type="${escapeHtml(item.type)}"
                    >

                        <div class="revision-item-title">

                            <div>
                                <span>
                                    Item ${index + 1}
                                </span>

                                <strong>
                                    ${escapeHtml(item.label || item.key)}
                                </strong>
                            </div>

                        </div>

                        <div class="revision-comparison">

                            <div class="revision-old-value">

                                <span>
                                    Sebelum Revisi
                                </span>

                                <p>
                                    ${escapeHtml(
                                        item.oldValue ||
                                        item.oldFile ||
                                        "-"
                                    )}
                                </p>

                            </div>

                            <div class="revision-arrow">
                                →
                            </div>

                            <div class="revision-new-value">

                                <span>
                                    Setelah Revisi
                                </span>

                                <p>
                                    ${escapeHtml(
                                        item.newValue ||
                                        item.newFile?.name ||
                                        "-"
                                    )}
                                </p>

                                ${item.type === 'document' && previewUrl ? `
                                    <button
                                        type="button"
                                        class="revision-preview-button admin-secondary-button"
                                        data-preview-url="${escapeHtml(previewUrl)}"
                                        data-preview-title="${escapeHtml(item.label || item.key)}"
                                        data-preview-filename="${escapeHtml(item.newFile.name)}"
                                        data-preview-mime="${escapeHtml(item.newFile.mimeType || '')}"
                                        style="margin-top: 8px; padding: 4px 12px; font-size: 12px;"
                                    >
                                        Preview Dokumen
                                    </button>
                                ` : ""}

                            </div>

                        </div>

                        <div class="revision-admin-feedback">

                            <span>
                                Feedback Sebelumnya
                            </span>

                            <p>
                                ${escapeHtml(item.feedback || "-")}
                            </p>

                        </div>

                        <div class="revision-review-actions">

                            <button
                                type="button"
                                class="revision-review-choice approve"
                                data-choice="approved"
                            >
                                ✓ Setujui Revisi
                            </button>

                            <button
                                type="button"
                                class="revision-review-choice revision"
                                data-choice="revision"
                            >
                                ! Revisi Lagi
                            </button>

                        </div>

                        <div class="revision-review-feedback">

                            <label>
                                Feedback Revisi Berikutnya
                            </label>

                            <textarea></textarea>

                        </div>

                    </article>
                    `;


                container.appendChild(
                    card
                );

            }
        );


        const items =
            Array.from(
                document.querySelectorAll(
                    "[data-review-item]"
                )
            );


        items.forEach(
            function (item) {

                const buttons =
                    item.querySelectorAll(
                        ".revision-review-choice"
                    );


                buttons.forEach(
                    function (button) {

                        button.addEventListener(
                            "click",
                            function () {

                                buttons.forEach(
                                    function (other) {

                                        other.classList
                                            .remove(
                                                "selected"
                                            );

                                    }
                                );


                                button.classList.add(
                                    "selected"
                                );


                                item.dataset.decision =
                                    button.dataset.choice;


                                item.querySelector(
                                    ".revision-review-feedback"
                                )
                                    ?.classList
                                    .toggle(
                                        "active",
                                        button.dataset.choice ===
                                        "revision"
                                    );

                                updateCounters();

                            }
                        );

                    }
                );

            }
        );


        function updateCounters() {
            let approved = 0;
            let revisionCount = 0;
            let pending = 0;

            items.forEach(function(item) {
                if (item.dataset.decision === 'approved') {
                    approved++;
                } else if (item.dataset.decision === 'revision') {
                    revisionCount++;
                } else {
                    pending++;
                }
            });

            const approvedEl = document.getElementById("revisionApprovedCount");
            const revisionEl = document.getElementById("revisionAgainCount");
            const pendingEl = document.getElementById("revisionPendingCount");

            if (approvedEl) approvedEl.textContent = approved;
            if (revisionEl) revisionEl.textContent = revisionCount;
            if (pendingEl) pendingEl.textContent = pending;

            const statusEl = document.getElementById("revisionResultStatus");
            if (statusEl) {
                statusEl.className = "revision-result-status";
                if (pending > 0) {
                    statusEl.classList.add("pending");
                    statusEl.textContent = "Belum Lengkap";
                } else if (revisionCount > 0) {
                    statusEl.classList.add("revision");
                    statusEl.textContent = "Ada Revisi";
                } else {
                    statusEl.classList.add("approved");
                    statusEl.textContent = "Semua Disetujui";
                }
            }
        }

        updateCounters();


        const submit =
            document.getElementById(
                "submitRevisionReview"
            );


        submit?.addEventListener(
            "click",
            async function () {

                const payload =
                    [];


                let valid =
                    true;


                items.forEach(
                    function (item) {

                        const decision =
                            item.dataset.decision;


                        const textarea =
                            item.querySelector(
                                "textarea"
                            );


                        if (!decision) {

                            valid =
                                false;

                            return;

                        }


                        if (
                            decision ===
                            "revision" &&
                            textarea.value
                                .trim() ===
                                ""
                        ) {

                            valid =
                                false;

                            return;

                        }


                        payload.push({

                            key:
                                item.dataset.itemKey,

                            type:
                                item.dataset.itemType,

                            decision:
                                decision,

                            feedback:
                                decision ===
                                    "revision"
                                    ? textarea.value.trim()
                                    : null

                        });

                    }
                );


                if (!valid) {

                    alert(
                        "Periksa seluruh item revisi terlebih dahulu."
                    );

                    return;

                }


                try {

                    submit.disabled =
                        true;


                    await window.YudisiumAPI
                        .reviewRevision(
                            submission.id,
                            {
                                items:
                                    payload
                            }
                        );


                    window.location.href =
                        "/admin/dashboard";

                } catch (error) {

                    alert(
                        "Backend belum terhubung. Hasil review belum dapat disimpan."
                    );

                } finally {

                    submit.disabled =
                        false;

                }

            }
        );



        const modal = document.getElementById("revisionPreviewModal");
        const closeBtn = document.getElementById("closeRevisionPreview");
        const iframe = document.getElementById("revisionPreviewFrame");
        const placeholder = document.getElementById("revisionPreviewPlaceholder");
        const titleEl = document.getElementById("revisionPreviewTitle");
        const filenameEl = document.getElementById("revisionPreviewFilename");
        const openNewTab = document.getElementById("revisionOpenNewTab");
        let currentUrl = "";

        document.addEventListener("click", function(e) {
            const btn = e.target.closest(".revision-preview-button, .preview-document-button");
            if (!btn) {
                return;
            }

            currentUrl = btn.dataset.previewUrl || "";
            const title = btn.dataset.previewTitle || "Dokumen";
            const filename = btn.dataset.previewFilename || "dokumen.pdf";

            if (titleEl) titleEl.textContent = title;
            if (filenameEl) filenameEl.textContent = filename;

            if (iframe && placeholder) {
                const isPdf = filename.toLowerCase().endsWith(".pdf");
                if (!isPdf) {
                    iframe.style.display = "none";
                    iframe.src = "";
                    placeholder.style.display = "flex";
                    const p = placeholder.querySelector("p");
                    if (p) p.textContent = "Format file tidak mendukung preview langsung. Silakan klik Buka di Tab Baru untuk mengunduh.";
                } else {
                    placeholder.style.display = "none";
                    iframe.style.display = "block";
                    iframe.src = currentUrl;
                }
            }

            if (modal) {
                modal.classList.add("active");
                window.YudisiumModalAccessibility?.open(modal, btn);
            }
        });

        function closePreview() {
            if (!modal) return;

            modal.classList.remove("active");
            if (iframe) iframe.src = "";
            window.YudisiumModalAccessibility?.close(modal);
        }

        window.YudisiumModalAccessibility?.enhance(modal, closePreview);

        if (closeBtn && modal) {
            closeBtn.addEventListener("click", closePreview);
        }

        if (openNewTab) {
            openNewTab.addEventListener("click", function () {
                if (currentUrl) {
                    window.open(currentUrl, "_blank");
                }
            });
        }

    }
);
