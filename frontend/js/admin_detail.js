document.addEventListener(
    "DOMContentLoaded",
    function () {

        console.log(
            "Admin detail pengajuan aktif"
        );


        const params =
            new URLSearchParams(
                window.location.search
            );


        const submission =
            window.YudisiumMockDB
                ?
                window.YudisiumMockDB
                    .getSubmission(
                        params.get("id")
                    )
                :
                null;


        if (
            !submission
        ) {

            window.location.href =
                "pengajuan.html";


            return;

        }


        const statusMeta =
            window.YudisiumMockDB
                .statusMeta[
                    submission.status
                ];


        const action =
            window.YudisiumMockDB
                .getAction(
                    submission
                );


        function setText(
            id,
            value
        ) {

            const element =
                document.getElementById(id);


            if (
                element
            ) {

                element.textContent =
                    value;

            }

        }


        setText(
            "detailSubmissionCode",
            submission.code
        );


        setText(
            "detailStudentAvatar",
            window.YudisiumMockDB
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
            "NIM " + submission.nim
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
            "detailSubmissionStatusText",
            statusMeta.label
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


        setText(
            "detailActionTitle",
            action.title
        );


        setText(
            "detailActionDescription",
            action.description
        );


        const headerStatus =
            document.getElementById(
                "detailSubmissionStatusBadge"
            );


        if (
            headerStatus
        ) {

            headerStatus.textContent =
                statusMeta.label;


            headerStatus.className =
                "admin-status-badge " +
                statusMeta.className;

        }


        const primaryAction =
            document.getElementById(
                "detailPrimaryAction"
            );


        if (
            primaryAction
        ) {

            primaryAction.textContent =
                action.label;


            primaryAction.href =
                action.href;


            if (
                action.disabled
            ) {

                primaryAction.setAttribute(
                    "aria-disabled",
                    "true"
                );


                primaryAction.style.opacity =
                    "0.6";


                primaryAction.style.pointerEvents =
                    "none";

            }

        }


        const previewButtons =
            document.querySelectorAll(
                ".preview-document-button"
            );


        const previewModal =
            document.getElementById(
                "documentPreviewModal"
            );


        const previewTitle =
            document.getElementById(
                "documentPreviewTitle"
            );


        const previewFilename =
            document.getElementById(
                "documentPreviewFilename"
            );


        const previewFrame =
            document.getElementById(
                "documentPreviewFrame"
            );


        const previewPlaceholder =
            document.getElementById(
                "documentPreviewPlaceholder"
            );


        const closePreview =
            document.getElementById(
                "closeDocumentPreview"
            );


        const openNewTab =
            document.getElementById(
                "openDocumentNewTab"
            );


        let currentFile =
            "";


        /* =====================================
           OPEN PREVIEW
        ===================================== */

        previewButtons.forEach(
            function (button) {

                button.addEventListener(
                    "click",
                    function () {

                        const title =
                            this.dataset.title;


                        const file =
                            this.dataset.file;


                        currentFile =
                            file;


                        previewTitle.textContent =
                            title;


                        previewFilename.textContent =
                            file;


                        /*
                         * Untuk frontend sementara
                         * kita belum punya file PDF.
                         *
                         * Karena itu iframe
                         * disembunyikan dan
                         * placeholder ditampilkan.
                         *
                         * Nanti ketika Laravel
                         * sudah memberikan URL file,
                         * cukup:
                         *
                         * previewFrame.src = fileUrl;
                         */

                        previewFrame.style.display =
                            "none";


                        previewPlaceholder.style.display =
                            "flex";


                        previewModal
                            .classList
                            .add(
                                "active"
                            );


                        document.body.style.overflow =
                            "hidden";

                    }
                );

            }
        );



        /* =====================================
           CLOSE PREVIEW
        ===================================== */

        function closePreviewModal() {

            previewModal
                .classList
                .remove(
                    "active"
                );


            document.body.style.overflow =
                "";


            previewFrame.src =
                "";


            currentFile =
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
                    previewModal.classList.contains(
                        "active"
                    )
                ) {

                    closePreviewModal();

                }

            }
        );



        /* =====================================
           OPEN NEW TAB
        ===================================== */

        openNewTab.addEventListener(
            "click",
            function () {

                /*
                 * Frontend simulation.
                 *
                 * Saat backend sudah aktif:
                 *
                 * window.open(
                 *     currentFileUrl,
                 *     "_blank"
                 * );
                 */

                if (
                    currentFile === ""
                ) {
                    return;
                }


                alert(
                    "File " +
                    currentFile +
                    " nantinya akan dibuka di tab baru setelah terhubung dengan backend."
                );

            }
        );

    }
);
