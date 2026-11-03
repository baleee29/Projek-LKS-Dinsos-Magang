<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="/index.html" aria-label="{{ $publicBrand }}, Dinas Sosial Kota Banjarbaru, beranda">
            <span class="brand-logos" aria-hidden="true"><img src="/assets/images/branding/logo-kota-banjarbaru.png" alt="" width="131" height="176"><img src="/assets/images/branding/logo-dinsos-banjarbaru.png" alt="" width="174" height="176"><img src="/assets/images/branding/logo-banjarbaru-emas.png" alt="" width="122" height="176"><img src="/assets/images/branding/logo-pemberdayaan-sosial.png" alt="" width="153" height="176"></span>
            <span class="brand-copy"><span class="brand-name">{{ $publicBrand }}</span><span class="brand-subtitle">Dinas Sosial Kota Banjarbaru</span></span>
        </a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="main-navigation" aria-label="Buka menu navigasi" hidden><span></span><span></span><span></span></button>
        <nav class="main-navigation" id="main-navigation" aria-label="Navigasi utama">
            <a class="nav-link{{ $navigation === 'home' ? ' is-current' : '' }}" href="/index.html" @if($navigation === 'home') aria-current="page" @endif>Beranda</a>
            <a class="nav-link{{ $navigation === 'profile' ? ' is-current' : '' }}" href="/profil.html" @if($navigation === 'profile') aria-current="page" @endif>Profil</a>
            <a class="nav-link{{ in_array($navigation, ['pillars', 'lks']) ? ' is-current' : '' }}" href="/pilar-sosial.html" @if(in_array($navigation, ['pillars', 'lks'])) aria-current="page" @endif>Pilar Sosial</a>
            <a class="nav-link{{ $navigation === 'contact' ? ' is-current' : '' }}" href="/kontak.html" @if($navigation === 'contact') aria-current="page" @endif>Kontak</a>
            <span class="nav-divider" aria-hidden="true"></span>
            <a class="button button-primary nav-login" href="/login">Masuk</a>
        </nav>
    </div>
</header>
