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


        const STATUS =
            API.STATUS;


        const params =
            new URLSearchParams(
                window.location.search
            );


        const submissionId =
            params.get("id");


        if (!submissionId) {

            window.location.href =
                "/admin/dashboard";

            return;

        }


        let submission =
            await API.getSubmission(
                submissionId
            );


        if (!submission) {

            window.location.href =
                "/admin/dashboard";

            return;

        }


        const allowedStatuses = [

            STATUS.TERVERIFIKASI,
            STATUS.PEMBUATAN_SK,
            STATUS.TTD_WAKIL_DEKAN,
            STATUS.TTD_DEKAN,
            STATUS.SK_SIAP_DIAMBIL

        ];


        if (
            !allowedStatuses.includes(
                submission.status
            )
        ) {

            window.location.href =
                "/admin/pengajuan/" +
                submission.id;

            return;

        }


        /* =====================================================
           ELEMENTS
        ===================================================== */

        const studentAvatar =
            document.getElementById(
                "skStudentAvatar"
            );


        const studentName =
            document.getElementById(
                "skStudentName"
            );


        const studentSummary =
            document.getElementById(
                "skStudentSummary"
            );


        const code =
            document.getElementById(
                "skCode"
            );


        const nim =
            document.getElementById(
                "skNim"
            );


        const department =
            document.getElementById(
                "skDepartment"
            );


        const submittedAt =
            document.getElementById(
                "skSubmittedAt"
            );


        const statusBadge =
            document.getElementById(
                "skStatusBadge"
            );


        const timeline =
            document.getElementById(
                "skProcessTimeline"
            );


        const percentage =
            document.getElementById(
                "skProgressPercentage"
            );


        const progressFill =
            document.getElementById(
                "skProgressFill"
            );


        const currentStatus =
            document.getElementById(
                "skCurrentStatusText"
            );


        const actionTitle =
            document.getElementById(
                "skActionTitle"
            );


        const actionDescription =
            document.getElementById(
                "skActionDescription"
            );


        const primaryAction =
            document.getElementById(
                "skPrimaryAction"
            );


        const actionBox =
            document.getElementById(
                "skActionBox"
            );


        const readySection =
            document.getElementById(
                "skReadySection"
            );


        const modal =
            document.getElementById(
                "skConfirmModal"
            );


        const confirmMessage =
            document.getElementById(
                "skConfirmMessage"
            );


        const confirmButton =
            document.getElementById(
                "confirmSkAction"
            );


        const cancelButton =
            document.getElementById(
                "cancelSkAction"
            );


        const sidebarRevisionCount =
            document.getElementById(
                "processSidebarRevisionCount"
            );


        let pendingStatus =
            null;


        /* =====================================================
           FLOW
        ===================================================== */

        const steps = [

            {
                status:
                    STATUS.TERVERIFIKASI,

                title:
                    "Data Terverifikasi",

                description:
                    "Data pengajuan telah dinyatakan lengkap."
            },

            {
                status:
                    STATUS.PEMBUATAN_SK,

                title:
                    "Pembuatan SK",

                description:
                    "Data digunakan untuk penyusunan dokumen SK."
            },

            {
                status:
                    STATUS.TTD_WAKIL_DEKAN,

                title:
                    "TTD Wakil Dekan",

                description:
                    "SK melalui proses tanda tangan Wakil Dekan."
            },

            {
                status:
                    STATUS.TTD_DEKAN,

                title:
                    "TTD Dekan",

                description:
                    "SK melalui proses tanda tangan Dekan."
            },

            {
                status:
                    STATUS.SK_SIAP_DIAMBIL,

                title:
                    "SK Siap Diambil",

                description:
                    "SK selesai dan dapat diambil mahasiswa."
            }

        ];


        const flow = {

            [STATUS.TERVERIFIKASI]: {

                next:
                    STATUS.PEMBUATAN_SK,

                title:
                    "Mulai Pembuatan SK",

                description:
                    "Data mahasiswa telah terverifikasi. Lanjutkan ketika data sudah siap digunakan dalam proses penyusunan SK.",

                button:
                    "Mulai Pembuatan SK"

            },


            [STATUS.PEMBUATAN_SK]: {

                next:
                    STATUS.TTD_WAKIL_DEKAN,

                title:
                    "Lanjut ke Tanda Tangan Wakil Dekan",

                description:
                    "Pastikan dokumen SK telah selesai disusun sebelum mengirimkannya ke tahap tanda tangan Wakil Dekan.",

                button:
                    "Lanjut ke TTD Wakil Dekan"

            },


            [STATUS.TTD_WAKIL_DEKAN]: {

                next:
                    STATUS.TTD_DEKAN,

                title:
                    "Lanjut ke Tanda Tangan Dekan",

                description:
                    "Pastikan proses tanda tangan Wakil Dekan sudah selesai sebelum melanjutkan SK ke Dekan.",

                button:
                    "Lanjut ke TTD Dekan"

            },


            [STATUS.TTD_DEKAN]: {

                next:
                    STATUS.SK_SIAP_DIAMBIL,

                title:
                    "Finalisasi Proses SK",

                description:
                    "Jika seluruh tanda tangan dan administrasi telah selesai, tandai SK sebagai siap diambil mahasiswa.",

                button:
                    "Tandai SK Siap Diambil"

            },


            [STATUS.SK_SIAP_DIAMBIL]: {

                next:
                    null,

                title:
                    "Proses Administrasi Selesai",

                description:
                    "SK Yudisium telah selesai diproses dan dapat diambil mahasiswa di Bagian Akademik FEB UPR.",

                button:
                    null

            }

        };


        /* =====================================================
           HELPERS
        ===================================================== */

        function setText(
            element,
            value
        ) {

            if (element) {

                element.textContent =
                    value || "-";

            }

        }


        function getCurrentIndex() {

            return steps.findIndex(
                function (step) {

                    return (
                        step.status ===
                        submission.status
                    );

                }
            );

        }


        /* =====================================================
           HEADER
        ===================================================== */

        function renderStudent() {

            setText(
                studentAvatar,
                API.getInitials(
                    submission.name
                )
            );


            setText(
                studentName,
                submission.name
            );


            setText(
                studentSummary,
                submission.nim +
                " • " +
                submission.department
            );


            setText(
                code,
                submission.code
            );


            setText(
                nim,
                submission.nim
            );


            setText(
                department,
                submission.department
            );


            setText(
                submittedAt,
                submission.submittedAt
            );


            const meta =
                API.getStatusMeta(
                    submission.status
                );


            statusBadge.textContent =
                meta.label;


            statusBadge.className =
                "admin-status-badge " +
                meta.className;

        }


        /* =====================================================
           PROGRESS
        ===================================================== */

        function renderProgress() {

            const currentIndex =
                getCurrentIndex();


            const safeIndex =
                Math.max(
                    currentIndex,
                    0
                );


            const progress =
                Math.round(
                    (
                        (
                            safeIndex +
                            1
                        ) /
                        steps.length
                    ) *
                    100
                );


            percentage.textContent =
                progress +
                "%";


            progressFill.style.width =
                progress +
                "%";


            timeline.innerHTML =
                "";


            steps.forEach(
                function (
                    step,
                    index
                ) {

                    const card =
                        document.createElement(
                            "article"
                        );


                    card.className =
                        "sk-process-step";


                    if (
                        index <
                        safeIndex
                    ) {

                        card.classList.add(
                            "completed"
                        );

                    }


                    if (
                        index ===
                        safeIndex
                    ) {

                        card.classList.add(
                            "current"
                        );

                    }


                    const marker =
                        index <
                        safeIndex
                            ? "✓"
                            : index + 1;


                    card.innerHTML =
                        `
                        <div class="sk-process-step-number">
                            ${marker}
                        </div>

                        <h3>
                            ${step.title}
                        </h3>

                        <p>
                            ${step.description}
                        </p>
                        `;


                    timeline.appendChild(
                        card
                    );

                }
            );

        }


        /* =====================================================
           ACTION
        ===================================================== */

        function renderAction() {

            const current =
                flow[
                    submission.status
                ];


            const meta =
                API.getStatusMeta(
                    submission.status
                );


            setText(
                currentStatus,
                meta.label
            );


            setText(
                actionTitle,
                current.title
            );


            setText(
                actionDescription,
                current.description
            );


            if (
                current.button
            ) {

                primaryAction.hidden =
                    false;


                primaryAction.textContent =
                    current.button;


                actionBox.hidden =
                    false;


                readySection.hidden =
                    true;

            } else {

                primaryAction.hidden =
                    true;


                actionBox.hidden =
                    true;


                readySection.hidden =
                    false;

            }

        }


        /* =====================================================
           SIDEBAR
        ===================================================== */

        async function updateSidebarRevisionCount() {

            if (
                !sidebarRevisionCount
            ) {

                return;

            }


            try {

                const submissions =
                    await API
                        .getSubmissions();


                const count =
                    submissions.filter(
                        function (item) {

                            return [

                                STATUS.PERLU_REVISI,
                                STATUS.REVISI_DIKIRIM

                            ].includes(
                                item.status
                            );

                        }
                    ).length;


                sidebarRevisionCount.textContent =
                    count;

            } catch (error) {

                sidebarRevisionCount.textContent =
                    "0";

            }

        }


        /* =====================================================
           RENDER
        ===================================================== */

        function render() {

            renderStudent();

            renderProgress();

            renderAction();

        }


        /* =====================================================
           MODAL
        ===================================================== */

        function openConfirm(
            nextStatus
        ) {

            const nextMeta =
                API.getStatusMeta(
                    nextStatus
                );


            pendingStatus =
                nextStatus;


            confirmMessage.textContent =
                "Pastikan proses pada tahap saat ini telah selesai. Status akan diubah menjadi “" +
                nextMeta.label +
                "”.";


            modal.classList.add(
                "active"
            );


            modal.setAttribute(
                "aria-hidden",
                "false"
            );

        }


        function closeConfirm() {

            pendingStatus =
                null;


            modal.classList.remove(
                "active"
            );


            modal.setAttribute(
                "aria-hidden",
                "true"
            );

        }


        primaryAction
            ?.addEventListener(
                "click",
                function () {

                    const current =
                        flow[
                            submission.status
                        ];


                    if (
                        current?.next
                    ) {

                        openConfirm(
                            current.next
                        );

                    }

                }
            );


        cancelButton
            ?.addEventListener(
                "click",
                closeConfirm
            );


        modal
            ?.addEventListener(
                "click",
                function (event) {

                    if (
                        event.target ===
                        modal
                    ) {

                        closeConfirm();

                    }

                }
            );


        confirmButton
            ?.addEventListener(
                "click",
                async function () {

                    if (
                        !pendingStatus
                    ) {

                        return;

                    }


                    try {

                        confirmButton.disabled =
                            true;


                        await API
                            .updateSkStatus(
                                submission.id,
                                pendingStatus
                            );


                        submission.status =
                            pendingStatus;


                        closeConfirm();


                        render();

                    } catch (error) {

                        console.error(
                            "Gagal memperbarui status SK:",
                            error
                        );


                        alert(
                            "Status belum dapat diperbarui karena backend belum terhubung."
                        );

                    } finally {

                        confirmButton.disabled =
                            false;

                    }

                }
            );


        /* =====================================================
           INITIAL
        ===================================================== */

        render();

        await updateSidebarRevisionCount();

    }
);