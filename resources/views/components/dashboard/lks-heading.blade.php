@props(['title', 'description', 'group' => 'Organisasi', 'home' => null])
<header class="lks-page-heading">
    <div>
        <nav class="lks-breadcrumb" aria-label="Navigasi halaman"><a href="{{ $home ?? '/'.session('demo_role').'/overview' }}">Dashboard</a><span aria-hidden="true">/</span><span>{{ $group }}</span><span aria-hidden="true">/</span><span aria-current="page">{{ $title }}</span></nav>
        <h1 id="view-title" tabindex="-1">{{ $title }}</h1>
        <p>{{ $description }}</p>
    </div>
    @if ($slot->isNotEmpty())
        <div class="lks-actions">{{ $slot }}</div>
    @endif
</header>
