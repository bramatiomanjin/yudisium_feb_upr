(function () {

    "use strict";


    /* =========================================================
       YUDISIUM FEB UPR
       CENTRAL FRONTEND API SERVICE
    ========================================================= */

    const CONFIG = {

        /*
         * Ubah ke true saat Laravel API sudah siap.
         */
        backendConnected: false,

        /*
         * Jika frontend berada dalam Laravel:
         * /api
         *
         * Jika terpisah:
         * http://127.0.0.1:8000/api
         */
        baseUrl: "/api",

        timeout: 15000

    };


    /* =========================================================
       STATUS
    ========================================================= */

    const STATUS = {

        MENUNGGU_VERIFIKASI:
            "menunggu_verifikasi",

        PERLU_REVISI:
            "perlu_revisi",

        REVISI_DIKIRIM:
            "revisi_dikirim",

        TERVERIFIKASI:
            "terverifikasi",

        PEMBUATAN_SK:
            "pembuatan_sk",

        TTD_WAKIL_DEKAN:
            "ttd_wakil_dekan",

        TTD_DEKAN:
            "ttd_dekan",

        SK_SIAP_DIAMBIL:
            "sk_siap_diambil"

    };


    const statusMeta = {

        [STATUS.MENUNGGU_VERIFIKASI]: {
            label: "Menunggu Verifikasi",
            className: "pending"
        },

        [STATUS.PERLU_REVISI]: {
            label: "Perlu Revisi",
            className: "revision"
        },

        [STATUS.REVISI_DIKIRIM]: {
            label: "Revisi Dikirim",
            className: "revision"
        },

        [STATUS.TERVERIFIKASI]: {
            label: "Terverifikasi",
            className: "verified"
        },

        [STATUS.PEMBUATAN_SK]: {
            label: "Pembuatan SK",
            className: "process"
        },

        [STATUS.TTD_WAKIL_DEKAN]: {
            label: "TTD Wakil Dekan",
            className: "process"
        },

        [STATUS.TTD_DEKAN]: {
            label: "TTD Dekan",
            className: "process"
        },

        [STATUS.SK_SIAP_DIAMBIL]: {
            label: "SK Siap Diambil",
            className: "completed"
        }

    };


    /* =========================================================
       FIELD CONFIG
    ========================================================= */

    const fieldConfig = {

        nama_lengkap: {
            label: "Nama Lengkap",
            property: "name",
            type: "text"
        },

        email: {
            label: "Email",
            property: "email",
            type: "email"
        },

        no_whatsapp: {
            label: "Nomor WhatsApp",
            property: "whatsapp",
            type: "tel"
        },

        tahun_angkatan: {
            label: "Tahun Angkatan",
            property: "year",
            type: "text"
        },

        jalur_masuk: {
            label: "Jalur Masuk",
            property: "entryRoute",
            type: "text"
        },

        jurusan: {
            label: "Jurusan",
            property: "department",
            type: "text"
        },

        karya_tulis: {
            label: "Jenis Karya Tulis",
            property: "workType",
            type: "text"
        },

        judul_karya_tulis: {
            label: "Judul Skripsi / Artikel",
            property: "title",
            type: "textarea"
        },

        tanggal_ujian: {
            label: "Tanggal Ujian",
            property: "examDate",
            type: "text"
        },

        nilai_angka: {
            label: "Nilai Ujian",
            property: "examScore",
            type: "text"
        },

        nilai_huruf: {
            label: "Nilai Huruf",
            property: "letterGrade",
            type: "text"
        }

    };


    /* =========================================================
       API ERROR
    ========================================================= */

    class YudisiumApiError extends Error {

        constructor(
            message,
            status = 0,
            data = null
        ) {

            super(message);

            this.name =
                "YudisiumApiError";

            this.status =
                status;

            this.data =
                data;

        }

    }


    /* =========================================================
       NORMALIZER
    ========================================================= */

    function normalizeStatus(status) {

        const value =
            String(status || "")
                .trim()
                .toLowerCase()
                .replace(/\s+/g, "_");


        const aliases = {

            diajukan:
                STATUS.MENUNGGU_VERIFIKASI,

            verifikasi_admin:
                STATUS.MENUNGGU_VERIFIKASI,

            menunggu_verifikasi:
                STATUS.MENUNGGU_VERIFIKASI,

            perlu_revisi:
                STATUS.PERLU_REVISI,

            revisi_dikirim:
                STATUS.REVISI_DIKIRIM,

            terverifikasi:
                STATUS.TERVERIFIKASI,

            pembuatan_sk:
                STATUS.PEMBUATAN_SK,

            ttd_wakil_dekan:
                STATUS.TTD_WAKIL_DEKAN,

            ttd_dekan:
                STATUS.TTD_DEKAN,

            /*
             * Compatibility data lama.
             */
            sk_terbit:
                STATUS.SK_SIAP_DIAMBIL,

            sk_siap_diambil:
                STATUS.SK_SIAP_DIAMBIL

        };


        return (
            aliases[value] ||
            value
        );

    }


    function normalizeSubmission(data) {

        if (!data) {

            return null;

        }


        return {

            id:
                data.id ?? null,

            code:
                data.code ??
                data.kode_sk ??
                data.kode_pengajuan ??
                data.kode_sk_yudisium ??
                "",

            nim:
                data.nim ?? "",

            name:
                data.name ??
                data.nama ??
                data.mahasiswa ??
                data.nama_mahasiswa ??
                "",

            department:
                data.department ??
                data.jurusan ??
                "",

            submittedAt:
                data.submittedAt ??
                data.tanggal_pengajuan ??
                data.created_at ??
                "",

            updatedAt:
                data.updatedAt ??
                data.updated_at ??
                "",

            status:
                normalizeStatus(
                    data.status
                ),

            email:
                data.email ?? "",

            whatsapp:
                data.whatsapp ??
                data.no_whatsapp ??
                "",

            year:
                data.year ??
                data.tahun_angkatan ??
                "",

            entryRoute:
                data.entryRoute ??
                data.jalur_masuk ??
                "",

            workType:
                data.workType ??
                data.jenis_karya_tulis ??
                data.karya_tulis ??
                "",

            title:
                data.title ??
                data.judul_karya_tulis ??
                "",

            examDate:
                data.examDate ??
                data.tanggal_ujian ??
                "",

            examScore:
                data.examScore ??
                data.nilai_angka ??
                "",

            letterGrade:
                data.letterGrade ??
                data.nilai_huruf ??
                "",

            revisionCount:
                Number(
                    data.revisionCount ??
                    data.revision_count ??
                    0
                ),

            digitalSkAvailable:
                Boolean(
                    data.digitalSkAvailable ??
                    data.digital_sk_available ??
                    false
                ),

            digitalSkUrl:
                data.digitalSkUrl ??
                data.digital_sk_url ??
                null

        };

    }


    function normalizeSubmissionCollection(payload) {

        const source =
            Array.isArray(payload)
                ? payload
                : (
                    Array.isArray(payload?.data)
                        ? payload.data
                        : []
                );


        return source.map(
            normalizeSubmission
        );

    }


    function normalizeAction(action) {

        const value =
            String(action || "")
                .trim()
                .toUpperCase();


        const aliases = {

            VERIFY:
                "VERIFICATION",

            VERIFIKASI:
                "VERIFICATION",

            VERIFICATION:
                "VERIFICATION",

            REVISION:
                "REVISION",

            REVISI:
                "REVISION",

            REVIEW_REVISION:
                "REVISION",

            UPDATE_SK:
                "UPDATE_SK_STATUS",

            UPDATE_SK_STATUS:
                "UPDATE_SK_STATUS",

            SK_STATUS:
                "UPDATE_SK_STATUS"

        };


        return (
            aliases[value] ||
            value
        );

    }


    function normalizeHistoryItem(data) {

        if (!data) {

            return null;

        }


        return {

            id:
                data.id ?? null,

            adminUsername:
                data.adminUsername ??
                data.admin_username ??
                data.username ??
                "",

            adminName:
                data.adminName ??
                data.admin_name ??
                data.nama_admin ??
                "",

            adminRole:
                data.adminRole ??
                data.admin_role ??
                data.role ??
                "ADMIN",

            submissionId:
                data.submissionId ??
                data.pengajuan_id ??
                data.submission_id ??
                null,

            submissionCode:
                data.submissionCode ??
                data.kode_pengajuan ??
                data.kode_sk ??
                "",

            nim:
                data.nim ?? "",

            studentName:
                data.studentName ??
                data.mahasiswa ??
                data.nama_mahasiswa ??
                "",

            department:
                data.department ??
                data.jurusan ??
                "",

            action:
                normalizeAction(
                    data.action ??
                    data.jenis_aktivitas
                ),

            actionLabel:
                data.actionLabel ??
                data.action_label ??
                data.label ??
                "",

            previousStatus:
                normalizeStatus(
                    data.previousStatus ??
                    data.old_status ??
                    data.status_sebelumnya
                ),

            newStatus:
                normalizeStatus(
                    data.newStatus ??
                    data.new_status ??
                    data.status_baru
                ),

            note:
                data.note ??
                data.catatan ??
                null,

            createdAt:
                data.createdAt ??
                data.created_at ??
                "",

            timestamp:
                data.timestamp ??
                null

        };

    }


    function normalizeHistoryCollection(payload) {

        const source =
            Array.isArray(payload)
                ? payload
                : (
                    Array.isArray(payload?.data)
                        ? payload.data
                        : []
                );


        return source
            .map(
                normalizeHistoryItem
            )
            .filter(Boolean);

    }


    /* =========================================================
       HTTP REQUEST
    ========================================================= */

    async function request(
        endpoint,
        options = {}
    ) {

        if (
            !CONFIG.backendConnected
        ) {

            throw new YudisiumApiError(
                "Backend Yudisium belum terhubung.",
                503
            );

        }


        const controller =
            new AbortController();


        const timeout =
            setTimeout(
                function () {

                    controller.abort();

                },
                CONFIG.timeout
            );


        try {

            const isFormData =
                options.body instanceof
                FormData;


            const response =
                await fetch(
                    CONFIG.baseUrl +
                    endpoint,
                    {

                        method:
                            options.method ||
                            "GET",

                        credentials:
                            "same-origin",

                        headers: {

                            "Accept":
                                "application/json",

                            ...(
                                isFormData
                                    ? {}
                                    : {
                                        "Content-Type":
                                            "application/json"
                                    }
                            ),

                            ...(
                                options.headers ||
                                {}
                            )

                        },

                        body:
                            options.body
                                ? (
                                    isFormData
                                        ? options.body
                                        : JSON.stringify(
                                            options.body
                                        )
                                )
                                : undefined,

                        signal:
                            controller.signal

                    }
                );


            let payload =
                null;


            const contentType =
                response.headers.get(
                    "content-type"
                ) || "";


            if (
                contentType.includes(
                    "application/json"
                )
            ) {

                payload =
                    await response.json();

            }


            if (
                !response.ok
            ) {

                throw new YudisiumApiError(

                    payload?.message ||
                    "Permintaan ke server gagal.",

                    response.status,

                    payload

                );

            }


            return payload;

        } catch (error) {

            if (
                error.name ===
                "AbortError"
            ) {

                throw new YudisiumApiError(
                    "Server tidak merespons dalam batas waktu.",
                    408
                );

            }


            if (
                error instanceof
                YudisiumApiError
            ) {

                throw error;

            }


            throw new YudisiumApiError(
                "Tidak dapat terhubung ke server.",
                0,
                error
            );

        } finally {

            clearTimeout(
                timeout
            );

        }

    }


    /* =========================================================
       SUBMISSIONS
    ========================================================= */

    async function getSubmissions(
        params = {}
    ) {

        if (
            !CONFIG.backendConnected
        ) {

            return [];

        }


        const query =
            new URLSearchParams();


        Object.entries(
            params
        )
            .forEach(
                function (
                    [key, value]
                ) {

                    if (
                        value !== undefined &&
                        value !== null &&
                        value !== ""
                    ) {

                        query.set(
                            key,
                            value
                        );

                    }

                }
            );


        const suffix =
            query.toString()
                ? "?" +
                    query.toString()
                : "";


        const payload =
            await request(
                "/submissions" +
                suffix
            );


        return normalizeSubmissionCollection(
            payload
        );

    }


    async function getSubmission(id) {

        if (
            !CONFIG.backendConnected
        ) {

            return null;

        }


        const payload =
            await request(
                "/submissions/" +
                encodeURIComponent(id)
            );


        return normalizeSubmission(
            payload?.data ||
            payload
        );

    }


    async function submitApplication(
        formData
    ) {

        const payload =
            await request(
                "/submissions",
                {

                    method:
                        "POST",

                    body:
                        formData

                }
            );


        const raw =
            payload?.data ||
            payload;


        const submission =
            normalizeSubmission(
                raw
            );


        /*
         * Backend minimal harus mengembalikan:
         *
         * id
         * nim
         * kode_sk / code
         */
        return {

            ...submission,

            message:
                payload?.message ||
                "Pengajuan berhasil dikirim."

        };

    }


    async function findSubmission(
        nim,
        code
    ) {

        if (
            !CONFIG.backendConnected
        ) {

            return null;

        }


        const payload =
            await request(
                "/tracking",
                {

                    method:
                        "POST",

                    body: {

                        nim:
                            String(
                                nim || ""
                            )
                                .trim(),

                        kode_sk:
                            String(
                                code || ""
                            )
                                .trim()
                                .toUpperCase()

                    }

                }
            );


        return normalizeSubmission(
            payload?.data ||
            payload
        );

    }


    /* =========================================================
       DOCUMENTS
    ========================================================= */

    async function getDocuments(
        submissionId
    ) {

        if (
            !CONFIG.backendConnected
        ) {

            return [];

        }


        const payload =
            await request(
                "/submissions/" +
                encodeURIComponent(
                    submissionId
                ) +
                "/documents"
            );


        if (
            Array.isArray(payload)
        ) {

            return payload;

        }


        return (
            payload?.data ||
            []
        );

    }


    /* =========================================================
       VERIFICATION / REVISION
    ========================================================= */

    async function getVerificationResult(
        submissionId
    ) {

        if (
            !CONFIG.backendConnected
        ) {

            return null;

        }


        const payload =
            await request(
                "/submissions/" +
                encodeURIComponent(
                    submissionId
                ) +
                "/verification"
            );


        return (
            payload?.data ||
            payload ||
            null
        );

    }


    async function verifySubmission(
        submissionId,
        data
    ) {

        return request(
            "/submissions/" +
            encodeURIComponent(
                submissionId
            ) +
            "/verify",
            {

                method:
                    "POST",

                body:
                    data

            }
        );

    }


    async function getRevisionSubmission(
        submissionId
    ) {

        if (
            !CONFIG.backendConnected
        ) {

            return null;

        }


        const payload =
            await request(
                "/submissions/" +
                encodeURIComponent(
                    submissionId
                ) +
                "/revision"
            );


        return (
            payload?.data ||
            payload ||
            null
        );

    }


    async function submitRevision(
        submissionId,
        formData
    ) {

        return request(
            "/submissions/" +
            encodeURIComponent(
                submissionId
            ) +
            "/revision",
            {

                method:
                    "POST",

                body:
                    formData

            }
        );

    }


    async function reviewRevision(
        submissionId,
        data
    ) {

        return request(
            "/submissions/" +
            encodeURIComponent(
                submissionId
            ) +
            "/revision/review",
            {

                method:
                    "POST",

                body:
                    data

            }
        );

    }


    /* =========================================================
       SK
    ========================================================= */

    async function updateSkStatus(
        submissionId,
        status
    ) {

        return request(
            "/submissions/" +
            encodeURIComponent(
                submissionId
            ) +
            "/sk-status",
            {

                method:
                    "PATCH",

                body: {

                    status:
                        normalizeStatus(
                            status
                        )

                }

            }
        );

    }


    async function exportExcel(
        filters = {}
    ) {

        if (
            !CONFIG.backendConnected
        ) {

            throw new YudisiumApiError(
                "Backend belum terhubung. Export Excel belum tersedia.",
                503
            );

        }


        const query =
            new URLSearchParams();


        Object.entries(
            filters
        )
            .forEach(
                function (
                    [key, value]
                ) {

                    if (
                        value !== undefined &&
                        value !== null &&
                        value !== ""
                    ) {

                        query.set(
                            key,
                            value
                        );

                    }

                }
            );


        const suffix =
            query.toString()
                ? "?" +
                    query.toString()
                : "";


        const response =
            await fetch(
                CONFIG.baseUrl +
                "/submissions/export-excel" +
                suffix,
                {
                    credentials:
                        "same-origin"
                }
            );


        if (
            !response.ok
        ) {

            throw new YudisiumApiError(
                "Export Excel gagal.",
                response.status
            );

        }


        return response.blob();

    }


    /* =========================================================
       HISTORY
    ========================================================= */

    async function getHistory(
        params = {}
    ) {

        if (
            !CONFIG.backendConnected
        ) {

            return [];

        }


        const query =
            new URLSearchParams();


        Object.entries(
            params
        )
            .forEach(
                function (
                    [key, value]
                ) {

                    if (
                        value !== undefined &&
                        value !== null &&
                        value !== ""
                    ) {

                        query.set(
                            key,
                            value
                        );

                    }

                }
            );


        const suffix =
            query.toString()
                ? "?" +
                    query.toString()
                : "";


        const payload =
            await request(
                "/history" +
                suffix
            );


        return normalizeHistoryCollection(
            payload
        );

    }


    async function getHistoryDetail(id) {

        if (
            !CONFIG.backendConnected
        ) {

            return null;

        }


        const payload =
            await request(
                "/history/" +
                encodeURIComponent(id)
            );


        return normalizeHistoryItem(
            payload?.data ||
            payload
        );

    }


    /* =========================================================
       DISPLAY HELPERS
    ========================================================= */

    function getStatusMeta(status) {

        const normalized =
            normalizeStatus(
                status
            );


        return (
            statusMeta[
                normalized
            ] ||
            {
                label:
                    normalized
                        ? normalized
                            .replace(
                                /_/g,
                                " "
                            )
                        : "-",

                className:
                    "default"
            }
        );

    }


    function getStatusLabel(status) {

        return getStatusMeta(
            status
        ).label;

    }


    function getInitials(name) {

        return String(
            name || ""
        )
            .trim()
            .split(/\s+/)
            .slice(0, 2)
            .map(
                function (word) {

                    return word
                        .charAt(0)
                        .toUpperCase();

                }
            )
            .join("");

    }


    function getFieldConfig(key) {

        return (
            fieldConfig[key] ||
            {
                label:
                    key || "-",

                property:
                    key || "",

                type:
                    "text"
            }
        );

    }


    function getFieldValue(
        submission,
        key
    ) {

        if (!submission) {

            return "";

        }


        const config =
            getFieldConfig(
                key
            );


        return (
            submission[
                config.property
            ] ??
            ""
        );

    }


    function getRoute(submission) {

        if (!submission) {

            return "pengajuan.html";

        }


        switch (
            normalizeStatus(
                submission.status
            )
        ) {

            case STATUS.MENUNGGU_VERIFIKASI:

                return (
                    "verifikasi.html?id=" +
                    submission.id
                );


            case STATUS.PERLU_REVISI:

                return (
                    "detail_pengajuan.html?id=" +
                    submission.id
                );


            case STATUS.REVISI_DIKIRIM:

                return (
                    "review_revisi.html?id=" +
                    submission.id
                );


            case STATUS.TERVERIFIKASI:

            case STATUS.PEMBUATAN_SK:

            case STATUS.TTD_WAKIL_DEKAN:

            case STATUS.TTD_DEKAN:

            case STATUS.SK_SIAP_DIAMBIL:

                return (
                    "proses_sk.html?id=" +
                    submission.id
                );


            default:

                return (
                    "detail_pengajuan.html?id=" +
                    submission.id
                );

        }

    }


    /* =========================================================
       EXPORT PUBLIC API
    ========================================================= */

    window.YudisiumAPI = {

        config:
            CONFIG,

        STATUS:
            STATUS,

        statusMeta:
            statusMeta,

        fieldConfig:
            fieldConfig,

        YudisiumApiError:
            YudisiumApiError,

        normalizeStatus:
            normalizeStatus,

        normalizeSubmission:
            normalizeSubmission,

        normalizeAction:
            normalizeAction,

        normalizeHistoryItem:
            normalizeHistoryItem,

        getStatusMeta:
            getStatusMeta,

        getStatusLabel:
            getStatusLabel,

        getInitials:
            getInitials,

        getFieldConfig:
            getFieldConfig,

        getFieldValue:
            getFieldValue,

        getRoute:
            getRoute,

        getSubmissions:
            getSubmissions,

        getSubmission:
            getSubmission,

        submitApplication:
            submitApplication,

        findSubmission:
            findSubmission,

        getDocuments:
            getDocuments,

        getVerificationResult:
            getVerificationResult,

        verifySubmission:
            verifySubmission,

        getRevisionSubmission:
            getRevisionSubmission,

        submitRevision:
            submitRevision,

        reviewRevision:
            reviewRevision,

        updateSkStatus:
            updateSkStatus,

        exportExcel:
            exportExcel,

        getHistory:
            getHistory,

        getHistoryDetail:
            getHistoryDetail

    };


    console.log(
        "YudisiumAPI siap:",
        CONFIG.backendConnected
            ? "BACKEND CONNECTED"
            : "EMPTY / API-READY MODE"
    );

})();