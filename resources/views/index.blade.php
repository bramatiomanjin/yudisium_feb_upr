<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pendaftaran Yudisium FEB UPR</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('css/student_theme.css') }}">
<link rel="stylesheet" href="{{ asset('css/student_pengajuan.css') }}">
</head>

<body>

    <main class="container">

        <!-- HEADER -->
        <header class="page-header student-page-header">
            <div class="student-header-content">
                <div class="student-header-main">
                    <span class="student-system-label">Sistem Yudisium FEB UPR</span>
                    <h1>Pendaftaran Yudisium</h1>
                    <p>Lengkapi data secara bertahap. Pastikan seluruh informasi sesuai dokumen resmi sebelum dikirim.</p>
                </div>

                <a class="student-status-link" href="/tracking">
                    Sudah Mengajukan? Cek Status
                </a>
            </div>
        </header>

        @if(!empty($pengumumanImages) && count($pengumumanImages) > 0)
        <style>
            .student-announcement-bar {
                background: #fff;
                border: 1px solid #e1e8e4;
                border-radius: 12px;
                margin-bottom: 20px;
                padding: 16px 24px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            }
            .pengumuman-modal-overlay {
                position: fixed;
                inset: 0;
                z-index: 99999;
                display: none;
                align-items: center;
                justify-content: center;
                padding: 16px;
                background: rgba(5,27,19,0.88);
                backdrop-filter: blur(4px);
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }
            .pengumuman-card-wrapper {
                position: relative;
                background: #ffffff;
                border-radius: 16px;
                width: 100%;
                max-width: 620px;
                max-height: 94vh;
                max-height: 94dvh;
                margin: auto;
                display: flex;
                flex-direction: column;
                overflow: hidden;
                box-shadow: 0 25px 60px rgba(0,0,0,0.45);
                border: 1px solid rgba(255,255,255,0.2);
                animation: pengumumanModalFadeIn 0.25s ease-out;
            }
            @keyframes pengumumanModalFadeIn {
                from { opacity: 0; transform: scale(0.96) translateY(10px); }
                to { opacity: 1; transform: scale(1) translateY(0); }
            }
            @keyframes pengumumanShake {
                0%, 100% { transform: translateX(0); }
                20%, 60% { transform: translateX(-6px); }
                40%, 80% { transform: translateX(6px); }
            }
            .pengumuman-modal-header {
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 10px 16px;
                background: #fdfdfd;
                border-bottom: 1px solid #eef2ef;
                gap: 12px;
            }
            .pengumuman-badge-title {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 0.92rem;
                font-weight: 800;
                color: #064e3b;
            }
            .pengumuman-counter-badge {
                background: #ecfdf5;
                color: #047857;
                border: 1px solid #a7f3d0;
                font-size: 0.78rem;
                font-weight: 700;
                padding: 3px 10px;
                border-radius: 999px;
            }
            .pengumuman-close-btn {
                background: #f3f4f6;
                border: none;
                width: 30px;
                height: 30px;
                border-radius: 50%;
                font-size: 1.05rem;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #4b5563;
                transition: all 0.2s;
            }
            .pengumuman-close-btn:hover {
                background: #e5e7eb;
                color: #111827;
            }
            .pengumuman-image-area {
                position: relative;
                flex: 1 1 auto;
                min-height: 0;
                width: 100%;
                background: #092017;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                padding: 6px;
                box-sizing: border-box;
            }
            .pengumuman-image-area img {
                max-width: 100%;
                max-height: 100%;
                width: auto;
                height: auto;
                object-fit: contain;
                display: block;
                margin: 0 auto;
                user-select: none;
            }
            .pengumuman-nav-btn {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                background: rgba(0, 0, 0, 0.65);
                color: white;
                border: 1.5px solid rgba(255, 255, 255, 0.4);
                width: 42px;
                height: 42px;
                border-radius: 50%;
                font-size: 1.3rem;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 14px rgba(0,0,0,0.35);
                z-index: 10;
                transition: all 0.2s;
            }
            .pengumuman-nav-btn:hover:not(:disabled) {
                background: rgba(0, 0, 0, 0.9);
                transform: translateY(-50%) scale(1.06);
            }
            .pengumuman-nav-btn:disabled {
                opacity: 0.25;
                cursor: not-allowed;
            }
            .pengumuman-nav-left { left: 14px; }
            .pengumuman-nav-right { right: 14px; }
            .pengumuman-modal-footer {
                flex-shrink: 0;
                padding: 10px 14px;
                background: #ffffff;
                border-top: 1px solid #eef2ef;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            .pengumuman-checkbox-card {
                display: flex;
                align-items: flex-start;
                gap: 8px;
                background: #f8faf9;
                border: 1.5px solid #d1fae5;
                padding: 8px 12px;
                border-radius: 8px;
                cursor: pointer;
                transition: border-color 0.2s, background 0.2s;
            }
            .pengumuman-checkbox-card.checked {
                background: #ecfdf5;
                border-color: #059669;
            }
            .pengumuman-checkbox-card input[type="checkbox"] {
                width: 18px;
                height: 18px;
                accent-color: #047857;
                cursor: pointer;
                margin-top: 1px;
                flex-shrink: 0;
            }
            .pengumuman-checkbox-text {
                font-size: 0.84rem;
                color: #1f2937;
                line-height: 1.35;
                font-weight: 600;
            }
            .pengumuman-footer-actions {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
            }
            .pengumuman-buttons-group {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-left: auto;
            }
            .pengumuman-buttons-group .pengumuman-prev-btn {
                order: 1;
            }
            .pengumuman-buttons-group .pengumuman-action-btn {
                order: 2;
            }
            .pengumuman-dots {
                display: flex;
                gap: 6px;
                align-items: center;
            }
            .pengumuman-dot {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: #d1d5db;
                transition: all 0.2s;
                cursor: pointer;
            }
            .pengumuman-dot.active {
                width: 20px;
                border-radius: 999px;
                background: #047857;
            }
            .pengumuman-action-btn {
                min-height: 38px;
                padding: 8px 18px;
                background: #047857;
                color: white;
                border: none;
                border-radius: 8px;
                font-weight: 700;
                font-size: 0.88rem;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                transition: all 0.2s;
            }
            .pengumuman-action-btn:hover:not(:disabled) {
                background: #065f46;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(4, 120, 87, 0.25);
            }
            .pengumuman-action-btn:disabled {
                background: #9ca3af;
                cursor: not-allowed;
                opacity: 0.7;
                box-shadow: none;
                transform: none;
            }
            .pengumuman-prev-btn {
                min-height: 36px;
                padding: 8px 14px;
                background: #f3f4f6;
                color: #374151;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                font-weight: 600;
                font-size: 0.82rem;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s;
            }
            .pengumuman-prev-btn:hover {
                background: #e5e7eb;
            }
            @media (max-width: 600px) {
                .student-announcement-bar {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 16px;
                }
                .student-announcement-bar button {
                    width: 100%;
                    justify-content: center;
                }
                .pengumuman-modal-overlay {
                    padding: 6px 8px;
                }
                .pengumuman-card-wrapper {
                    max-height: 94vh;
                    max-height: 94dvh;
                    border-radius: 12px;
                }
                /* Sembunyikan tombol panah kiri-kanan di HP karena diganti swipe gesture */
                .pengumuman-nav-btn {
                    display: none !important;
                }
                .pengumuman-image-area {
                    touch-action: pan-y;
                    cursor: grab;
                    padding: 4px;
                }
                .pengumuman-image-area img {
                    pointer-events: none;
                }
                .pengumuman-modal-header {
                    padding: 8px 12px;
                }
                .pengumuman-badge-title {
                    font-size: 0.84rem;
                }
                .pengumuman-counter-badge {
                    font-size: 0.72rem;
                    padding: 2px 8px;
                }
                .pengumuman-modal-footer {
                    padding: 8px 10px;
                    gap: 6px;
                }
                .pengumuman-checkbox-card {
                    padding: 6px 10px;
                }
                .pengumuman-checkbox-text {
                    font-size: 0.78rem;
                }
                /* Susun tombol di HP: Tombol utama di ATAS, Tombol Kembali di BAWAH */
                .pengumuman-footer-actions {
                    flex-direction: column;
                    gap: 6px;
                    width: 100%;
                }
                .pengumuman-buttons-group {
                    display: flex;
                    flex-direction: column;
                    gap: 6px;
                    width: 100%;
                    margin-left: 0;
                }
                .pengumuman-buttons-group .pengumuman-action-btn {
                    order: 1; /* Paling atas */
                    width: 100%;
                    margin-left: 0;
                    text-align: center;
                    min-height: 38px;
                    font-size: 0.84rem;
                    padding: 8px 12px;
                }
                .pengumuman-buttons-group .pengumuman-prev-btn {
                    order: 2; /* Di bawah tombol utama */
                    width: 100%;
                    text-align: center;
                    background: #f9fafb;
                    border: 1px solid #e5e7eb;
                    color: #4b5563;
                    min-height: 34px;
                    font-size: 0.8rem;
                    padding: 6px 12px;
                }
                .pengumuman-dots {
                    order: 3; /* Indikator dots di paling bawah */
                    justify-content: center;
                    margin-top: 2px;
                }
            }
        </style>

        <div class="student-announcement-bar">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 1.5rem;">📢</span>
                <div>
                    <h3 style="margin: 0; font-size: 1rem; color: #15382b;">Pengumuman Penting</h3>
                    <p style="margin: 4px 0 0; font-size: 0.9rem; color: #4a5c53;">Ketentuan jadwal pendaftaran dan penerbitan SK Yudisium.</p>
                </div>
            </div>
            <button type="button" class="btn btn-primary" onclick="openPengumumanModal(false)" style="padding: 10px 20px; white-space: nowrap;">Lihat Pengumuman</button>
        </div>

        <!-- Pengumuman Modal -->
        <div id="pengumumanModal" class="pengumuman-modal-overlay">
            <div class="pengumuman-card-wrapper" onclick="event.stopPropagation()">
                <!-- Header -->
                <div class="pengumuman-modal-header">
                    <div class="pengumuman-badge-title">
                        <span>📢</span>
                        <span>Pengumuman Yudisium</span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span id="pengumumanCounter" class="pengumuman-counter-badge" style="{{ count($pengumumanImages) > 1 ? '' : 'display: none;' }}">
                            Poster 1 dari {{ count($pengumumanImages) }}
                        </span>
                        <button type="button" class="pengumuman-close-btn" id="closePengumumanBtn" onclick="closePengumumanModal()" style="display: none;" title="Tutup Pengumuman">✕</button>
                    </div>
                </div>

                <!-- Image Area -->
                <div class="pengumuman-image-area" id="pengumumanImageArea">
                    @if(count($pengumumanImages) > 1)
                    <button type="button" id="pengumumanPrevNavBtn" class="pengumuman-nav-btn pengumuman-nav-left" onclick="prevPengumuman()" aria-label="Previous">❮</button>
                    <button type="button" id="pengumumanNextNavBtn" class="pengumuman-nav-btn pengumuman-nav-right" onclick="nextPengumuman()" aria-label="Next">❯</button>
                    @endif

                    <img id="pengumumanImgElement" src="{{ asset($pengumumanImages[0]) }}" alt="Pengumuman Yudisium">
                </div>

                <!-- Footer with Checkbox & Actions -->
                <div class="pengumuman-modal-footer">
                    <label class="pengumuman-checkbox-card" id="pengumumanCheckboxCard">
                        <input type="checkbox" id="pengumumanCheckbox" onchange="handleCheckboxChange(this.checked)">
                        <span class="pengumuman-checkbox-text" id="pengumumanCheckboxText">
                            Saya telah membaca dan memahami informasi pada poster pengumuman ini.
                        </span>
                    </label>

                    <div class="pengumuman-footer-actions">
                        @if(count($pengumumanImages) > 1)
                        <div class="pengumuman-dots">
                            @foreach($pengumumanImages as $index => $img)
                            <div class="pengumuman-dot {{ $index === 0 ? 'active' : '' }}" id="pengumumanDot{{ $index }}" onclick="goToPengumuman({{ $index }})"></div>
                            @endforeach
                        </div>
                        @endif

                        <div class="pengumuman-buttons-group">
                            <button type="button" id="pengumumanActionBtn" class="pengumuman-action-btn" onclick="handlePengumumanAction()" disabled>
                                {{ count($pengumumanImages) > 1 ? 'Lanjut ke Poster Berikutnya (2/' . count($pengumumanImages) . ') ❯' : 'Saya Mengerti & Lanjut ke Pendaftaran ✓' }}
                            </button>

                            @if(count($pengumumanImages) > 1)
                            <button type="button" id="pengumumanPrevActionBtn" class="pengumuman-prev-btn" onclick="prevPengumuman()" style="display: none;">
                                ❮ Kembali
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const pengumumanImages = @json(array_map(function($img) { return asset($img); }, $pengumumanImages));
            let currentPengumumanIndex = 0;
            const verifiedImages = {};
            let isMandatoryMode = true;

            function isAllVerified() {
                for (let i = 0; i < pengumumanImages.length; i++) {
                    if (!verifiedImages[i]) return false;
                }
                return true;
            }

            function updatePengumumanView() {
                const total = pengumumanImages.length;
                const imgEl = document.getElementById('pengumumanImgElement');
                if (imgEl) imgEl.src = pengumumanImages[currentPengumumanIndex];

                // Counter
                const counterEl = document.getElementById('pengumumanCounter');
                if (counterEl) {
                    if (total > 1) {
                        counterEl.textContent = `Poster ${currentPengumumanIndex + 1} dari ${total}`;
                        counterEl.style.display = 'inline-block';
                    } else {
                        counterEl.style.display = 'none';
                    }
                }

                // Dots
                for (let idx = 0; idx < total; idx++) {
                    const dot = document.getElementById('pengumumanDot' + idx);
                    if (dot) {
                        if (idx === currentPengumumanIndex) {
                            dot.classList.add('active');
                        } else {
                            dot.classList.remove('active');
                        }
                    }
                }

                // Checkbox status for current image
                const checkbox = document.getElementById('pengumumanCheckbox');
                const checkboxCard = document.getElementById('pengumumanCheckboxCard');
                const isChecked = !!verifiedImages[currentPengumumanIndex];
                if (checkbox) checkbox.checked = isChecked;
                if (checkboxCard) {
                    if (isChecked) {
                        checkboxCard.classList.add('checked');
                    } else {
                        checkboxCard.classList.remove('checked');
                    }
                }

                // Nav buttons
                const prevNavBtn = document.getElementById('pengumumanPrevNavBtn');
                const nextNavBtn = document.getElementById('pengumumanNextNavBtn');
                if (prevNavBtn) {
                    prevNavBtn.style.display = currentPengumumanIndex > 0 ? 'flex' : 'none';
                }
                if (nextNavBtn) {
                    const canGoNext = isChecked || !isMandatoryMode;
                    nextNavBtn.disabled = !canGoNext;
                    nextNavBtn.style.display = currentPengumumanIndex < total - 1 ? 'flex' : 'none';
                }

                // Footer action buttons
                const prevActionBtn = document.getElementById('pengumumanPrevActionBtn');
                if (prevActionBtn) {
                    prevActionBtn.style.display = currentPengumumanIndex > 0 ? 'inline-flex' : 'none';
                }

                const actionBtn = document.getElementById('pengumumanActionBtn');
                if (actionBtn) {
                    const isLast = currentPengumumanIndex === total - 1;
                    if (isLast) {
                        actionBtn.innerHTML = 'Saya Mengerti & Lanjut ke Pendaftaran ✓';
                        actionBtn.disabled = isMandatoryMode ? !isChecked : false;
                    } else {
                        actionBtn.innerHTML = `Lanjut ke Poster Berikutnya (${currentPengumumanIndex + 2}/${total}) ❯`;
                        actionBtn.disabled = isMandatoryMode ? !isChecked : false;
                    }
                }

                // Close button (✕)
                const closeBtn = document.getElementById('closePengumumanBtn');
                if (closeBtn) {
                    closeBtn.style.display = (!isMandatoryMode || isAllVerified()) ? 'flex' : 'none';
                }
            }

            function handleCheckboxChange(checked) {
                if (checked) {
                    verifiedImages[currentPengumumanIndex] = true;
                } else {
                    delete verifiedImages[currentPengumumanIndex];
                }
                updatePengumumanView();
            }

            function nextPengumuman() {
                if (isMandatoryMode && !verifiedImages[currentPengumumanIndex]) {
                    shakeCheckboxCard();
                    return;
                }
                if (currentPengumumanIndex < pengumumanImages.length - 1) {
                    currentPengumumanIndex++;
                    updatePengumumanView();
                }
            }

            function prevPengumuman() {
                if (currentPengumumanIndex > 0) {
                    currentPengumumanIndex--;
                    updatePengumumanView();
                }
            }

            function goToPengumuman(index) {
                if (isMandatoryMode) {
                    for (let i = 0; i < index; i++) {
                        if (!verifiedImages[i]) {
                            shakeCheckboxCard();
                            return;
                        }
                    }
                }
                currentPengumumanIndex = index;
                updatePengumumanView();
            }

            function handlePengumumanAction() {
                const isLast = currentPengumumanIndex === pengumumanImages.length - 1;
                if (isLast) {
                    if (isMandatoryMode && !verifiedImages[currentPengumumanIndex]) {
                        shakeCheckboxCard();
                        return;
                    }
                    isMandatoryMode = false;
                    closePengumumanModal();
                } else {
                    nextPengumuman();
                }
            }

            function shakeCheckboxCard() {
                const card = document.getElementById('pengumumanCheckboxCard');
                if (card) {
                    card.style.animation = 'none';
                    void card.offsetWidth;
                    card.style.animation = 'pengumumanShake 0.4s ease';
                }
            }

            function openPengumumanModal(fromAutoOpen = false) {
                const modal = document.getElementById('pengumumanModal');
                if (modal) {
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                    if (!fromAutoOpen) {
                        isMandatoryMode = false;
                    }
                    updatePengumumanView();
                }
            }

            function closePengumumanModal() {
                if (isMandatoryMode && !isAllVerified()) {
                    shakeCheckboxCard();
                    return;
                }
                const modal = document.getElementById('pengumumanModal');
                if (modal) {
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                }
            }

            // Touch Swipe Gesture untuk HP / Layar Sentuh
            let touchStartX = 0;
            let touchStartY = 0;
            let touchEndX = 0;
            let touchEndY = 0;

            function handleSwipe() {
                if (pengumumanImages.length <= 1) return;

                const diffX = touchEndX - touchStartX;
                const diffY = touchEndY - touchStartY;

                // Threshold minimum 40px dan pastikan arah geser horizontal lebih dominan daripada vertikal
                if (Math.abs(diffX) > 40 && Math.abs(diffX) > Math.abs(diffY)) {
                    if (diffX < 0) {
                        // Geser layar ke kiri -> Poster berikutnya
                        if (currentPengumumanIndex < pengumumanImages.length - 1) {
                            if (isMandatoryMode && !verifiedImages[currentPengumumanIndex]) {
                                shakeCheckboxCard();
                            } else {
                                nextPengumuman();
                            }
                        }
                    } else {
                        // Geser layar ke kanan -> Poster sebelumnya
                        if (currentPengumumanIndex > 0) {
                            prevPengumuman();
                        }
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Otomatis muncul saat mahasiswa membuka halaman
                openPengumumanModal(true);

                // Pasang touch swipe pada area gambar
                const imageArea = document.getElementById('pengumumanImageArea');
                if (imageArea) {
                    imageArea.addEventListener('touchstart', function(e) {
                        if (e.touches && e.touches.length === 1) {
                            touchStartX = e.touches[0].clientX;
                            touchStartY = e.touches[0].clientY;
                        }
                    }, { passive: true });

                    imageArea.addEventListener('touchend', function(e) {
                        if (e.changedTouches && e.changedTouches.length === 1) {
                            touchEndX = e.changedTouches[0].clientX;
                            touchEndY = e.changedTouches[0].clientY;
                            handleSwipe();
                        }
                    }, { passive: true });
                }

                const modal = document.getElementById('pengumumanModal');
                if (modal) {
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) {
                            if (isMandatoryMode && !isAllVerified()) {
                                shakeCheckboxCard();
                            } else {
                                closePengumumanModal();
                            }
                        }
                    });
                }

                document.addEventListener('keydown', function(e) {
                    if (modal && modal.style.display === 'flex') {
                        if (e.key === 'Escape') {
                            if (isMandatoryMode && !isAllVerified()) {
                                shakeCheckboxCard();
                            } else {
                                closePengumumanModal();
                            }
                        }
                        if (pengumumanImages.length > 1) {
                            if (e.key === 'ArrowRight') nextPengumuman();
                            if (e.key === 'ArrowLeft') prevPengumuman();
                        }
                    }
                });
            });
        </script>
        @endif

        <div class="student-helper-bar" aria-label="Informasi pengisian">
            <div class="student-helper-item">
                <span class="student-helper-icon">i</span>
                <span>Kolom bertanda <strong>*</strong> wajib diisi.</span>
            </div>

            <div class="student-helper-item">
                <span class="student-helper-icon">4</span>
                <span>Pengajuan terdiri dari <strong>4 tahap</strong>.</span>
            </div>
        </div>


        <!-- STEP INDICATOR -->
        <div class="step-indicator">

            <div class="step active" id="indicatorStep1">
                <div class="step-number">1</div>
                <span>Identitas</span>
            </div>

            <div class="step" id="indicatorStep2">
                <div class="step-number">2</div>
                <span>Akademik</span>
            </div>

            <div class="step" id="indicatorStep3">
                <div class="step-number">3</div>
                <span>Dokumen</span>
            </div>

            <div class="step" id="indicatorStep4">
                <div class="step-number">4</div>
                <span>Konfirmasi</span>
            </div>

        </div>


        <!-- FORM -->
        <form id="formYudisium" novalidate enctype="multipart/form-data">
            @csrf

            <!-- ==================================================
                 STEP 1 - IDENTITAS
            =================================================== -->
            <section class="form-section active" id="step1">

                <div class="section-header">
                    <h2>Data Identitas Mahasiswa</h2>

                    <p>
                        Pastikan data yang dimasukkan sesuai dengan data resmi mahasiswa.
                    </p>
                </div>

                <div class="form-group">
                    <label for="nama_lengkap">
                        Nama Lengkap
                        <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="nama_lengkap"
                        name="nama_lengkap"
                        placeholder="Masukkan nama lengkap sesuai ijazah"
                        required
                    >

                    <div class="error-message">
                        Nama lengkap wajib diisi.
                    </div>
                </div>

                <div class="form-group">
                    <label for="nim">
                        NIM
                        <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="nim"
                        name="nim"
                        placeholder="Masukkan NIM"
                        inputmode="numeric"
                        autocomplete="off"
                        maxlength="20"
                        required
                    >

                    <small>
                        NIM hanya boleh berisi angka.
                    </small>

                    <div class="error-message">
                        NIM wajib diisi dan hanya boleh berisi angka.
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">
                        Email
                        <span class="required">*</span>
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="contoh@gmail.com / contoh@mhs.eng.upr.ac.id"
                        required
                    >

                    <div class="error-message">
                        Masukkan alamat email yang valid.
                    </div>
                </div>

                <div class="form-group">
                    <label for="no_whatsapp">
                        Nomor WhatsApp
                        <span class="required">*</span>
                    </label>

                    <input
                        type="tel"
                        id="no_whatsapp"
                        name="no_whatsapp"
                        placeholder="Contoh: 081234567890"
                        inputmode="numeric"
                        maxlength="15"
                        required
                    >

                    <small>
                        Nomor WhatsApp hanya boleh berisi angka.
                    </small>

                    <div class="error-message">
                        Nomor WhatsApp wajib diisi dan hanya boleh berisi angka.
                    </div>
                </div>

                <div class="form-group">
                    <label for="tahun_angkatan">
                        Tahun Angkatan
                        <span class="required">*</span>
                    </label>

                    <input
                        type="number"
                        id="tahun_angkatan"
                        name="tahun_angkatan"
                        placeholder="Contoh: 2023"
                        min="2000"
                        max="2100"
                        required
                    >

                    <div class="error-message">
                        Tahun angkatan wajib diisi.
                    </div>
                </div>

                <div class="form-group">
                    <label for="jalur_masuk">
                        Jalur Masuk Kuliah
                        <span class="required">*</span>
                    </label>

                    <select id="jalur_masuk" name="jalur_masuk" required>

                        <option value="">
                            Pilih jalur masuk
                        </option>

                        <option value="REGULER">
                            Reguler
                        </option>

                        <option value="RPL">
                            RPL
                        </option>

                    </select>

                    <div class="error-message">
                        Pilih jalur masuk kuliah.
                    </div>
                </div>

                <div class="form-group">
                    <label for="jurusan">
                        Jurusan
                        <span class="required">*</span>
                    </label>

                    <select id="jurusan" name="jurusan" required>

                        <option value="">
                            Pilih jurusan
                        </option>

                        <option value="EKONOMI PEMBANGUNAN">
                            Ekonomi Pembangunan
                        </option>

                        <option value="MANAJEMEN">
                            Manajemen
                        </option>

                        <option value="AKUNTANSI">
                            Akuntansi
                        </option>

                    </select>

                    <div class="error-message">
                        Pilih jurusan.
                    </div>
                </div>

                <div class="form-navigation navigation-end">

                    <button
                        type="button"
                        class="btn btn-primary"
                        id="nextStep1"
                    >
                        Berikutnya
                    </button>

                </div>

                @include('mahasiswa.partials.watermark')

            </section>


            <!-- ==================================================
                 STEP 2 - AKADEMIK
            =================================================== -->
            <section class="form-section" id="step2">

                <div class="section-header">

                    <h2>Data Akademik</h2>

                    <p>
                        Isi data akademik sesuai dengan dokumen resmi dan hasil ujian Skripsi/Artikel.
                    </p>

                </div>

                <div class="form-group">
                    <label for="karya_tulis">
                        Karya Tulis Ilmiah
                        <span class="required">*</span>
                    </label>

                    <select id="karya_tulis" name="karya_tulis" required>

                        <option value="">
                            Pilih jenis karya tulis
                        </option>

                        <option value="SKRIPSI">
                            Skripsi
                        </option>

                        <option value="ARTIKEL">
                            Artikel
                        </option>

                    </select>

                    <div class="error-message">
                        Pilih jenis karya tulis.
                    </div>
                </div>

                <div class="form-group">
                    <label for="judul_karya_tulis">
                        Judul Skripsi / Artikel
                        <span class="required">*</span>
                    </label>

                    <textarea
                        id="judul_karya_tulis"
                        name="judul_karya_tulis"
                        rows="5"
                        placeholder="Masukkan judul Skripsi / Artikel secara lengkap"
                        required
                    ></textarea>

                    <small>
                        Pastikan judul sama persis dengan dokumen resmi.
                    </small>

                    <div class="error-message">
                        Judul Skripsi / Artikel wajib diisi.
                    </div>
                </div>

                <div class="form-group">
                    <label for="tanggal_ujian">
                        Tanggal Ujian Skripsi / Artikel
                        <span class="required">*</span>
                    </label>

                    <input
                        type="date"
                        id="tanggal_ujian"
                        name="tanggal_ujian"
                        required
                    >

                    <div class="error-message">
                        Tanggal ujian wajib diisi.
                    </div>
                </div>

                <div class="form-group">
                    <label for="nilai_angka">
                        Nilai Ujian Skripsi / Artikel
                        <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="nilai_angka"
                        name="nilai_angka"
                        inputmode="decimal"
                        placeholder="Contoh: 80,00"
                        required
                    >

                    <small>
                        Gunakan tanda koma. Contoh: 80,00.
                    </small>

                    <div class="error-message">
                        Masukkan nilai dengan format yang benar, contoh 80,00.
                    </div>
                </div>

                <div class="form-group">
                    <label for="nilai_huruf">
                        Nilai Huruf
                        <span class="required">*</span>
                    </label>

                    <select id="nilai_huruf" name="nilai_huruf" required>

                        <option value="">
                            Pilih nilai huruf
                        </option>

                        <option value="A">A</option>
                        <option value="A-">A-</option>
                        <option value="A/B">A/B</option>
                        <option value="B+">B+</option>
                        <option value="B">B</option>
                        <option value="B-">B-</option>

                    </select>

                    <div class="error-message">
                        Pilih nilai huruf.
                    </div>
                </div>

                <div class="form-navigation">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        id="backStep2"
                    >
                        Kembali
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary"
                        id="nextStep2"
                    >
                        Berikutnya
                    </button>

                </div>

                @include('mahasiswa.partials.watermark')

            </section>


            <!-- ==================================================
                 STEP 3 - DOKUMEN
            =================================================== -->
            <section class="form-section" id="step3">

                @php
                    $isDocumentRequired = fn (string $code): bool => in_array($code, $requiredDocs, true);
                @endphp

                <div class="section-header">

                    <h2>Upload Dokumen Persyaratan</h2>

                    <p>
                        Upload dokumen sesuai ketentuan. Pastikan file jelas,
                        dapat dibuka, dan tidak melebihi batas ukuran.
                    </p>

                </div>

                <div class="document-upload-summary" aria-live="polite">
                    <div class="document-upload-summary-main">
                        <span class="document-upload-summary-label">Kelengkapan Dokumen</span>
                        <strong id="documentProgressText">0 dokumen wajib dipilih</strong>
                        <p>Dokumen khusus jurusan akan dihitung otomatis sesuai jurusan Anda.</p>
                    </div>

                    <div class="document-upload-progress" aria-hidden="true">
                        <span id="documentProgressValue"></span>
                    </div>
                </div>

                @if(in_array('FORM_YUDISIUM', $activeDocs))
                <div class="form-group file-group">

                    <label for="form_yudisium">
                        Formulir Pendaftaran Yudisium
                        @if($isDocumentRequired('FORM_YUDISIUM'))
                            <span class="required">*</span>
                        @endif
                    </label>

                    <input
                        type="file"
                        id="form_yudisium"
                        name="form_yudisium"
                        accept=".pdf,application/pdf"
                        data-max-size="1"
                        data-required="{{ $isDocumentRequired('FORM_YUDISIUM') ? 'true' : 'false' }}"
                        @if($isDocumentRequired('FORM_YUDISIUM')) required @endif
                    >

                    <small>PDF, maksimal 1 MB.</small>

                    <div class="error-message">
                        Formulir Pendaftaran Yudisium wajib berupa PDF maksimal 1 MB.
                    </div>

                </div>
                @endif

                @if(in_array('FOTO_3X4', $activeDocs))
                <div class="form-group file-group">

                    <label for="foto_3x4">
                        Foto 3×4 Berwarna
                        @if($isDocumentRequired('FOTO_3X4'))
                            <span class="required">*</span>
                        @endif
                    </label>

                    <input
                        type="file"
                        id="foto_3x4"
                        name="foto_3x4"
                        accept=".pdf,application/pdf"
                        data-max-size="1"
                        data-required="{{ $isDocumentRequired('FOTO_3X4') ? 'true' : 'false' }}"
                        @if($isDocumentRequired('FOTO_3X4')) required @endif
                    >

                    <small>
                        Kebaya untuk perempuan dan jas untuk laki-laki.
                        PDF, maksimal 1 MB.
                    </small>

                    <div class="error-message">
                        Foto 3×4 wajib berupa PDF maksimal 1 MB.
                    </div>

                </div>
                @endif

                @if(in_array('IJAZAH_SLTA', $activeDocs))
                <div class="form-group file-group">

                    <label for="ijazah_slta">
                        Ijazah SLTA
                        @if($isDocumentRequired('IJAZAH_SLTA'))
                            <span class="required">*</span>
                        @endif
                    </label>

                    <input
                        type="file"
                        id="ijazah_slta"
                        name="ijazah_slta"
                        accept=".pdf,application/pdf"
                        data-max-size="1"
                        data-required="{{ $isDocumentRequired('IJAZAH_SLTA') ? 'true' : 'false' }}"
                        @if($isDocumentRequired('IJAZAH_SLTA')) required @endif
                    >

                    <small>PDF, maksimal 1 MB.</small>

                    <div class="error-message">
                        Ijazah SLTA wajib berupa PDF maksimal 1 MB.
                    </div>

                </div>
                @endif

                @if(in_array('BERITA_ACARA_UJIAN', $activeDocs))
                <div class="form-group file-group">

                    <label for="berita_acara_ujian">
                        Berita Acara Ujian Skripsi / Artikel
                        @if($isDocumentRequired('BERITA_ACARA_UJIAN'))
                            <span class="required">*</span>
                        @endif
                    </label>

                    <input
                        type="file"
                        id="berita_acara_ujian"
                        name="berita_acara_ujian"
                        accept=".pdf,application/pdf"
                        data-max-size="1"
                        data-required="{{ $isDocumentRequired('BERITA_ACARA_UJIAN') ? 'true' : 'false' }}"
                        @if($isDocumentRequired('BERITA_ACARA_UJIAN')) required @endif
                    >

                    <small>
                        Sudah ditandatangani WD I dan dicap. PDF, maksimal 1 MB.
                    </small>

                    <div class="error-message">
                        Berita Acara Ujian wajib berupa PDF maksimal 1 MB.
                    </div>

                </div>
                @endif

                @if(in_array('REKAP_NILAI', $activeDocs))
                <div class="form-group file-group">

                    <label for="rekap_nilai">
                        Rekapitulasi Nilai Ujian Skripsi / Artikel
                        @if($isDocumentRequired('REKAP_NILAI'))
                            <span class="required">*</span>
                        @endif
                    </label>

                    <input
                        type="file"
                        id="rekap_nilai"
                        name="rekap_nilai"
                        accept=".pdf,application/pdf"
                        data-max-size="1"
                        data-required="{{ $isDocumentRequired('REKAP_NILAI') ? 'true' : 'false' }}"
                        @if($isDocumentRequired('REKAP_NILAI')) required @endif
                    >

                    <small>
                        Disahkan Wakil Dekan I. PDF, maksimal 1 MB.
                    </small>

                    <div class="error-message">
                        Rekapitulasi Nilai wajib berupa PDF maksimal 1 MB.
                    </div>

                </div>
                @endif

                @if(in_array('BLANKO_REVISI', $activeDocs))
                <div class="form-group file-group">

                    <label for="blanko_revisi">
                        Blanko Revisi
                        @if($isDocumentRequired('BLANKO_REVISI'))
                            <span class="required">*</span>
                        @endif
                    </label>

                    <input
                        type="file"
                        id="blanko_revisi"
                        name="blanko_revisi"
                        accept=".pdf,application/pdf"
                        data-max-size="1"
                        data-required="{{ $isDocumentRequired('BLANKO_REVISI') ? 'true' : 'false' }}"
                        @if($isDocumentRequired('BLANKO_REVISI')) required @endif
                    >

                    <small>
                        Sudah ditandatangani dosen. PDF, maksimal 1 MB.
                    </small>

                    <div class="error-message">
                        Blanko Revisi wajib berupa PDF maksimal 1 MB.
                    </div>

                </div>
                @endif

                @if(in_array('TANDA_TERIMA', $activeDocs))
                <div class="form-group file-group">

                    <label for="tanda_terima">
                        Tanda Terima Skripsi / Artikel
                        @if($isDocumentRequired('TANDA_TERIMA'))
                            <span class="required">*</span>
                        @endif
                    </label>

                    <input
                        type="file"
                        id="tanda_terima"
                        name="tanda_terima"
                        accept=".pdf,.doc,.docx"
                        data-max-size="10"
                        data-required="{{ $isDocumentRequired('TANDA_TERIMA') ? 'true' : 'false' }}"
                        @if($isDocumentRequired('TANDA_TERIMA')) required @endif
                    >

                    <small>
                        PDF, DOC, atau DOCX. Maksimal 10 MB.
                    </small>

                    <div class="error-message">
                        Tanda Terima wajib berupa PDF/DOC/DOCX maksimal 10 MB.
                    </div>

                </div>
                @endif

                @if(in_array('SURAT_PERNYATAAN_IJAZAH', $activeDocs))
                <div class="form-group file-group">

                    <label for="surat_pernyataan_ijazah">
                        Surat Pernyataan untuk Proses Penulisan Ijazah
                        @if($isDocumentRequired('SURAT_PERNYATAAN_IJAZAH'))
                            <span class="required">*</span>
                        @endif
                    </label>

                    <input
                        type="file"
                        id="surat_pernyataan_ijazah"
                        name="surat_pernyataan_ijazah"
                        accept=".pdf,application/pdf"
                        data-max-size="1"
                        data-required="{{ $isDocumentRequired('SURAT_PERNYATAAN_IJAZAH') ? 'true' : 'false' }}"
                        @if($isDocumentRequired('SURAT_PERNYATAAN_IJAZAH')) required @endif
                    >

                    <small>PDF, maksimal 1 MB.</small>

                    <div class="error-message">
                        Surat Pernyataan wajib berupa PDF maksimal 1 MB.
                    </div>

                </div>
                @endif

                @if(in_array('BEBAS_PERPUS_UNIV', $activeDocs))
                <div class="form-group file-group">

                    <label for="bebas_perpus_univ">
                        Surat Bebas Pinjam Perpustakaan Universitas Asli
                        @if($isDocumentRequired('BEBAS_PERPUS_UNIV'))
                            <span class="required">*</span>
                        @endif
                    </label>

                    <input
                        type="file"
                        id="bebas_perpus_univ"
                        name="bebas_perpus_univ"
                        accept=".pdf,application/pdf"
                        data-max-size="1"
                        data-required="{{ $isDocumentRequired('BEBAS_PERPUS_UNIV') ? 'true' : 'false' }}"
                        @if($isDocumentRequired('BEBAS_PERPUS_UNIV')) required @endif
                    >

                    <small>PDF, maksimal 1 MB.</small>

                    <div class="error-message">
                        Surat Bebas Pinjam Perpustakaan Universitas wajib berupa PDF maksimal 1 MB.
                    </div>

                </div>
                @endif

                @if(in_array('BEBAS_PERPUS_FAKULTAS', $activeDocs))
                <div class="form-group file-group">

                    <label for="bebas_perpus_fakultas">
                        Surat Bebas Pinjam Perpustakaan Fakultas Asli
                        @if($isDocumentRequired('BEBAS_PERPUS_FAKULTAS'))
                            <span class="required">*</span>
                        @endif
                    </label>

                    <input
                        type="file"
                        id="bebas_perpus_fakultas"
                        name="bebas_perpus_fakultas"
                        accept=".pdf,application/pdf"
                        data-max-size="1"
                        data-required="{{ $isDocumentRequired('BEBAS_PERPUS_FAKULTAS') ? 'true' : 'false' }}"
                        @if($isDocumentRequired('BEBAS_PERPUS_FAKULTAS')) required @endif
                    >

                    <small>
                        PDF, maksimal 1 MB. Tidak bertanda wajib pada Google Form lama.
                    </small>

                    <div class="error-message">
                        File harus berupa PDF maksimal 1 MB.
                    </div>

                </div>
                @endif

                @if(in_array('KHS', $activeDocs))
                <div class="form-group file-group">

                    <label for="khs">
                        KHS Semester 1 s/d Terbaru
                        @if($isDocumentRequired('KHS'))
                            <span class="required">*</span>
                        @endif
                    </label>

                    <input
                        type="file"
                        id="khs"
                        name="khs"
                        accept=".pdf,application/pdf"
                        data-max-size="1"
                        data-required="{{ $isDocumentRequired('KHS') ? 'true' : 'false' }}"
                        @if($isDocumentRequired('KHS')) required @endif
                    >

                    <small>
                        Sudah ditandatangani Kajur. PDF, maksimal 1 MB.
                    </small>

                    <div class="error-message">
                        KHS wajib berupa PDF maksimal 1 MB.
                    </div>

                </div>
                @endif

                @if(in_array('KHS_SEMESTER_BERJALAN', $activeDocs))
