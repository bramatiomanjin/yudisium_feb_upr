(function () {
    "use strict";

    const SUBMISSION_STORAGE_KEY =
        "yudisium_submission_statuses";

    const HISTORY_STORAGE_KEY =
        "yudisium_admin_activity";

    const submissions = [
        {
            id: 1,
            code: "YDS-2026-0001",
            name: "Andi Saputra",
            nim: "2301110001",
            department: "Manajemen",
            submittedAt: "02 September 2026",
            status: "menunggu verifikasi",
            email: "andi.saputra@example.com",
            whatsapp: "081234567890",
            year: "2023",
            entryRoute: "Reguler",
            workType: "Skripsi",
            title: "Analisis Sistem Informasi Akademik Fakultas Ekonomi dan Bisnis",
            examDate: "28 Agustus 2026",
            examScore: "80,00",
            letterGrade: "A"
        },
        {
            id: 2,
            code: "YDS-2026-0002",
            name: "Citra Lestari",
            nim: "2301120002",
            department: "Akuntansi",
            submittedAt: "02 September 2026",
            status: "perlu revisi",
            email: "citra.lestari@example.com",
            whatsapp: "081298761234",
            year: "2023",
            entryRoute: "Reguler",
            workType: "Skripsi",
            title: "Analisis Penerapan Akuntansi Digital pada UMKM Kota Palangka Raya",
            examDate: "27 Agustus 2026",
            examScore: "78,50",
            letterGrade: "A"
        },
        {
            id: 3,
            code: "YDS-2026-0003",
            name: "Deni Pratama",
            nim: "2301130003",
            department: "Ekonomi Pembangunan",
            submittedAt: "01 September 2026",
            status: "terverifikasi",
            email: "deni.pratama@example.com",
            whatsapp: "081377788899",
            year: "2023",
            entryRoute: "Reguler",
            workType: "Skripsi",
            title: "Pengaruh Investasi Daerah terhadap Pertumbuhan Ekonomi Kalimantan Tengah",
            examDate: "26 Agustus 2026",
            examScore: "82,00",
            letterGrade: "A"
        },
        {
            id: 4,
            code: "YDS-2026-0004",
            name: "Eva Natalia",
            nim: "2301110004",
            department: "Manajemen",
            submittedAt: "01 September 2026",
            status: "ttd wakil dekan",
            email: "eva.natalia@example.com",
            whatsapp: "082155566677",
            year: "2023",
            entryRoute: "Reguler",
            workType: "Skripsi",
            title: "Strategi Pemasaran Digital pada Usaha Kuliner Lokal",
            examDate: "25 Agustus 2026",
            examScore: "81,00",
            letterGrade: "A"
        },
        {
            id: 5,
            code: "YDS-2026-0005",
            name: "Fajar Rahman",
            nim: "2301120005",
            department: "Akuntansi",
            submittedAt: "31 Agustus 2026",
            status: "revisi dikirim",
            email: "fajar.rahman@example.com",
            whatsapp: "081344455566",
            year: "2023",
            entryRoute: "Reguler",
            workType: "Skripsi",
            title: "Evaluasi Sistem Informasi Akuntansi Persediaan pada Perusahaan Dagang",
            examDate: "24 Agustus 2026",
            examScore: "79,50",
            letterGrade: "A"
        },
        {
            id: 6,
            code: "YDS-2026-0006",
            name: "Grace Amelia",
            nim: "2301110006",
            department: "Manajemen",
            submittedAt: "31 Agustus 2026",
            status: "pembuatan sk",
            email: "grace.amelia@example.com",
            whatsapp: "082244455599",
            year: "2023",
            entryRoute: "Reguler",
            workType: "Skripsi",
            title: "Pengaruh Kualitas Pelayanan terhadap Loyalitas Pelanggan",
            examDate: "23 Agustus 2026",
            examScore: "83,00",
            letterGrade: "A"
        },
        {
            id: 7,
            code: "YDS-2026-0007",
            name: "Hendra Wijaya",
            nim: "2301130007",
            department: "Ekonomi Pembangunan",
            submittedAt: "30 Agustus 2026",
            status: "ttd dekan",
            email: "hendra.wijaya@example.com",
            whatsapp: "081252527777",
            year: "2023",
            entryRoute: "Reguler",
            workType: "Skripsi",
            title: "Analisis Ketimpangan Pendapatan Antarwilayah di Kalimantan Tengah",
            examDate: "22 Agustus 2026",
            examScore: "80,50",
            letterGrade: "A"
        },
        {
            id: 8,
            code: "YDS-2026-0008",
            name: "Intan Permata",
            nim: "2301120008",
            department: "Akuntansi",
            submittedAt: "30 Agustus 2026",
            status: "sk terbit",
            email: "intan.permata@example.com",
            whatsapp: "082133344488",
            year: "2023",
            entryRoute: "Reguler",
            workType: "Skripsi",
            title: "Analisis Transparansi Pelaporan Keuangan Organisasi Nirlaba",
            examDate: "21 Agustus 2026",
            examScore: "84,00",
            letterGrade: "A"
        }
    ];

    const statusMeta = {
        "menunggu verifikasi": {
            label: "Menunggu Verifikasi",
            className: "pending"
        },
        "perlu revisi": {
            label: "Perlu Revisi",
            className: "revision"
        },
        "revisi dikirim": {
            label: "Revisi Dikirim",
            className: "revision-submitted"
        },
        "terverifikasi": {
            label: "Terverifikasi",
            className: "verified"
        },
        "pembuatan sk": {
            label: "Pembuatan SK",
            className: "process"
        },
        "ttd wakil dekan": {
            label: "TTD Wakil Dekan",
            className: "process"
        },
        "ttd dekan": {
            label: "TTD Dekan",
            className: "process"
        },
        "sk terbit": {
            label: "SK Terbit",
            className: "completed"
        }
    };

    function readObject(key) {
        try {
            const value = JSON.parse(
                localStorage.getItem(key) || "{}"
            );

            return value && typeof value === "object"
                ? value
                : {};
        } catch (error) {
            console.error("Gagal membaca penyimpanan", error);
            return {};
        }
    }

    function getSubmission(id) {
        const requestedId = Number(id) || 1;
        const base = submissions.find(function (item) {
            return item.id === requestedId;
        });

        if (!base) {
            return null;
        }

        const savedStatuses = readObject(
            SUBMISSION_STORAGE_KEY
        );

        return Object.assign({}, base, {
            status: savedStatuses[String(base.id)] || base.status
        });
    }

    function setSubmissionStatus(id, status) {
        const savedStatuses = readObject(
            SUBMISSION_STORAGE_KEY
        );

        savedStatuses[String(id)] = status;

        localStorage.setItem(
            SUBMISSION_STORAGE_KEY,
            JSON.stringify(savedStatuses)
        );
    }

    function getRoute(submission, includeDetail) {
        const id = submission.id;
        const status = submission.status;

        if (includeDetail) {
            return "detail_pengajuan.html?id=" + id;
        }

        if (status === "menunggu verifikasi") {
            return "verifikasi.html?id=" + id;
        }

        if (status === "revisi dikirim") {
            return "review_revisi.html?id=" + id;
        }

        if (
            status === "terverifikasi" ||
            status === "pembuatan sk" ||
            status === "ttd wakil dekan" ||
            status === "ttd dekan" ||
            status === "sk terbit"
        ) {
            return "proses_sk.html?id=" + id;
        }

        return "detail_pengajuan.html?id=" + id;
    }

    function getAction(submission) {
        const actions = {
            "menunggu verifikasi": {
                title: "Verifikasi Pengajuan",
                description: "Periksa seluruh data dan dokumen sebelum menentukan hasil verifikasi.",
                label: "Mulai Verifikasi",
                href: getRoute(submission, false),
                disabled: false
            },
            "perlu revisi": {
                title: "Menunggu Revisi Mahasiswa",
                description: "Mahasiswa belum mengirimkan perbaikan. Detail pengajuan tetap dapat diperiksa.",
                label: "Menunggu Revisi",
                href: "#",
                disabled: true
            },
            "revisi dikirim": {
                title: "Review Revisi Mahasiswa",
                description: "Periksa perbaikan yang telah dikirim mahasiswa.",
                label: "Review Revisi",
                href: getRoute(submission, false),
                disabled: false
            },
            "terverifikasi": {
                title: "Proses SK Yudisium",
                description: "Pengajuan telah terverifikasi dan siap masuk ke tahapan penerbitan SK.",
                label: "Mulai Proses SK",
                href: getRoute(submission, false),
                disabled: false
            },
            "sk terbit": {
                title: "SK Yudisium Telah Terbit",
                description: "Lihat ringkasan tahapan penerbitan SK mahasiswa.",
                label: "Lihat Status SK",
                href: getRoute(submission, false),
                disabled: false
            }
        };

        return actions[submission.status] || {
            title: "Lanjutkan Proses SK",
            description: "Lanjutkan tahapan penerbitan SK sesuai status terakhir.",
            label: "Lanjutkan Proses SK",
            href: getRoute(submission, false),
            disabled: false
        };
    }

    function getInitials(name) {
        return String(name)
            .split(" ")
            .filter(Boolean)
            .slice(0, 2)
            .map(function (part) {
                return part.charAt(0).toUpperCase();
            })
            .join("");
    }

    function formatHistoryDate(date) {
        return new Intl.DateTimeFormat("id-ID", {
            day: "numeric",
            month: "short",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit"
        }).format(date).replace(" pukul ", ", ");
    }

    function saveActivity(details) {
        let history = [];

        try {
            const parsed = JSON.parse(
                localStorage.getItem(HISTORY_STORAGE_KEY) || "[]"
            );
            history = Array.isArray(parsed) ? parsed : [];
        } catch (error) {
            console.error("Gagal membaca history", error);
        }

        const now = new Date();
        const submission = getSubmission(details.pengajuan_id);

        history.unshift(Object.assign({
            id: now.getTime(),
            admin_username: sessionStorage.getItem("admin_username") || "admin",
            admin_role: sessionStorage.getItem("admin_role") || "ADMIN",
            kode_pengajuan: submission ? submission.code : "-",
            nim: submission ? submission.nim : "-",
            mahasiswa: submission ? submission.name : "-",
            jurusan: submission ? submission.department : "-",
            created_at: formatHistoryDate(now),
            timestamp: now.getTime()
        }, details));

        localStorage.setItem(
            HISTORY_STORAGE_KEY,
            JSON.stringify(history)
        );
    }

    window.YudisiumMockDB = {
        submissions: submissions,
        statusMeta: statusMeta,
        getSubmission: getSubmission,
        setSubmissionStatus: setSubmissionStatus,
        getRoute: getRoute,
        getAction: getAction,
        getInitials: getInitials,
        saveActivity: saveActivity
    };
})();
