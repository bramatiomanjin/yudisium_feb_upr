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


        const id =
            params.get("id");


        if (!id) {

            window.location.href =
                "pengajuan.html";

            return;

        }


        const submission =
            await window.YudisiumAPI
                .getSubmission(id);


        if (!submission) {

            window.location.href =
                "pengajuan.html";

            return;

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
            submission.submittedAt
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


        setText(
            "detailSubmissionStatusText",
            meta.label
        );


        const badge =
            document.getElementById(
                "detailSubmissionStatusBadge"
            );


        if (badge) {

            badge.textContent =
                meta.label;

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


            switch (
                submission.status
            ) {

                case window.YudisiumAPI
                    .STATUS
                    .MENUNGGU_VERIFIKASI:

                    action.textContent =
                        "Mulai Verifikasi";

                    break;


                case window.YudisiumAPI
                    .STATUS
                    .PERLU_REVISI:

                    action.textContent =
                        "Menunggu Revisi Mahasiswa";

                    action.style.pointerEvents =
                        "none";

                    action.style.opacity =
                        ".55";

                    break;


                case window.YudisiumAPI
                    .STATUS
                    .REVISI_DIKIRIM:

                    action.textContent =
                        "Review Revisi";

                    break;


                case window.YudisiumAPI
                    .STATUS
                    .SK_SIAP_DIAMBIL:

                    action.textContent =
                        "Lihat Status SK";

                    break;


                default:

                    action.textContent =
                        "Proses SK";

            }

        }


        /* =====================================================
           DOCUMENT
        ===================================================== */

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

                const item =
                    document.createElement(
                        "div"
                    );


                item.className =
                    "detail-document-item";


                item.innerHTML =
                    `
                    <div class="detail-document-info">

                        <div class="detail-document-icon">
                            FILE
                        </div>

                        <div>
                            <strong>
                                ${documentData.title || documentData.label || "-"}
                            </strong>

                            <span>
                                ${documentData.filename || "-"}
                            </span>
                        </div>

                    </div>

                    <div class="detail-document-actions">

                        ${
                            documentData.url
                                ? `
                                    <a
                                        href="${documentData.url}"
                                        target="_blank"
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

    }
);