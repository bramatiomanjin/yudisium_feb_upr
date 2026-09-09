document.addEventListener(
    "DOMContentLoaded",
    async function () {

        "use strict";


        if (
            !window.YudisiumAPI
        ) {

            console.error(
                "YudisiumAPI tidak ditemukan."
            );

            return;

        }


        const API =
            window.YudisiumAPI;


        /* =====================================================
           SESSION
        ===================================================== */

        const currentUsername =
            sessionStorage.getItem(
                "admin_username"
            ) || "";


        const currentRole =
            sessionStorage.getItem(
                "admin_role"
            ) || "ADMIN";


        const isSuperAdmin =
            currentRole ===
            "SUPER_ADMIN";


        /* =====================================================
           QUERY
        ===================================================== */

        const params =
            new URLSearchParams(
                window.location.search
            );


        const historyId =
            params.get(
                "id"
            );


        if (
            !historyId
        ) {

            window.location.href =
                "history.html";

            return;

        }


        /* =====================================================
           ELEMENTS
        ===================================================== */

        const content =
            document.getElementById(
                "detailHistoryContent"
            );


        const accessError =
            document.getElementById(
                "detailHistoryAccessError"
            );


        const documentList =
            document.getElementById(
                "detailHistoryDocumentList"
            );


        const sidebarBadge =
            document.querySelector(
                ".admin-sidebar .admin-nav-count"
            );


        /* =====================================================
           HELPERS
        ===================================================== */

        function setText(
            id,
            value
        ) {

            const element =
                document.getElementById(
                    id
                );


            if (
                element
            ) {

                element.textContent =
                    value ||
                    "-";

            }

        }


        function escapeHtml(value) {

            return String(
                value ?? ""
            )
                .replace(
                    /&/g,
                    "&amp;"
                )
                .replace(
                    /</g,
                    "&lt;"
                )
                .replace(
                    />/g,
                    "&gt;"
                )
                .replace(
                    /"/g,
                    "&quot;"
                )
                .replace(
                    /'/g,
                    "&#039;"
                );

        }


        function getActionLabel(activity) {

            if (
                activity.actionLabel
            ) {

                return activity.actionLabel;

            }


            const labels = {

                VERIFICATION:
                    "Verifikasi Pengajuan",

                REVISION:
                    "Revisi Pengajuan",

                UPDATE_SK_STATUS:
                    "Proses SK"

            };


            return (
                labels[
                    activity.action
                ] ||
                "Aktivitas Administrasi"
            );

        }


        function getActionDescription(activity) {

            switch (
                activity.action
            ) {

                case "VERIFICATION":

                    return (
                        "Admin melakukan pemeriksaan data dan dokumen pengajuan mahasiswa."
                    );


                case "REVISION":

                    return (
                        "Admin melakukan tindakan pada proses revisi pengajuan mahasiswa."
                    );


                case "UPDATE_SK_STATUS":

                    return (
                        "Admin memperbarui tahapan proses administrasi SK Yudisium."
                    );


                default:

                    return (
                        "Aktivitas administrasi pada pengajuan mahasiswa."
                    );

            }

        }


        function getActionBadge(activity) {

            switch (
                activity.action
            ) {

                case "VERIFICATION":

                    return "VERIFIKASI";


                case "REVISION":

                    return "REVISI";


                case "UPDATE_SK_STATUS":

                    return "PROSES SK";


                default:

                    return "AKTIVITAS";

            }

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


            return date
                .toLocaleString(
                    "id-ID",
                    {

                        day:
                            "2-digit",

                        month:
                            "long",

                        year:
                            "numeric",

                        hour:
                            "2-digit",

                        minute:
                            "2-digit"

                    }
                );

        }


        /* =====================================================
           ACCESS ERROR
        ===================================================== */

        function showAccessError() {

            if (
                content
            ) {

                content.style.display =
                    "none";

            }


            if (
                accessError
            ) {

                accessError.style.display =
                    "flex";

            }

        }


        function showContent() {

            if (
                accessError
            ) {

                accessError.style.display =
                    "none";

            }


            if (
                content
            ) {

                content.style.display =
                    "";

            }

        }


        /* =====================================================
           SIDEBAR REVISION COUNT
        ===================================================== */

        async function updateSidebarCount() {

            if (
                !sidebarBadge
            ) {

                return;

            }


            try {

                const submissions =
                    await API
                        .getSubmissions();


                const count =
                    submissions.filter(
                        function (submission) {

                            return [

                                API.STATUS
                                    .PERLU_REVISI,

                                API.STATUS
                                    .REVISI_DIKIRIM

                            ].includes(
                                submission.status
                            );

                        }
                    ).length;


                sidebarBadge.textContent =
                    count;

            } catch (error) {

                sidebarBadge.textContent =
                    "0";

            }

        }


        /* =====================================================
           DOCUMENTS
        ===================================================== */

        function renderDocuments(
            documents
        ) {

            if (
                !documentList
            ) {

                return;

            }


            documentList.innerHTML =
                "";


            if (
                !Array.isArray(
                    documents
                ) ||
                documents.length ===
                    0
            ) {

                documentList.innerHTML =
                    `
                    <div
                        style="
                            padding: 26px;
                            text-align: center;
                        "
                    >
                        <strong>
                            Tidak ada dokumen untuk ditampilkan
                        </strong>

                        <p>
                            Dokumen akan tersedia dari backend ketika pengajuan sudah tersimpan.
                        </p>
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
                        "detail-history-document-item";


                    item.innerHTML =
                        `
                        <div class="detail-history-document-main">

                            <div class="detail-history-document-icon">
                                FILE
                            </div>

                            <div>

                                <strong>
                                    ${escapeHtml(
                                        documentData.title ||
                                        documentData.label ||
                                        "Dokumen"
                                    )}
                                </strong>

                                <span>
                                    ${escapeHtml(
                                        documentData.filename ||
                                        "-"
                                    )}
                                </span>

                            </div>

                        </div>


                        ${
                            documentData.url
                                ? `
                                <a
                                    href="${escapeHtml(
                                        documentData.url
                                    )}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="detail-history-preview-button"
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
                        `;


                    documentList.appendChild(
                        item
                    );

                }
            );

        }


        /* =====================================================
           RENDER
        ===================================================== */

        function renderActivity(
            activity,
            submission
        ) {

            setText(
                "detailHistoryCode",
                activity.submissionCode ||
                submission?.code
            );


            setText(
                "detailActivityTitle",
                getActionLabel(
                    activity
                )
            );


            setText(
                "detailActivityDescription",
                getActionDescription(
                    activity
                )
            );


            setText(
                "detailActivityBadge",
                getActionBadge(
                    activity
                )
            );


            setText(
                "detailAdminUsername",
                activity.adminName ||
                activity.adminUsername
            );


            setText(
                "detailAdminRole",
                activity.adminRole
            );


            setText(
                "detailActivityTime",
                formatDate(
                    activity.createdAt
                )
            );


            setText(
                "detailActivityType",
                getActionLabel(
                    activity
                )
            );


            setText(
                "detailOldStatus",
                API.getStatusLabel(
                    activity.previousStatus
                )
            );


            setText(
                "detailNewStatus",
                API.getStatusLabel(
                    activity.newStatus
                )
            );


            const noteBox =
                document.getElementById(
                    "detailHistoryNoteBox"
                );


            if (
                activity.note
            ) {

                if (
                    noteBox
                ) {

                    noteBox.style.display =
                        "";

                }


                setText(
                    "detailHistoryNote",
                    activity.note
                );

            } else {

                if (
                    noteBox
                ) {

                    noteBox.style.display =
                        "none";

                }

            }


            const studentName =
                submission?.name ||
                activity.studentName;


            const nim =
                submission?.nim ||
                activity.nim;


            setText(
                "detailStudentName",
                studentName
            );


            setText(
                "detailStudentNim",
                nim
            );


            setText(
                "studentFullName",
                studentName
            );


            setText(
                "studentNim",
                nim
            );


            setText(
                "studentEmail",
                submission?.email
            );


            setText(
                "studentWhatsapp",
                submission?.whatsapp
            );


            setText(
                "studentYear",
                submission?.year
            );


            setText(
                "studentEntryRoute",
                submission?.entryRoute
            );


            setText(
                "studentDepartment",
                submission?.department ||
                activity.department
            );

        }


        /* =====================================================
           LOAD
        ===================================================== */

        async function loadDetail() {

            /*
             * Backend offline = tidak ada data palsu.
             */
            if (
                !API.config.backendConnected
            ) {

                window.location.href =
                    "history.html";

                return;

            }


            try {

                const activity =
                    await API
                        .getHistoryDetail(
                            historyId
                        );


                if (
                    !activity
                ) {

                    window.location.href =
                        "history.html";

                    return;

                }


                /*
                 * UI guard.
                 * Laravel tetap WAJIB melakukan authorization.
                 */
                if (
                    !isSuperAdmin &&
                    activity.adminUsername !==
                        currentUsername
                ) {

                    showAccessError();

                    return;

                }


                showContent();


                let submission =
                    null;


                let documents =
                    [];


                if (
                    activity.submissionId
                ) {

                    [
                        submission,
                        documents
                    ] =
                        await Promise.all([

                            API.getSubmission(
                                activity.submissionId
                            ),

                            API.getDocuments(
                                activity.submissionId
                            )

                        ]);

                }


                renderActivity(
                    activity,
                    submission
                );


                renderDocuments(
                    documents
                );


                await updateSidebarCount();

            } catch (error) {

                console.error(
                    "Gagal memuat detail History:",
                    error
                );


                window.location.href =
                    "history.html";

            }

        }


        await loadDetail();

    }
);