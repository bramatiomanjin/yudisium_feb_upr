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


        if (
            submission.status !==
            window.YudisiumAPI
                .STATUS
                .REVISI_DIKIRIM
        ) {

            window.location.href =
                "detail_pengajuan.html?id=" +
                submission.id;

            return;

        }


        const revision =
            await window.YudisiumAPI
                .getRevisionSubmission(
                    submission.id
                );


        const container =
            document.getElementById(
                "reviewRevisionItemsContainer"
            );


        if (
            !revision ||
            !Array.isArray(
                revision.items
            )
        ) {

            if (container) {

                container.innerHTML =
                    `
                    <div class="revision-info-box">
                        Data revisi belum tersedia dari backend.
                    </div>
                    `;

            }

            return;

        }


        document.getElementById(
            "reviewSubmissionCode"
        ).textContent =
            submission.code;


        document.getElementById(
            "reviewStudentName"
        ).textContent =
            submission.name;


        document.getElementById(
            "reviewStudentAvatar"
        ).textContent =
            window.YudisiumAPI
                .getInitials(
                    submission.name
                );


        document.getElementById(
            "reviewStudentSummary"
        ).textContent =

            "NIM " +
            submission.nim +
            " • " +
            submission.department;


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


                card.innerHTML =
                    `
                    <article
                        class="revision-review-item"
                        data-review-item
                        data-item-key="${item.key}"
                        data-item-type="${item.type}"
                    >

                        <div class="revision-item-title">

                            <div>
                                <span>
                                    Item ${index + 1}
                                </span>

                                <strong>
                                    ${item.label || item.key}
                                </strong>
                            </div>

                        </div>

                        <div class="revision-comparison">

                            <div class="revision-old-value">

                                <span>
                                    Sebelum Revisi
                                </span>

                                <p>
                                    ${
                                        item.oldValue ||
                                        item.oldFile ||
                                        "-"
                                    }
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
                                    ${
                                        item.newValue ||
                                        item.newFile?.name ||
                                        "-"
                                    }
                                </p>

                            </div>

                        </div>

                        <div class="revision-admin-feedback">

                            <span>
                                Feedback Sebelumnya
                            </span>

                            <p>
                                ${item.feedback || "-"}
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

                            }
                        );

                    }
                );

            }
        );


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
                        "pengajuan.html";

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

    }
);