<div class="form-group file-group">

    <label for="khs_semester_berjalan">
        KHS terbaru yang ada nilai skripsi dan tanda tangan ketua jurusan
        @if($isDocumentRequired('KHS_SEMESTER_BERJALAN'))
            <span class="required">*</span>
        @endif
    </label>

    <input
        type="file"
        id="khs_semester_berjalan"
        name="khs_semester_berjalan"
        accept=".pdf,application/pdf"
        data-max-size="1"
        data-required="{{ $isDocumentRequired('KHS_SEMESTER_BERJALAN') ? 'true' : 'false' }}"
        @if($isDocumentRequired('KHS_SEMESTER_BERJALAN')) required @endif
    >

    <small>
        PDF, maksimal 1 MB.
    </small>

    <div class="error-message">
        KHS terbaru wajib berupa PDF maksimal 1 MB.
    </div>

</div>
@endif


                @if(in_array('TRANSKRIP', $activeDocs))
                <div class="form-group file-group">

                    <label for="transkrip">
                        Transkrip Nilai Ujian Skripsi
                        @if($isDocumentRequired('TRANSKRIP'))
                            <span class="required">*</span>
                        @endif
                    </label>

                    <input
                        type="file"
                        id="transkrip"
                        name="transkrip"
                        accept=".pdf,application/pdf"
                        data-max-size="1"
                        data-required="{{ $isDocumentRequired('TRANSKRIP') ? 'true' : 'false' }}"
                        @if($isDocumentRequired('TRANSKRIP')) required @endif
                    >

                    <small>
                        Sudah dicap Fakultas. PDF, maksimal 1 MB.
                    </small>

                    <div class="error-message">
                        Transkrip Nilai wajib berupa PDF maksimal 1 MB.
                    </div>

                </div>
                @endif

                @if(in_array('SURAT_TUGAS_DOSBING', $activeDocs))
                <div class="form-group file-group">

                    <label for="surat_tugas_dosbing">
                        Surat Tugas Dosen Pembimbing Skripsi
                        @if($isDocumentRequired('SURAT_TUGAS_DOSBING'))
                            <span class="required">*</span>
                        @endif
                    </label>

                    <input
                        type="file"
                        id="surat_tugas_dosbing"
                        name="surat_tugas_dosbing"
                        accept=".pdf,application/pdf"
                        data-max-size="10"
                        data-required="{{ $isDocumentRequired('SURAT_TUGAS_DOSBING') ? 'true' : 'false' }}"
                        @if($isDocumentRequired('SURAT_TUGAS_DOSBING')) required @endif
                    >

                    <small>
                        Masih berlaku 6 bulan terakhir. PDF, maksimal 10 MB.
                    </small>

                    <div class="error-message">
                        Surat Tugas Pembimbing wajib berupa PDF maksimal 10 MB.
                    </div>

                </div>
                @endif

                @if(in_array('BEBAS_TUNGGAKAN', $activeDocs))
                <div class="form-group file-group">

                    <label for="bebas_tunggakan">
                        Surat Verifikasi Bebas Tunggakan dari Keuangan Rektorat
                        @if($isDocumentRequired('BEBAS_TUNGGAKAN'))
                            <span class="required">*</span>
                        @endif
                    </label>

                    <input
                        type="file"
                        id="bebas_tunggakan"
                        name="bebas_tunggakan"
                        accept=".pdf,application/pdf"
                        data-max-size="1"
                        data-required="{{ $isDocumentRequired('BEBAS_TUNGGAKAN') ? 'true' : 'false' }}"
                        @if($isDocumentRequired('BEBAS_TUNGGAKAN')) required @endif
                    >

                    <small>PDF, maksimal 1 MB.</small>

                    <div class="error-message">
                        Surat Bebas Tunggakan wajib berupa PDF maksimal 1 MB.
                    </div>

                </div>
                @endif


                <!-- DOKUMEN KHUSUS JURUSAN -->
                <div class="jurusan-document-wrapper">

                    <h3>Dokumen Khusus Jurusan</h3>

                    <p class="jurusan-info">
                        Dokumen menyesuaikan jurusan yang dipilih pada Step Identitas.
                    </p>

                    @if(in_array('JURNAL_JMSO', $activeDocs))
                    <div class="form-group file-group jurusan-document" id="dokumenManajemen">

                        <label for="jurnal_jmso">
                            Bukti Pengisian Jurnal "Manajemen Sains dan Organisasi" (JMSO)
                            @if($isDocumentRequired('JURNAL_JMSO'))
                                <span class="required">*</span>
                            @endif
                        </label>

                        <input
                            type="file"
                            id="jurnal_jmso"
                            name="jurnal_jmso"
                            accept=".pdf,application/pdf"
                            data-max-size="1"
                            data-required="{{ $isDocumentRequired('JURNAL_JMSO') ? 'true' : 'false' }}"
                        >

                        <small>
                            Khusus Jurusan Manajemen. PDF, maksimal 1 MB.
                        </small>

                        <div class="error-message">
                            Dokumen JMSO harus berupa PDF maksimal 1 MB.
                        </div>

                    </div>
                    @endif

                    @if(in_array('JURNAL_EP', $activeDocs))
                    <div class="form-group file-group jurusan-document" id="dokumenEkonomi">

                        <label for="jurnal_ep">
                            Bukti Pengisian Jurnal Jurusan Ekonomi Pembangunan
                            @if($isDocumentRequired('JURNAL_EP'))
                                <span class="required">*</span>
                            @endif
                        </label>

                        <input
                            type="file"
                            id="jurnal_ep"
                            name="jurnal_ep"
                            accept=".pdf,application/pdf"
                            data-max-size="1"
                            data-required="{{ $isDocumentRequired('JURNAL_EP') ? 'true' : 'false' }}"
                        >

                        <small>
                            Khusus Jurusan Ekonomi Pembangunan. PDF, maksimal 1 MB.
                        </small>

                        <div class="error-message">
                            Dokumen jurnal Ekonomi Pembangunan harus berupa PDF maksimal 1 MB.
                        </div>

                    </div>
                    @endif

                    @if(in_array('JURNAL_AKUNTANSI', $activeDocs))
                    <div class="form-group file-group jurusan-document" id="dokumenAkuntansi">

                        <label for="jurnal_akuntansi">
                            Bukti Pengisian Jurnal Jurusan Akuntansi
                            @if($isDocumentRequired('JURNAL_AKUNTANSI'))
                                <span class="required">*</span>
                            @endif
                        </label>

                        <input
                            type="file"
                            id="jurnal_akuntansi"
                            name="jurnal_akuntansi"
                            accept=".pdf,.doc,.docx"
                            data-max-size="10"
                            data-required="{{ $isDocumentRequired('JURNAL_AKUNTANSI') ? 'true' : 'false' }}"
                        >

                        <small>
                            Khusus Jurusan Akuntansi. Maksimal 10 MB.
                        </small>

                        <div class="error-message">
                            Dokumen jurnal Akuntansi maksimal 10 MB.
                        </div>

                    </div>
                    @endif

                </div>


                <div class="form-navigation">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        id="backStep3"
                    >
                        Kembali
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary"
                        id="nextStep3"
                    >
                        Berikutnya
                    </button>

                </div>

                @include('mahasiswa.partials.watermark')

            </section>


            <!-- ==================================================
                 STEP 4 - KONFIRMASI
            =================================================== -->
            <section class="form-section" id="step4">

                <div class="section-header">

                    <h2>Konfirmasi Data Pengajuan</h2>

                    <p>
                        Periksa kembali seluruh data sebelum mengirim pengajuan.
                    </p>

                </div>


                <!-- IDENTITAS -->
                <div class="review-card">

                    <div class="review-card-header">

                        <h3>Data Identitas</h3>

                        <button
                            type="button"
                            class="btn-edit"
                            id="editIdentitas"
                        >
                            Edit
                        </button>

                    </div>

                    <div class="review-grid">

                        <div class="review-item">
                            <span class="review-label">
                                Nama Lengkap
                            </span>

                            <strong id="reviewNama">-</strong>
                        </div>

                        <div class="review-item">
                            <span class="review-label">
                                NIM
                            </span>

                            <strong id="reviewNim">-</strong>
                        </div>

                        <div class="review-item">
                            <span class="review-label">
                                Email
                            </span>

                            <strong id="reviewEmail">-</strong>
                        </div>

                        <div class="review-item">
                            <span class="review-label">
                                Nomor WhatsApp
                            </span>

                            <strong id="reviewWhatsapp">-</strong>
                        </div>

                        <div class="review-item">
                            <span class="review-label">
                                Tahun Angkatan
                            </span>

                            <strong id="reviewAngkatan">-</strong>
                        </div>

                        <div class="review-item">
                            <span class="review-label">
                                Jalur Masuk
                            </span>

                            <strong id="reviewJalur">-</strong>
                        </div>

                        <div class="review-item review-full">
                            <span class="review-label">
                                Jurusan
                            </span>

                            <strong id="reviewJurusan">-</strong>
                        </div>

                    </div>

                </div>


                <!-- AKADEMIK -->
                <div class="review-card">

                    <div class="review-card-header">

                        <h3>Data Akademik</h3>

                        <button
                            type="button"
                            class="btn-edit"
                            id="editAkademik"
                        >
                            Edit
                        </button>

                    </div>

                    <div class="review-grid">

                        <div class="review-item">
                            <span class="review-label">
                                Karya Tulis Ilmiah
                            </span>

                            <strong id="reviewKaryaTulis">-</strong>
                        </div>

                        <div class="review-item">
                            <span class="review-label">
                                Tanggal Ujian
                            </span>

                            <strong id="reviewTanggalUjian">-</strong>
                        </div>

                        <div class="review-item">
                            <span class="review-label">
                                Nilai Angka
                            </span>

                            <strong id="reviewNilaiAngka">-</strong>
                        </div>

                        <div class="review-item">
                            <span class="review-label">
                                Nilai Huruf
                            </span>

                            <strong id="reviewNilaiHuruf">-</strong>
                        </div>

                        <div class="review-item review-full">
                            <span class="review-label">
                                Judul Skripsi / Artikel
                            </span>

                            <strong id="reviewJudul">-</strong>
                        </div>

                    </div>

                </div>


                <!-- DOKUMEN -->
                <div class="review-card">

                    <div class="review-card-header">

                        <h3>Dokumen Persyaratan</h3>

                        <button
                            type="button"
                            class="btn-edit"
                            id="editDokumen"
                        >
                            Edit
                        </button>

                    </div>

                    <div
                        class="document-review-list"
                        id="documentReviewList"
                    >
                    </div>

                </div>


                <!-- PERNYATAAN -->
                <div class="confirmation-box">

                    <label class="confirmation-label">

                        <input
                            type="checkbox"
                            id="konfirmasiData"
                            name="konfirmasi_data"
                        >

                        <span>
                            Saya menyatakan bahwa seluruh data dan dokumen yang
                            saya masukkan telah saya periksa dan sesuai dengan
                            dokumen yang sebenarnya.
                        </span>

                    </label>

                    <div
                        class="confirmation-error"
                        id="confirmationError"
                    >
                        Anda harus menyetujui pernyataan sebelum mengirim pengajuan.
                    </div>

                </div>


                <div class="important-notice">

                    <strong>Perhatian</strong>

                    <p>
                        Setelah pengajuan dikirim, data akan diperiksa oleh admin.
                        Jika terdapat kesalahan, Anda hanya dapat memperbaiki
                        data yang ditandai untuk direvisi oleh admin.
                    </p>

                </div>


                <div class="form-navigation">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        id="backStep4"
                    >
                        Kembali
                    </button>

                    <button
                        type="submit"
                        class="btn btn-success"
                        id="submitPengajuan"
                    >
                        Kirim Pengajuan
                    </button>

                </div>

                @include('mahasiswa.partials.watermark')

            </section>

        </form>

    </main>


    <!-- MODAL PENGAJUAN BERHASIL -->
    <div class="modal-overlay" id="successModal" role="dialog" aria-modal="true" aria-labelledby="successModalTitle">

        <div class="modal-card student-success-modal">

            <div class="success-icon" aria-hidden="true">
                ✓
            </div>

            <span class="success-eyebrow">Pengajuan Berhasil</span>

            <h2 id="successModalTitle">Simpan Kode Pengajuan Yudisium Anda</h2>

            <p class="success-description">
                Kode ini digunakan bersama NIM untuk membuka status pengajuan dan melihat feedback dari admin.
                Simpan kode sebelum menutup halaman.
            </p>

            <div class="submission-code-box">
                <span>Kode Pengajuan Yudisium</span>

                <div class="submission-code-row">
                    <strong id="generatedSubmissionCode">-</strong>

                    <button
                        type="button"
                        class="copy-code-button"
                        id="copySubmissionCode"
                    >
                        Salin Kode
                    </button>
                </div>

                <small id="copyCodeFeedback">Simpan kode ini di tempat yang mudah ditemukan.</small>
            </div>

            <div class="success-security-note">
                <strong>Penting:</strong> jangan mengandalkan ingatan saja. Kode diperlukan untuk cek status pengajuan.
            </div>

            <div class="success-modal-actions">
                <button
                    type="button"
                    class="btn btn-secondary"
                    id="closeSuccessModal"
                >
                    Tutup
                </button>

                <button
                    type="button"
                    class="btn btn-primary"
                    id="goToTracking"
                >
                    Cek Status Pengajuan
                </button>
            </div>

        </div>

    </div>


    <script src="{{ asset('js/yudisium_api.js') }}"></script>
    <script src="{{ asset('js/student_pengajuan_api_bridge.js') }}"></script>
    <script src="{{ asset('js/student_pengajuan.js') }}"></script>

</body>

</html>
