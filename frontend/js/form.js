const btnNext = document.getElementById("btnNext");
const form = document.getElementById("formYudisium");

btnNext.addEventListener("click", function () {

    const requiredInputs = form.querySelectorAll(
        "input[required], select[required]"
    );

    let formValid = true;

    requiredInputs.forEach(function (input) {

        if (!input.value.trim()) {

            input.style.borderColor = "red";
            formValid = false;

        } else {

            input.style.borderColor = "#d1d5db";

        }

    });

    if (!formValid) {

        alert("Silakan lengkapi seluruh data yang wajib diisi.");

        return;

    }

    console.log("Data identitas valid.");

    alert("Data identitas berhasil diisi. Selanjutnya masuk ke data akademik.");

});