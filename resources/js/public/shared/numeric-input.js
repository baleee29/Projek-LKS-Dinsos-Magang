"use strict";

/*
 * Isian angka yang ramah ponsel (docs/plans/isian-angka-mobile.md).
 * Setiap <input data-numeric="nik|kk|phone|count"> otomatis mendapat keyboard angka, pembersihan karakter
 * saat mengetik/menempel, dan (untuk NIK/KK) penghitung digit serta tanda lengkap 16 digit.
 * Formulir dashboard dirender ulang lewat innerHTML, jadi kolom baru dipasang oleh MutationObserver.
 */
window.SidayaNumericInput = (() => {
    const kinds = {
        nik: { mode: "numeric", length: 16, label: "NIK" },
        kk: { mode: "numeric", length: 16, label: "Nomor KK" },
        phone: { mode: "tel" },
        count: { mode: "numeric", min: 1, max: 1000000 },
    };
    const enhanced = new WeakSet();

    function clean(value, kind) {
        const text = String(value ?? "");
        if (kind === "phone") {
            const plus = text.trimStart().startsWith("+") ? "+" : "";
            return plus + text.replace(/\D/g, "").slice(0, 15);
        }
        const digits = text.replace(/\D/g, "");
        const length = kinds[kind]?.length;
        return length ? digits.slice(0, length) : digits.slice(0, 7);
    }

    const phoneOk = (value) => /^(?:08\d{8,11}|\+?628\d{8,11})$/.test(value);

    function countError(value) {
        if (value === "") return "";
        const number = Number(value);
        return number >= kinds.count.min && number <= kinds.count.max
            ? ""
            : "Isi jumlah peserta antara 1 dan 1.000.000 orang.";
    }

    function status(input) {
        return input._numericStatus;
    }

    function render(input, { leaving = false } = {}) {
        const kind = input.dataset.numeric;
        const node = status(input);
        if (!node) return;
        const value = input.value;
        let text = "";
        let state = "";
        if (kind === "nik" || kind === "kk") {
            const { length, label } = kinds[kind];
            const name = input.dataset.numericLabel || label;
            if (!value) text = input.dataset.numericHint || `${length} digit sesuai dokumen`;
            else if (value.length === length) {
                text = `${name} lengkap (${length} digit)`;
                state = "complete";
            } else if (leaving) {
                text = `${name} kurang ${length - value.length} digit`;
                state = "incomplete";
            } else text = `${value.length}/${length} digit`;
        } else if (kind === "phone") {
            if (value && phoneOk(value)) {
                text = "Format nomor sesuai";
                state = "complete";
            } else if (value && leaving) {
                text = "Gunakan format 08xxxxxxxxxx atau +628xxxxxxxxxx.";
                state = "incomplete";
            } else text = input.dataset.numericHint || "Contoh: 081234567890";
        } else if (kind === "count") {
            const error = countError(value);
            if (value && error && leaving) {
                text = error;
                state = "incomplete";
            } else if (value && !error)
                text = `± ${Number(value).toLocaleString("id-ID")} orang`;
            else text = input.dataset.numericHint || "Isi angka saja, misalnya 50.";
        }
        node.querySelector("[data-numeric-text]").textContent = text;
        node.dataset.state = state;
        input.classList.toggle("is-numeric-complete", state === "complete" && kind !== "phone");
        input.classList.toggle("is-numeric-incomplete", state === "incomplete");
        if (state === "incomplete") input.setAttribute("aria-invalid", "true");
        else if (input.getAttribute("aria-invalid") === "true" && input.dataset.numericOwnsInvalid)
            input.setAttribute("aria-invalid", "false");
        input.dataset.numericOwnsInvalid = state === "incomplete" ? "1" : "";
        /* Umumkan hanya saat lengkap atau saat peringatan muncul, bukan di setiap ketukan. */
        const live = node.querySelector("[data-numeric-live]");
        const announce = state ? text : "";
        if (live.textContent !== announce) live.textContent = announce;
    }

    function enhance(input) {
        const kind = input.dataset.numeric;
        if (enhanced.has(input) || !kinds[kind]) return;
        enhanced.add(input);
        const config = kinds[kind];
        input.setAttribute("inputmode", config.mode);
        input.setAttribute("enterkeyhint", "next");
        if (kind === "phone") {
            input.type = "tel";
            input.setAttribute("autocomplete", "tel");
        } else {
            if (input.type === "number") input.type = "text";
            input.setAttribute("pattern", "[0-9]*");
            input.setAttribute("autocomplete", "off");
            input.removeAttribute("min");
            input.removeAttribute("max");
            input.removeAttribute("step");
        }
        if (config.length) input.setAttribute("maxlength", String(config.length));

        const node = document.createElement("span");
        node.className = `numeric-status numeric-status-${kind}`;
        node.id = `${input.id || `numeric-${Math.random().toString(36).slice(2)}`}-numeric`;
        node.innerHTML =
            '<span class="numeric-status-icon" aria-hidden="true"></span><span data-numeric-text aria-live="off"></span><span class="numeric-sr" data-numeric-live aria-live="polite"></span>';
        input.insertAdjacentElement("afterend", node);
        input.setAttribute(
            "aria-describedby",
            [input.getAttribute("aria-describedby"), node.id].filter(Boolean).join(" "),
        );
        input._numericStatus = node;

        const sanitize = () => {
            const before = input.value;
            const next = clean(before, kind);
            if (next !== before) {
                const caret = input.selectionStart ?? before.length;
                const kept = clean(before.slice(0, caret), kind).length;
                input.value = next;
                try {
                    input.setSelectionRange(kept, kept);
                } catch {
                    /* Beberapa tipe input tidak mendukung seleksi. */
                }
            }
            render(input);
        };
        input.addEventListener("input", sanitize);
        input.addEventListener("blur", () => render(input, { leaving: true }));
        input.addEventListener("focus", () => {
            if (input.classList.contains("is-numeric-incomplete")) render(input);
        });
        if (input.value !== clean(input.value, kind)) input.value = clean(input.value, kind);
        render(input);
    }

    function scan(root) {
        if (root.matches?.("input[data-numeric]")) enhance(root);
        root.querySelectorAll?.("input[data-numeric]").forEach(enhance);
    }

    if (typeof document !== "undefined") {
        const start = () => {
            scan(document);
            new MutationObserver((records) =>
                records.forEach((record) =>
                    record.addedNodes.forEach((node) => node.nodeType === 1 && scan(node)),
                ),
            ).observe(document.body, { childList: true, subtree: true });
        };
        if (document.body) start();
        else document.addEventListener("DOMContentLoaded", start, { once: true });
    }

    return { clean, phoneOk, countError, enhance, scan };
})();
