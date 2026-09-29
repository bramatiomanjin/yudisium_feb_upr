(function (window) {
    "use strict";

    const controllers = new WeakMap();
    const focusableSelector = [
        "a[href]",
        "button:not([disabled])",
        "input:not([disabled]):not([type='hidden'])",
        "select:not([disabled])",
        "textarea:not([disabled])",
        "[tabindex]:not([tabindex='-1'])"
    ].join(",");

    function enhance(modal, onRequestClose) {
        if (!modal) {
            return null;
        }

        if (controllers.has(modal)) {
            const existing = controllers.get(modal);
            existing.onRequestClose = onRequestClose || existing.onRequestClose;
            return existing;
        }

        const controller = {
            active: false,
            previousFocus: null,
            onRequestClose
        };

        function getFocusableElements() {
            return Array.from(
                modal.querySelectorAll(focusableSelector)
            ).filter(function (element) {
                return element.offsetParent !== null;
            });
        }

        modal.addEventListener("keydown", function (event) {
            if (!controller.active) {
                return;
            }

            if (event.key === "Escape" && controller.onRequestClose) {
                event.preventDefault();
                controller.onRequestClose();
                return;
            }

            if (event.key !== "Tab") {
                return;
            }

            const focusable = getFocusableElements();

            if (focusable.length === 0) {
                event.preventDefault();
                modal.focus();
                return;
            }

            const first = focusable[0];
            const last = focusable[focusable.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        });

        document.addEventListener("focusin", function (event) {
            if (
                !controller.active ||
                modal.contains(event.target)
            ) {
                return;
            }

            (getFocusableElements()[0] || modal).focus();
        });

        controllers.set(modal, controller);
        return controller;
    }

    function open(modal, trigger) {
        const controller = enhance(modal);

        if (!controller) {
            return;
        }

        controller.previousFocus = trigger || document.activeElement;
        controller.active = true;
        modal.setAttribute("aria-hidden", "false");

        window.requestAnimationFrame(function () {
            const preferred = modal.querySelector(
                "[data-modal-initial-focus]"
            );
            const target =
                preferred?.offsetParent !== null
                    ? preferred
                    : Array.from(
                        modal.querySelectorAll(focusableSelector)
                    ).find(function (element) {
                        return element.offsetParent !== null;
                    });

            (target || modal).focus();
        });
    }

    function close(modal) {
        const controller = controllers.get(modal);

        if (!controller) {
            return;
        }

        controller.active = false;
        modal.setAttribute("aria-hidden", "true");

        if (
            controller.previousFocus &&
            document.contains(controller.previousFocus)
        ) {
            controller.previousFocus.focus();
        }

        controller.previousFocus = null;
    }

    window.YudisiumModalAccessibility = {
        enhance,
        open,
        close
    };
})(window);
