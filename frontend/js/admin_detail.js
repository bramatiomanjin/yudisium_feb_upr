document.addEventListener(
    "DOMContentLoaded",
    function () {

        console.log(
            "Admin detail pengajuan aktif"
        );


        const previewButtons =
            document.querySelectorAll(
                ".preview-document-button"
            );


        const previewModal =
            document.getElementById(
                "documentPreviewModal"
            );


        const previewTitle =
            document.getElementById(
                "documentPreviewTitle"
            );


        const previewFilename =
            document.getElementById(
                "documentPreviewFilename"
            );


        const previewFrame =
            document.getElementById(
                "documentPreviewFrame"
            );


        const previewPlaceholder =
            document.getElementById(
                "documentPreviewPlaceholder"
            );


        const closePreview =
            document.getElementById(
                "closeDocumentPreview"
            );


        const openNewTab =
            document.getElementById(
                "openDocumentNewTab"
            );


        let currentFile =
            "";


        /* =====================================
           OPEN PREVIEW
        ===================================== */

        previewButtons.forEach(
            function (button) {

                button.addEventListener(
                    "click",
                    function () {

                        const title =
                            this.dataset.title;


                        const file =
                            this.dataset.file;


                        currentFile =
                            file;


                        previewTitle.textContent =
                            title;


                        previewFilename.textContent =
                            file;


                        /*
                         * Untuk frontend sementara
                         * kita belum punya file PDF.
                         *
                         * Karena itu iframe
                         * disembunyikan dan
                         * placeholder ditampilkan.
                         *
                         * Nanti ketika Laravel
                         * sudah memberikan URL file,
                         * cukup:
                         *
                         * previewFrame.src = fileUrl;
                         */

                        previewFrame.style.display =
                            "none";


                        previewPlaceholder.style.display =
                            "flex";


                        previewModal
                            .classList
                            .add(
                                "active"
                            );


                        document.body.style.overflow =
                            "hidden";

                    }
                );

            }
        );



        /* =====================================
           CLOSE PREVIEW
        ===================================== */

        function closePreviewModal() {

            previewModal
                .classList
                .remove(
                    "active"
                );


            document.body.style.overflow =
                "";


            previewFrame.src =
                "";


            currentFile =
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



        /* =====================================
           OPEN NEW TAB
        ===================================== */

        openNewTab.addEventListener(
            "click",
            function () {

                /*
                 * Frontend simulation.
                 *
                 * Saat backend sudah aktif:
                 *
                 * window.open(
                 *     currentFileUrl,
                 *     "_blank"
                 * );
                 */

                if (
                    currentFile === ""
                ) {
                    return;
                }


                alert(
                    "File " +
                    currentFile +
                    " nantinya akan dibuka di tab baru setelah terhubung dengan backend."
                );

            }
        );

    }
);