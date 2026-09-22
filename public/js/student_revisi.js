document.addEventListener(
    "DOMContentLoaded",
    async function () {

        "use strict";

        if (!window.YudisiumAPI) {
            console.error(
                "YudisiumAPI tidak ditemukan."
            );
            return;
        }

        const API =
            window.YudisiumAPI;

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


        try {

            // =====================================================
            // 1. AMBIL DATA PENGAJUAN
            // =====================================================

            const submission =
                await API.getSubmission(
                    id,
                    code
                );


            if (!submission) {

                window.location.href =
                    "/detail_tracking";

                return;
            }


            if (
                submission.status !==
                    API.STATUS.PERLU_REVISI &&
                submission.status !==
                    API.STATUS.REVISI_DIKIRIM
            ) {

                window.location.href =
                    "/detail_tracking";

                return;
            }


            // =====================================================
            // 2. AMBIL HASIL VERIFIKASI FIELD
            // =====================================================

            const verification =
                await API.getVerificationResult(
                    submission.id,
                    code
                );


            const fieldItems =
                Array.isArray(
                    verification?.items
                )
                    ? verification.items
                    : [];


            // =====================================================
            // 3. AMBIL DOKUMEN
            // =====================================================

            const documents =
                await API.getDocuments(
                    submission.id,
                    code
                );


            // =====================================================
            // 4. NORMALISASI FIELD
            // =====================================================

            const normalizedFields =
                fieldItems.map(
                    function (item) {

                        const config =
                            API.getFieldConfig(
                                item.key
                            );


                        return {

                            type:
                                "field",

                            key:
                                item.key,

                            label:
                                config.label ||
                                formatLabel(
                                    item.key
                                ),

                            status:
                                normalizeVerificationStatus(
                                    item.status ??
                                    item.decision
                                ),

                            feedback:
                                item.feedback ||
                                "",

                            oldValue:
                                API.getFieldValue(
                                    submission,
                                    item.key
                                )
                        };
                    }
                );


            // =====================================================
            // 5. NORMALISASI DOKUMEN
            // =====================================================

            const normalizedDocuments =
                Array.isArray(
                    documents
                )
                    ? documents.map(
                        function (doc) {

                            return {

                                type:
                                    "document",

                                /*
                                 * Untuk revisi dokumen backend
                                 * membutuhkan ID pengajuan_dokumen.
                                 */
                                key:
                                    String(
                                        doc.id
                                    ),

                                label:
                                    doc.title ||
                                    doc.label ||
                                    doc.name ||
                                    "Dokumen",

                                filename:
                                    doc.filename ||
                                    doc.name ||
                                    "-",

                                oldValue:
                                    doc.filename ||
                                    doc.name ||
                                    "-",

                                status:
                                    normalizeVerificationStatus(
                                        doc.status
                                    ),

                                feedback:
                                    doc.feedback ||
                                    ""
                            };
                        }
                    )
                    : [];


            // =====================================================
            // 6. GABUNGKAN FIELD + DOKUMEN
            // =====================================================

            const allItems = [
                ...normalizedFields,
                ...normalizedDocuments
            ];


            const revisions =
                allItems.filter(
                    function (item) {

                        return (
                            item.status ===
                            "REVISI"
                        );
                    }
                );


            const approved =
                allItems.filter(
                    function (item) {

                        return (
                            item.status ===
                            "DISETUJUI"
                        );
                    }
                );


            // =====================================================
            // 7. HEADER / SUMMARY
            // =====================================================

            setText(
                "revisionKode",
                submission.code
            );

            setText(
                "revisionNim",
                submission.nim
            );

            setText(
                "revisionFieldCount",
                revisions.length
            );

            setText(
                "revisionBadge",
                revisions.length +
                " Perbaikan"
            );


            // =====================================================
            // 8. RENDER LOCKED DATA
            // =====================================================

            renderApprovedItems(
                approved
            );


            // =====================================================
            // 9. RENDER ITEM REVISI
            // =====================================================

            renderRevisionItems(
                revisions
            );


            // =====================================================
            // 10. FORM SUBMIT
            // =====================================================

            setupRevisionForm(
                submission,
                revisions
            );


            // =====================================================
            // 11. STATUS REVISI SUDAH DIKIRIM
            // =====================================================

            if (
                submission.status ===
                API.STATUS.REVISI_DIKIRIM
            ) {

                disableRevisionForm();
            }


            console.log(
                "Data revisi mahasiswa:",
                {
                    submission,
                    fieldItems,
                    documents,
                    approved,
                    revisions
                }
            );

        } catch (error) {

            console.error(
                "Gagal memuat halaman revisi:",
                error
            );

            alert(
                "Data revisi belum dapat dimuat."
            );
        }


        // =========================================================
        // NORMALISASI STATUS
        // =========================================================

        function normalizeVerificationStatus(
            value
        ) {

            const status =
                String(
                    value || ""
                )
                    .trim()
                    .toUpperCase();


            if (
                status === "REVISION" ||
                status === "REVISI"
            ) {

                return "REVISI";
            }


            if (
                status === "APPROVED" ||
                status === "DISETUJUI" ||
                status === "VALID"
            ) {

                return "DISETUJUI";
            }


            return "PENDING";
        }


        // =========================================================
        // LABEL
        // =========================================================

        function formatLabel(
            value
        ) {

            return String(
                value || ""
            )
                .replace(
                    /_/g,
                    " "
                )
                .replace(
                    /\b\w/g,
                    function (char) {
                        return char.toUpperCase();
                    }
                );
        }


        // =========================================================
        // TEXT
        // =========================================================

        function setText(
            id,
            value
        ) {

            const element =
                document.getElementById(
                    id
                );


            if (!element) {
                return;
            }


            if (
                value === undefined ||
                value === null ||
                value === ""
            ) {

                element.textContent =
                    "-";

                return;
            }


            element.textContent =
                value;
        }


        // =========================================================
        // ESCAPE HTML
        // =========================================================

        function escapeHtml(
            value
        ) {

            const div =
                document.createElement(
                    "div"
                );

            div.textContent =
                value ?? "";

            return div.innerHTML;
        }


        // =========================================================
        // APPROVED / LOCKED
        // =========================================================

        function renderApprovedItems(
            items
        ) {

            const container =
                document.getElementById(
                    "approvedDataGrid"
                );


            if (!container) {
                return;
            }


            container.innerHTML =
                "";


            if (!items.length) {

                container.innerHTML = `
                    <div class="locked-data-item">
                        <span>
                            Belum ada data yang dikunci
                        </span>
                    </div>
                `;

                return;
            }


            items.forEach(
                function (item) {

                    const card =
                        document.createElement(
                            "div"
                        );


                    card.className =
                        "locked-data-item";


                    const displayedValue =
                        item.type ===
                        "document"
                            ? item.filename
                            : item.oldValue;


                    card.innerHTML = `
                        <span class="locked-data-label">
                            ${escapeHtml(
                                item.label
                            )}
                        </span>

                        <strong>
                            ${escapeHtml(
                                displayedValue ||
                                "-"
                            )}
                        </strong>

                        <small>
                            ✓ Disetujui
                        </small>
                    `;


                    container.appendChild(
                        card
                    );
                }
            );
        }


        // =========================================================
        // REVISION ITEMS
        // =========================================================

        function renderRevisionItems(
            revisions
        ) {

            const container =
                document.getElementById(
                    "revisionItemsContainer"
                );


            if (!container) {
                return;
            }


            container.innerHTML =
                "";


            if (!revisions.length) {

                container.innerHTML = `
                    <div class="revision-empty-state">
                        Tidak ada item yang perlu diperbaiki.
                    </div>
                `;

                return;
            }


            revisions.forEach(
                function (
                    item,
                    index
                ) {

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


                    let inputHtml =
                        "";


                    let acceptAttr = ".pdf,.doc,.docx,application/pdf";
                    let formatText = "Pilih file pengganti sesuai feedback Admin.";
                    if (item.key === 'foto_3x4') {
                        acceptAttr = ".jpg,.jpeg,.png,image/jpeg,image/png";
                        formatText = "Pilih file gambar (JPG/PNG) pengganti sesuai feedback Admin.";
                    }

                    if (isDocument) {

                        inputHtml = `
                            <label>
                                Upload Dokumen Perbaikan
                                <span class="required">
                                    *
                                </span>
                            </label>

                            <input
                                type="file"
                                data-revision-file
                                accept="${acceptAttr}"
                                required
                            >

                            <small>
                                ${formatText}
                            </small>
                        `;

                    } else {

                        const config =
                            API.getFieldConfig(
                                item.key
                            );


                        const currentValue =
                            item.oldValue ??
                            "";


                        if (
                            config.type ===
                            "textarea"
                        ) {

                            inputHtml = `
                                <label>
                                    ${escapeHtml(
                                        item.label
                                    )} Baru

                                    <span class="required">
                                        *
                                    </span>
                                </label>

                                <textarea
                                    data-revision-input
                                    rows="5"
                                    required
                                >${escapeHtml(
                                    currentValue
                                )}</textarea>
                            `;

                        } else {

                            let inputType =
                                config.type ||
                                "text";


                            if (
                                inputType ===
                                "select"
                            ) {

                                inputType =
                                    "text";
                            }


                            inputHtml = `
                                <label>
                                    ${escapeHtml(
                                        item.label
                                    )} Baru

                                    <span class="required">
                                        *
                                    </span>
                                </label>

                                <input
                                    type="${escapeHtml(
                                        inputType
                                    )}"
                                    data-revision-input
                                    value="${escapeHtml(
                                        currentValue
                                    )}"
                                    required
                                >
                            `;
                        }
                    }


                    card.innerHTML = `
                        <div class="revision-field-number">
                            ${index + 1}
                        </div>

                        <div class="revision-field-content">

                            <div class="revision-field-heading">

                                <div>

                                    <span class="revision-field-label">
                                        ${escapeHtml(
                                            item.label
                                        )}
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
                                    ${escapeHtml(
                                        item.feedback ||
                                        "-"
                                    )}
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
                                    ${escapeHtml(
                                        item.oldValue ||
                                        "-"
                                    )}
                                </p>

                            </div>


                            <div class="form-group">
                                ${inputHtml}
                            </div>

                        </div>
                    `;


                    container.appendChild(
                        card
                    );
                }
            );
        }


        // =========================================================
        // FORM SUBMIT
        // =========================================================

        function setupRevisionForm(
            submission,
            revisions
        ) {

            const form =
                document.getElementById(
                    "revisionForm"
                );


            const confirmation =
                document.getElementById(
                    "revisionConfirmation"
                );


            const confirmationError =
                document.getElementById(
                    "revisionConfirmationError"
                );


            const submitButton =
                document.getElementById(
                    "submitRevision"
                );


            if (!form) {
                return;
            }


            form.addEventListener(
                "submit",
                async function (event) {

                    event.preventDefault();


                    if (
                        submission.status ===
                        API.STATUS.REVISI_DIKIRIM
                    ) {
                        return;
                    }


                    if (
                        !revisions.length
                    ) {

                        alert(
                            "Tidak ada bagian yang perlu direvisi."
                        );

                        return;
                    }


                    if (
                        !confirmation?.checked
                    ) {

                        confirmationError
                            ?.classList
                            .add(
                                "active"
                            );

                        return;
                    }


                    confirmationError
                        ?.classList
                        .remove(
                            "active"
                        );


                    const cards =
                        Array.from(
                            document.querySelectorAll(
                                ".revision-field-card"
                            )
                        );


                    const formData =
                        new FormData();


                    /*
                     * CSRF Laravel.
                     */
                    const csrfToken =
                        document.querySelector(
                            'input[name="_token"]'
                        )
                            ?.value;


                    if (csrfToken) {

                        formData.append(
                            "_token",
                            csrfToken
                        );
                    }


                    let valid =
                        true;


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
                                    input?.files?.[0];


                                if (!file) {

                                    valid =
                                        false;

                                    return;
                                }

                                const maxSizeBytes = 1 * 1024 * 1024; // 1 MB
                                if (file.size > maxSizeBytes) {
                                    alert("Ukuran file " + file.name + " terlalu besar. Maksimal 1 MB.");
                                    valid = false;
                                    return;
                                }


                                /*
                                 * Backend TrackingController
                                 * mengharapkan:
                                 *
                                 * revisi_dokumen[ID_DOKUMEN]
                                 */
                                formData.append(
                                    "revisi_dokumen[" +
                                    key +
                                    "]",
                                    file
                                );

                            } else {

                                const input =
                                    card.querySelector(
                                        "[data-revision-input]"
                                    );


                                const value =
                                    input?.value?.trim();


                                if (!value) {

                                    valid =
                                        false;

                                    return;
                                }


                                /*
                                 * Backend TrackingController
                                 * mengharapkan:
                                 *
                                 * revisi_field[field_key]
                                 */
                                formData.append(
                                    "revisi_field[" +
                                    key +
                                    "]",
                                    value
                                );
                            }
                        }
                    );


                    if (!valid) {

                        alert(
                            "Lengkapi seluruh item revisi."
                        );

                        return;
                    }


                    try {

                        if (
                            submitButton
                        ) {

                            submitButton.disabled =
                                true;

                            submitButton.textContent =
                                "Mengirim Revisi...";
                        }


                        const response =
                            await fetch(
                                "/revisi/" +
                                encodeURIComponent(
                                    submission.code
                                ),
                                {
                                    method:
                                        "POST",

                                    body:
                                        formData,

                                    credentials:
                                        "same-origin",

                                    headers: {
                                        "Accept":
                                            "text/html"
                                    }
                                }
                            );


                        if (
                            !response.ok
                        ) {

                            const errorText =
                                await response.text();

                            throw new Error(
                                errorText ||
                                "Gagal mengirim revisi."
                            );
                        }


                        /*
                         * Backend redirect setelah sukses.
                         * Kita tampilkan modal UI sendiri.
                         */
                        const modal =
                            document.getElementById(
                                "revisionSuccessModal"
                            );


                        modal
                            ?.classList
                            .add(
                                "active"
                            );


                        document.body.style.overflow =
                            "hidden";


                    } catch (error) {

                        console.error(
                            "Submit revisi gagal:",
                            error
                        );


                        alert(
                            "Revisi gagal dikirim. Silakan coba kembali.\n\n" + (error.message || "")
                        );

                    } finally {

                        if (
                            submitButton
                        ) {

                            submitButton.disabled =
                                false;

                            submitButton.textContent =
                                "Kirim Revisi";
                        }
                    }

                }
            );


            // =====================================================
            // SUCCESS MODAL
            // =====================================================

            const closeButton =
                document.getElementById(
                    "closeRevisionModal"
                );


            closeButton
                ?.addEventListener(
                    "click",
                    function () {

                        window.location.href =
                            "/detail_tracking";
                    }
                );
        }


        // =========================================================
        // DISABLE SETELAH REVISI DIKIRIM
        // =========================================================

        function disableRevisionForm() {

            const form =
                document.getElementById(
                    "revisionForm"
                );


            if (!form) {
                return;
            }


            form
                .querySelectorAll(
                    "input, textarea, select, button"
                )
                .forEach(
                    function (element) {

                        element.disabled =
                            true;
                    }
                );


            const submitButton =
                document.getElementById(
                    "submitRevision"
                );


            if (submitButton) {

                submitButton.textContent =
                    "Revisi Sudah Dikirim";
            }
        }

    }
);