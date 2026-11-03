/* Shared information dialog for the Blade public footer. */
(() => {
    "use strict";
    const footer = document.querySelector(".site-footer");
    if (!footer) return;
    let dialog;
    let trigger;
    footer.addEventListener("click", (event) => {
        const button = event.target.closest("[data-content-message]");
        if (!button) return;
        if (!dialog) {
            dialog = document.createElement("dialog");
            dialog.className = "site-chrome-dialog";
            dialog.setAttribute("aria-labelledby", "site-chrome-dialog-title");
            dialog.setAttribute(
                "aria-describedby",
                "site-chrome-dialog-description",
            );
            dialog.innerHTML =
                '<h2 id="site-chrome-dialog-title"></h2><p id="site-chrome-dialog-description"></p><form method="dialog"><button autofocus>Tutup</button></form>';
            document.body.append(dialog);
            dialog.addEventListener("close", () => trigger?.focus());
            dialog.addEventListener("click", (click) => {
                if (click.target !== dialog) return;
                const bounds = dialog.getBoundingClientRect();
                if (
                    click.clientX < bounds.left ||
                    click.clientX > bounds.right ||
                    click.clientY < bounds.top ||
                    click.clientY > bounds.bottom
                )
                    dialog.close();
            });
        }
        trigger = button;
        dialog.querySelector("h2").textContent = button.dataset.contentTitle;
        dialog.querySelector("p").textContent = button.dataset.contentMessage;
        dialog.showModal();
    });
})();
