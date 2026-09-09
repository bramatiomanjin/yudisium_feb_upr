document.addEventListener("DOMContentLoaded", function () {
    "use strict";

    console.log("Kelola Admin (Backend Mode) aktif.");

    /* =====================================
       CLIENT-SIDE SEARCH FILTER
       (Karena data sudah dirender oleh Blade)
    ===================================== */
    const searchInput = document.getElementById("manageAdminSearch");
    const searchButton = document.getElementById("applyAdminSearch");
    const resetButton = document.getElementById("resetAdminSearch");

    function applySearch() {
        if (!searchInput) return;
        
        const query = searchInput.value.toLowerCase().trim();
        const adminCards = document.querySelectorAll(".manage-admin-item"); // Asumsi Blade menggunakan class ini

        adminCards.forEach(function (card) {
            const textContent = card.textContent.toLowerCase();
            if (textContent.includes(query)) {
                card.style.display = "";
            } else {
                card.style.display = "none";
            }
        });
    }

    if (searchButton) {
        searchButton.addEventListener("click", applySearch);
    }

    if (searchInput) {
        searchInput.addEventListener("keydown", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                applySearch();
            }
        });
    }

    if (resetButton) {
        resetButton.addEventListener("click", function () {
            if (searchInput) {
                searchInput.value = "";
            }
            applySearch(); // Reset filter (tampilkan semua)
        });
    }
    
    // Catatan: Logika modal Approve/Reject/Deactivate telah dihapus
    // karena pengelolaannya kini ditangani langsung oleh form POST di Blade Laravel
});