document.addEventListener("DOMContentLoaded", function () {
    "use strict";
    console.log("Admin frontend aktif (Backend Mode)");

    /* =====================================
       GLOBAL HELPER
    ===================================== */
    function setError(field, hasError) {
        const formGroup = field.closest(".form-group");
        if (hasError) {
            field.classList.add("form-control-error");
            if (formGroup) formGroup.classList.add("has-error");
        } else {
            field.classList.remove("form-control-error");
            if (formGroup) formGroup.classList.remove("has-error");
        }
    }

    function validateEmailField(field) {
        const value = field.value.trim();
        const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const valid = pattern.test(value);
        setError(field, !valid);
        return valid;
    }

    function setupPasswordToggle(button, input, openIcon, closedIcon) {
        if (!button || !input || !openIcon || !closedIcon) return;

        button.addEventListener("click", function () {
            const visible = input.type === "text";
            if (visible) {
                input.type = "password";
                openIcon.classList.remove("hidden");
                closedIcon.classList.add("hidden");
                button.setAttribute("aria-label", "Tampilkan password");
            } else {
                input.type = "text";
                openIcon.classList.add("hidden");
                closedIcon.classList.remove("hidden");
                button.setAttribute("aria-label", "Sembunyikan password");
            }
        });
    }

    /* =====================================
       LOGIN & REGISTER (UI Interactions Only)
    ===================================== */
    const passwordInput = document.getElementById("admin_password") || document.getElementById("register_password");
    if (passwordInput) {
        setupPasswordToggle(
            document.getElementById("togglePassword") || document.getElementById("toggleRegisterPassword"),
            passwordInput,
            document.getElementById("eyeOpenIcon") || document.getElementById("registerEyeOpen"),
            document.getElementById("eyeClosedIcon") || document.getElementById("registerEyeClosed")
        );
    }

    const confirmationPassword = document.getElementById("register_password_confirmation");
    if (confirmationPassword) {
        setupPasswordToggle(
            document.getElementById("toggleConfirmPassword"),
            confirmationPassword,
            document.getElementById("confirmEyeOpen"),
            document.getElementById("confirmEyeClosed")
        );
    }

    /* =====================================
       LOGOUT
    ===================================== */
    const logoutButton = document.getElementById("adminLogout");
    if (logoutButton && logoutButton.dataset.logoutInitialized !== "true") {
        logoutButton.dataset.logoutInitialized = "true";
        logoutButton.addEventListener("click", function (e) {
            e.preventDefault();
            // Buat form tersembunyi untuk logout via POST (Standar keamanan Laravel)
            const form = document.createElement("form");
            form.method = "POST";
            form.action = "/admin/logout";
            
            // Tambahkan CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrfToken) {
                const hiddenToken = document.createElement("input");
                hiddenToken.type = "hidden";
                hiddenToken.name = "_token";
                hiddenToken.value = csrfToken;
                form.appendChild(hiddenToken);
            }
            
            document.body.appendChild(form);
            form.submit();
        });
    }

    /* =====================================
       DASHBOARD SIDEBAR NAVIGATION
    ===================================== */
    const quickRevision = document.getElementById("dashboardQuickRevision");
    const quickSk = document.getElementById("dashboardQuickSk");
    const revisionMenu = document.getElementById("dashboardRevisionMenu");
    const skMenu = document.getElementById("dashboardSkMenu");


    /* =====================================
       EXPORT MODAL
    ===================================== */
    const quickExport = document.getElementById("quickExport");
    const exportModal = document.getElementById("exportModal");
    const closeExportModal = document.getElementById("closeExportModal");

    if (quickExport && exportModal) {
        quickExport.addEventListener("click", function () {
            exportModal.classList.add("active");
        });
    }

    if (closeExportModal && exportModal) {
        closeExportModal.addEventListener("click", function () {
            exportModal.classList.remove("active");
        });
    }

    const registerPassword = document.getElementById("register_password");
    if (registerPassword) {
        registerPassword.addEventListener("input", function () {
            const fill = document.getElementById("passwordStrengthFill");
            const text = document.getElementById("passwordStrengthText");
            if (!fill || !text) return;

            const val = this.value;
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[a-z]/.test(val)) score++;
            if (/\d/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            fill.className = "password-strength-fill";
            if (val.length === 0) {
                fill.style.width = "0%"; text.textContent = "Belum ada password";
            } else if (score <= 2) {
                fill.style.width = "33%"; fill.classList.add("weak"); text.textContent = "Password lemah";
            } else if (score <= 4) {
                fill.style.width = "66%"; fill.classList.add("medium"); text.textContent = "Password cukup";
            } else {
                fill.style.width = "100%"; fill.classList.add("strong"); text.textContent = "Password kuat";
            }
        });
    }
    
});