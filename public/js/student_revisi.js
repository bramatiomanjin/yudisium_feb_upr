document.addEventListener(
    "DOMContentLoaded",
    async function () {

        "use strict";


        if (!window.YudisiumAPI) {
            return;
        }


        const id =
            sessionStorage.getItem(
                "tracking_submission_id"
            );


        const nim =
            sessionStorage.getItem(
                "tracking_nim"
            );


        const code =
            sessionStorage.getItem(
                "tracking_kode"
            );


        if (
            !id ||
            !nim ||
            !code
        ) {

            window.location.href =
                "/tracking";

            return;

        }


        const submission =
            await window.YudisiumAPI
                .findSubmission(
                    nim,
                    code
                );


        if (
            !submission ||
            submission.status !==
                window.YudisiumAPI
                    .STATUS
                    .PERLU_REVISI
        ) {

            window.location.href =
                "/detail_tracking";

            return;

        }


        const verification =
            await window.YudisiumAPI
                .getVerificationResult(
                    submission.id
                );


        if (
            !verification ||
            !Array.isArray(
                verification.items
            )
        ) {

            window.location.href =
                "/detail_tracking";

            return;

        }


        const revisions =
            verification.items.filter(
                function (item) {

                    return (
                        item.decision ===
                        "revision"
                    );

                }
            );


        document.getElementById(
            "revisionKode"
        ).textContent =
            submission.code;


        document.getElementById(
            "revisionNim"
        ).textContent =
            submission.nim;


        document.getElementById(
            "revisionFieldCount"
        ).textContent =
            revisions.length;


        document.getElementById(
            "revisionBadge"
        ).textContent =
            revisions.length +
            " Perbaikan";


        const container =
            document.getElementById(
                "revisionItemsContainer"
            );


        container.innerHTML =
            "";


        revisions.forEach(
            function (
                item,
                index
            ) {

                const config =
                    window.YudisiumAPI
                        .getFieldConfig(
                            item.key
                        );


                const card =
                    document.createElement(
                        "article"
                    );


                card.className =
                    "revision-field-card";


                card.dataset.type =
                    item.type;

                card.dataset.key =
                    item.key;


                const isDocument =
                    item.type ===
                    "document";


                card.innerHTML =
                    `
                    <div class="revision-field-number">
                        ${index + 1}
                    </div>

                    <div class="revision-field-content">

                        <div class="revision-field-heading">

                            <div>

                                <span class="revision-field-label">
                                    ${item.label || config.label}
                                </span>

                                <span class="revision-field-status">
                                    Perlu diperbaiki
                                </span>

                            </div>

                        </div>

                        <div class="feedback-box">

                            <span class="feedback-label">
                                Feedback Admin
                            </span>

                            <p>
                                ${item.feedback || "-"}
                            </p>

                        </div>

                        <div class="old-value-box">

                            <span>
                                ${
                                    isDocument
                                        ? "Dokumen Sebelumnya"
                                        : "Data Sebelumnya"
                                }
                            </span>

                            <p>
                                ${
                                    item.oldValue ||
                                    item.filename ||
                                    "-"
                                }
                            </p>

                        </div>

                        <div class="form-group">

                            ${
                                isDocument
                                    ? `
                                        <label>
                                            Upload Dokumen Perbaikan
                                            <span class="required">*</span>
                                        </label>

                                        <input
                                            type="file"
                                            data-revision-file
                                            required
                                        >
                                    `
                                    : `
                                        <label>
                                            ${item.label || config.label} Baru
                                            <span class="required">*</span>
                                        </label>

                                        ${
                                            config.type ===
                                            "textarea"
                                                ? `
                                                    <textarea
                                                        data-revision-input
                                                        rows="5"
                                                        required
                                                    ></textarea>
                                                `
                                                : `
                                                    <input
                                                        type="${config.type || "text"}"
                                                        data-revision-input
                                                        required
                                                    >
                                                `
                                        }
                                    `
                            }

                        </div>

                    </div>
                    `;


                container.appendChild(
                    card
                );

            }
        );


        const form =
            document.getElementById(
                "revisionForm"
            );


        form.addEventListener(
            "submit",
            async function (event) {

                event.preventDefault();


                const cards =
                    Array.from(
                        document.querySelectorAll(
                            ".revision-field-card"
                        )
                    );


                const formData =
                    new FormData();


                let valid =
                    true;


                const payload =
                    [];


                cards.forEach(
                    function (card) {

                        const type =
                            card.dataset.type;


                        const key =
                            card.dataset.key;


                        if (
                            type ===
                            "document"
                        ) {

                            const input =
                                card.querySelector(
                                    "[data-revision-file]"
                                );


                            const file =
                                input.files[0];


                            if (!file) {

                                valid =
                                    false;

                                return;

                            }


                            formData.append(
                                "files[" +
                                key +
                                "]",
                                file
                            );


                            payload.push({

                                key:
                                    key,

                                type:
                                    "document"

                            });

                        } else {

                            const input =
                                card.querySelector(
                                    "[data-revision-input]"
                                );


                            const value =
                                input.value.trim();


                            if (!value) {

                                valid =
                                    false;

                                return;

                            }


                            payload.push({

                                key:
                                    key,

                                type:
                                    "field",

                                value:
                                    value

                            });

                        }

                    }
                );


                if (!valid) {

                    alert(
                        "Lengkapi seluruh item revisi."
                    );

                    return;

                }


                formData.append(
                    "items",
                    JSON.stringify(
                        payload
                    )
                );


                try {

                    await window.YudisiumAPI
                        .submitRevision(
                            submission.id,
                            formData
                        );


                    window.location.href =
                        "/detail_tracking";

                } catch (error) {

                    alert(
                        "Backend belum terhubung. Revisi belum dapat dikirim."
                    );

                }

            }
        );

    }
);