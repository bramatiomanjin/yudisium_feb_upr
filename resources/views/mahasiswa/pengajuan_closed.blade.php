<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Ditutup - FEB UPR</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student_theme.css') }}">
    <style>
        .closed-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f6f4;
            padding: 20px;
        }
        .closed-card {
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            text-align: center;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border-top: 5px solid #c93b3b;
        }
        .closed-icon {
            font-size: 3rem;
            color: #c93b3b;
            margin-bottom: 20px;
        }
        .closed-title {
            color: #15382b;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        .closed-desc {
            color: #4a5c53;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .btn-tracking {
            display: inline-block;
            background: #117d5b;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-tracking:hover {
            background: #0a684c;
        }
    </style>
</head>
<body>
    <main class="closed-container">
        <div class="closed-card">
            <div class="closed-icon">🔒</div>
            <h1 class="closed-title">Pendaftaran Yudisium Ditutup</h1>
            <p class="closed-desc">
                Mohon maaf, saat ini form pengajuan yudisium sedang dinonaktifkan oleh administrator. Silakan periksa kembali nanti atau hubungi pihak fakultas untuk informasi lebih lanjut.
            </p>
            <a href="/tracking" class="btn-tracking">Cek Status Pengajuan Saya</a>
        </div>
    </main>
</body>
</html>
