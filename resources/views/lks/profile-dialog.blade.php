{{-- Dialog Edit Profil dari editProfile() di shared/workspace-profile.js. TODO(Tahap 2): PUT /organization/profile, POST /organization/logo, POST /organization/coordinates. --}}
<x-dashboard.dialog id="edit-profile-dialog" title="Edit profil organisasi">
    <form id="organization-form" novalidate data-demo-form>
        <p class="muted">Perubahan profil digunakan pada pengiriman berikutnya. Identitas dalam laporan yang telah dikirim tetap tercatat.</p>
        <div class="psm-form-grid">
            <div class="psm-form-field is-wide" style="grid-column: 1 / -1; margin-bottom: 8px; padding: 14px; background: var(--kt-profile-soft, #f8fafc); border-radius: 8px; border: 1px solid var(--line, #e2e8f0);">
                <label style="font-weight: 600; font-size: 13px; display: flex; align-items: center; gap: 6px; color: var(--text, #1e293b); margin-bottom: 4px;">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; color: #0284c7;"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                    Logo Profil Organisasi / Lembaga
                </label>
                <p class="muted" style="font-size: 12px; margin: 0 0 12px 0;">Unggah berkas gambar logo resmi (PNG, JPG, atau WEBP, maksimal 2 MB). Logo ini langsung dapat dilihat oleh Admin Dinas Sosial.</p>
                <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                    <div id="org-logo-preview-box" data-placeholder="🏢" style="width: 72px; height: 72px; border-radius: 8px; border: 1px dashed var(--line, #cbd5e1); background: #ffffff; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative; box-shadow: 0 1px 3px rgba(0,0,0,0.05); flex-shrink: 0;">
                        <span id="org-logo-placeholder" style="font-size: 28px; opacity: 0.6;">🏢</span>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <label class="export-button" style="font-size: 12px; padding: 6px 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; margin: 0;">
                                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                Pilih Logo
                                <input type="file" id="org-logo-file-input" accept="image/png,image/jpeg,image/webp" data-max-mb="2" data-preview="org-logo-preview-box" data-status="org-logo-status-text" style="display: none;">
                            </label>
                        </div>
                        <span id="org-logo-status-text" style="font-size: 11px; color: var(--muted, #64748b);">Belum ada logo dipilih</span>
                    </div>
                </div>
            </div>
            <div class="psm-field">
                <label for="m-title">Nama organisasi</label>
                <input id="m-title" name="title" aria-describedby="m-title-error" aria-invalid="false" type="text" value="{{ $org['title'] }}" maxlength="150" required>
                <p class="q-field-error" id="m-title-error" hidden></p>
            </div>
            <div class="psm-field">
                <label for="m-chair">Penanggung jawab</label>
                <input id="m-chair" name="chair" aria-describedby="m-chair-error" aria-invalid="false" type="text" value="{{ $org['chair'] }}" maxlength="150" required>
                <p class="q-field-error" id="m-chair-error" hidden></p>
            </div>
            <div class="psm-field">
                <label for="m-contact">Kontak (nomor HP/telepon)</label>
                <input id="m-contact" name="contact" aria-describedby="m-contact-error" aria-invalid="false" type="tel" value="{{ $org['contact'] }}" maxlength="40" autocomplete="tel" data-numeric="phone">
                <p class="q-field-error" id="m-contact-error" hidden></p>
            </div>
            <div class="psm-field">
                <label for="m-address">Alamat sekretariat</label>
                <textarea id="m-address" name="address" rows="4" maxlength="3000" aria-describedby="m-address-error" aria-invalid="false">{{ $org['address'] }}</textarea>
                <p class="q-field-error" id="m-address-error" hidden></p>
            </div>
            <div class="psm-field">
                <label for="m-legal">Legalitas dan dokumen lembaga</label>
                <textarea id="m-legal" name="legal" rows="4" maxlength="3000" aria-describedby="m-legal-error" aria-invalid="false">{{ $org['legal'] }}</textarea>
                <p class="q-field-error" id="m-legal-error" hidden></p>
            </div>
            <div class="psm-form-field is-wide" style="grid-column: 1 / -1; margin-top: 8px; padding: 14px; background: var(--kt-profile-soft, #f8fafc); border-radius: 8px; border: 1px solid var(--line, #e2e8f0);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                    <span style="font-weight: 600; font-size: 13px; display: flex; align-items: center; gap: 6px; color: var(--text, #1e293b);">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; color: #0284c7;"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        Titik Koordinat Sekretariat (Latitude &amp; Longitude)
                    </span>
                </div>
                <p class="muted" style="font-size: 12px; margin: 0 0 10px 0;">Isi titik koordinat letak sekretariat.</p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <label style="font-size: 12px; display: flex; flex-direction: column; gap: 4px;">
                        <span>Latitude (Garis Lintang)</span>
                        <input type="text" name="latitude" value="{{ $org['latitude'] }}" placeholder="Contoh: -3.441162" style="width: 100%; padding: 8px 10px; border: 1px solid var(--line, #cbd5e1); border-radius: 6px; font-size: 13px;">
                    </label>
                    <label style="font-size: 12px; display: flex; flex-direction: column; gap: 4px;">
                        <span>Longitude (Garis Bujur)</span>
                        <input type="text" name="longitude" value="{{ $org['longitude'] }}" placeholder="Contoh: 114.843105" style="width: 100%; padding: 8px 10px; border: 1px solid var(--line, #cbd5e1); border-radius: 6px; font-size: 13px;">
                    </label>
                </div>
            </div>
        </div>
    </form>
    <x-slot:actions>
        <button class="text-button" type="button" data-close-dialog>Batal</button><button class="export-button" type="submit" form="organization-form">Simpan profil</button>
    </x-slot:actions>
</x-dashboard.dialog>
