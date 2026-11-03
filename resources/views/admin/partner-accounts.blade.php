@extends('layouts.dashboard')

@section('title', 'Buat Akun Mitra SiDaya')

@php
    use App\Support\Format;
@endphp

@push('scripts')
    @vite(['resources/js/pages/partner-accounts.js'])
@endpush

@section('content')
    <div class="admin-accounts-view">
        <header class="admin-accounts-heading">
            <div>
                <small>SiDaya · Admin Dinas Sosial</small>
                <h1 id="view-title" tabindex="-1">Buat &amp; Kelola Akun Mitra</h1>
                <p>Buat akun login resmi (username &amp; kata sandi) untuk pendaftar LKS dan Karang Taruna, lalu kirimkan langsung via WhatsApp.</p>
            </div>
            <span class="admin-accounts-badge">Akun SiDaya</span>
        </header>
        <div id="accounts-main-content" style="min-width: 0;">
            <div class="admin-accounts-grid">
                <section class="admin-accounts-card" style="min-width: 0;">
                    <h2>Formulir Buat Akun Mitra</h2>
                    <p class="card-desc">Pilih pendaftaran yang masuk untuk mengisi data secara otomatis, atau masukkan data secara manual. Akun akan tersimpan dan dapat digunakan untuk masuk ke SiDaya.</p>
                    {{-- TODO(Tahap 2): POST /admin/partner-accounts. Kata sandi tidak pernah disimpan di browser. --}}
                    <form class="admin-accounts-form" id="form-create-account" novalidate>
                        <div class="admin-form-group">
                            <label for="acc-reg-select">Ambil dari Pendaftaran Mitra (Opsional)</label>
                            <select id="acc-reg-select" style="width: 100%; min-width: 0;">
                                <option value="">-- Pilih pendaftaran atau isi manual --</option>
                                @foreach ($registrations as $registration)
                                    <option value="{{ $registration['id'] }}" data-pillar="{{ $registration['pillar'] }}" data-whatsapp="{{ $registration['whatsapp'] }}" data-name="{{ $registration['organization_name'] }}" @selected(($preselected['id'] ?? null) === $registration['id'])>[{{ strtoupper($registration['pillar']) }}] {{ $registration['organization_name'] }} ({{ $registration['status'] }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="admin-form-row" style="grid-template-columns: repeat(auto-fit, minmax(min(100%, 180px), 1fr));">
                            <div class="admin-form-group" style="min-width: 0;">
                                <label for="acc-role">Pilar Organisasi Mitra *</label>
                                <select id="acc-role" required style="width: 100%; min-width: 0;">
                                    <option value="lks" @selected(($preselected['pillar'] ?? '') === 'lks')>Lembaga Kesejahteraan Sosial (LKS)</option>
                                    <option value="karang-taruna" @selected(($preselected['pillar'] ?? '') === 'karang-taruna')>Karang Taruna</option>
                                    <option value="psm">Ketua PSM</option>
                                </select>
                            </div>
                            <div class="admin-form-group" style="min-width: 0;">
                                <label for="acc-whatsapp">Nomor WhatsApp *</label>
                                <input type="tel" id="acc-whatsapp" data-numeric="phone" placeholder="Contoh: 08123456789" value="{{ $preselected['whatsapp'] ?? '' }}" required>
                            </div>
                        </div>
                        <div class="admin-form-group" id="acc-psm-org-group" hidden>
                            <label for="acc-psm-org">Organisasi PSM *</label>
                            <select id="acc-psm-org">
                                <option value="">-- Pilih organisasi PSM --</option>
                                @foreach ($psmOrganizations as $organization)
                                    <option value="{{ $organization['id'] }}">{{ $organization['title'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="admin-form-group">
                            <label for="acc-name">Nama Lembaga / Mitra *</label>
                            <input type="text" id="acc-name" placeholder="Nama yayasan, LKS, atau Karang Taruna" value="{{ $preselected['organization_name'] ?? '' }}" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="acc-username">Username Login *</label>
                            <div class="admin-input-with-button">
                                <input type="text" id="acc-username" placeholder="Contoh: lks_harapanmulia" required>
                                <button type="button" class="admin-btn-action admin-btn-secondary" id="btn-gen-username" title="Buat username otomatis dari nama">Otomatis</button>
                            </div>
                        </div>
                        <div class="admin-form-group">
                            <label for="acc-password">Kata Sandi (Password) *</label>
                            <div class="admin-input-with-button">
                                <input type="text" id="acc-password" placeholder="Minimal 10 karakter, huruf dan angka" autocomplete="off" required>
                                <button type="button" class="admin-btn-action admin-btn-secondary" id="btn-gen-password" title="Buat kata sandi acak yang aman">Acak Sandi</button>
                            </div>
                        </div>
                        <div id="form-error-alert" class="notice is-error" role="alert" hidden></div>
                        <div style="margin-top: 8px;">
                            <button type="submit" class="admin-btn-action admin-btn-primary" id="btn-submit-account" style="width: 100%;">Simpan Akun</button>
                        </div>
                    </form>
                </section>
                <section class="admin-accounts-card" style="display: flex; flex-direction: column; gap: 16px; min-width: 0;">
                    <h2>Alur Pembuatan Akun</h2>
                    <p class="card-desc">Panduan singkat pengelolaan akun akses mitra baru:</p>
                    <ol style="margin: 0; padding-left: 20px; font-size: 13px; line-height: 1.6; color: var(--q-text, #334155); display: flex; flex-direction: column; gap: 10px;">
                        <li><strong>Verifikasi Berkas:</strong> Periksa kelengkapan berkas pendaftaran mitra pada menu <em>Pendaftaran Mitra</em>.</li>
                        <li><strong>Buat Akun:</strong> Masukkan nama, username unik, dan password akun mitra di formulir ini.</li>
                        <li><strong>Simpan Akun:</strong> Akun langsung terdaftar di sistem otentikasi resmi SiDaya.</li>
                        <li><strong>Kirim via WhatsApp:</strong> Klik tombol hijau WhatsApp yang muncul untuk mengirim kredensial login langsung ke nomor pengurus mitra.</li>
                    </ol>
                    <div style="margin-top: auto; padding: 14px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; font-size: 12px; color: #64748b;">
                        <strong>Catatan Keamanan:</strong> Mitra dapat langsung masuk melalui halaman login (<code style="color: #0284c7;">/login</code>) menggunakan Username atau Email resmi mereka beserta kata sandi yang dikirimkan.
                    </div>
                </section>
            </div>
            <section class="admin-accounts-table-card" style="margin-top: 24px; min-width: 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <div>
                        <h2>Daftar Akun Mitra Aktif</h2>
                        <p class="card-desc" style="margin: 0;">Total {{ $accounts->count() }} akun LKS &amp; Karang Taruna terdaftar di database.</p>
                    </div>
                </div>
                <div class="admin-accounts-table-wrap">
                    <table class="admin-accounts-table">
                        <thead>
                            <tr><th style="width: 40px;">No</th><th>Nama Lembaga / Mitra</th><th>Pilar</th><th>Username</th><th>WhatsApp</th><th>Tanggal Dibuat</th><th style="text-align: right;">Aksi</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($accounts as $account)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $account['name'] }}</strong></td>
                                    <td><span class="admin-role-badge" data-role="{{ $account['role'] }}">{{ $roleBadges[$account['role']] ?? $account['role'] }}</span></td>
                                    <td><code>{{ $account['username'] }}</code></td>
                                    <td>{{ $account['phone'] ?: '-' }}</td>
                                    <td>{{ Format::date($account['created_at'], '-') }}</td>
                                    <td style="text-align: right;">
                                        <div class="admin-action-group" style="justify-content: flex-end;">
                                            @if ($account['phone'])
                                                <a class="admin-btn-action admin-btn-whatsapp" style="padding: 6px 12px; font-size: 12px;" href="https://wa.me/62{{ ltrim($account['phone'], '0') }}" target="_blank" rel="noopener noreferrer" title="Kirim pesan WhatsApp">WhatsApp</a>
                                            @endif
                                            <button type="button" class="admin-btn-action admin-btn-secondary" style="padding: 6px 12px; font-size: 12px;" data-reset-acc="{{ $account['name'] }}" data-role="{{ $account['role'] }}" title="Reset kata sandi">Reset Sandi</button>
                                            <button type="button" class="admin-btn-action admin-btn-danger" data-demo-action data-confirm="Akses login {{ $account['name'] }} akan dicabut. Data organisasi, laporan, surat, dan notifikasinya tetap tersimpan." data-confirm-title="Cabut Akses Akun Mitra?" data-confirm-text="Ya, Cabut Akses" title="Cabut akses login mitra">Cabut Akses</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" style="text-align: center; padding: 32px; color: #64748b;">Belum ada akun mitra yang dibuat.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
    <dialog id="admin-accounts-reset-dialog" class="admin-modal-dialog" aria-labelledby="admin-reset-title">
        <div class="admin-modal-body">
            <div class="admin-modal-icon primary" aria-hidden="true">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            </div>
            <h3 id="admin-reset-title" class="admin-modal-title">Reset Kata Sandi</h3>
            <p class="admin-modal-desc">Masukkan kata sandi baru untuk akun <strong data-reset-name></strong>:</p>
            <div class="admin-modal-input-wrap">
                <label for="modal-new-password">Kata Sandi Baru (Min. 10 Karakter)</label>
                <div class="admin-modal-input-row">
                    <input type="text" id="modal-new-password" autocomplete="off" spellcheck="false">
                    <button type="button" class="admin-btn-secondary" id="modal-btn-regenerate" title="Acak Kata Sandi" style="padding: 0 12px; cursor: pointer;">🎲 Acak</button>
                </div>
                <p class="field-error" id="modal-password-error" role="alert" hidden></p>
            </div>
        </div>
        <div class="admin-modal-actions">
            <button type="button" class="admin-modal-btn admin-modal-btn-cancel" data-close-dialog>Batal</button>
            <button type="button" class="admin-modal-btn admin-modal-btn-primary" data-modal-confirm>Simpan Kata Sandi</button>
        </div>
    </dialog>
@endsection
