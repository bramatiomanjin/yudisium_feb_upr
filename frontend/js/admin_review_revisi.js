document.addEventListener(
    "DOMContentLoaded",
    function () {

        console.log(
            "Admin review revisi aktif"
        );


        const params =
            new URLSearchParams(
                window.location.search
            );


        const submission =
            window.YudisiumMockDB
                .getSubmission(
                    params.get("id") || 5
                );


        if (
            !submission
        ) {

            window.location.href =
                "pengajuan.html";


            return;

        }


        document.querySelector(
            ".revision-review-header h1"
        ).textContent =
            submission.code;


        document.querySelector(
            ".revision-student-avatar"
        ).textContent =
            window.YudisiumMockDB
                .getInitials(
                    submission.name
                );


        document.querySelector(
            ".revision-student-card h2"
        ).textContent =
            submission.name;


        document.querySelector(
            ".revision-student-card p"
        ).textContent =
            "NIM " +
            submission.nim +
            " • " +
            submission.department +
            " • Angkatan " +
            submission.year;


        const items =
            Array.from(
                document.querySelectorAll(
                    "[data-review-item]"
                )
            );


        const resultStatus =
            document.getElementById(
                "revisionResultStatus"
            );


        const approvedCount =
            document.getElementById(
                "revisionApprovedCount"
            );


        const revisionAgainCount =
            document.getElementById(
                "revisionAgainCount"
            );


        const pendingCount =
            document.getElementById(
                "revisionPendingCount"
            );


        const confirmation =
            document.getElementById(
                "revisionReviewConfirmation"
            );


        const confirmationError =
            document.getElementById(
                "revisionConfirmationError"
            );


        const submitButton =
            document.getElementById(
                "submitRevisionReview"
            );


        const successModal =
            document.getElementById(
                "revisionReviewSuccessModal"
            );


        const successTitle =
            document.getElementById(
                "revisionReviewSuccessTitle"
            );


        const successMessage =
            document.getElementById(
                "revisionReviewSuccessMessage"
            );


        const successButton =
            document.getElementById(
                "revisionReviewSuccessButton"
            );


        /* =====================================
           REVIEW ITEM
        ===================================== */

        items.forEach(
            function (item) {

                const buttons =
                    item.querySelectorAll(
                        ".revision-review-choice"
                    );


                const feedbackBox =
                    item.querySelector(
                        ".revision-review-feedback"
                    );


                const textarea =
                    feedbackBox.querySelector(
                        "textarea"
                    );


                const error =
                    feedbackBox.querySelector(
                        ".revision-feedback-error"
                    );


                buttons.forEach(
                    function (button) {

                        button.addEventListener(
                            "click",
                            function () {

                                buttons.forEach(
                                    function (otherButton) {

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


                                const decision =
                                    this.dataset.choice;


                                item.dataset.decision =
                                    decision;


                                item.classList.remove(
                                    "approved",
                                    "revision"
                                );


                                if (
                                    decision ===
                                    "approved"
                                ) {

                                    item.classList.add(
                                        "approved"
                                    );


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

                                } else {

                                    item.classList.add(
                                        "revision"
                                    );


                                    feedbackBox
                                        .classList
                                        .add(
                                            "active"
                                        );

                                }


                                updateSummary();

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



        /* =====================================
           SUMMARY
        ===================================== */

        function updateSummary() {

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


            approvedCount.textContent =
                approved;


            revisionAgainCount.textContent =
                revision;


            pendingCount.textContent =
                pending;


            resultStatus.className =
                "revision-result-status";


            if (
                pending > 0
            ) {

                resultStatus
                    .classList
                    .add(
                        "pending"
                    );


                resultStatus.textContent =
                    "Belum Lengkap";

            } else if (
                revision > 0
            ) {

                resultStatus
                    .classList
                    .add(
                        "revision"
                    );


                resultStatus.textContent =
                    "Perlu Revisi Lagi";

            } else {

                resultStatus
                    .classList
                    .add(
                        "approved"
                    );


                resultStatus.textContent =
                    "Revisi Disetujui";

            }

        }


        updateSummary();



        /* =====================================
           VALIDATION
        ===================================== */

        function validateReview() {

            let valid =
                true;


            let firstInvalid =
                null;


            items.forEach(
                function (item) {

                    item.classList.remove(
                        "review-error"
                    );


                    if (
                        !item.dataset.decision
                    ) {

                        valid =
                            false;


                        item.classList.add(
                            "review-error"
                        );


                        if (
                            !firstInvalid
                        ) {

                            firstInvalid =
                                item;

                        }


                        return;

                    }


                    if (
                        item.dataset.decision ===
                        "revision"
                    ) {

                        const textarea =
                            item.querySelector(
                                ".revision-review-feedback textarea"
                            );


                        const error =
                            item.querySelector(
                                ".revision-feedback-error"
                            );


                        if (
                            textarea.value
                                .trim() ===
                            ""
                        ) {

                            valid =
                                false;


                            error
                                .classList
                                .add(
                                    "active"
                                );


                            item.classList.add(
                                "review-error"
                            );


                            if (
                                !firstInvalid
                            ) {

                                firstInvalid =
                                    item;

                            }

                        }

                    }

                }
            );


            if (
                firstInvalid
            ) {

                firstInvalid.scrollIntoView(
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
                    !validateReview()
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


                const payload =
                    [];


                let revisionAgain =
                    0;


                items.forEach(
                    function (item) {

                        const decision =
                            item.dataset.decision;


                        const feedback =
                            item.querySelector(
                                ".revision-review-feedback textarea"
                            );


                        if (
                            decision ===
                            "revision"
                        ) {

                            revisionAgain++;

                        }


                        payload.push(
                            {
                                type:
                                    item.dataset
                                        .itemType,

                                key:
                                    item.dataset
                                        .itemKey,

                                decision:
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
                    revisionAgain > 0
                        ?
                        "PERLU_REVISI"
                        :
                        "TERVERIFIKASI";


                console.log(
                    {
                        pengajuan_id:
                            submission.id,

                        revisi_ke:
                            1,

                        final_status:
                            finalStatus,

                        items:
                            payload
                    }
                );


                const storedStatus =
                    finalStatus ===
                    "PERLU_REVISI"
                        ?
                        "perlu revisi"
                        :
                        "terverifikasi";


                window.YudisiumMockDB
                    .setSubmissionStatus(
                        submission.id,
                        storedStatus
                    );


                window.YudisiumMockDB
                    .saveActivity(
                        {
                            pengajuan_id:
                                submission.id,

                            action:
                                "REVISION",

                            action_label:
                                "Review Revisi",

                            old_status:
                                "REVISI_DIKIRIM",

                            new_status:
                                finalStatus,

                            note:
                                finalStatus ===
                                "TERVERIFIKASI"
                                    ?
                                    "Seluruh perbaikan mahasiswa telah diterima."
                                    :
                                    "Masih terdapat " +
                                    revisionAgain +
                                    " item yang perlu diperbaiki kembali."
                        }
                    );


                if (
                    finalStatus ===
                    "TERVERIFIKASI"
                ) {

                    successTitle.textContent =
                        "Revisi Disetujui";


                    successMessage.textContent =
                        "Seluruh item revisi telah disetujui. Pengajuan sekarang berstatus Terverifikasi dan dapat dilanjutkan ke proses SK.";

                } else {

                    successTitle.textContent =
                        "Revisi Lanjutan Diperlukan";


                    successMessage.textContent =
                        "Masih terdapat item yang perlu diperbaiki kembali oleh mahasiswa.";

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



        /* =====================================
           PREVIEW
        ===================================== */

        const previewModal =
            document.getElementById(
                "revisionPreviewModal"
            );


        const previewTitle =
            document.getElementById(
                "revisionPreviewTitle"
            );


        const previewFilename =
            document.getElementById(
                "revisionPreviewFilename"
            );


        const closePreview =
            document.getElementById(
                "closeRevisionPreview"
            );


        const openNewTab =
            document.getElementById(
                "revisionOpenNewTab"
            );


        let currentFile =
            "";


        document.addEventListener(
            "click",
            function (event) {

                const button =
                    event.target.closest(
                        ".revision-preview-button"
                    );


                if (
                    !button
                ) {

                    return;

                }


                previewTitle.textContent =
                    button.dataset
                        .previewTitle;


                previewFilename.textContent =
                    button.dataset
                        .previewFile;


                currentFile =
                    button.dataset
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
                    previewModal
                        .classList
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
                    currentFile ===
                    ""
                ) {

                    return;

                }


                alert(
                    "File " +
                    currentFile +
                    " akan dibuka menggunakan URL Laravel setelah integrasi backend."
                );

            }
        );

    }
);
