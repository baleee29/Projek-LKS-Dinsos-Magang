@props(['title', 'identity'])
<header class="topbar">
    <div class="breadcrumbs">
        <button class="icon-button mobile-menu" id="mobile-menu" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="sidebar">
            <x-dashboard.icon name="menu" />
        </button>
        <button class="icon-button desktop-sidebar-toggle" id="sidebar-toggle" type="button" aria-label="Sembunyikan sidebar" aria-expanded="true" aria-controls="sidebar">
            <x-dashboard.icon name="panel" />
        </button>
        <span class="crumb-muted">Dashboard</span><span class="crumb-divider">/</span><span id="current-crumb">{{ $title }}</span>
    </div>
    <div class="top-actions">
        <span class="admin-label">{{ $identity['account'] }}</span>
        <button class="icon-button" id="theme-toggle" type="button" aria-label="Aktifkan mode gelap" title="Ubah tema">
            <x-dashboard.icon name="sun" />
        </button>
        <span class="top-avatar" aria-label="{{ $identity['name'] }}">{{ $identity['initials'] }}</span>
    </div>
</header>
