@props(['paginator', 'variant' => 'shared', 'noun' => 'data', 'label' => 'Halaman daftar'])
{{-- Tiga gaya paginasi sumber: lks (lks/client-lists.js), lks-report (lks/reports.js), shared (.pagination).
     Tetap <button> seperti sumber karena CSS menargetkan elemen itu; data-href menavigasi lewat dashboard/filters.js. --}}
@php
    $current = $paginator->currentPage();
    $last = $paginator->lastPage();
    $from = $paginator->count() ? $paginator->firstItem() : 0;
    $to = $paginator->count() ? $paginator->lastItem() : 0;
@endphp
@if ($variant === 'lks')
    <div class="lks-pagination">
        <p role="status" aria-live="polite" tabindex="-1">Menampilkan {{ $from }}–{{ $to }} dari {{ $paginator->total() }} {{ $noun }} · Halaman {{ $current }} dari {{ $last }}</p>
        <nav aria-label="{{ $label }}">
            @if ($current === 1)
                <button class="lks-button" type="button" disabled>Sebelumnya</button>
            @else
                <button class="lks-button" type="button" data-href="{{ $paginator->url($current - 1) }}">Sebelumnya</button>
            @endif
            @foreach (range(1, $last) as $page)
                @if (abs($page - $current) <= 1 || $page === 1 || $page === $last)
                    <button @class(['lks-page-number', 'is-active' => $page === $current]) type="button" data-href="{{ $paginator->url($page) }}" aria-label="Halaman {{ $page }}" @if ($page === $current) aria-current="page" @endif>{{ $page }}</button>
                @endif
            @endforeach
            @if ($current === $last)
                <button class="lks-button" type="button" disabled>Berikutnya</button>
            @else
                <button class="lks-button" type="button" data-href="{{ $paginator->url($current + 1) }}">Berikutnya</button>
            @endif
        </nav>
    </div>
@elseif ($variant === 'lks-report')
    <div class="lks-report-pagination">
        <p role="status" aria-live="polite" tabindex="-1">Menampilkan {{ $from }}–{{ $to }} dari {{ $paginator->total() }} {{ $noun }} · Halaman {{ $current }} dari {{ $last }}</p>
        <nav aria-label="{{ $label }}">
            @if ($current === 1)
                <button class="lks-button lks-button-plain" type="button" disabled>Sebelumnya</button>
            @else
                <button class="lks-button lks-button-plain" type="button" data-href="{{ $paginator->url($current - 1) }}">Sebelumnya</button>
            @endif
            <span aria-current="page">{{ $current }}</span>
            @if ($current === $last)
                <button class="lks-button lks-button-plain" type="button" disabled>Berikutnya</button>
            @else
                <button class="lks-button lks-button-plain" type="button" data-href="{{ $paginator->url($current + 1) }}">Berikutnya</button>
            @endif
        </nav>
    </div>
@elseif ($last > 1)
    <div {{ $attributes->merge(['class' => 'pagination']) }}>
        <span>Halaman {{ $current }} dari {{ $last }}</span>
        <div class="q-actions">
            @if ($current === 1)
                <button class="text-button" type="button" disabled>Sebelumnya</button>
            @else
                <button class="text-button" type="button" data-href="{{ $paginator->url($current - 1) }}">Sebelumnya</button>
            @endif
            @if ($current === $last)
                <button class="text-button" type="button" disabled>Berikutnya</button>
            @else
                <button class="text-button" type="button" data-href="{{ $paginator->url($current + 1) }}">Berikutnya</button>
            @endif
        </div>
    </div>
@endif
