@extends('layouts.dashboard')

@php
    use App\Support\Demo\DemoLetters;
    use App\Support\Format;
    $pageTitle = $admin ? 'Persuratan LKS · Ruang Admin' : 'Surat Masuk & Keluar';
    $tabUrl = fn (string $name) => request()->fullUrlWithQuery(['tab' => $name === 'incoming' ? null : $name]);
@endphp

@section('title', $pageTitle)

@push('scripts')
    @vite(['resources/js/pages/letters.js'])
@endpush

@section('content')
    <div class="lks-workspace lks-letters-page lks-letters-view">
        <header class="lks-page-heading">
            <div>
                <div class="lks-breadcrumb">
                    <a href="/{{ session('demo_role') }}/overview">Dashboard</a>
                    <span>/</span>
                    @if ($admin)<span>LKS</span><span>/</span>@endif
                    <span aria-current="page">Surat Masuk &amp; Keluar</span>
                </div>
                <h1 id="view-title" tabindex="-1">{{ $pageTitle }}</h1>
                <p>{{ $admin ? 'Pantau surat masuk dari Lembaga Kesejahteraan Sosial (LKS) dan kirimkan surat balasan/resmi dinas.' : 'Kelola arsip surat keluar dan pantau surat masuk dari Dinas Sosial untuk Lembaga Kesejahteraan Sosial (LKS).' }}</p>
            </div>
            <div>
                @if ($isOutgoing)
                    <button type="button" class="export-button lks-letter-create-button" id="btn-toggle-add-letter" aria-expanded="{{ $reply ? 'true' : 'false' }}" aria-controls="letter-form-section" data-open-text="{{ $admin ? 'Buat Surat ke LKS' : 'Buat Surat Keluar' }}">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                        <span>{{ $admin ? 'Buat Surat ke LKS' : 'Buat Surat Keluar' }}</span>
                    </button>
                @endif
            </div>
        </header>

        <div class="lks-letters-tabs" role="tablist">
            <button type="button" class="lks-letters-tab {{ $isOutgoing ? '' : 'is-active' }}" data-href="{{ $tabUrl('incoming') }}" role="tab" aria-selected="{{ $isOutgoing ? 'false' : 'true' }}">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                <span>{{ $admin ? 'Surat Masuk (dari LKS)' : 'Surat Masuk' }}</span>
                <span class="lks-letters-badge">{{ $counts['incoming'] }}</span>
            </button>
            <button type="button" class="lks-letters-tab {{ $isOutgoing ? 'is-active' : '' }}" data-href="{{ $tabUrl('outgoing') }}" role="tab" aria-selected="{{ $isOutgoing ? 'true' : 'false' }}">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;"><path d="M22 2 11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                <span>{{ $admin ? 'Surat Keluar (ke LKS)' : 'Surat Keluar' }}</span>
                <span class="lks-letters-badge">{{ $counts['outgoing'] }}</span>
            </button>
        </div>

        @if ($isOutgoing)
            <section class="lks-letter-card" aria-labelledby="form-heading" id="letter-form-section" @unless ($reply) hidden @endunless>
                <div class="lks-letter-card-header">
                    <div>
                        <h2 id="form-heading">
                            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--lks-primary, #304a6b);"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            {{ $admin ? 'Kirim Surat Resmi ke LKS' : 'Formulir Surat Keluar' }}
                        </h2>
                        <p>{{ $admin ? 'Pilih LKS tujuan dan lampirkan surat keputusan / surat dinas resmi (maksimal 5 MB).' : 'Catat surat keluar resmi dari LKS dan lampirkan berkas dokumen (maksimal 5 MB).' }}</p>
                    </div>
                </div>
                {{-- TODO(Tahap 2): POST /lks/letters (LksLetterController sumber). --}}
                <form id="lks-letter-form" class="lks-letter-form" novalidate data-demo-form>
                    <div class="lks-letter-form-grid">
                        @if ($admin)
                            <div class="lks-form-field is-full">
                                <label for="input-target-lks">Pilih LKS Penerima <span class="required">*</span></label>
                                <select id="input-target-lks" name="organization_id" required data-label="LKS penerima" aria-describedby="input-target-lks-error" style="padding: 10px 14px; border: 1px solid var(--lks-line, #e5eaf2); border-radius: 8px; font-size: 14px;">
                                    <option value="">-- Pilih Lembaga Kesejahteraan Sosial --</option>
                                    @foreach ($organizations as $org)
                                        <option value="{{ $org['id'] }}" @selected(($reply['organization_id'] ?? null) === $org['id'])>{{ $org['title'] }}</option>
                                    @endforeach
                                </select>
                                <p class="field-error" id="input-target-lks-error" hidden></p>
                            </div>
                        @endif
                        <div class="lks-form-field">
                            <label for="input-letter-number">Nomor Surat</label>
                            <input type="text" id="input-letter-number" name="letter_number" placeholder="{{ $admin ? 'Contoh: 460/012/DINSOS/2026' : 'Contoh: 021/LKS-MTS/X/2026' }}">
                        </div>
                        <div class="lks-form-field">
                            <label for="input-letter-date">Tanggal Surat <span class="required">*</span></label>
                            <input type="date" id="input-letter-date" name="letter_date" value="{{ $today }}" required data-label="Tanggal surat" aria-describedby="input-letter-date-error">
                            <p class="field-error" id="input-letter-date-error" hidden></p>
                        </div>
                        <div class="lks-form-field is-full">
                            <label for="input-subject">Perihal / Judul Surat <span class="required">*</span></label>
                            <input type="text" id="input-subject" name="subject" value="{{ $reply ? 'Balasan: '.$reply['subject'] : '' }}" placeholder="Contoh: Permohonan Rekomendasi / Surat Edaran / Pemberitahuan" required data-label="Perihal surat" aria-describedby="input-subject-error">
                            <p class="field-error" id="input-subject-error" hidden></p>
                        </div>
                        <div class="lks-form-field is-full">
                            <label for="input-description">Keterangan / Ringkasan Isi</label>
                            <textarea id="input-description" name="description" rows="3" placeholder="Tambahkan catatan atau ringkasan isi surat jika diperlukan...">{{ $reply ? 'Menindaklanjuti surat nomor '.($reply['letter_number'] ?: '-').' tanggal '.Format::date($reply['letter_date']).' perihal "'.$reply['subject'].'".' : '' }}</textarea>
                        </div>
                        <div class="lks-form-field is-full">
                            <label>Tempat Upload Berkas Surat</label>
                            <div class="lks-file-dropzone" id="lks-dropzone" data-dropzone>
                                <input type="file" id="input-letter-file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" data-max-mb="5">
                                <div class="lks-dropzone-content">
                                    <svg class="lks-dropzone-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M12 18v-6m-3 3 3-3 3 3"/></svg>
                                    <span class="lks-dropzone-title">Klik atau seret berkas surat ke area ini</span>
                                    <span class="lks-dropzone-hint">Format didukung: PDF, DOC, DOCX, JPG, PNG (Maksimal 5 MB)</span>
                                </div>
                            </div>
                            <div id="selected-file-preview"></div>
                        </div>
                    </div>
                    <div class="lks-form-actions">
                        <button type="button" class="lks-btn-secondary" id="btn-cancel-form">Batal</button>
                        <button type="submit" class="lks-btn-primary">{{ $admin ? 'Kirim Surat ke LKS' : 'Simpan Surat Keluar' }}</button>
                    </div>
                </form>
            </section>
        @endif

        <section class="lks-letter-card" aria-labelledby="table-heading">
            <div class="lks-letter-card-header">
                <div>
                    <h2 id="table-heading">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--lks-primary, #304a6b);"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                        {{ $admin ? (! $isOutgoing ? 'Tabel Surat Masuk dari LKS' : 'Tabel Surat Keluar Dinas ke LKS') : (! $isOutgoing ? 'Tabel Surat Masuk' : 'Tabel Surat Keluar') }}
                    </h2>
                    <p>
                        @if ($admin)
                            {{ ! $isOutgoing ? 'Daftar surat resmi yang dikirimkan oleh berbagai Lembaga Kesejahteraan Sosial (LKS) ke Dinas Sosial.' : 'Daftar surat resmi dari Dinas Sosial yang telah dikirimkan ke LKS. Surat ini tidak memerlukan balasan.' }}
                        @else
                            {{ ! $isOutgoing ? 'Surat dari Dinas Sosial untuk dibaca dan diunduh. Surat ini tidak memerlukan balasan.' : 'Daftar surat resmi keluar yang diajukan atau dikirimkan oleh organisasi.' }}
                        @endif
                    </p>
                </div>
            </div>
            <div class="lks-table-toolbar">
                <form method="get" data-filter-form style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                    <input type="hidden" name="tab" value="{{ $isOutgoing ? 'outgoing' : '' }}">
                    <div class="lks-table-search">
                        <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        <input type="search" id="lks-search-input" name="q" placeholder="Cari nomor, perihal, keterangan..." value="{{ $filters['q'] }}" aria-label="Cari surat" @if ($filters['q']) data-autofocus @endif>
                    </div>
                    @if ($admin)
                        <div class="lks-filter-group">
                            <label for="filter-lks-org">Filter LKS:</label>
                            <select id="filter-lks-org" name="org">
                                <option value="all">Semua LKS</option>
                                @foreach ($organizations as $org)
                                    <option value="{{ $org['id'] }}" @selected($filters['org'] === $org['id'])>{{ $org['title'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="lks-filter-group">
                        <label for="filter-lks-month">Bulan:</label>
                        <select id="filter-lks-month" name="month">
                            <option value="all">Semua Bulan</option>
                            @foreach ($months as $index => $month)
                                <option value="{{ $index + 1 }}" @selected($filters['month'] === (string) ($index + 1))>{{ $month }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="lks-filter-group">
                        <label for="filter-lks-year">Tahun:</label>
                        <select id="filter-lks-year" name="year">
                            <option value="all">Semua Tahun</option>
                            @foreach ($years as $year)
                                <option value="{{ $year }}" @selected($filters['year'] === (string) $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($hasActiveFilter)
                        <button type="button" class="lks-btn-secondary" data-href="{{ $rolePrefix }}{{ $isOutgoing ? '?tab=outgoing' : '' }}" style="padding: 7px 12px; font-size: 13px; height: 36px; white-space: nowrap;">Hapus Filter</button>
                    @endif
                </form>
                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <div style="font-size: 13px; color: var(--lks-muted, #64748b);">Total: <strong>{{ $letters->count() }}</strong> surat tercatat</div>
                    <div class="lks-export-actions" style="display: inline-flex; gap: 8px;">
                        {{-- TODO(Tahap 2): ekspor dari lks/letters-export.js. --}}
                        <button type="button" class="export-button" @disabled($letters->isEmpty()) data-demo-download title="Unduh daftar surat dalam format PDF"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg><span>Unduh PDF</span></button>
                        <button type="button" class="export-button" @disabled($letters->isEmpty()) data-demo-download title="Unduh daftar surat dalam format Excel"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg><span>Unduh Excel</span></button>
                    </div>
                </div>
            </div>

            @if ($letters->isNotEmpty())
                <div class="lks-table-wrapper">
                    <table class="lks-letter-table">
                        <thead>
                            <tr>
                                <th style="width: 48px; text-align: center;">No</th>
                                <th style="width: 120px;">Tanggal</th>
                                <th style="width: 160px;">No. Surat</th>
                                @if ($admin)<th>LKS Terkait</th>@endif
                                <th>Perihal &amp; Keterangan</th>
                                <th style="width: 130px;">Berkas</th>
                                <th style="width: 90px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($letters as $item)
                                <tr>
                                    <td style="text-align: center; color: var(--lks-muted, #64748b);">{{ $loop->iteration }}</td>
                                    <td><strong>{{ Format::date($item['letter_date']) }}</strong></td>
                                    <td><code style="background: var(--lks-soft, #f1f3ff); padding: 2px 6px; border-radius: 4px; font-size: 12px; color: var(--lks-primary, #304a6b);">{{ $item['letter_number'] ?: 'Tidak bernomor' }}</code></td>
                                    @if ($admin)
                                        <td><span class="{{ $isOutgoing ? 'lks-tag-destination' : 'lks-tag-sender' }}">{{ $isOutgoing ? $item['destination'] : $item['sender'] }}</span></td>
                                    @endif
                                    <td>
                                        <div style="font-weight: 600; color: var(--lks-ink, #1e293b); display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                            <span>{{ $item['subject'] }}</span>
                                            @if (! $isOutgoing && ! $item['is_read'])
                                                <span style="font-size: 10px; font-weight: 700; background: #0284c7; color: #fff; padding: 2px 6px; border-radius: 4px; letter-spacing: 0.3px;">BARU</span>
                                            @endif
                                        </div>
                                        @if ($item['description'])
                                            <div style="font-size: 12px; color: var(--lks-muted, #64748b); margin-top: 2px; max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $item['description'] }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item['has_file'])
                                            <button type="button" class="lks-table-file-btn" data-demo-download title="Unduh {{ $item['file_name'] }}">
                                                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;"><path d="M12 3v12m-5-5 5 5 5-5M4 16v5h16v-5"/></svg>
                                                <span>Unduh</span>
                                            </button>
                                            <span style="display: block; font-size: 11px; color: var(--lks-muted, #64748b); margin-top: 2px;">{{ DemoLetters::fileSize($item['file_size']) }}</span>
                                        @else
                                            <span style="color: var(--lks-muted, #94a3b8); font-size: 12px;">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="lks-table-actions" style="justify-content: center;">
                                            <button type="button" class="lks-action-btn" data-open-dialog="letter-detail-{{ $item['id'] }}" title="Lihat detail" aria-label="Lihat detail {{ $item['subject'] }}">
                                                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 15px; height: 15px;"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            </button>
                                            @if ($admin && $item['type'] === 'outgoing')
                                                <button type="button" class="lks-action-btn is-reply" data-href="{{ $rolePrefix }}?tab=outgoing&reply={{ $item['id'] }}" title="Balas Surat ini" aria-label="Balas surat {{ $item['subject'] }}"><svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 15px; height: 15px;"><path d="m9 17-5-5 5-5"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg></button>
                                            @endif
                                            @if ($admin || $isOutgoing)
                                                <button type="button" class="lks-action-btn is-delete" data-demo-action data-confirm="Surat &quot;{{ $item['subject'] }}&quot; akan dihapus." data-confirm-title="Hapus surat?" data-confirm-text="Ya, hapus surat" title="Hapus surat" aria-label="Hapus surat {{ $item['subject'] }}">
                                                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 15px; height: 15px;"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="lks-empty-state">
                    <svg class="lks-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
                    <h3>Belum ada {{ ! $isOutgoing ? 'Surat Masuk' : 'Surat Keluar' }}</h3>
                    <p>{{ $filters['q'] ? 'Tidak ada surat yang sesuai dengan kata kunci pencarian.' : (! $isOutgoing ? 'Belum ada berkas surat masuk yang tercatat.' : ($admin ? "Klik tombol '+ Buat Surat ke LKS' di atas untuk mengirimkan surat resmi ke LKS." : "Klik tombol '+ Buat Surat Keluar' di atas untuk mencatat surat keluar pertama.")) }}</p>
                </div>
            @endif
        </section>
    </div>

    @foreach ($letters as $item)
        @php($sent = ($item['type'] === ($admin ? 'incoming' : 'outgoing')))
        <dialog id="letter-detail-{{ $item['id'] }}" class="q-dialog" aria-labelledby="letter-detail-{{ $item['id'] }}-title" style="max-width: 880px; width: 95%; max-height: 92vh; padding: 24px; border-radius: 12px; overflow-y: auto; box-sizing: border-box;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 20px;">
                <div>
                    <span class="{{ $sent ? 'lks-tag-destination' : 'lks-tag-sender' }}" style="font-size: 11px; margin-bottom: 6px;">{{ $sent ? 'SURAT KELUAR' : 'SURAT MASUK' }}</span>
                    <h2 id="letter-detail-{{ $item['id'] }}-title" style="font-size: 18px; font-weight: 700; margin-top: 4px; color: var(--lks-ink, #1e293b);">{{ $item['subject'] }}</h2>
                </div>
                <button type="button" class="lks-action-btn" data-close-dialog style="flex-shrink: 0;" aria-label="Tutup"><svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; font-size: 13px; margin-bottom: 20px; background: var(--lks-soft, #f8faff); padding: 16px; border-radius: 8px;">
                <div><span style="color: var(--lks-muted, #64748b); display: block; font-size: 12px;">Nomor Surat</span><strong>{{ $item['letter_number'] ?: 'Belum dicatat' }}</strong></div>
                <div><span style="color: var(--lks-muted, #64748b); display: block; font-size: 12px;">Tanggal Surat</span><strong>{{ Format::date($item['letter_date']) }}</strong></div>
                @if ($admin)
                    <div><span style="color: var(--lks-muted, #64748b); display: block; font-size: 12px;">LKS Terkait</span><strong style="color: var(--lks-primary, #304a6b);">{{ $sent ? $item['destination'] : $item['sender'] }}</strong></div>
                @endif
            </div>
            @if ($item['description'])
                <div style="margin-bottom: 20px;">
                    <span style="font-size: 12px; font-weight: 600; color: var(--lks-muted, #64748b); display: block; margin-bottom: 4px;">Keterangan / Ringkasan</span>
                    <p style="font-size: 13px; color: var(--lks-ink, #1e293b); background: #fff; border: 1px solid var(--lks-line, #e5eaf2); padding: 12px; border-radius: 8px; white-space: pre-line;">{{ $item['description'] }}</p>
                </div>
            @endif
            <div style="margin-bottom: 24px;">
                <span style="font-size: 12px; font-weight: 600; color: var(--lks-muted, #64748b); display: block; margin-bottom: 8px;">Tampilan Isi Berkas Lampiran</span>
                @if ($item['has_file'])
                    {{-- Berkas dummy tidak punya isi, jadi memakai tampilan "pratinjau tidak didukung" dari sumber. --}}
                    <div style="padding: 24px; text-align: center; border: 1px dashed var(--lks-line, #e5eaf2); border-radius: 8px; background: var(--lks-soft, #f8faff);">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width: 44px; height: 44px; color: var(--lks-primary, #304a6b); margin-bottom: 8px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
                        <p style="font-weight: 600; font-size: 14px; margin-bottom: 4px; color: var(--lks-ink, #1e293b);">{{ $item['file_name'] }} ({{ DemoLetters::fileSize($item['file_size']) }})</p>
                        <p style="font-size: 12px; color: var(--lks-muted, #64748b); margin-bottom: 16px;">Pratinjau langsung tidak didukung untuk jenis dokumen ini di peramban.</p>
                        <button type="button" class="lks-btn-primary" data-demo-download style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; padding: 8px 18px;"><svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 15px; height: 15px;"><path d="M12 3v12m-5-5 5 5 5-5M4 16v5h16v-5"/></svg>Unduh Berkas untuk Membuka</button>
                    </div>
                @else
                    <div style="padding: 24px; text-align: center; border: 1px dashed var(--lks-line, #e5eaf2); border-radius: 8px; color: var(--lks-muted, #64748b); font-style: italic;">Tidak ada berkas yang dilampirkan pada surat ini.</div>
                @endif
            </div>
            @if ($item['type'] === 'incoming')
                <p class="lks-letter-one-way-note">Surat dari Dinas Sosial ini tidak memerlukan balasan. LKS dapat membaca dan mengunduh lampirannya.</p>
            @endif
            <div style="display: flex; justify-content: flex-end; gap: 10px; flex-wrap: wrap;">
                @if ($admin && $item['type'] === 'outgoing')
                    <button type="button" class="lks-btn-primary" data-href="{{ $rolePrefix }}?tab=outgoing&reply={{ $item['id'] }}" style="display: inline-flex; align-items: center; gap: 6px;"><svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;"><path d="m9 17-5-5 5-5"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>Balas Surat Ini</button>
                @endif
                @if (! $sent && ! $item['is_read'])
                    {{-- Di sumber surat otomatis terbaca saat dibuka. TODO(Tahap 2): POST /lks/letters/{id}/read. --}}
                    <button type="button" class="lks-btn-secondary" data-demo-action>Tandai dibaca</button>
                @endif
                <button type="button" class="lks-btn-secondary" data-close-dialog>Tutup</button>
            </div>
        </dialog>
    @endforeach
@endsection
