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
                .MENUNGGU_VERIFIKASI
        ) {

            window.location.href =
                "/admin/pengajuan/" +
                submission.id;

            return;

        }


        const documents =
            await window.YudisiumAPI
                .getDocuments(
                    submission.id
                );


        function setText(
            selector,
            value
        ) {

            const element =
                document.querySelector(
                    selector
                );


            if (element) {

                element.textContent =
                    value || "-";

            }

        }


        setText(
            ".verification-header h1",
            submission.code
        );

        setText(
            ".verification-student-avatar",
            window.YudisiumAPI
                .getInitials(
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
            submission.nim +
            " • " +
            submission.department +
            " • Angkatan " +
            submission.year

        );


        /* =====================================================
           DOCUMENT RENDER
        ===================================================== */

        const documentContainer =
            document.getElementById(
                "verificationDocuments"
            );


        if (documentContainer) {

            documentContainer.innerHTML =
                "";


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
                        doc.id;


                    item.innerHTML =
                        `
                        <div class="verification-document-main">

                            <div class="verification-document-icon">
                                FILE
                            </div>

                            <div class="verification-document-info">

                                <strong>
                                    ${doc.title || doc.label || "-"}
                                </strong>

                                <span>
                                    ${doc.filename || "-"}
                                </span>

                            </div>

                            ${
                                doc.url
                                    ? `
                                        <a
                                            href="${doc.url}"
                                            target="_blank"
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


        const submitButton =
            document.getElementById(
                "submitVerification"
            );


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

                            }
                        );

                    }
                );

            }
        );


        if (!submitButton) {
            return;
        }


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

                            return;

                        }


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
                                    ? textarea.value.trim()
                                    : null

                        });

                    }
                );


                if (
                    !confirmation?.checked
                ) {

                    valid =
                        false;

                }


                if (!valid) {

                    alert(
                        "Lengkapi seluruh keputusan verifikasi dan feedback revisi."
                    );

                    return;

                }


                try {

                    submitButton.disabled =
                        true;


                    await window.YudisiumAPI
                        .verifySubmission(
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

                    alert(
                        "Backend belum terhubung. Hasil verifikasi belum dapat disimpan."
                    );


                    console.error(error);

                } finally {

                    submitButton.disabled =
                        false;

                }

            }
        );

    }
);