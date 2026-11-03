@props(['title', 'description', 'eyebrow' => 'Dinas Sosial Kota Banjarbaru'])
<div class="section-heading">
    <div>
        @if ($eyebrow)
            <p class="eyebrow">{{ $eyebrow }}</p>
        @endif
        <h1 id="view-title" tabindex="-1">{{ $title }}</h1>
        <p class="view-description">{{ $description }}</p>
    </div>
    <div class="q-actions">{{ $slot }}</div>
</div>
