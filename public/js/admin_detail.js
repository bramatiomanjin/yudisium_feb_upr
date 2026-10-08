document.addEventListener(
    "DOMContentLoaded",
    async function () {

        "use strict";


        if (!window.YudisiumAPI) {
            return;
        }


        const params =
            new URLSearchParams(
                window.location.search
            );


                let id =
            params.get("id");

        if (!id) {
            const parts = window.location.pathname.split("/");
            if (parts.length > 2 && parts[parts.length - 2] === "pengajuan") {
                id = parts[parts.length - 1];
            }
        }


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


        function formatDateTime(value) {
            if (!value) return "-";
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return value;
            return new Intl.DateTimeFormat("id-ID", {
                timeZone: "Asia/Jakarta",
                day: "2-digit",
                month: "long",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit",
                hour12: false
            }).format(date).replace(".", ":") + " WIB";
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


        function setText(
            id,
            value
        ) {

            const element =
                document.getElementById(id);


            if (element) {

                element.textContent =
                    value || "-";

            }

        }


        setText(
            "detailSubmissionCode",
            submission.code
        );

        setText(
            "detailStudentAvatar",
            window.YudisiumAPI
                .getInitials(
                    submission.name
                )
        );

        setText(
            "detailStudentName",
            submission.name
        );

        setText(
            "detailStudentNimSummary",
            "NIM " +
            submission.nim
        );

        setText(
            "detailStudentDepartmentSummary",
            submission.department
        );

        setText(
            "detailSubmittedAt",
            formatDateTime(submission.submittedAt)
        );

        setText(
            "detailStudentFullName",
            submission.name
        );

        setText(
            "detailStudentNim",
            submission.nim
        );

        setText(
            "detailStudentEmail",
            submission.email
        );

        setText(
            "detailStudentWhatsapp",
            submission.whatsapp
        );

        setText(
            "detailStudentYear",
            submission.year
        );

        setText(
            "detailStudentEntryRoute",
            submission.entryRoute
        );

        setText(
            "detailStudentDepartment",
            submission.department
        );

        setText(
            "detailWorkType",
            submission.workType
        );

        setText(
            "detailWorkTitle",
            submission.title
        );

        setText(
            "detailExamDate",
            submission.examDate
        );

        setText(
            "detailExamScore",
            submission.examScore
        );

        setText(
            "detailLetterGrade",
            submission.letterGrade
        );


        const meta =
            window.YudisiumAPI
                .getStatusMeta(
                    submission.status
                );


        const adminStatusLabel =
            submission.status === window.YudisiumAPI.STATUS.PERLU_REVISI
                ? "Menunggu Perbaikan Mahasiswa"
                : submission.status === window.YudisiumAPI.STATUS.REVISI_DIKIRIM
                    ? "Revisi Siap Direview"
                    : meta.label;


        setText(
            "detailSubmissionStatusText",
            adminStatusLabel
        );


        const badge =
            document.getElementById(
                "detailSubmissionStatusBadge"
            );


        if (badge) {

            badge.textContent =
                adminStatusLabel;

            badge.className =
                "admin-status-badge " +
                meta.className;

        }


        const action =
            document.getElementById(
                "detailPrimaryAction"
            );


        if (action) {

            action.href =
                window.YudisiumAPI
                    .getRoute(
                        submission
                    );

            action.style.pointerEvents = "";
            action.style.opacity = "";
            action.removeAttribute("aria-disabled");

            let title = "Proses SK";
            let description = "Lihat dan lanjutkan tahapan proses SK sesuai status pengajuan saat ini.";
            let actionLabel = "Proses SK";


            switch (
                submission.status
            ) {

                case window.YudisiumAPI
                    .STATUS
                    .MENUNGGU_VERIFIKASI:

                    title = "Verifikasi Pengajuan";
                    description = "Periksa data dan dokumen mahasiswa sebelum menentukan hasil verifikasi.";
                    actionLabel = "Verifikasi Pengajuan";

                    break;


                case window.YudisiumAPI
                    .STATUS
                    .PERLU_REVISI:

                    title = "Menunggu Perbaikan Mahasiswa";
                    description = "Mahasiswa belum mengirim perbaikan. Tidak ada tindakan Admin yang diperlukan saat ini.";
                    actionLabel = "Menunggu Perbaikan Mahasiswa";

                    action.style.pointerEvents =
                        "none";

                    action.style.opacity =
                        ".55";

                    action.setAttribute("aria-disabled", "true");

                    break;


                case window.YudisiumAPI
                    .STATUS
                    .REVISI_DIKIRIM:

                    title = "Review Revisi";
                    description = "Perbaikan sudah dikirim mahasiswa dan siap diperiksa oleh Admin.";
                    actionLabel = "Review Revisi";

                    break;


                default:

                    title = "Proses SK";
                    description = "Lihat dan lanjutkan tahapan proses SK sesuai status pengajuan saat ini.";
                    actionLabel = "Proses SK";

            }

            setText("detailActionTitle", title);
            setText("detailActionDescription", description);
            setText("detailPageDescription", description);
            action.textContent = actionLabel;

        }


        function updateSidebarNav(submission) {
            const STATUS = window.YudisiumAPI.STATUS;
            const status = submission.status;

            // Check if we came from the History page
            const urlParams = new URLSearchParams(window.location.search);
            const fromHistory = urlParams.get("from") === "history";

            document.querySelectorAll(".admin-nav-item").forEach(function (el) {
                el.classList.remove("active");
            });

            let activeHref;
            if (fromHistory) {
                activeHref = "/admin/history";
            } else if (status === STATUS.PERLU_REVISI) {
                activeHref = "/admin/pengajuan?filter=menunggu-revisi";
            } else if (status === STATUS.REVISI_DIKIRIM) {
                activeHref = "/admin/pengajuan?filter=review-revisi";
            } else if ([STATUS.TERVERIFIKASI, STATUS.PEMBUATAN_SK, STATUS.PARAF_PIMPINAN, STATUS.TTD_DEKAN, STATUS.SK_SIAP_DIAMBIL].includes(status)) {
                activeHref = "/admin/pengajuan?filter=proses-sk";
            } else {
                activeHref = "/admin/pengajuan";
            }

            const activeItem = document.querySelector('.admin-nav-item[href="' + activeHref + '"]');
            if (activeItem) {
                activeItem.classList.add("active");
            }
        }


        /* =====================================================
           DOCUMENT
        ===================================================== */

        updateSidebarNav(submission);

        const documents =
            await window.YudisiumAPI
                .getDocuments(
                    submission.id
                );


        const list =
            document.querySelector(
                ".detail-document-list"
            );


        const count =
            document.querySelector(
                ".detail-document-count"
            );


        if (count) {

            count.textContent =
                documents.length +
                " Dokumen";

        }


        if (!list) {
            return;
        }


        list.innerHTML =
            "";


        if (
            documents.length === 0
        ) {

            list.innerHTML =
                `
                <div style="padding:24px;text-align:center;">
                    Dokumen akan tampil setelah data tersedia dari backend.
                </div>
                `;

            return;

        }


        documents.forEach(
            function (documentData) {

                const previewUrl =
                    safeDocumentUrl(
                        documentData.url
                    );

                const item =
                    document.createElement(
                        "div"
                    );


                item.className =
                    "detail-document-item";


                item.innerHTML =
                    `
                    <div class="detail-document-info" style="display: flex; align-items: center; gap: 12px;">
                        ${previewUrl ? `<input type="checkbox" class="document-checkbox" value="${escapeHtml(previewUrl)}">` : ''}
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
                                    <a
                                        href="${escapeHtml(previewUrl)}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="admin-detail-button"
                                    >
                                        Preview
                                    </a>
                                `
                                : `
                                    <span class="admin-status-badge pending">
                                        File belum tersedia
                                    </span>
                                `
                        }

                    </div>
                    `;


                list.appendChild(item);

            }
        );

        // Bulk action logic
        const selectAllCheckbox = document.getElementById("selectAllDocuments");
        const bulkActionBar = document.getElementById("bulkActionBar");
        const bulkSelectedCount = document.getElementById("bulkSelectedCount");
        const btnBulkPreview = document.getElementById("btnBulkPreview");

        function updateBulkActionBar() {
            const checkboxes = document.querySelectorAll(".document-checkbox");
            const selectedCheckboxes = document.querySelectorAll(".document-checkbox:checked");
            const selectedCount = selectedCheckboxes.length;

            if (selectedCount > 0) {
                bulkActionBar.style.display = "flex";
                bulkSelectedCount.textContent = selectedCount;
            } else {
                bulkActionBar.style.display = "none";
            }

            if (selectAllCheckbox) {
                selectAllCheckbox.checked = checkboxes.length > 0 && selectedCount === checkboxes.length;
                selectAllCheckbox.indeterminate = selectedCount > 0 && selectedCount < checkboxes.length;
            }
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener("change", function () {
                const checkboxes = document.querySelectorAll(".document-checkbox");
                checkboxes.forEach(function (cb) {
                    cb.checked = selectAllCheckbox.checked;
                });
                updateBulkActionBar();
            });
        }

        list.addEventListener("change", function (e) {
            if (e.target.classList.contains("document-checkbox")) {
                updateBulkActionBar();
            }
        });

        if (btnBulkPreview) {
            btnBulkPreview.addEventListener("click", function () {
                const selectedCheckboxes = document.querySelectorAll(".document-checkbox:checked");
                selectedCheckboxes.forEach(function (cb) {
                    if (cb.value) {
                        window.open(cb.value, "_blank");
                    }
                });
            });
        }

    }
);
