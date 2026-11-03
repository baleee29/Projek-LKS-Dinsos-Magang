@extends('layouts.dashboard')

@section('title', 'Kelola Landing Page')

@push('scripts')
    @vite(['resources/js/pages/landing-content.js'])
@endpush

@section('content')
    <div class="landing-view">
        <header class="landing-heading">
            <div>
                <small>SiDaya · Admin Dinas Sosial</small>
                <h1 id="view-title" tabindex="-1">Kelola Landing Page</h1>
                <p>Ganti foto dan teks halaman publik. Perubahan langsung tampil setelah tersimpan, dan setiap isian dapat dikembalikan ke isi bawaan.</p>
            </div>
        </header>
        <div>
            <div class="landing-tabs" role="tablist" aria-label="Halaman publik">
                @foreach ($pages as $page)
                    <button type="button" role="tab" aria-selected="{{ $page['key'] === $current['key'] ? 'true' : 'false' }}" @class(['landing-tab', 'is-active' => $page['key'] === $current['key']]) data-href="{{ request()->fullUrlWithQuery(['filter' => $page['key'], 'q' => null]) }}">{{ $page['label'] }}</button>
                @endforeach
            </div>
            <div class="landing-toolbar">
                <form class="landing-search" role="search" method="get" data-filter-form style="display:contents">
                    <input type="hidden" name="filter" value="{{ $current['key'] ?? '' }}">
                    <label class="landing-search"><span class="sr-only">Cari isian</span><input type="search" name="q" value="{{ $q }}" placeholder="Cari teks di halaman ini…" @if ($q) data-autofocus @endif></label>
                </form>
                <div class="landing-summary">
                    <span>{{ $total }} isian · {{ $changed }} diubah</span>
                    @if ($current)
                        <a class="landing-button" href="{{ $current['url'] }}" target="_blank" rel="noopener">Lihat halaman<span class="sr-only"> (tab baru)</span></a>
                    @endif
                </div>
            </div>
            <div>
                @forelse ($groups as $index => $group)
                    <details class="landing-group" @if ($index === 0 || $q) open @endif>
                        <summary><strong>{{ $group['label'] }}</strong><span>{{ count($group['slots']) }} isian</span></summary>
                        <div class="landing-slots">
                            @foreach ($group['slots'] as $slot)
                                @php($id = 'landing-'.preg_replace('/[^a-z0-9]/i', '-', $slot['key']))
                                @php($badge = $slot['changed'] ? '<span class="landing-badge is-changed">Diubah</span>' : '<span class="landing-badge">Bawaan</span>')
                                @if ($slot['type'] === 'image')
                                    <article class="landing-slot is-image" data-slot="{{ $slot['key'] }}">
                                        <header><span class="landing-slot-label">{{ $slot['label'] }}</span>{!! $badge !!}</header>
                                        <figure class="landing-preview" id="{{ $id }}-preview"><img src="{{ $slot['image_url'] }}" alt="" loading="lazy" decoding="async"></figure>
                                        <label class="landing-field" for="{{ $id }}-file">Ganti foto <small>JPG, PNG, atau WebP · maks. {{ round($limits['max_kb'] / 1024) }} MB · lebar min. {{ $limits['min_width'] }} px</small></label>
                                        <input id="{{ $id }}-file" type="file" accept="{{ implode(',', $limits['mimes']) }}" data-max-mb="2" data-preview="{{ $id }}-preview" data-landing-file>
                                        <label class="landing-field" for="{{ $id }}-alt">Teks alternatif (deskripsi foto)</label>
                                        <input id="{{ $id }}-alt" type="text" maxlength="300" value="{{ $slot['value'] }}" data-landing-alt placeholder="Kosongkan bila foto hanya hiasan">
                                        <div class="landing-actions">
                                            <button type="button" class="landing-button is-primary" data-landing-upload data-demo-action disabled>Unggah foto</button>
                                            <button type="button" class="landing-button" data-landing-save-alt data-demo-action disabled>Simpan deskripsi</button>
                                            @if ($slot['changed'])
                                                <button type="button" class="landing-button" data-demo-action data-confirm="&quot;{{ $slot['label'] }}&quot; akan kembali ke isi bawaan di halaman publik." data-confirm-title="Kembalikan isi bawaan?" data-confirm-text="Ya, kembalikan">Kembalikan bawaan</button>
                                            @endif
                                        </div>
                                    </article>
                                @else
                                    @php($multiline = mb_strlen($slot['value']) > 90)
                                    <article class="landing-slot" data-slot="{{ $slot['key'] }}">
                                        <header><label for="{{ $id }}">{{ $slot['label'] }}</label>{!! $badge !!}</header>
                                        @if ($multiline)
                                            <textarea id="{{ $id }}" data-landing-input rows="{{ min(8, max(3, (int) ceil(mb_strlen($slot['value']) / 80))) }}" maxlength="{{ $slot['max'] }}">{{ $slot['value'] }}</textarea>
                                        @else
                                            <input id="{{ $id }}" data-landing-input type="text" maxlength="{{ $slot['max'] }}" value="{{ $slot['value'] }}">
                                        @endif
                                        <div class="landing-meta"><span data-landing-count data-max="{{ $slot['max'] }}">{{ mb_strlen($slot['value']) }}/{{ $slot['max'] }}</span>@if ($slot['changed'])<span>Bawaan: {{ $slot['default'] }}</span>@endif</div>
                                        <p class="landing-hint">Tulis <code>*kata*</code> untuk huruf miring.@if ($multiline) Tekan Enter untuk baris baru.@endif</p>
                                        <div class="landing-actions">
                                            <button type="button" class="landing-button is-primary" data-landing-save data-demo-action disabled>Simpan</button>
                                            @if ($slot['changed'])
                                                <button type="button" class="landing-button" data-demo-action data-confirm="&quot;{{ $slot['label'] }}&quot; akan kembali ke isi bawaan di halaman publik." data-confirm-title="Kembalikan isi bawaan?" data-confirm-text="Ya, kembalikan">Kembalikan bawaan</button>
                                            @endif
                                        </div>
                                    </article>
                                @endif
                            @endforeach
                        </div>
                    </details>
                @empty
                    <p class="notice">{{ $current ? 'Tidak ada isian yang cocok dengan pencarian.' : 'Isi halaman publik belum tersedia.' }}</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
