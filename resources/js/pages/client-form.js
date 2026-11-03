// Interaksi form klien, dari lks/client-form.js: bagian, isian bersyarat, pratinjau, kelengkapan, dan foto.
// TODO(Tahap 2): kirim ke POST /lks/clients; aturan akhir ada di shared/data-validation.js.
import { notify, DEMO_SAVE_MESSAGE } from '../dashboard/toast.js';

const form = document.querySelector('#resident-form');

if (form) {
    const page = document.querySelector('[data-client-form-page]');
    const schema = JSON.parse(document.querySelector('#client-form-schema').textContent);
    const keys = Object.keys(schema);
    const today = form.querySelector('[type="date"]')?.max || '';

    const valueOf = () => {
        const value = {};
        keys.forEach((key) => {
            const control = form.elements.namedItem(key);
            if (!control) return;
            if (schema[key].type === 'checkbox') value[key] = control.checked;
            else value[key] = control.value ?? '';
        });
        return value;
    };
    const filled = (value) => value !== '' && value !== null && value !== undefined && value !== false;

    const ageOf = (value) => {
        if (value.birthDateUnknown) {
            return value.approximateAge === '' ? 'Usia belum diketahui' : `± ${value.approximateAge} tahun (perkiraan)`;
        }
        if (!/^\d{4}-\d{2}-\d{2}$/.test(value.birthDate || '')) return null;
        return Number(today.slice(0, 4)) - Number(value.birthDate.slice(0, 4)) - (today.slice(5) < value.birthDate.slice(5) ? 1 : 0);
    };

    const conditionMet = (rule, value) => {
        if (!rule) return true;
        const [key, expected] = rule.split('=');
        return expected === undefined ? Boolean(value[key]) : value[key] === expected;
    };

    function isRequired(key, value) {
        const field = schema[key];
        if (key === 'educationStatus') {
            const age = ageOf(value);
            return value.category === 'Anak' || (typeof age === 'number' && age < 18);
        }
        if (key === 'approximateAge') return Boolean(value.birthDateUnknown);
        if (key === 'birthDate') return !value.birthDateUnknown;
        if (['exitDate', 'exitReason'].includes(key)) return value.residenceStatus === 'Sudah keluar';
        if (key === 'guardianName' || key === 'guardianRelationship') return value.guardianStatus === 'Keluarga/wali diketahui';
        if (key === 'schoolName' || key === 'educationLevel') return value.educationStatus === 'Sedang sekolah';
        return field.required;
    }

    function fieldError(key, value) {
        const raw = value[key];
        const label = schema[key].label;
        if (isRequired(key, value) && !filled(raw)) return `${label} wajib diisi.`;
        if (!filled(raw) || schema[key].type === 'checkbox') return '';
        if (['nik', 'familyCardNumber'].includes(key) && !/^\d{16}$/.test(raw)) return `${label} harus 16 digit.`;
        if (key === 'healthCoverageNumber' && !/^\d{13}$/.test(raw)) return `${label} harus 13 digit.`;
        if (schema[key].type === 'date' && today && raw > today) return `${label} tidak boleh melewati hari ini.`;
        if (key === 'exitDate' && value.admissionDate && raw < value.admissionDate) return 'Tanggal keluar tidak boleh sebelum tanggal mulai layanan.';
        return '';
    }

    const errorsOf = (value) => {
        const errors = {};
        keys.forEach((key) => {
            const wrapper = page.querySelector(`[data-resident-field="${key}"]`);
            if (wrapper?.hidden) return;
            const message = fieldError(key, value);
            if (message) errors[key] = message;
        });
        return errors;
    };

    function showField(key, message) {
        const node = document.getElementById(`resident-${key}-error`);
        if (node) {
            node.textContent = message || '';
            node.hidden = !message;
        }
        const control = form.elements.namedItem(key);
        const nodes = control instanceof RadioNodeList ? [...control] : control ? [control] : [];
        nodes.forEach((item) => item.setAttribute('aria-invalid', message ? 'true' : 'false'));
    }

    const preview = page.querySelector('[data-resident-preview]');
    const previewTag = page.querySelector('[data-resident-preview-tag]');
    const avatarIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="7" r="3"/><path d="M6 21v-4a6 6 0 0 1 12 0v4M9 21v-5m6 5v-5"/></svg>';
    let photoUrl = '';

    const el = (tag, className, text) => {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined) node.textContent = text;
        return node;
    };

    function dateText(value) {
        if (!value) return '';
        return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeZone: 'Asia/Makassar' }).format(new Date(`${value}T12:00:00+08:00`));
    }

    function renderPreview(value) {
        const identity = el('div', 'lrf-preview-identity');
        const avatar = el('span', 'lrf-preview-avatar');
        avatar.setAttribute('aria-hidden', 'true');
        if (photoUrl) {
            const image = el('img');
            image.src = photoUrl;
            image.alt = 'Foto';
            image.style.cssText = 'width:100%;height:100%;object-fit:cover;border-radius:inherit;';
            avatar.append(image);
        } else {
            avatar.innerHTML = avatarIcon;
        }
        const person = el('div', 'lrf-preview-person');
        person.append(
            el('strong', '', value.name || 'Nama belum diisi'),
            el('span', '', value.code || 'Kode belum diisi'),
            el('small', '', `NIK: ${value.nik || 'Belum dicatat'}`),
        );
        identity.append(avatar, person);

        const age = ageOf(value);
        const ageText = typeof age === 'number' ? `${age} tahun` : (age || 'Usia belum diketahui');
        const pairs = [
            ['Jenis kelamin', value.gender],
            ['Usia & agama', ageText + (value.religion ? ` / ${value.religion}` : '')],
            ['Pendidikan', value.educationStatus === 'Sedang sekolah' ? (value.educationLevel || value.educationStatus) : value.educationStatus],
            ['Layanan asuhan', value.serviceType],
            ['Status layanan', value.residenceStatus],
            ['Mulai menerima layanan', dateText(value.admissionDate)],
        ];
        const list = el('dl', 'lrf-preview-details');
        pairs.forEach(([label, text]) => {
            const row = el('div');
            row.append(el('dt', '', label), el('dd', '', text || 'Belum dicatat'));
            list.append(row);
        });
        const health = el('p', 'lrf-preview-health');
        health.append(el('strong', '', 'BPJS / KIS'), el('span', '', value.healthCoverageStatus || 'Belum dicatat'));
        preview.replaceChildren(identity, list, health);
        previewTag.textContent = value.residenceStatus === 'Sudah keluar' ? 'Sudah keluar' : 'Pratinjau Kartu';
    }

    function renderProgress(value, errors) {
        const required = keys.filter((key) => !page.querySelector(`[data-resident-field="${key}"]`)?.hidden && isRequired(key, value));
        const complete = required.filter((key) => filled(value[key]) && !errors[key]).length;
        let percent = required.length ? Math.floor((complete / required.length) * 100) : 0;
        if (Object.keys(errors).length && percent === 100) percent = 99;
        page.querySelector('[data-resident-progress-text]').textContent = `${percent}%`;
        page.querySelector('[data-resident-progress-bar]').style.width = `${percent}%`;
        const track = page.querySelector('.lrf-progress-track');
        track.setAttribute('aria-valuenow', String(percent));
        track.setAttribute('aria-valuetext', `${complete} dari ${required.length} isian wajib lengkap dan valid`);
        page.querySelector('[data-resident-progress-note]').textContent = `${complete} dari ${required.length} isian wajib lengkap dan valid.${percent === 99 ? ' Periksa juga isian opsional yang belum valid.' : ''}`;

        form.querySelectorAll('fieldset.lrf-card').forEach((card) => {
            const done = card.dataset.sectionFields.split(',').every((key) => !errors[key] && (!isRequired(key, value) || filled(value[key]) || page.querySelector(`[data-resident-field="${key}"]`)?.hidden));
            const row = page.querySelector(`[data-resident-progress-section="${card.id.replace('lrf-section-', '')}"]`);
            row.classList.toggle('is-complete', done);
            row.querySelector('[data-resident-progress-mark]').textContent = done ? '✓' : '○';
        });
        page.querySelectorAll('[data-resident-counter]').forEach((counter) => {
            counter.textContent = `${String(value[counter.dataset.residentCounter] || '').length}/16`;
        });
    }

    let shownErrors = {};

    function refresh() {
        const value = valueOf();
        page.querySelectorAll('[data-conditional]').forEach((wrapper) => {
            wrapper.hidden = !conditionMet(wrapper.dataset.conditional, value);
        });
        page.querySelectorAll('[data-resident-field]').forEach((wrapper) => {
            const marker = wrapper.querySelector('[data-resident-required]');
            if (marker) marker.hidden = !isRequired(wrapper.dataset.residentField, value);
        });
        page.querySelectorAll('.lrf-option input').forEach((input) => {
            input.closest('.lrf-option').classList.toggle('is-selected', input.checked);
        });
        const errors = errorsOf(value);
        Object.keys(shownErrors).forEach((key) => {
            if (!errors[key]) {
                showField(key, '');
                delete shownErrors[key];
            } else {
                showField(key, errors[key]);
                shownErrors[key] = errors[key];
            }
        });
        renderPreview(value);
        renderProgress(value, errors);
    }

    form.addEventListener('input', refresh);
    form.addEventListener('change', refresh);

    form.addEventListener('focusout', (event) => {
        const key = event.target.name;
        if (!key || !schema[key]) return;
        const value = valueOf();
        const message = fieldError(key, value);
        showField(key, message);
        if (message) shownErrors[key] = message;
        else delete shownErrors[key];
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        refresh();
        const value = valueOf();
        const errors = errorsOf(value);
        shownErrors = { ...errors };
        keys.forEach((key) => showField(key, errors[key] || ''));
        const banner = page.querySelector('#resident-form-errors');
        const entries = Object.entries(errors);
        if (entries.length) {
            const heading = el('h2', '', 'Biodata belum disimpan');
            const list = el('ul');
            entries.forEach(([key, message]) => {
                const item = el('li');
                const button = el('button', '', message);
                button.type = 'button';
                button.addEventListener('click', () => {
                    const control = form.elements.namedItem(key);
                    (control instanceof RadioNodeList ? control[0] : control)?.focus();
                });
                item.append(button);
                list.append(item);
            });
            banner.replaceChildren(heading, list);
            banner.hidden = false;
            banner.focus();
            return;
        }
        banner.hidden = true;
        notify(DEMO_SAVE_MESSAGE);
    });

    // Navigasi bagian.
    const steps = [...page.querySelectorAll('[data-resident-section]')];
    const activate = (key) => {
        steps.forEach((step) => {
            const active = step.dataset.residentSection === key;
            step.classList.toggle('is-active', active);
            if (active) step.setAttribute('aria-current', 'step');
            else step.removeAttribute('aria-current');
        });
    };
    steps.forEach((step) => {
        step.addEventListener('click', () => {
            const key = step.dataset.residentSection;
            page.querySelector(`#lrf-section-${key}`).scrollIntoView({ behavior: 'smooth', block: 'start' });
            page.querySelector(`#lrf-title-${key}`).focus({ preventScroll: true });
            activate(key);
        });
    });
    form.addEventListener('focusin', (event) => {
        const card = event.target.closest('fieldset.lrf-card');
        if (card) activate(card.id.replace('lrf-section-', ''));
    });
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            const visible = entries.filter((entry) => entry.isIntersecting).sort((a, b) => b.intersectionRatio - a.intersectionRatio);
            if (visible[0]) activate(visible[0].target.id.replace('lrf-section-', ''));
        }, { rootMargin: '-20% 0px -60% 0px', threshold: [0, 0.25, 0.5] });
        form.querySelectorAll('fieldset.lrf-card').forEach((card) => observer.observe(card));
    }

    // Foto profil: pratinjau lokal saja.
    const photoInput = page.querySelector('#client-photo-input');
    const photoPreview = page.querySelector('#client-photo-preview');
    const photoRemove = page.querySelector('#client-photo-remove');
    const photoError = page.querySelector('#client-photo-error');
    const placeholderMarkup = photoPreview?.innerHTML;

    photoInput?.addEventListener('change', () => {
        const file = photoInput.files?.[0];
        if (!file) return;
        const problem = !['image/jpeg', 'image/png', 'image/webp'].includes(file.type)
            ? 'Pilih berkas gambar JPG, PNG, atau WebP.'
            : file.size > 5 * 1024 * 1024 ? 'Ukuran foto maksimal 5 MB.' : '';
        photoError.textContent = problem;
        photoError.hidden = !problem;
        if (problem) {
            photoInput.value = '';
            return;
        }
        if (photoUrl) URL.revokeObjectURL(photoUrl);
        photoUrl = URL.createObjectURL(file);
        const image = el('img', 'lks-photo-preview-img');
        image.src = photoUrl;
        image.alt = 'Preview foto';
        photoPreview.replaceChildren(image);
        photoRemove.style.display = '';
        refresh();
    });
    photoRemove?.addEventListener('click', () => {
        if (photoUrl) URL.revokeObjectURL(photoUrl);
        photoUrl = '';
        photoInput.value = '';
        photoPreview.innerHTML = placeholderMarkup;
        photoRemove.style.display = 'none';
        refresh();
    });

    // Isi data contoh: nilai fiktif, sama perannya dengan data-resident-demo di sumber.
    page.querySelector('[data-resident-demo]')?.addEventListener('click', () => {
        const sample = {
            name: 'Klien Contoh Fiktif', code: 'WB-999', religion: 'Islam', birthPlace: 'Banjarbaru', birthDate: '2015-05-12',
            guardianStatus: 'Keluarga/wali diketahui', guardianName: 'Wali Contoh', guardianRelationship: 'Paman',
            guardianContact: '080000009999', originAddress: 'Jl. Contoh Asal, Kota Banjarbaru', guardianAddress: 'Jl. Contoh Wali, Kota Banjarbaru',
            educationStatus: 'Sedang sekolah', schoolName: 'Sekolah Contoh', educationLevel: 'Kelas 5',
            careNeeds: 'Pendampingan pendidikan.', carePlan: 'Evaluasi setiap semester.', identityDocumentStatus: 'Tersedia',
            placementDocumentStatus: 'Tersedia', referralSource: 'Keluarga', intakeReason: 'Membutuhkan dukungan pengasuhan.', caseWorker: 'Pendamping Contoh',
        };
        Object.entries(sample).forEach(([key, value]) => {
            const control = form.elements.namedItem(key);
            if (control) control.value = value;
        });
        form.elements.namedItem('gender').value = 'Laki-laki';
        form.elements.namedItem('healthCoverageStatus').value = 'Belum diketahui';
        refresh();
    });

    refresh();
}
