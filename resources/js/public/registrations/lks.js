// Pendaftaran LKS: isian dan berkas dikirim ke server, tidak disimpan di browser.
// Hasilnya disampaikan Admin melalui WhatsApp.
import { isWhatsAppNumber, lksChoiceOptions } from './lks-rules.js';

(() => {
    "use strict";

    const form = document.getElementById("form-lks");
    if (!form) return;

    const fieldNames = [
        "lks-nama",
        "lks-ketua",
        "lks-whatsapp",
        "lks-izin",
        "lks-rekening",
        "lks-fokus",
    ];
    const controls = Object.fromEntries(
        fieldNames.map((name) => [name, form.elements.namedItem(name)]),
    );
    const errorSummary = document.getElementById("form-error-banner");
    const formView = document.getElementById("view-form-grid");
    const summaryView = document.getElementById("view-summary");
    const successView = document.getElementById("view-success");
    const documentSummary = document.getElementById("ringkasan-dokumen");
    const demoButton = document.getElementById("btn-fill-demo");
    const submitButton = document.getElementById("btn-review-submit");
    const confirmDialog = document.getElementById("modal-konfirmasi");
    const fileDialog = document.getElementById("modal-file-preview");
    const formatDialog = document.getElementById("modal-format-preview");
    const filePreview = document.getElementById("file-preview-content");
    const formatPreview = document.getElementById("format-preview-content");
    const returnFocus = new WeakMap();
    let previewUrl = null;
    let formatPreviewUrl = null;
    let validationAttempted = false;
    let reviewedData = null;
    const choiceLabels = {
        "lks-prioritas-pelayanan": "Prioritas Pelayanan",
        "lks-jenis-pelayanan": "Jenis Pelayanan",
        "lks-sasaran-pelayanan": "Sasaran Pelayanan",
        "lks-jenis-kegiatan": "Jenis Kegiatan",
        "lks-sifat-pelayanan": "Sifat Pelayanan",
    };
    const choiceOptions = lksChoiceOptions();
    const choiceGroups = Object.fromEntries(
        Object.keys(choiceLabels).map((name) => [
            name,
            document.getElementById(name),
        ]),
    );
    const valueNames = [...fieldNames, ...Object.keys(choiceLabels)];

    const { definitions, pdfPreview, examplePdf } = window.SidayaLKSRegistrationDocuments;
    const attachments = definitions.map((definition) => {
        const wrapper = form.querySelector(
            '[data-lks-attachment="' + definition.key + '"]',
        );
        const inputId =
            definition.inputId || "lks-" + definition.key + "-input";
        const input = document.getElementById(inputId);
        return {
            ...definition,
            inputId,
            wrapper,
            input,
            empty: wrapper?.querySelector("[data-file-empty]"),
            selected: wrapper?.querySelector("[data-file-selected]"),
            name: wrapper?.querySelector("[data-file-name]"),
            size: wrapper?.querySelector("[data-file-size]"),
            status: wrapper?.querySelector("[data-file-status]"),
            errorElement: wrapper?.querySelector("[data-file-error]"),
            file: null,
            isExample: false,
            error: "",
        };
    });
    const completeMarkup =
        fieldNames.every((name) => controls[name]) &&
        Object.values(choiceGroups).every((group) =>
            group?.querySelector('input[type="checkbox"]'),
        ) &&
        attachments.every(
            (attachment) =>
                attachment.wrapper &&
                attachment.input?.type === "file" &&
                attachment.wrapper.contains(attachment.input) &&
                Number(attachment.wrapper.dataset.maxMb) === attachment.maxMb &&
                attachment.wrapper.dataset.label?.trim() &&
                attachment.empty &&
                !attachment.empty.contains(attachment.input) &&
                attachment.selected &&
                attachment.name &&
                attachment.size &&
                attachment.status &&
                attachment.errorElement?.id === "err-" + attachment.inputId,
        );
    if (
        !completeMarkup ||
        !errorSummary ||
        !formView ||
        !summaryView ||
        !successView ||
        !documentSummary ||
        !submitButton
    )
        return;

    const allNames = [
        ...valueNames,
        ...attachments.map((attachment) => attachment.inputId),
    ];
    const typeByExtension = {
        pdf: "application/pdf",
        jpg: "image/jpeg",
        jpeg: "image/jpeg",
        png: "image/png",
    };
    attachments.forEach((attachment) => {
        attachment.label = attachment.wrapper.dataset.label.trim();
    });

    let requestId = null;

    function values() {
        return {
            ...Object.fromEntries(
                fieldNames.map((name) => [name, controls[name].value.trim()]),
            ),
            ...Object.fromEntries(
                Object.entries(choiceGroups).map(([name, group]) => [
                    name,
                    [
                        ...group.querySelectorAll(
                            'input[type="checkbox"]:checked',
                        ),
                    ].map((input) => input.value),
                ]),
            ),
        };
    }

    function checkFile(attachment, candidate) {
        if (!candidate) return "";
        if (!candidate.size)
            return "Berkas kosong. Pilih dokumen yang berisi data.";
        if (candidate.size > attachment.maxMb * 1024 * 1024)
            return "Ukuran berkas maksimal " + attachment.maxMb + " MB.";
        const extension = candidate.name.split(".").pop().toLowerCase();
        const expectedType = typeByExtension[extension];
        if (
            !expectedType ||
            (candidate.type && candidate.type !== expectedType)
        )
            return "Pilih berkas PDF, JPG, atau PNG.";
        return "";
    }

    function validate(currentValues = values()) {
        const errors = {};
        if (
            !currentValues["lks-nama"] ||
            currentValues["lks-nama"].length > 150
        )
            errors["lks-nama"] =
                "Isi nama Yayasan / LKS, maksimal 150 karakter.";
        if (
            !currentValues["lks-ketua"] ||
            currentValues["lks-ketua"].length > 150
        )
            errors["lks-ketua"] =
                "Isi nama pengisi data atau penanggung jawab, maksimal 150 karakter.";
        if (!isWhatsAppNumber(currentValues["lks-whatsapp"]))
            errors["lks-whatsapp"] =
                "Isi nomor WhatsApp yang bisa dihubungi dengan awalan 08, 62, atau +62.";
        if (currentValues["lks-izin"].length > 150)
            errors["lks-izin"] =
                "Nomor izin operasional lama maksimal 150 karakter.";
        if (
            !currentValues["lks-rekening"] ||
            currentValues["lks-rekening"].length > 150
        )
            errors["lks-rekening"] =
                "Isi rekening bank LKS, maksimal 150 karakter.";
        if (
            !currentValues["lks-fokus"] ||
            currentValues["lks-fokus"].length > 2000
        )
            errors["lks-fokus"] =
                "Isi fokus pelayanan, maksimal 2.000 karakter.";
        for (const [name, label] of Object.entries(choiceLabels)) {
            const selected = currentValues[name];
            if (
                !Array.isArray(selected) ||
                !selected.length ||
                selected.some((value) => !choiceOptions[name].includes(value))
            )
                errors[name] =
                    "Pilih sedikitnya satu pilihan pada " + label + ".";
        }
        attachments.forEach((attachment) => {
            const problem =
                attachment.error || checkFile(attachment, attachment.file);
            if (problem)
                errors[attachment.inputId] = attachment.label + ": " + problem;
            else if (!attachment.file)
                errors[attachment.inputId] =
                    attachment.label + " wajib dilampirkan.";
        });
        return errors;
    }

    function setFieldError(name, message = "") {
        document
            .getElementById(name)
            .setAttribute("aria-invalid", String(Boolean(message)));
        const error = document.getElementById("err-" + name);
        if (!error) return;
        error.textContent = message;
        error.hidden = !message;
    }

    function refreshErrors(focus = false) {
        const errors = validationAttempted
            ? validate()
            : Object.fromEntries(
                  attachments
                      .filter((attachment) => attachment.error)
                      .map((attachment) => [
                          attachment.inputId,
                          attachment.label + ": " + attachment.error,
                      ]),
              );
        allNames.forEach((name) => setFieldError(name, errors[name]));
        attachments.forEach((attachment) => {
            attachment.wrapper.classList.toggle(
                "has-error",
                Boolean(errors[attachment.inputId]),
            );
            attachment.wrapper
                .querySelectorAll("[data-file-clear]")
                .forEach((button) => {
                    button.hidden = !errors[attachment.inputId];
                });
            if (!attachment.file)
                attachment.status.textContent = errors[attachment.inputId]
                    ? "Periksa berkas"
                    : "Belum ada berkas";
        });
        errorSummary.replaceChildren();
        errorSummary.hidden =
            !validationAttempted || !Object.keys(errors).length;
        if (errorSummary.hidden) return;
        const heading = document.createElement("p");
        heading.textContent = "Periksa isian berikut sebelum melanjutkan.";
        const list = document.createElement("ul");
        Object.entries(errors).forEach(([name, message]) => {
            const item = document.createElement("li");
            const link = document.createElement("a");
            link.href = "#" + name;
            link.textContent = message;
            link.addEventListener("click", (event) => {
                event.preventDefault();
                document.getElementById(name).focus();
            });
            item.append(link);
            list.append(item);
        });
        errorSummary.append(heading, list);
        if (focus) errorSummary.focus();
    }

    function fileSize(bytes) {
        if (bytes < 1024) return bytes + " B";
        const isMb = bytes >= 1024 * 1024;
        const size = bytes / (isMb ? 1024 * 1024 : 1024);
        return (
            size.toLocaleString("id-ID", {
                maximumFractionDigits: isMb ? 2 : 1,
            }) + (isMb ? " MB" : " KB")
        );
    }

    function fileStatus(attachment) {
        return (
            fileSize(attachment.file.size) +
            " · " +
            (attachment.isExample
                ? "Dokumen contoh"
                : "Terpilih untuk pengajuan")
        );
    }

    function revokePreview() {
        filePreview?.replaceChildren();
        if (previewUrl) URL.revokeObjectURL(previewUrl);
        previewUrl = null;
    }

    function revokeFormatPreview() {
        formatPreview?.replaceChildren();
        if (formatPreviewUrl) URL.revokeObjectURL(formatPreviewUrl);
        formatPreviewUrl = null;
    }

    function closeFilePreview() {
        if (fileDialog?.open) fileDialog.close();
        revokePreview();
    }

    function renderAttachment(attachment) {
        const selected = Boolean(attachment.file);
        attachment.empty.hidden = selected;
        attachment.selected.hidden = !selected;
        attachment.wrapper.classList.toggle("has-file", selected);
        attachment.name.textContent = selected ? attachment.file.name : "";
        attachment.size.textContent = selected
            ? fileSize(attachment.file.size)
            : "";
        attachment.status.textContent = selected
            ? attachment.isExample
                ? "Contoh dokumen"
                : "Terpilih"
            : "Belum ada berkas";
    }

    function selectFile(attachment, candidate, isExample = false, issue = "") {
        closeFilePreview();
        reviewedData = null;
        attachment.error = issue || checkFile(attachment, candidate);
        attachment.file = attachment.error ? null : candidate;
        attachment.isExample = Boolean(attachment.file && isExample);
        // Generated or dropped Files stay in memory; the novalidate form uses custom review validation.
        if (!attachment.file || isExample) attachment.input.value = "";
        renderAttachment(attachment);
        refreshErrors();
    }

    function clearAttachment(attachment, focus = true) {
        attachment.input.value = "";
        selectFile(attachment, null);
        if (focus) attachment.input.focus();
    }

    function showView(view) {
        formView.hidden = view !== "form";
        summaryView.hidden = view !== "summary";
        successView.hidden = view !== "success";
        if (demoButton) demoButton.hidden = view !== "form";
        const target =
            view === "form"
                ? controls["lks-nama"]
                : document.getElementById(
                      view === "summary" ? "summary-title" : "success-title",
                  );
        target?.focus();
    }

    function openDialog(dialog) {
        if (!dialog || typeof dialog.showModal !== "function") return false;
        if (dialog.open) return true;
        returnFocus.set(dialog, document.activeElement);
        dialog.showModal();
        document.body.classList.add("dialog-open");
        return true;
    }

    [confirmDialog, fileDialog, formatDialog]
        .filter(Boolean)
        .forEach((dialog) => {
            dialog
                .querySelectorAll("[data-close-dialog]")
                .forEach((button) =>
                    button.addEventListener("click", () => dialog.close()),
                );
            dialog.addEventListener("click", (event) => {
                if (event.target !== dialog) return;
                const bounds = dialog.getBoundingClientRect();
                if (
                    event.clientX < bounds.left ||
                    event.clientX > bounds.right ||
                    event.clientY < bounds.top ||
                    event.clientY > bounds.bottom
                )
                    dialog.close();
            });
            dialog.addEventListener("close", () => {
                const anotherDialogOpen =
                    document.querySelector("dialog[open]");
                if (!anotherDialogOpen)
                    document.body.classList.remove("dialog-open");
                if (dialog === fileDialog) revokePreview();
                if (dialog === formatDialog) revokeFormatPreview();
                const target = returnFocus.get(dialog);
                if (
                    !anotherDialogOpen &&
                    target?.isConnected &&
                    !target.disabled &&
                    !target.closest("[hidden]")
                )
                    target.focus();
                returnFocus.delete(dialog);
            });
        });


    function setText(id, text) {
        const element = document.getElementById(id);
        if (element) element.textContent = text;
    }

    function previewAttachment(attachment) {
        if (
            !attachment.file ||
            !filePreview ||
            typeof fileDialog?.showModal !== "function"
        )
            return;
        revokePreview();
        const filename = attachment.file.name;
        setText("file-preview-title", "Pratinjau " + attachment.label);
        setText("file-preview-name", filename);
        setText("file-preview-size", fileStatus(attachment));
        previewUrl = URL.createObjectURL(attachment.file);
        if (/\.pdf$/i.test(filename))
            pdfPreview(
                filePreview,
                previewUrl,
                "Pratinjau " + filename,
                filename,
            );
        else {
            const image = document.createElement("img");
            image.src = previewUrl;
            image.alt = "Pratinjau dokumen " + filename;
            filePreview.append(image);
        }
        openDialog(fileDialog);
    }

    attachments.forEach((attachment) => {
        attachment.input.addEventListener("change", () => {
            const candidates = attachment.input.files;
            const issue =
                candidates.length > 1
                    ? "Pilih satu berkas untuk dokumen ini."
                    : "";
            selectFile(attachment, candidates[0] || null, false, issue);
        });
        attachment.wrapper
            .querySelectorAll("[data-file-view]")
            .forEach((button) =>
                button.addEventListener("click", () =>
                    previewAttachment(attachment),
                ),
            );
        attachment.wrapper
            .querySelectorAll("[data-file-replace]")
            .forEach((button) =>
                button.addEventListener("click", () =>
                    attachment.input.click(),
                ),
            );
        attachment.wrapper
            .querySelectorAll("[data-file-remove], [data-file-clear]")
            .forEach((button) =>
                button.addEventListener("click", () =>
                    clearAttachment(attachment),
                ),
            );
        attachment.wrapper.addEventListener("dragover", (event) => {
            event.preventDefault();
            if (event.dataTransfer) event.dataTransfer.dropEffect = "copy";
            attachment.wrapper.classList.add("is-dragging");
        });
        attachment.wrapper.addEventListener("dragleave", (event) => {
            if (!attachment.wrapper.contains(event.relatedTarget))
                attachment.wrapper.classList.remove("is-dragging");
        });
        attachment.wrapper.addEventListener("drop", (event) => {
            event.preventDefault();
            attachment.wrapper.classList.remove("is-dragging");
            const candidates = event.dataTransfer?.files;
            if (!candidates?.length) return;
            attachment.input.value = "";
            const issue =
                candidates.length !== 1
                    ? "Pilih satu berkas untuk dokumen ini."
                    : "";
            selectFile(attachment, issue ? null : candidates[0], false, issue);
        });
    });

    document.querySelectorAll("a[data-lks-format]").forEach((link) => {
        link.addEventListener("click", async (event) => {
            if (!formatPreview || typeof formatDialog?.showModal !== "function")
                return;
            const url = link.getAttribute("href");
            if (!url) return;
            event.preventDefault();
            const title =
                link.dataset.formatTitle || "Format Berkas Pendaftaran";
            const filename = url.split("/").pop().split(/[?#]/)[0];
            revokeFormatPreview();
            setText("format-preview-title", title);
            const download = document.getElementById("format-download-link");
            if (download) {
                download.href = url;
                download.download = filename;
            }
            openDialog(formatDialog);

            const loading = document.createElement("p");
            loading.style.padding = "2rem";
            loading.style.textAlign = "center";
            loading.style.color = "var(--lksr-muted, #64748b)";
            loading.textContent = "Memuat pratinjau berkas...";
            formatPreview.append(loading);

            try {
                const response = await fetch(url);
                if (!response.ok) throw new Error("Gagal mengambil berkas PDF");
                const blob = await response.blob();
                if (!formatDialog.open) return;
                const pdfBlob = new Blob([blob], { type: "application/pdf" });
                formatPreviewUrl = URL.createObjectURL(pdfBlob);
                formatPreview.replaceChildren();
                pdfPreview(formatPreview, formatPreviewUrl, title, filename);
            } catch (err) {
                if (!formatDialog.open) return;
                formatPreview.replaceChildren();
                pdfPreview(formatPreview, url, title, filename);
            }
        });
    });

    fieldNames.forEach((name) =>
        controls[name].addEventListener("input", () => {
            reviewedData = null;
            refreshErrors();
        }),
    );
    Object.values(choiceGroups).forEach((group) =>
        group.addEventListener("change", () => {
            reviewedData = null;
            refreshErrors();
        }),
    );


    demoButton?.addEventListener("click", () => {
        validationAttempted = false;
        controls["lks-nama"].value = "LKS Harapan Mulia Banjarbaru";
        controls["lks-ketua"].value = "Hj. Nurhayati, S.Sos";
        controls["lks-whatsapp"].value = "0800 0000 9876";
        controls["lks-izin"].value = "DEMO-00123/2020";
        controls["lks-rekening"].value =
            "Bank Contoh - 000.00.00.00000 a.n Yayasan Harapan Mulia";
        controls["lks-fokus"].value =
            "Pelayanan dan pendampingan sosial bagi anak telantar, lansia potensial, dan keluarga rentan di Kota Banjarbaru.";
        const exampleChoices = {
            "lks-prioritas-pelayanan": ["Keterlantaran", "Kemiskinan"],
            "lks-jenis-pelayanan": [
                "Rehabilitasi Sosial",
                "Pemberdayaan Sosial",
            ],
            "lks-sasaran-pelayanan": ["Perseorangan", "Keluarga"],
            "lks-jenis-kegiatan": [
                "Unit pelayanan sosial untuk anak",
                "Unit pelayanan sosial untuk lansia",
            ],
            "lks-sifat-pelayanan": ["Dalam LKS", "Luar LKS"],
        };
        Object.entries(choiceGroups).forEach(([name, group]) =>
            group
                .querySelectorAll('input[type="checkbox"]')
                .forEach((input) => {
                    input.checked = exampleChoices[name].includes(input.value);
                }),
        );
        attachments.forEach((attachment) =>
            selectFile(attachment, examplePdf(attachment), true),
        );
        refreshErrors();
        controls["lks-nama"].focus();
    });

    function writeSummary(currentValues) {
        Object.entries({
            "ringkasan-nama": currentValues["lks-nama"],
            "ringkasan-ketua": currentValues["lks-ketua"],
            "ringkasan-whatsapp": currentValues["lks-whatsapp"],
            "ringkasan-izin": currentValues["lks-izin"] || "Tidak diisi",
            "ringkasan-rekening":
                currentValues["lks-rekening"] || "Tidak diisi",
            "ringkasan-fokus": currentValues["lks-fokus"],
            ...Object.fromEntries(
                Object.keys(choiceLabels).map((name) => [
                    "ringkasan-" + name.slice(4),
                    currentValues[name].join("; "),
                ]),
            ),
        }).forEach(([id, text]) => setText(id, text));
        documentSummary.replaceChildren();
        attachments.forEach((attachment) => {
            const row = document.createElement("div");
            const label = document.createElement("dt");
            label.textContent = attachment.label;
            const detail = document.createElement("dd");
            const filename = document.createElement("strong");
            filename.textContent = attachment.file.name;
            const status = document.createElement("span");
            status.textContent = fileStatus(attachment);
            detail.append(filename, status);
            row.append(label, detail);
            documentSummary.append(row);
        });
        reviewedData = {
            values: currentValues,
            documents: attachments.map((attachment) => ({
                key: attachment.key,
                file: attachment.file,
            })),
        };
    }

    function prepareReview() {
        validationAttempted = true;
        const currentValues = values();
        const errors = validate(currentValues);
        if (Object.keys(errors).length) {
            showView("form");
            refreshErrors(true);
            return false;
        }
        fieldNames.forEach((name) => {
            controls[name].value = currentValues[name];
        });
        refreshErrors();
        writeSummary(currentValues);
        return true;
    }

    form.addEventListener("submit", (event) => {
        event.preventDefault();
        if (prepareReview()) showView("summary");
    });

    document
        .getElementById("btn-back-to-edit")
        ?.addEventListener("click", () => {
            reviewedData = null;
            showView("form");
        });
    document.getElementById("btn-confirm")?.addEventListener("click", () => {
        if (prepareReview()) openDialog(confirmDialog);
    });
    document
        .getElementById("btn-proceed")
        ?.addEventListener("click", async (event) => {
            const proceed = event.currentTarget;
            const currentValues = values();
            const sameReview =
                reviewedData &&
                valueNames.every(
                    (name) =>
                        JSON.stringify(currentValues[name]) ===
                        JSON.stringify(reviewedData.values[name]),
                ) &&
                attachments.every((attachment) =>
                    reviewedData.documents.some(
                        (item) =>
                            item.key === attachment.key &&
                            item.file === attachment.file,
                    ),
                );
            if (!sameReview || Object.keys(validate(currentValues)).length) {
                confirmDialog?.close();
                if (prepareReview()) showView("summary");
                return;
            }
            // request_id tetap sama bila dikirim ulang, supaya tidak tercatat ganda.
            requestId ??= crypto.randomUUID();
            const body = new FormData();
            body.set("request_id", requestId);
            body.set("website", form.elements.namedItem("website")?.value || "");
            Object.entries(reviewedData.values).forEach(([name, value]) => {
                if (Array.isArray(value)) value.forEach((item) => body.append(name + "[]", item));
                else body.set(name, value);
            });
            attachments.forEach((attachment) => {
                body.set("files[" + attachment.key + "]", attachment.file);
                body.set("file_labels[" + attachment.key + "]", attachment.wrapper.dataset.label.trim());
            });
            proceed.disabled = true;
            let result;
            try {
                result = await fetch("/pendaftaran/mitra", {
                    method: "POST",
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content || "",
                    },
                    body,
                });
            } catch {
                result = null;
            }
            proceed.disabled = false;
            confirmDialog?.close();
            if (!result?.ok) {
                const data = result ? await result.json().catch(() => ({})) : {};
                const detail = Object.values(data.errors || {})[0]?.[0] || data.message;
                errorSummary.textContent = result?.status === 429
                    ? "Terlalu banyak percobaan. Tunggu sebentar, lalu kirim lagi."
                    : detail || "Pendaftaran belum terkirim. Periksa koneksi, lalu coba lagi.";
                errorSummary.hidden = false;
                errorSummary.focus?.();
                return;
            }
            requestId = null;
            setText("success-reference", "Menunggu verifikasi Admin");
            setText("success-nama", reviewedData.values["lks-nama"]);
            setText("success-berkas", attachments.length + " dokumen persyaratan terkirim");
            setText(
                "success-permohonan",
                attachments.find((attachment) => attachment.key === "surat_permohonan")?.file?.name ||
                    attachments[attachments.length - 1]?.file?.name ||
                    "",
            );
            setText(
                "success-time",
                new Intl.DateTimeFormat("id-ID", {
                    timeZone: "Asia/Makassar",
                    day: "numeric",
                    month: "long",
                    year: "numeric",
                    hour: "2-digit",
                    minute: "2-digit",
                }).format(new Date()) + " WITA",
            );
            showView("success");
        });

    function resetForm() {
        [confirmDialog, fileDialog, formatDialog]
            .filter((dialog) => dialog?.open)
            .forEach((dialog) => dialog.close());
        closeFilePreview();
        form.reset();
        validationAttempted = false;
        reviewedData = null;
        submitButton.textContent = "Periksa Data";
        attachments.forEach((attachment) => clearAttachment(attachment, false));
        refreshErrors();
        documentSummary.replaceChildren();
        [
            "ringkasan-nama",
            "ringkasan-ketua",
            "ringkasan-whatsapp",
            "ringkasan-izin",
            "ringkasan-fokus",
            ...Object.keys(choiceLabels).map(
                (name) => "ringkasan-" + name.slice(4),
            ),
            "success-nama",
            "success-berkas",
            "success-permohonan",
            "success-time",
        ].forEach((id) => setText(id, ""));
        showView("form");
    }

    ["btn-reset", "btn-reset-form"].forEach((id) =>
        document.getElementById(id)?.addEventListener("click", (event) => {
            event.preventDefault();
            resetForm();
        }),
    );
    window.addEventListener("pagehide", revokePreview);
    attachments.forEach(renderAttachment);
    refreshErrors();
    submitButton.disabled = false;
    submitButton.hidden = false;
    const resetButton = document.getElementById("btn-reset-form");
    if (resetButton) {
        resetButton.disabled = false;
        resetButton.hidden = false;
    }
    if (demoButton) {
        demoButton.disabled = false;
        demoButton.hidden = false;
    }
})();
