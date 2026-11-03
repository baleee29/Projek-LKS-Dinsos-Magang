@extends('layouts.auth')

@section('title', 'Masuk ke SiDaya')
@section('body-class', 'has-site-footer sidaya-login')

@push('scripts')
    @vite(['resources/js/auth/login.js'])
@endpush

@php
    $crew = [
        ['key' => 'lks', 'label' => 'LKS', 'body' => '#00a86b', 'accent' => '#0b3c26', 'greeting' => 'LKS hadir untuk sesama!'],
    ];
@endphp

@section('content')
    <main class="login-page">
        <div class="login-logo-field" aria-hidden="true"></div>
        <div class="login-layout">
            <section class="login-intro" aria-labelledby="welcome-title">
                <span class="intro-glow" aria-hidden="true"></span>
                <span class="intro-orb orb-a" aria-hidden="true"></span>
                <span class="intro-orb orb-b" aria-hidden="true"></span>
                <p class="intro-eyebrow">TERHUBUNG UNTUK BERDAYA</p>
                <h2 id="welcome-title">
                    Selamat datang<br>kembali di SiDaya.
                </h2>
                <p class="intro-description">
                    Satu ruang untuk PSM, LKS, dan Karang Taruna bergerak
                    bersama.
                </p>
                <div class="login-crew" aria-hidden="true">
                    @foreach ($crew as $member)
                        <button type="button" tabindex="-1" class="crew-member crew-{{ $member['key'] }}" style="--body: {{ $member['body'] }}; --accent: {{ $member['accent'] }}" data-greeting="{{ $member['greeting'] }}">
                        <svg viewBox="0 0 120 170">
                            <ellipse class="crew-shadow" cx="60" cy="163" rx="34" ry="6" />
                            <g class="crew-arm crew-arm-left"><path d="M30 104 Q14 118 18 136" /></g>
                            <g class="crew-arm crew-arm-right"><path d="M90 104 Q106 118 102 136" /></g>
                            <path class="crew-body" d="M28 160V112a32 32 0 0 1 64 0v48Z" />
                            <text class="crew-tag" x="60" y="140" text-anchor="middle">{{ $member['label'] }}</text>
                            <circle class="crew-head" cx="60" cy="62" r="34" />
                            <g class="crew-hat">
                                <path d="M28 52a34 34 0 0 1 64 0c-9-5-20-8-32-8s-23 3-32 8Z" />
                                <path class="crew-leaf" d="M60 30c-2-10 4-18 14-20-1 10-6 17-14 20Z" />
                            </g>
                            <g class="crew-eyes">
                                <g class="crew-eye">
                                    <ellipse cx="47" cy="68" rx="8.5" ry="9.5" />
                                    <circle class="crew-pupil" cx="47" cy="69" data-x="47" data-y="69" r="4.2" />
                                </g>
                                <g class="crew-eye">
                                    <ellipse cx="73" cy="68" rx="8.5" ry="9.5" />
                                    <circle class="crew-pupil" cx="73" cy="69" data-x="73" data-y="69" r="4.2" />
                                </g>
                            </g>
                            <path class="crew-closed" d="M40 69q7 5 14 0M66 69q7 5 14 0" />
                            <circle class="crew-cheek" cx="38" cy="82" r="5" />
                            <circle class="crew-cheek" cx="82" cy="82" r="5" />
                            <path class="crew-mouth" d="M52 84q8 8 16 0" />
                            <ellipse class="crew-mouth-open" cx="60" cy="87" rx="6" ry="5" />
                            <g class="crew-hands">
                                <circle cx="44" cy="68" r="9" />
                                <circle cx="76" cy="68" r="9" />
                            </g>
                        </svg>
                        </button>
                    @endforeach
                </div>
            </section>

            <section class="login-card" aria-labelledby="login-title" @if ($errors->any()) data-has-errors="1" @endif>
                <header class="card-header">
                                        <a class="login-brand" href="/" aria-label="Beranda SiDaya">
                        <img src="/assets/images/branding/sidaya-logo-480.webp" alt="" width="64" height="64">
                    </a>
                    <p class="form-eyebrow">AKUN SiDaya</p>
                    <h1 id="login-title">Masuk ke akun Anda</h1>
                    <p class="login-description">
                        Gunakan email dan kata sandi sesuai akun Admin, PSM,
                        LKS, atau Karang Taruna. SiDaya akan membuka dashboard
                        akun tersebut.
                    </p>
                </header>

                {{-- TODO(Tahap 2): form ini memakai autentikasi sungguhan (SessionController sumber). --}}
                <form id="login-form" method="post" action="{{ route('login.store') }}" novalidate>
                    @csrf
                    <div class="field email-field">
                        <label for="email">Email atau Username<span class="desktop-label"> Anda</span></label>
                        <div class="input-shell">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="8" r="4" /><path d="M4 21a8 8 0 0 1 16 0" /></svg>
                            <input id="email" name="email" value="{{ old('email') }}" @error('email') aria-invalid="true" @enderror type="text" placeholder="Email atau username akun" autocomplete="username" autocapitalize="none" spellcheck="false" required aria-describedby="email-error">
                        </div>
                        @error('email')
                            <p class="field-error" id="email-error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field password-field">
                        <div class="password-label">
                            <label for="password">Kata Sandi</label>
                        </div>
                        <div class="password-input input-shell">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="4" y="10" width="16" height="11" rx="2" /><path d="M8 10V7a4 4 0 0 1 8 0v3" /></svg>
                            <input id="password" name="password" type="password" @error('password') aria-invalid="true" @enderror placeholder="Masukkan kata sandi" autocomplete="current-password" required aria-describedby="password-rules password-error">
                            <button class="password-toggle" type="button" aria-label="Tampilkan kata sandi" aria-controls="password" aria-pressed="false">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                                    <g data-password-icon="shown" hidden><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.5 5.3A13 13 0 0 1 12 5c6 0 10 7 10 7a19 19 0 0 1-3.2 3.8M6.1 6.1A24 24 0 0 0 2 12s4 7 10 7a12 12 0 0 0 5-1.2" /></g>
                                    <g data-password-icon="hidden"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z" /><circle cx="12" cy="12" r="3" /></g>
                                </svg>
                            </button>
                        </div>
                        <p class="caps-warning" role="status" hidden>Caps Lock aktif</p>
                        <ul class="password-rules" id="password-rules" aria-live="polite">
                            @foreach (['length' => 'Minimal 10 karakter', 'letter' => 'Memuat huruf', 'number' => 'Memuat angka'] as $rule => $label)
                                <li data-rule="{{ $rule }}">
                                    <span class="password-rule-icon" aria-hidden="true">•</span>
                                    {{ $label }}
                                    <span class="visually-hidden">(belum terpenuhi)</span>
                                </li>
                            @endforeach
                        </ul>
                        @error('password')
                            <p class="field-error" id="password-error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <button class="submit-button" type="submit">
                        <span>Masuk</span>
                        <svg class="submit-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                    </button>
                    <div class="login-return">
                        <a class="back-link login-back-link" href="/">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 5-7 7 7 7M5 12h14" /></svg>
                            Kembali ke Beranda SiDaya
                        </a>
                    </div>
                </form>

                @if (count($demoAccounts))
                    <section class="demo-accounts" aria-labelledby="demo-accounts-title">
                        <h2 id="demo-accounts-title">Pilihan Akun Cepat</h2>
                        <p>Pilih akun untuk langsung masuk ke dashboard demo. Kata sandi tidak diperiksa.</p>
                        <div class="demo-account-list">
                            @foreach ($demoAccounts as $account)
                                <div class="demo-account">
                                    <div>
                                        <strong>{{ $account['label'] }}</strong><span>{{ $account['hint'] }}</span>
                                    </div>
                                    <form method="post" action="{{ route('login.demo', $account['role']) }}">
                                        @csrf
                                        <button type="submit" data-demo-account="{{ $account['role'] }}" aria-label="Masuk sebagai {{ $account['label'] }}">Masuk</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
                <noscript><p class="noscript-message">Aktifkan JavaScript untuk mencoba form login ini.</p></noscript>
            </section>
        </div>
    </main>
    <footer class="site-footer">
            <div class="container">
                <div class="footer-main">
                    <div class="footer-brand">
                        <a class="brand" href="/login">SiDaya</a>
                        <p>
                            Informasi pemberdayaan sosial untuk masyarakat<br />Kota
                            Banjarbaru.
                        </p>

                        <div class="social-icons" aria-label="Media sosial Dinas Sosial">
                            <a
                                href="https://www.instagram.com/dinsoskotabanjarbaru?exln=MXV6MWtiM2lhZ3FxeA%3D%3D"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="Instagram Dinas Sosial Kota Banjarbaru (tab baru)"
                            >
                                <svg aria-hidden="true" width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <rect x="3" y="3" width="18" height="18" rx="5" />
                                    <circle cx="12" cy="12" r="4" />
                                    <circle cx="17.5" cy="6.5" r=".7" fill="currentColor" stroke="none" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="footer-column">
                        <h2><span class="brand-name-case">SiDaya</span></h2>
                        <a href="/index.html#profil">Kenali SiDaya</a>
                        <a href="/pilar-sosial.html">Pilar sosial</a>
                        <a href="/index.html#layanan">Hubungi Dinsos</a>
                        <a href="/index.html#galeri">Galeri kegiatan</a>
                    </div>
                    <div class="footer-column">
                        <h2>Pilar Sosial</h2>
                        <a href="/pilar/lks.html">LKS</a>
                        <a href="/pilar/lks.html#pendaftaran">Panduan pendaftaran</a>
                        <a href="/pilar-sosial.html">Semua pilar sosial</a>
                    </div>
                    <div class="footer-column">
                        <h2>Tautan</h2>
                        <a
                            href="https://dinsos.banjarbarukota.go.id/pelayanan-dinas-sosial/"
                            >Layanan Dinsos</a
                        >
                        <a
                            href="https://dinsos.banjarbarukota.go.id/jam-pelayanan/"
                            >Jam pelayanan</a
                        >
                        <a href="https://banjarbarukota.go.id/perangkat-daerah"
                            >Pemkot Banjarbaru</a
                        >
                        <a href="/kontak.html">Halaman kontak</a>
                    </div>
                </div>

                <div class="footer-bottom">
                    <p>© SiDaya · Dinas Sosial Kota Banjarbaru</p>
                    <div>
                        <button
                            type="button"
                            data-content-title="Kebijakan Privasi"
                            data-content-message="Kebijakan privasi SiDaya belum tersedia. Peta pada halaman publik SiDaya dimuat dari Google Maps. Kebijakan layanan pihak tersebut berlaku saat Anda menggunakannya."
                        >
                            Kebijakan Privasi
                        </button>
                        <button
                            type="button"
                            data-content-title="Ketentuan Penggunaan"
                            data-content-message="Ketentuan penggunaan SiDaya belum tersedia. Informasi beranda membantu Anda mengenali layanan; persyaratan dan jadwal terbaru dapat ditanyakan langsung kepada Dinas Sosial Kota Banjarbaru."
                        >
                            Ketentuan Penggunaan
                        </button>
                    </div>
                </div>
            </div>
        </footer>
    <dialog class="site-chrome-dialog" aria-labelledby="footer-info-title" aria-describedby="footer-info-message">
        <h2 id="footer-info-title"></h2>
        <p id="footer-info-message"></p>
        <form method="dialog"><button autofocus>Tutup</button></form>
    </dialog>
@endsection
