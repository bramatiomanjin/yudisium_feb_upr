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
           PROCESS FLOW
        ===================================================== */

        const steps = [

            {

                status:
                    STATUS.TERVERIFIKASI,

                title:
                    "Data Terverifikasi",

                description:
                    "Data mahasiswa telah dinyatakan lengkap dan siap masuk proses administrasi SK."

            },

            {

                status:
                    STATUS.PEMBUATAN_SK,

                title:
                    "Pembuatan SK",

                description:
                    "Data mahasiswa digunakan untuk penyusunan dokumen SK Yudisium."

            },

            {

                status:
                    STATUS.TTD_WAKIL_DEKAN,

                title:
                    "TTD Wakil Dekan",

                description:
                    "Dokumen SK menunggu atau menjalani proses tanda tangan Wakil Dekan."

            },

            {

                status:
                    STATUS.TTD_DEKAN,

                title:
                    "TTD Dekan",

                description:
                    "Dokumen SK menunggu atau menjalani proses tanda tangan Dekan."

            },

            {

                status:
                    STATUS.SK_SIAP_DIAMBIL,

                title:
                    "SK Siap Diambil",

                description:
                    "Seluruh proses selesai dan SK dapat diambil oleh mahasiswa."

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
                    "Pastikan dokumen SK telah selesai disusun sebelum melanjutkan ke proses tanda tangan Wakil Dekan.",

                button:
                    "Lanjut ke TTD Wakil Dekan"

            },


            [STATUS.TTD_WAKIL_DEKAN]: {

                next:
                    STATUS.TTD_DEKAN,

                title:
                    "Lanjut ke Tanda Tangan Dekan",

                description:
                    "Pastikan proses tanda tangan Wakil Dekan sudah selesai sebelum meneruskan dokumen kepada Dekan.",

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

            if (!element) {
                return;
            }


            element.textContent =
                value ??
                "-";
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


        function formatDateTime(
            value
        ) {

            if (!value) {
                return "-";
            }


            const date =
                new Date(
                    value
                );


            if (
                Number.isNaN(
                    date.getTime()
                )
            ) {

                return value;
            }


            const formatted =
                new Intl.DateTimeFormat(
                    "id-ID",
                    {

                        timeZone:
                            "Asia/Jakarta",

                        day:
                            "2-digit",

                        month:
                            "long",

                        year:
                            "numeric",

                        hour:
                            "2-digit",

                        minute:
                            "2-digit",

                        hour12:
                            false

                    }
                )
                    .format(
                        date
                    );


            return (
                formatted
                    .replace(
                        ".",
                        ":"
                    )
                +
                " WIB"
            );
        }


        function getStepState(
            index,
            currentIndex
        ) {

            if (
                index <
                currentIndex
            ) {

                return {
                    className:
                        "done",

                    label:
                        "Selesai",

                    marker:
                        "✓"
                };
            }


            if (
                index ===
                currentIndex
            ) {

                return {
                    className:
                        "active",

                    label:
                        "Tahap Saat Ini",

                    marker:
                        String(
                            index + 1
                        )
                };
            }


            return {
                className:
                    "pending",

                label:
                    "Belum Diproses",

                marker:
                    String(
                        index + 1
                    )
            };
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
                formatDateTime(
                    submission.submittedAt
                )
            );


            const meta =
                API.getStatusMeta(
                    submission.status
                );


            if (statusBadge) {

                statusBadge.textContent =
                    meta.label;


                statusBadge.className =
                    "admin-status-badge " +
                    meta.className;
            }
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
                        )
                        /
                        steps.length
                    )
                    *
                    100
                );


            if (percentage) {

                percentage.textContent =
                    progress +
                    "%";
            }


            if (progressFill) {

                progressFill.style.width =
                    progress +
                    "%";
            }


            if (!timeline) {
                return;
            }


            timeline.innerHTML =
                "";


            steps.forEach(
                function (
                    step,
                    index
                ) {

                    const state =
                        getStepState(
                            index,
                            safeIndex
                        );


                    const item =
                        document.createElement(
                            "article"
                        );


                    item.className =
                        "sk-timeline-step " +
                        state.className;


                    item.innerHTML =
                        `
                        <div class="sk-timeline-dot">
                            ${state.marker}
                        </div>

                        <div class="sk-timeline-body">

                            <span class="sk-timeline-state">
                                ${state.label}
                            </span>

                            <strong>
                                ${step.title}
                            </strong>

                            <p>
                                ${step.description}
                            </p>

                        </div>

                        <div class="sk-timeline-timestamp">
                            ${
                                state.className ===
                                "done"
                                    ? "Selesai"
                                    : state.className ===
                                      "active"
                                        ? "Aktif"
                                        : "Menunggu"
                            }
                        </div>
                        `;


                    timeline.appendChild(
                        item
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


            if (!current) {
                return;
            }


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

                if (primaryAction) {

                    primaryAction.hidden =
                        false;


                    primaryAction.textContent =
                        current.button;
                }


                if (actionBox) {

                    actionBox.hidden =
                        false;
                }


                if (readySection) {

                    readySection.hidden =
                        true;
                }

            } else {

                if (primaryAction) {

                    primaryAction.hidden =
                        true;
                }


                if (actionBox) {

                    actionBox.hidden =
                        true;
                }


                if (readySection) {

                    readySection.hidden =
                        false;
                }
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
                    )
                        .length;


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
           CONFIRM MODAL
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


            if (confirmMessage) {

                confirmMessage.textContent =
                    "Pastikan proses pada tahap saat ini telah selesai. Status akan diubah menjadi “" +
                    nextMeta.label +
                    "”.";
            }


            if (modal) {

                modal.classList.add(
                    "active"
                );


                modal.setAttribute(
                    "aria-hidden",
                    "false"
                );
            }
        }


        function closeConfirm() {

            pendingStatus =
                null;


            if (modal) {

                modal.classList.remove(
                    "active"
                );


                modal.setAttribute(
                    "aria-hidden",
                    "true"
                );
            }
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


                        /*
                         * Status lokal baru diubah setelah
                         * backend berhasil menerima update.
                         */
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
                            error?.message ||
                            "Status belum dapat diperbarui."
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