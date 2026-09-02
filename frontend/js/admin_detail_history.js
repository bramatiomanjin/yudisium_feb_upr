document.addEventListener(
    "DOMContentLoaded",
    function () {

        console.log(
            "Admin detail history aktif"
        );


        /* =====================================
           SESSION
        ===================================== */

        const currentUsername =
            sessionStorage.getItem(
                "admin_username"
            ) || "admin";


        const currentRole =
            sessionStorage.getItem(
                "admin_role"
            ) || "ADMIN";


        const isSuperAdmin =
            currentRole ===
            "SUPER_ADMIN";


        /* =====================================
           QUERY ID
        ===================================== */

        const params =
            new URLSearchParams(
                window.location.search
            );


        const requestedId =
            params.get(
                "id"
            );


        /* =====================================
           ELEMENTS
        ===================================== */

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


        /* =====================================
           MOCK HISTORY
        ===================================== */

        const mockActivities = [

            {
                id:
                    1001,

                admin_username:
                    "adminfeb",

                admin_role:
                    "ADMIN",

                pengajuan_id:
                    1,

                kode_pengajuan:
                    "YDS-2026-0001",

                nim:
                    "2301110001",

                mahasiswa:
                    "Andi Saputra",

                jurusan:
                    "Manajemen",

                action:
                    "VERIFICATION",

                action_label:
                    "Verifikasi Pengajuan",

                old_status:
                    "VERIFIKASI_ADMIN",

                new_status:
                    "TERVERIFIKASI",

                note:
                    "Seluruh data dan dokumen mahasiswa telah disetujui.",

                created_at:
                    "2 Sep 2026, 08.15",

                timestamp:
                    1788308100000
            },

            {
                id:
                    1002,

                admin_username:
                    "adminfeb",

                admin_role:
                    "ADMIN",

                pengajuan_id:
                    2,

                kode_pengajuan:
                    "YDS-2026-0002",

                nim:
                    "2301120002",

                mahasiswa:
                    "Citra Lestari",

                jurusan:
                    "Akuntansi",

                action:
                    "REVISION",

                action_label:
                    "Permintaan Revisi",

                old_status:
                    "VERIFIKASI_ADMIN",

                new_status:
                    "PERLU_REVISI",

                note:
                    "Judul karya tulis dan dokumen rekap nilai perlu diperbaiki.",

                created_at:
                    "2 Sep 2026, 08.45",

                timestamp:
                    1788309900000
            },

            {
                id:
                    1003,

                admin_username:
                    "ciko_admin",

                admin_role:
                    "ADMIN",

                pengajuan_id:
                    3,

                kode_pengajuan:
                    "YDS-2026-0003",

                nim:
                    "2301130003",

                mahasiswa:
                    "Deni Pratama",

                jurusan:
                    "Ekonomi Pembangunan",

                action:
                    "UPDATE_SK_STATUS",

                action_label:
                    "Update Status SK",

                old_status:
                    "TERVERIFIKASI",

                new_status:
                    "PEMBUATAN_SK",

                note:
                    "Dokumen siap masuk tahap pembuatan SK.",

                created_at:
                    "2 Sep 2026, 09.10",

                timestamp:
                    1788311400000
            },

            {
                id:
                    1004,

                admin_username:
                    "ciko_admin",

                admin_role:
                    "ADMIN",

                pengajuan_id:
                    4,

                kode_pengajuan:
                    "YDS-2026-0004",

                nim:
                    "2301110004",

                mahasiswa:
                    "Eva Natalia",

                jurusan:
                    "Manajemen",

                action:
                    "UPDATE_SK_STATUS",

                action_label:
                    "Update Status SK",

                old_status:
                    "PEMBUATAN_SK",

                new_status:
                    "TTD_WAKIL_DEKAN",

                note:
                    null,

                created_at:
                    "2 Sep 2026, 09.25",

                timestamp:
                    1788312300000
            },

            {
                id:
                    1005,

                admin_username:
                    "superadmin",

                admin_role:
                    "SUPER_ADMIN",

                pengajuan_id:
                    5,

                kode_pengajuan:
                    "YDS-2026-0005",

                nim:
                    "2301120005",

                mahasiswa:
                    "Fajar Rahman",

                jurusan:
                    "Akuntansi",

                action:
                    "REVISION",

                action_label:
                    "Review Revisi",

                old_status:
                    "REVISI_DIKIRIM",

                new_status:
                    "TERVERIFIKASI",

                note:
                    "Seluruh perbaikan mahasiswa telah diterima.",

                created_at:
                    "2 Sep 2026, 09.50",

                timestamp:
                    1788313800000
            },

            {
                id:
                    1006,

                admin_username:
                    "superadmin",

                admin_role:
                    "SUPER_ADMIN",

                pengajuan_id:
                    6,

                kode_pengajuan:
                    "YDS-2026-0006",

                nim:
                    "2301110006",

                mahasiswa:
                    "Grace Amelia",

                jurusan:
                    "Manajemen",

                action:
                    "UPDATE_SK_STATUS",

                action_label:
                    "Update Status SK",

                old_status:
                    "TTD_WAKIL_DEKAN",

                new_status:
                    "TTD_DEKAN",

                note:
                    "Dokumen diteruskan ke Dekan.",

                created_at:
                    "2 Sep 2026, 10.05",

                timestamp:
                    1788314700000
            }

        ];


        /* =====================================
           MOCK STUDENTS
        ===================================== */

        const students = {

            "2301110001": {
                email:
                    "andi.saputra@example.com",

                whatsapp:
                    "081234567890",

                year:
                    "2023",

                entryRoute:
                    "Reguler"
            },

            "2301120002": {
                email:
                    "citra.lestari@example.com",

                whatsapp:
                    "081298761234",

                year:
                    "2023",

                entryRoute:
                    "Reguler"
            },

            "2301130003": {
                email:
                    "deni.pratama@example.com",

                whatsapp:
                    "081377788899",

                year:
                    "2023",

                entryRoute:
                    "Reguler"
            },

            "2301110004": {
                email:
                    "eva.natalia@example.com",

                whatsapp:
                    "082155566677",

                year:
                    "2023",

                entryRoute:
                    "Reguler"
            },

            "2301120005": {
                email:
                    "fajar.rahman@example.com",

                whatsapp:
                    "081344455566",

                year:
                    "2023",

                entryRoute:
                    "Reguler"
            },

            "2301110006": {
                email:
                    "grace.amelia@example.com",

                whatsapp:
                    "082244455599",

                year:
                    "2023",

                entryRoute:
                    "Reguler"
            }

        };


        /* =====================================
           DOCUMENTS
        ===================================== */

        const documents = [

            {
                title:
                    "Formulir Pendaftaran Yudisium",

                filename:
                    "formulir_yudisium.pdf",

                size:
                    "650 KB"
            },

            {
                title:
                    "Foto 3x4 Berwarna",

                filename:
                    "foto_3x4.pdf",

                size:
                    "420 KB"
            },

            {
                title:
                    "Ijazah SLTA",

                filename:
                    "ijazah_slta.pdf",

                size:
                    "830 KB"
            },

            {
                title:
                    "Berita Acara Ujian Skripsi / Artikel",

                filename:
                    "berita_acara.pdf",

                size:
                    "790 KB"
            },

            {
                title:
                    "Rekapitulasi Nilai Ujian",

                filename:
                    "rekap_nilai.pdf",

                size:
                    "740 KB"
            },

            {
                title:
                    "Blanko Revisi",

                filename:
                    "blanko_revisi.pdf",

                size:
                    "510 KB"
            },

            {
                title:
                    "Tanda Terima Skripsi / Artikel",

                filename:
                    "tanda_terima.pdf",

                size:
                    "1.2 MB"
            },

            {
                title:
                    "Surat Pernyataan Penulisan Ijazah",

                filename:
                    "pernyataan_ijazah.pdf",

                size:
                    "560 KB"
            },

            {
                title:
                    "Surat Bebas Pinjam Perpustakaan Universitas",

                filename:
                    "bebas_pinjam_universitas.pdf",

                size:
                    "690 KB"
            },

            {
                title:
                    "Surat Bebas Pinjam Perpustakaan Fakultas",

                filename:
                    "bebas_pinjam_fakultas.pdf",

                size:
                    "620 KB"
            },

            {
                title:
                    "KHS Semester 1 s/d Terbaru",

                filename:
                    "khs.pdf",

                size:
                    "880 KB"
            },

            {
                title:
                    "Transkrip Nilai Ujian Skripsi",

                filename:
                    "transkrip.pdf",

                size:
                    "710 KB"
            },

            {
                title:
                    "Surat Tugas Dosen Pembimbing",

                filename:
                    "surat_tugas_pembimbing.pdf",

                size:
                    "1.4 MB"
            },

            {
                title:
                    "Surat Verifikasi Bebas Tunggakan",

                filename:
                    "bebas_tunggakan.pdf",

                size:
                    "530 KB"
            },

            {
                title:
                    "Bukti Pengisian Jurnal Jurusan",

                filename:
                    "bukti_jurnal.pdf",

                size:
                    "680 KB"
            }

        ];


        /* =====================================
           LOCAL ACTIVITIES
        ===================================== */

        function loadStoredActivities() {

            try {

                const saved =
                    localStorage.getItem(
                        "yudisium_admin_activity"
                    );


                if (!saved) {

                    return [];

                }


                const parsed =
                    JSON.parse(
                        saved
                    );


                return Array.isArray(
                    parsed
                )
                    ?
                    parsed
                    :
                    [];

            } catch (
                error
            ) {

                console.error(
                    "Gagal membaca local history",
                    error
                );


                return [];

            }

        }


        function getAllActivities() {

            const all =
                [
                    ...loadStoredActivities(),
                    ...mockActivities
                ];


            const map =
                new Map();


            all.forEach(
                function (activity) {

                    const key =
                        String(
                            activity.id
                        );


                    if (
                        !map.has(
                            key
                        )
                    ) {

                        map.set(
                            key,
                            activity
                        );

                    }

                }
            );


            return Array.from(
                map.values()
            );

        }


        /* =====================================
           FIND ACTIVITY
        ===================================== */

        const activity =
            getAllActivities().find(
                function (item) {

                    return (
                        String(
                            item.id
                        ) ===
                        String(
                            requestedId
                        )
                    );

                }
            );


        /* =====================================
           ACCESS
        ===================================== */

        function denyAccess() {

            content.style.display =
                "none";


            accessError.style.display =
                "flex";

        }


        function allowAccess() {

            accessError.style.display =
                "none";


            content.style.display =
                "block";

        }


        if (
            !activity
        ) {

            denyAccess();

            return;

        }


        /*
         * ADMIN:
         * hanya history sendiri.
         *
         * SUPER_ADMIN:
         * seluruh history.
         */

        if (
            !isSuperAdmin &&
            activity.admin_username !==
            currentUsername
        ) {

            denyAccess();

            return;

        }


        allowAccess();


        /* =====================================
           FORMAT STATUS
        ===================================== */

        function formatStatus(
            status
        ) {

            const labels = {

                DIAJUKAN:
                    "Diajukan",

                VERIFIKASI_ADMIN:
                    "Verifikasi Admin",

                PERLU_REVISI:
                    "Perlu Revisi",

                REVISI_DIKIRIM:
                    "Revisi Dikirim",

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
                labels[
                    status
                ] ||
                status ||
                "-"
            );

        }


        function getActivityBadge(
            action
        ) {

            if (
                action ===
                "VERIFICATION"
            ) {

                return {
                    label:
                        "VERIFIKASI",

                    className:
                        "verification"
                };

            }


            if (
                action ===
                "REVISION"
            ) {

                return {
                    label:
                        "REVISI",

                    className:
                        "revision"
                };

            }


            return {
                label:
                    "PROSES SK",

                className:
                    "process"
            };

        }


        /* =====================================
           STUDENT DATA
        ===================================== */

        const student =
            students[
                activity.nim
            ] || {

                email:
                    "mahasiswa@example.com",

                whatsapp:
                    "081234567890",

                year:
                    "2023",

                entryRoute:
                    "Reguler"
            };


        /* =====================================
           INITIAL
        ===================================== */

        const badge =
            getActivityBadge(
                activity.action
            );


        document.getElementById(
            "detailHistoryCode"
        ).textContent =
            activity.kode_pengajuan;


        document.getElementById(
            "detailActivityTitle"
        ).textContent =
            activity.action_label ||
            activity.action;


        document.getElementById(
            "detailActivityDescription"
        ).textContent =
            "Aktivitas pada pengajuan " +
            activity.mahasiswa +
            ".";


        const activityBadge =
            document.getElementById(
                "detailActivityBadge"
            );


        activityBadge.textContent =
            badge.label;


        activityBadge.className =
            "detail-history-status-badge " +
            badge.className;


        document.getElementById(
            "detailAdminUsername"
        ).textContent =
            activity.admin_username;


        document.getElementById(
            "detailAdminRole"
        ).textContent =
            activity.admin_role ||
            "ADMIN";


        document.getElementById(
            "detailActivityTime"
        ).textContent =
            activity.created_at ||
            "-";


        document.getElementById(
            "detailActivityType"
        ).textContent =
            activity.action_label ||
            activity.action;


        document.getElementById(
            "detailOldStatus"
        ).textContent =
            formatStatus(
                activity.old_status
            );


        document.getElementById(
            "detailNewStatus"
        ).textContent =
            formatStatus(
                activity.new_status
            );


        const noteBox =
            document.getElementById(
                "detailHistoryNoteBox"
            );


        if (
            activity.note
        ) {

            document.getElementById(
                "detailHistoryNote"
            ).textContent =
                activity.note;

        } else {

            noteBox.style.display =
                "none";

        }


        document.getElementById(
            "detailStudentName"
        ).textContent =
            activity.mahasiswa;


        document.getElementById(
            "detailStudentNim"
        ).textContent =
            "NIM " +
            activity.nim;


        document.getElementById(
            "studentFullName"
        ).textContent =
            activity.mahasiswa;


        document.getElementById(
            "studentNim"
        ).textContent =
            activity.nim;


        document.getElementById(
            "studentEmail"
        ).textContent =
            student.email;


        document.getElementById(
            "studentWhatsapp"
        ).textContent =
            student.whatsapp;


        document.getElementById(
            "studentYear"
        ).textContent =
            student.year;


        document.getElementById(
            "studentEntryRoute"
        ).textContent =
            student.entryRoute;


        document.getElementById(
            "studentDepartment"
        ).textContent =
            activity.jurusan ||
            "Manajemen";


        /* =====================================
           INITIALS
        ===================================== */

        function getInitials(
            name
        ) {

            return String(
                name
            )
                .split(" ")
                .filter(
                    function (part) {

                        return (
                            part !==
                            ""
                        );

                    }
                )
                .slice(
                    0,
                    2
                )
                .map(
                    function (part) {

                        return (
                            part.charAt(
                                0
                            )
                        );

                    }
                )
                .join("")
                .toUpperCase();

        }


        document.querySelector(
            ".detail-history-avatar"
        ).textContent =
            getInitials(
                activity.mahasiswa
            );


        /* =====================================
           RENDER DOCUMENTS
        ===================================== */

        documents.forEach(
            function (
                documentData,
                index
            ) {

                const item =
                    document.createElement(
                        "article"
                    );


                item.className =
                    "detail-history-document-item";


                item.innerHTML = `
                    <div class="detail-history-document-info">

                        <div class="detail-history-document-icon">
                            PDF
                        </div>

                        <div>

                            <span>
                                Dokumen ${index + 1}
                            </span>

                            <strong>
                                ${documentData.title}
                            </strong>

                            <small>
                                ${documentData.filename}
                                •
                                ${documentData.size}
                            </small>

                        </div>

                    </div>


                    <button
                        type="button"
                        class="detail-history-preview-button"
                        data-title="${documentData.title}"
                        data-file="${documentData.filename}"
                    >
                        Preview
                    </button>
                `;


                documentList.appendChild(
                    item
                );

            }
        );


        /* =====================================
           PREVIEW
        ===================================== */

        const previewModal =
            document.getElementById(
                "historyDocumentPreview"
            );


        const previewTitle =
            document.getElementById(
                "historyPreviewTitle"
            );


        const previewFilename =
            document.getElementById(
                "historyPreviewFilename"
            );


        const footerFilename =
            document.getElementById(
                "historyPreviewFooterName"
            );


        const previewFrame =
            document.getElementById(
                "historyPreviewFrame"
            );


        const previewPlaceholder =
            document.getElementById(
                "historyPreviewPlaceholder"
            );


        const closePreview =
            document.getElementById(
                "closeHistoryPreview"
            );


        const openNewTab =
            document.getElementById(
                "openHistoryDocumentNewTab"
            );


        let currentFile =
            "";


        document.addEventListener(
            "click",
            function (event) {

                const button =
                    event.target.closest(
                        ".detail-history-preview-button"
                    );


                if (!button) {

                    return;

                }


                currentFile =
                    button.dataset.file;


                previewTitle.textContent =
                    button.dataset.title;


                previewFilename.textContent =
                    currentFile;


                footerFilename.textContent =
                    currentFile;


                /*
                 * Frontend simulation.
                 *
                 * Nanti:
                 *
                 * previewFrame.src = fileUrl;
                 * previewFrame.style.display = "block";
                 * previewPlaceholder.style.display = "none";
                 */

                previewFrame.style.display =
                    "none";


                previewPlaceholder.style.display =
                    "flex";


                previewModal.classList.add(
                    "active"
                );


                document.body.style.overflow =
                    "hidden";

            }
        );


        function closePreviewModal() {

            previewModal.classList.remove(
                "active"
            );


            previewFrame.src =
                "";


            currentFile =
                "";


            document.body.style.overflow =
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
                    "Dokumen " +
                    currentFile +
                    " akan dibuka menggunakan URL file dari Laravel."
                );

            }
        );

    }
);