@props(['role', 'identity', 'counts', 'navigation'])
@php
    $roleName = config('navigation.role_names.'.$role);
    $href = fn (string $path) => "/{$role}/{$path}";
    $count = fn (array $item) => isset($item['count']) ? ($counts[$item['count']] ?? null) : null;
@endphp
<aside @class(['sidebar', 'admin-sidebar' => $role === 'admin']) id="sidebar" aria-label="Navigasi {{ $roleName }}">
    <div class="sidebar-brand">
        <span class="dashboard-brand-logo">
            <img src="/assets/images/branding/sidaya-logo-480.webp" alt="SiDaya" width="1080" height="1080" decoding="async">
        </span>
        <span>SiDaya · RUANG {{ $roleName }}</span>
        <button class="icon-button sidebar-close" id="sidebar-close" type="button" aria-label="Tutup menu">
            <x-dashboard.icon name="close" />
        </button>
    </div>
    <div class="profile">
        <span class="avatar" aria-hidden="true">{{ $identity['initials'] }}</span>
        <div class="profile-details">
            <span class="profile-name">{{ $identity['name'] }}</span>
            @if ($identity['organization'])
                <span class="profile-role">{{ $identity['organization'] }}</span>
            @endif
        </div>
    </div>
    <nav class="side-nav" id="side-nav" aria-label="Menu dashboard {{ $roleName }}">
        @if ($role === 'admin')
            @foreach (config('navigation.admin') as $section)
                <section @class(['admin-sidebar-group', 'admin-pillar-group' => $section['type'] === 'pillar'])>
                    @if ($section['type'] === 'pillar')
                        <button @class(['admin-pillar-card-toggle', 'is-current' => $navigation->groupActive($section['key'])]) type="button" aria-expanded="true" aria-controls="nav-admin-{{ $section['key'] }}" data-nav-toggle>
                            @isset($section['logo'])
                                <div class="pillar-toggle-icon has-logo"><img src="{{ $section['logo'] }}" alt="" width="30" height="30"></div>
                            @else
                                <div class="pillar-toggle-icon"><x-dashboard.icon :name="$section['icon']" /></div>
                            @endisset
                            <div class="pillar-toggle-info">
                                <strong class="pillar-toggle-title">{{ $section['title'] }}</strong>
                                <span class="pillar-toggle-desc">{{ $section['description'] }}</span>
                            </div>
                            <x-dashboard.icon name="chevron" class="pillar-toggle-chevron" />
                        </button>
                    @else
                        <button @class(['admin-sidebar-section-toggle', 'is-current' => $navigation->groupActive($section['key'])]) type="button" aria-expanded="true" aria-controls="nav-admin-{{ $section['key'] }}" data-nav-toggle>
                            <span>{{ $section['label'] }}</span><x-dashboard.icon name="chevron" />
                        </button>
                    @endif
                    <div id="nav-admin-{{ $section['key'] }}" @class(['admin-sidebar-group-items', 'admin-pillar-subitems' => $section['type'] === 'pillar'])>
                        @foreach ($section['items'] as $item)
                            @if (($item['type'] ?? null) === 'group')
                                <div>
                                    <button @class(['nav-item', 'group-toggle', 'is-active' => $navigation->groupActive($item['key'])]) type="button" aria-expanded="true" aria-controls="{{ $item['id'] }}" data-nav-toggle>
                                        <x-dashboard.icon :name="$item['icon']" /><span>{{ $item['label'] }}</span><x-dashboard.icon name="chevron" />
                                    </button>
                                    <div id="{{ $item['id'] }}" class="nav-children">
                                        @foreach ($item['items'] as $child)
                                            <x-dashboard.nav-link :href="$href($child['path'])" :label="$child['label']" :active="$navigation->active($child['path'])" :count="$count($child)" child />
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <x-dashboard.nav-link :href="$href($item['path'])" :label="$item['label']" :icon="$item['icon'] ?? null" :active="$navigation->active($item['path'])" :count="$count($item)" :unread="$item['unread'] ?? false" />
                            @endif
                        @endforeach
                    </div>
                </section>
            @endforeach
        @else
            @foreach (config('navigation.lks') as $index => $group)
                <p @class(['nav-heading', 'nav-heading-main' => $index > 0])>{{ $group['heading'] }}</p>
                @foreach ($group['items'] as $item)
                    <x-dashboard.nav-link :href="$href($item['path'])" :label="$item['label']" :icon="$item['icon']" :active="$navigation->active($item['path'])" :count="$count($item)" :unread="$item['unread'] ?? false" />
                @endforeach
            @endforeach
        @endif
    </nav>
    <div class="sidebar-footer">
        @if ($role === 'admin')
            <a @class(['q-account-link', 'is-active' => $navigation->active('account')]) id="account-settings-link" @if ($navigation->active('account')) aria-current="page" @endif href="{{ $href('account') }}"><x-dashboard.icon name="settings" /><span>Pengaturan Akun</span></a>
            <a class="q-account-link" id="change-password-link" href="/account/password"><x-dashboard.icon name="settings" /><span>Ganti Kata Sandi</span></a>
        @endif
        <button class="logout-button" id="logout-button" type="button">
            Logout
            <x-dashboard.icon name="arrow" />
        </button>
        <span>SiDaya · Kota Banjarbaru</span>
    </div>
</aside>
