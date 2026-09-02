document.addEventListener(
    "DOMContentLoaded",
    function () {

        console.log(
            "Admin proses SK aktif"
        );


        /* =====================================
           CONFIG
        ===================================== */

        const params =
            new URLSearchParams(
                window.location.search
            );


        const submissionData =
            window.YudisiumMockDB
                .getSubmission(
                    params.get("id") || 6
                );


        if (
            !submissionData
        ) {

            window.location.href =
                "pengajuan.html";


            return;

        }


        const submission = {
            id: submissionData.id,
            code: submissionData.code,
            nim: submissionData.nim,
            student: submissionData.name,
            department: submissionData.department,
            year: submissionData.year,
            submittedAt: submissionData.submittedAt,
            status: submissionData.status
        };


        document.querySelector(
            ".sk-process-header h1"
        ).textContent =
            submission.code;


        document.querySelector(
            ".sk-student-avatar"
        ).textContent =
            window.YudisiumMockDB
                .getInitials(
                    submission.student
                );


        document.querySelector(
            ".sk-student-main h2"
        ).textContent =
            submission.student;


        document.querySelector(
            ".sk-student-main p"
        ).textContent =
            "NIM " + submission.nim;


        const studentMeta =
            document.querySelectorAll(
                ".sk-student-meta strong"
            );


        studentMeta[0].textContent =
            submission.department;


        studentMeta[1].textContent =
            submission.year;


        studentMeta[2].textContent =
            submission.submittedAt;


        const stages = [

            {
                key:
                    "TERVERIFIKASI",

                title:
                    "Pengajuan Terverifikasi",

                nextButton:
                    ""
            },

            {
                key:
                    "PEMBUATAN_SK",

                title:
                    "Pembuatan SK",

                nextButton:
                    "Lanjutkan ke Pembuatan SK"
            },

            {
                key:
                    "TTD_WAKIL_DEKAN",

                title:
                    "TTD Wakil Dekan",

                nextButton:
                    "Lanjutkan ke TTD Wakil Dekan"
            },

            {
                key:
                    "TTD_DEKAN",

                title:
                    "TTD Dekan",

                nextButton:
                    "Lanjutkan ke TTD Dekan"
            },

            {
                key:
                    "SK_TERBIT",

                title:
                    "SK Terbit",

                nextButton:
                    "Tandai SK Telah Terbit"
            }

        ];


        /*
         * Status awal untuk simulasi.
         *
         * Nanti status ini berasal
         * dari database Laravel.
         */

        let currentStageIndex =
            0;


        /* =====================================
           ELEMENTS
        ===================================== */

        const stageElements =
            Array.from(
                document.querySelectorAll(
                    ".sk-stage-item"
                )
            );


        const headerStatus =
            document.getElementById(
                "skHeaderStatus"
            );


        const progressText =
            document.getElementById(
                "skProgressText"
            );


        const progressFill =
            document.getElementById(
                "skProgressFill"
            );


        const actionCard =
            document.getElementById(
                "skActionCard"
            );


        const completedCard =
            document.getElementById(
                "skCompletedCard"
            );


        const nextStageTitle =
            document.getElementById(
                "skNextStageTitle"
            );


        const nextStageDescription =
            document.getElementById(
                "skNextStageDescription"
            );


        const nextStatus =
            document.getElementById(
                "skNextStatus"
            );


        const note =
            document.getElementById(
                "skProcessNote"
            );


        const confirmation =
            document.getElementById(
                "skProcessConfirmation"
            );


        const confirmationError =
            document.getElementById(
                "skConfirmationError"
            );


        const advanceButton =
            document.getElementById(
                "advanceSkProcess"
            );


        const confirmModal =
            document.getElementById(
                "skConfirmModal"
            );


        const confirmStatus =
            document.getElementById(
                "skConfirmStatus"
            );


        const cancelUpdate =
            document.getElementById(
                "cancelSkUpdate"
            );


        const confirmUpdate =
            document.getElementById(
                "confirmSkUpdate"
            );


        const successModal =
            document.getElementById(
                "skSuccessModal"
            );


        const successMessage =
            document.getElementById(
                "skSuccessMessage"
            );


        const closeSuccess =
            document.getElementById(
                "closeSkSuccess"
            );


        const activityList =
            document.getElementById(
                "skActivityList"
            );


        /* =====================================
           ADMIN
        ===================================== */

        function getCurrentAdmin() {

            const username =
                sessionStorage.getItem(
                    "admin_username"
                ) || "admin";


            const role =
                sessionStorage.getItem(
                    "admin_role"
                ) || "ADMIN";


            return {
                username:
                    username,

                role:
                    role
            };

        }


        /* =====================================
           DATE
        ===================================== */

        function getCurrentDateTime() {

            const date =
                new Date();


            return new Intl
                .DateTimeFormat(
                    "id-ID",
                    {
                        dateStyle:
                            "medium",

                        timeStyle:
                            "short"
                    }
                )
                .format(
                    date
                );

        }


        /* =====================================
           STATUS LABEL
        ===================================== */

        function getStageDescription(
            key
        ) {

            const descriptions = {

                PEMBUATAN_SK:
                    "Tandai bahwa proses pembuatan SK telah dimulai.",

                TTD_WAKIL_DEKAN:
                    "Tandai bahwa SK telah masuk ke proses tanda tangan Wakil Dekan.",

                TTD_DEKAN:
                    "Tandai bahwa SK telah masuk ke proses tanda tangan Dekan.",

                SK_TERBIT:
                    "Konfirmasi bahwa seluruh proses selesai dan SK telah terbit."

            };


            return (
                descriptions[key] ||
                ""
            );

        }



        function getHeaderClass(
            status
        ) {

            if (
                status ===
                "TERVERIFIKASI"
            ) {

                return "verified";

            }


            if (
                status ===
                "SK_TERBIT"
            ) {

                return "completed";

            }


            return "process";

        }



        function formatStatus(
            status
        ) {

            const labels = {

                TERVERIFIKASI:
                    "Terverifikasi",

                PEMBUATAN_SK:
                    "Pembuatan SK",

                TTD_WAKIL_DEKAN:
                    "TTD Wakil Dekan",

                TTD_DEKAN:
                    "TTD Dekan",

                SK_TERBIT:
                    "SK Terbit"

            };


            return (
                labels[status] ||
                status
            );

        }


        /* =====================================
           LOCAL HISTORY
        ===================================== */

        function getActivityStorageKey() {

            return (
                "yudisium_sk_activity_" +
                submission.id
            );

        }



        function loadActivities() {

            try {

                const saved =
                    localStorage.getItem(
                        getActivityStorageKey()
                    );


                if (
                    !saved
                ) {

                    return [];

                }


                return JSON.parse(
                    saved
                );

            } catch (
                error
            ) {

                console.error(
                    "Gagal membaca history",
                    error
                );


                return [];

            }

        }



        function saveActivities(
            activities
        ) {

            localStorage.setItem(
                getActivityStorageKey(),
                JSON.stringify(
                    activities
                )
            );

        }



        function saveGlobalActivity(
            activity
        ) {

            const key =
                "yudisium_admin_activity";


            let activities =
                [];


            try {

                const saved =
                    localStorage.getItem(
                        key
                    );


                if (
                    saved
                ) {

                    activities =
                        JSON.parse(
                            saved
                        );

                }

            } catch (
                error
            ) {

                activities =
                    [];

            }


            activities.unshift(
                activity
            );


            localStorage.setItem(
                key,
                JSON.stringify(
                    activities
                )
            );

        }



        function addActivity(
            fromStatus,
            toStatus,
            processNote
        ) {

            const admin =
                getCurrentAdmin();


            const activity = {

                id:
                    Date.now(),

                admin_username:
                    admin.username,

                admin_role:
                    admin.role,

                pengajuan_id:
                    submission.id,

                kode_pengajuan:
                    submission.code,

                nim:
                    submission.nim,

                mahasiswa:
                    submission.student,

                action:
                    "UPDATE_SK_STATUS",

                action_label:
                    "Update Status SK",

                old_status:
                    fromStatus,

                new_status:
                    toStatus,

                note:
                    processNote || null,

                created_at:
                    getCurrentDateTime(),

                timestamp:
                    Date.now()

            };


            const activities =
                loadActivities();


            activities.unshift(
                activity
            );


            saveActivities(
                activities
            );


            saveGlobalActivity(
                activity
            );

        }


        /* =====================================
           RENDER ACTIVITY
        ===================================== */

        function renderActivities() {

            const activities =
                loadActivities();


            activityList.innerHTML =
                "";


            if (
                activities.length ===
                0
            ) {

                activityList.innerHTML =
                    `
                    <div class="sk-activity-empty">
                        Belum ada aktivitas tambahan.
                    </div>
                    `;


                return;

            }


            activities.forEach(
                function (activity) {

                    const item =
                        document.createElement(
                            "div"
                        );


                    item.className =
                        "sk-activity-item";


                    item.innerHTML = `
                        <div class="sk-activity-marker">
                            ✓
                        </div>

                        <div class="sk-activity-content">

                            <strong>
                                ${formatStatus(
                                    activity.new_status
                                )}
                            </strong>

                            <p>
                                Status diubah dari
                                ${formatStatus(
                                    activity.old_status
                                )}
                                menjadi
                                ${formatStatus(
                                    activity.new_status
                                )}.
                            </p>

                            ${
                                activity.note
                                    ?
                                    `
                                    <div class="sk-activity-note">
                                        ${activity.note}
                                    </div>
                                    `
                                    :
                                    ""
                            }

                            <span>
                                ${activity.admin_username}
                                •
                                ${activity.created_at}
                            </span>

                        </div>
                    `;


                    activityList.appendChild(
                        item
                    );

                }
            );

        }


        /* =====================================
           RESTORE STATE
        ===================================== */

        function restoreStageFromActivities() {

            const activities =
                loadActivities();


            if (
                activities.length ===
                0
            ) {

                const statusMap = {
                    "terverifikasi": "TERVERIFIKASI",
                    "pembuatan sk": "PEMBUATAN_SK",
                    "ttd wakil dekan": "TTD_WAKIL_DEKAN",
                    "ttd dekan": "TTD_DEKAN",
                    "sk terbit": "SK_TERBIT"
                };


                const initialStatus =
                    statusMap[
                        submission.status
                    ] ||
                    "TERVERIFIKASI";


                currentStageIndex =
                    Math.max(
                        0,
                        stages.findIndex(
                            function (stage) {

                                return (
                                    stage.key ===
                                    initialStatus
                                );

                            }
                        )
                    );


                return;

            }


            const latestStatus =
                activities[0]
                    .new_status;


            const index =
                stages.findIndex(
                    function (stage) {

                        return (
                            stage.key ===
                            latestStatus
                        );

                    }
                );


            if (
                index >=
                0
            ) {

                currentStageIndex =
                    index;

            }

        }


        /* =====================================
           UPDATE UI
        ===================================== */

        function updateStageUI() {

            const currentStage =
                stages[
                    currentStageIndex
                ];


            stageElements.forEach(
                function (
                    element,
                    index
                ) {

                    const badge =
                        element.querySelector(
                            ".sk-stage-badge"
                        );


                    const date =
                        element.querySelector(
                            ".sk-stage-date"
                        );


                    const admin =
                        element.querySelector(
                            ".sk-stage-admin"
                        );


                    element.classList.remove(
                        "completed",
                        "current",
                        "waiting"
                    );


                    if (
                        index <
                        currentStageIndex
                    ) {

                        element
                            .classList
                            .add(
                                "completed"
                            );


                        badge.textContent =
                            "Selesai";


                        badge.className =
                            "sk-stage-badge completed";


                    } else if (
                        index ===
                        currentStageIndex
                    ) {

                        element
                            .classList
                            .add(
                                "current"
                            );


                        badge.textContent =
                            currentStage.key ===
                            "SK_TERBIT"
                                ?
                                "Selesai"
                                :
                                "Status Saat Ini";


                        badge.className =
                            currentStage.key ===
                            "SK_TERBIT"
                                ?
                                "sk-stage-badge completed"
                                :
                                "sk-stage-badge current";


                    } else {

                        element
                            .classList
                            .add(
                                "waiting"
                            );


                        badge.textContent =
                            "Menunggu";


                        badge.className =
                            "sk-stage-badge waiting";

                    }


                    date.textContent =
                        "-";


                    admin.textContent =
                        "-";

                }
            );


            /*
             * Isi metadata berdasarkan
             * aktivitas yang tersimpan.
             */

            const activities =
                loadActivities();


            activities.forEach(
                function (activity) {

                    const element =
                        stageElements.find(
                            function (
                                stageElement
                            ) {

                                return (
                                    stageElement.dataset
                                        .stage ===
                                    activity.new_status
                                );

                            }
                        );


                    if (
                        !element
                    ) {

                        return;

                    }


                    element.querySelector(
                        ".sk-stage-date"
                    ).textContent =
                        activity.created_at;


                    element.querySelector(
                        ".sk-stage-admin"
                    ).textContent =
                        "oleh " +
                        activity.admin_username;

                }
            );


            /*
             * Status terverifikasi awal.
             */

            const firstElement =
                stageElements[0];


            if (
                firstElement.querySelector(
                    ".sk-stage-date"
                ).textContent ===
                "-"
            ) {

                firstElement.querySelector(
                    ".sk-stage-date"
                ).textContent =
                    "31 Agu 2026";


                firstElement.querySelector(
                    ".sk-stage-admin"
                ).textContent =
                    "Pengajuan telah terverifikasi";

            }


            /* HEADER */

            headerStatus.className =
                "admin-status-badge " +
                getHeaderClass(
                    currentStage.key
                );


            headerStatus.textContent =
                formatStatus(
                    currentStage.key
                );


            /* PROGRESS */

            progressText.textContent =
                (
                    currentStageIndex +
                    1
                ) +
                " / " +
                stages.length;


            progressFill.style.width =
                (
                    (
                        currentStageIndex +
                        1
                    ) /
                    stages.length *
                    100
                ) +
                "%";


            /* NEXT */

            if (
                currentStageIndex >=
                stages.length -
                1
            ) {

                actionCard.style.display =
                    "none";


                completedCard
                    .classList
                    .add(
                        "active"
                    );


                return;

            }


            completedCard
                .classList
                .remove(
                    "active"
                );


            actionCard.style.display =
                "block";


            const upcomingStage =
                stages[
                    currentStageIndex +
                    1
                ];


            nextStageTitle.textContent =
                upcomingStage.title;


            nextStageDescription.textContent =
                getStageDescription(
                    upcomingStage.key
                );


            nextStatus.textContent =
                upcomingStage.key;


            advanceButton.textContent =
                upcomingStage.nextButton;

        }


        /* =====================================
           OPEN CONFIRMATION
        ===================================== */

        advanceButton.addEventListener(
            "click",
            function () {

                confirmationError
                    .classList
                    .remove(
                        "active"
                    );


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


                if (
                    currentStageIndex >=
                    stages.length -
                    1
                ) {

                    return;

                }


                const upcomingStage =
                    stages[
                        currentStageIndex +
                        1
                    ];


                confirmStatus.textContent =
                    formatStatus(
                        upcomingStage.key
                    );


                confirmModal
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


        /* =====================================
           CANCEL
        ===================================== */

        cancelUpdate.addEventListener(
            "click",
            function () {

                confirmModal
                    .classList
                    .remove(
                        "active"
                    );

            }
        );


        confirmModal.addEventListener(
            "click",
            function (event) {

                if (
                    event.target ===
                    confirmModal
                ) {

                    confirmModal
                        .classList
                        .remove(
                            "active"
                        );

                }

            }
        );


        /* =====================================
           CONFIRM UPDATE
        ===================================== */

        confirmUpdate.addEventListener(
            "click",
            function () {

                if (
                    currentStageIndex >=
                    stages.length -
                    1
                ) {

                    return;

                }


                const oldStage =
                    stages[
                        currentStageIndex
                    ];


                const newStage =
                    stages[
                        currentStageIndex +
                        1
                    ];


                const processNote =
                    note.value
                        .trim();


                /*
                 * NANTI BACKEND:
                 *
                 * UPDATE pengajuan_yudisium
                 * SET status = newStage.key
                 *
                 * INSERT riwayat_status
                 *
                 * INSERT activity_logs
                 */

                addActivity(
                    oldStage.key,
                    newStage.key,
                    processNote
                );


                const storedStatus = {
                    TERVERIFIKASI: "terverifikasi",
                    PEMBUATAN_SK: "pembuatan sk",
                    TTD_WAKIL_DEKAN: "ttd wakil dekan",
                    TTD_DEKAN: "ttd dekan",
                    SK_TERBIT: "sk terbit"
                }[
                    newStage.key
                ];


                window.YudisiumMockDB
                    .setSubmissionStatus(
                        submission.id,
                        storedStatus
                    );


                currentStageIndex++;


                confirmModal
                    .classList
                    .remove(
                        "active"
                    );


                note.value =
                    "";


                confirmation.checked =
                    false;


                updateStageUI();


                renderActivities();


                successMessage.textContent =
                    "Status pengajuan berhasil diperbarui menjadi " +
                    formatStatus(
                        newStage.key
                    ) +
                    ".";


                successModal
                    .classList
                    .add(
                        "active"
                    );

            }
        );


        /* =====================================
           SUCCESS
        ===================================== */

        closeSuccess.addEventListener(
            "click",
            function () {

                successModal
                    .classList
                    .remove(
                        "active"
                    );

            }
        );


        /* =====================================
           INITIALIZE
        ===================================== */

        restoreStageFromActivities();

        updateStageUI();

        renderActivities();

    }
);
