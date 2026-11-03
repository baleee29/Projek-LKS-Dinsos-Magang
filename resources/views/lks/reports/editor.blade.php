@extends('layouts.dashboard')

@php
    use App\Support\Format;
    $updating = $report['status'] === 'Terkirim';
    $pageTitle = $updating ? 'Perbarui Laporan Semester LKS' : 'Laporan Semester LKS';
    $semesterNumber = (int) $report['quarter'] >= 2 ? 2 : 1;
    $icons = [
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
        'fileText' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>',
        'attachment' => '<path d="m8 12 6-6a3 3 0 0 1 4 4l-8 8a5 5 0 0 1-7-7l8-8M6 14l8-8"/>',
        'save' => '<path d="M5 3h12l4 4v14H3V3h2ZM7 3v6h10V3M7 21v-7h10v7"/>',
        'preview' => '<path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',
    ];
    $icon = fn (string $name) => '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true">'.$icons[$name].'</svg>';
    $saveText = $updating ? 'Simpan perubahan' : 'Simpan draf';
@endphp

@section('title', $pageTitle)

@push('scripts')
    @vite(['resources/js/pages/report-editor.js'])
@endpush

@section('content')
    <div class="lks-workspace lks-report-editor">
        <header class="lks-page-heading">
            <div>
                <nav class="lks-breadcrumb" aria-label="Navigasi halaman">
                    <a href="/lks/overview">Dashboard</a><span aria-hidden="true">/</span><span>Pelaporan</span><span aria-hidden="true">/</span><a href="/lks/reports">Laporan Semester</a><span aria-hidden="true">/</span><span aria-current="page">{{ $updating ? 'Perbarui laporan' : 'Susun draf' }}</span>
                </nav>
                <h1 id="view-title" tabindex="-1">{{ $pageTitle }}</h1>
                <p>Laporan penyelenggaraan kesejahteraan sosial LKS per semester (6 bulan).</p>
            </div>
            <div class="lks-actions">
                <x-dashboard.status-badge :status="$report['status']" variant="lks" />
                <button type="button" class="lks-button" data-report-save>{!! $icon('save') !!}{{ $saveText }}</button>
            </div>
        </header>

        @if ($period['interim'])
            <p class="lks-report-note"><strong>Rekap sementara.</strong> Periode semester ini masih berjalan. Draf dapat diisi sekarang; pengiriman tersedia setelah periode berakhir.</p>
        @endif
        @if ($updating)
            <p class="lks-report-note">Perubahan disimpan sebagai draf privat. Admin tetap melihat kiriman terakhir sampai Anda mengirim ulang laporan.</p>
        @endif

        <form data-q-editor novalidate data-url="/lks/reports/{{ $report['id'] }}" data-version="{{ $report['version'] }}" data-interim="{{ $period['interim'] ? '1' : '0' }}">
            <div class="psm-error-summary q-errors" data-q-errors tabindex="-1" role="alert" hidden></div>
            <div class="q-section-stack lks-report-sections">
                <section class="detail-card psm-form-card q-form-section lks-report-card" data-q-step="0">
                    <header class="lks-report-card-heading">
                        <span class="lks-report-icon">{!! $icon('calendar') !!}</span>
                        <div><h2 tabindex="-1">1. Periode & Identitas Lembaga</h2><p>Informasi periode semester dan profil resmi lembaga pelapor.</p></div>
                    </header>
                    <div class="lks-report-card-body">
                        <dl class="lks-report-period">
                            <div><dt>Periode Laporan</dt><dd><strong>{{ $period['label'] }}</strong></dd><small>{{ Format::date($period['start']) }} – {{ Format::date($period['end']) }}</small></div>
                            <div><dt>Tahun Laporan</dt><dd><strong>Tahun {{ $report['year'] }}</strong></dd><small>Semester {{ $semesterNumber }} (6 Bulan)</small></div>
                        </dl>
                        <section class="lks-report-profile" style="margin-top: 16px;">
                            <header><h3>Profil Lembaga</h3><span>Sesuai data terdaftar</span></header>
                            <dl>
                                <div><dt>Nama LKS</dt><dd>{{ $organization['title'] }}</dd></div>
                                <div><dt>ID Lembaga</dt><dd>{{ $report['organizationId'] }}</dd></div>
                                <div><dt>Penanggung Jawab</dt><dd>{{ $organization['chair'] }}</dd></div>
                                <div class="lks-report-value-wide"><dt>Alamat Sekretariat</dt><dd>{{ $organization['address'] }}</dd></div>
                                <div><dt>Kontak Lembaga</dt><dd>{{ $organization['contact'] }}</dd></div>
                                <div><dt>Wilayah</dt><dd>{{ $organization['village'] }}, {{ $organization['district'] }}</dd></div>
                                <div class="lks-report-value-wide"><dt>Legalitas Lembaga</dt><dd>{{ $organization['legal'] ?: 'Belum dicatat' }}</dd></div>
                            </dl>
                        </section>
                    </div>
                </section>

                <section class="detail-card psm-form-card q-form-section lks-report-card" data-q-step="1">
                    <header class="lks-report-card-heading">
                        <span class="lks-report-icon">{!! $icon('fileText') !!}</span>
                        <div><h2 tabindex="-1">2. Isi Laporan Semester</h2><p>Tuliskan judul atau perihal laporan dan lampirkan berkas dokumen laporan.</p></div>
                    </header>
                    <div class="lks-report-card-body">
                        <div class="psm-field" style="margin-bottom: 24px;">
                            <label for="q-field-title" style="font-weight: 600; font-size: 1rem; display: block; margin-bottom: 6px;">Judul atau Perihal Laporan <span class="required" style="color: #dc2626;" aria-hidden="true">*</span></label>
                            <input id="q-field-title" type="text" data-q-field="title" class="psm-input" value="{{ $report['title'] }}" placeholder="Contoh: Laporan Penyelenggaraan Kesejahteraan Sosial Semester 1 Tahun {{ $report['year'] }}" required maxlength="255" style="width: 100%; padding: 10px 12px; font-size: 1rem; border-radius: 6px; border: 1px solid #cbd5e1;" aria-describedby="q-field-title-error">
                            <p class="field-error" id="q-field-title-error" role="alert" hidden></p>
                            <small class="muted" style="display: block; margin-top: 4px; color: #64748b;">Tuliskan perihal atau judul laporan semester ini secara jelas dan lengkap.</small>
                        </div>
                        <div class="psm-field">
                            <label style="font-weight: 600; font-size: 1rem; display: block; margin-bottom: 6px;">Berkas Laporan &amp; Dokumen Pendukung <span class="required" style="color: #dc2626;" aria-hidden="true">*</span></label>
                            <div class="lks-report-upload-grid">
                                <label class="psm-upload lks-report-upload" for="q-report-files" style="cursor: pointer;" data-dropzone>
                                    {!! $icon('attachment') !!}
                                    <strong>Pilih Berkas Laporan (Bisa Beberapa Berkas Sekaligus)</strong>
                                    <span>Klik atau seret berkas laporan ke area ini untuk mengunggah.</span>
                                    <input id="q-report-files" type="file" accept="application/pdf,.pdf,.doc,.docx,.xls,.xlsx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,image/jpeg,image/png,image/webp,text/plain" multiple>
                                    <small>Format didukung: PDF, Word (DOC/DOCX), Excel (XLS/XLSX), Gambar (JPG/PNG/WEBP), atau TXT. Maksimal 5 MB per berkas (hingga 10 berkas).</small>
                                </label>
                                <aside class="lks-report-attachment-note">
                                    <h3>Petunjuk Unggah Berkas</h3>
                                    <ul>
                                        <li>Dapat mengunggah <strong>beberapa berkas sekaligus</strong> (laporan utama, rekap, dokumentasi).</li>
                                        <li>Pastikan berkas dapat dibuka dan dibaca dengan jelas sebelum dikirim.</li>
                                        <li>Format utama yang disarankan adalah <strong>PDF</strong> atau dokumen Office (DOCX / XLSX).</li>
                                        <li>Butuh acuan struktur laporan? <a href="/assets/documents/Template_Laporan_LKS.pdf" download="Template_Laporan_LKS.pdf" target="_blank" rel="noopener" style="font-weight:600;color:var(--lks-primary,#0284c7);text-decoration:underline;">Unduh Template Laporan (PDF)</a></li>
                                    </ul>
                                </aside>
                            </div>
                            <p class="field-error" data-upload-error tabindex="-1" role="alert" hidden></p>
                            <div data-q-attachments style="margin-top: 14px;" data-saved="{{ collect($report['attachments'])->map(fn ($file) => ['id' => $file['id'], 'url' => $file['url'], 'name' => $file['name'], 'size' => $file['size'], 'type' => $file['type']])->toJson() }}"></div>
                        </div>
                        <div class="lks-report-note" style="margin-top: 24px;">
                            <h3>Kelengkapan Laporan Semester</h3>
                            <p data-q-completeness></p>
                            <p>Draf tersimpan ketika Anda memilih Simpan draf atau membuka pratinjau. Pengiriman laporan memerlukan pratinjau dan konfirmasi akhir.</p>
                        </div>
                    </div>
                </section>
            </div>

            <div class="psm-form-actions q-form-footer lks-report-actions" style="margin-top: 24px;">
                <span data-save-state role="status"></span>
                <button type="button" class="lks-button" data-report-save>{!! $icon('save') !!}{{ $saveText }}</button>
                <button type="button" class="lks-button lks-button-primary" data-preview aria-haspopup="dialog">{!! $icon('preview') !!}Pratinjau &amp; kirim</button>
            </div>

            <div class="lks-report-return" style="margin-top: 16px;">
                <a href="/lks/reports">← Kembali ke daftar laporan</a>
                @if ($report['status'] === 'Draf')
                    <button type="button" class="psm-delete-button" data-action-url="/lks/reports/{{ $report['id'] }}" data-action-method="DELETE" data-confirm="Draf {{ $period['label'] }} milik {{ $organization['title'] }}, termasuk isian dan lampirannya, akan dihapus. Tindakan ini tidak dapat dibatalkan." data-confirm-title="Hapus draf laporan?" data-confirm-text="Ya, hapus draf" aria-haspopup="dialog">Hapus draf</button>
                @endif
            </div>
        </form>
    </div>
    {{-- preview() dari shared/reports.js. --}}
    <dialog class="q-dialog psm-preview-dialog" id="report-preview-dialog" aria-labelledby="report-preview-title">
        <h2 id="report-preview-title">Pratinjau laporan semester</h2>
        <div class="q-preview-body">
            <p class="q-inline-note">Draf sudah disimpan. Periksa isi dan kelengkapan berkas di bawah, kemudian lanjutkan ke konfirmasi pengiriman.</p>
            <section class="detail-card">
                <h2>Informasi Laporan Semester</h2>
                <dl class="field-grid">
                    <div><dt>Periode</dt><dd>{{ $period['label'] }}</dd></div>
                    <div><dt>Organisasi</dt><dd>{{ $organization['title'] }}</dd></div>
                </dl>
                <dl class="field-grid">
                    <div><dt>Judul / Perihal Laporan</dt><dd style="font-size:1.05rem;font-weight:600;color:var(--text-color, #1e293b);" data-preview-title></dd></div>
                    <div><dt>Jumlah Berkas Lampiran</dt><dd data-preview-count></dd></div>
                </dl>
            </section>
            <section class="detail-card">
                <h2 data-preview-files-title>Berkas Dokumen Laporan</h2>
                <div data-preview-files></div>
            </section>
            <p class="notice" @unless ($period['interim']) hidden @endunless>Periode masih berjalan. Pengiriman tersedia setelah {{ Format::date($period['end']) }}.</p>
        </div>
        <div class="psm-form-actions q-actions">
            <button type="button" class="text-button" data-close-dialog>Kembali mengedit</button>
            <button type="button" class="export-button" data-report-send data-confirm="{{ $organization['title'] }} akan mengirim {{ $period['label'] }} kepada Admin tanpa memerlukan persetujuan. Pastikan judul dan berkas lampiran sudah benar. Perubahan berikutnya tetap privat sampai dikirim ulang." data-confirm-title="Kirim laporan semester?" data-confirm-text="Ya, kirim laporan" data-confirm-safe @disabled($period['interim'])>Lanjutkan pengiriman</button>
        </div>
    </dialog>
@endsection
