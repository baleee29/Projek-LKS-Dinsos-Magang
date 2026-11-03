<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1a2536">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ringkasan') — SiDaya</title>
    {{-- Dipasang sebelum CSS agar tema gelap tidak berkedip. Hanya pilihan tema yang disimpan di browser. --}}
    <script>
        try {
            if (localStorage.getItem('sidaya-theme') === 'dark') document.documentElement.classList.add('dark');
        } catch (error) {}
    </script>
    @vite(['resources/css/entries/'.$role.'.css', 'resources/js/dashboard/main.js'])
    @stack('scripts')
</head>
<body class="quarterly-dashboard" data-role="{{ $role }}">
    <a class="skip-link" href="#main-content">Lewati ke konten utama</a>
    <x-dashboard.symbols />
    <div class="dashboard-shell" id="dashboard-shell">
        <div class="sidebar-backdrop" id="sidebar-backdrop" hidden></div>
        <x-dashboard.sidebar :role="$role" :identity="$identity" :counts="$counts" :navigation="$navigation" />
        <div class="main-area" id="main-area">
            <x-dashboard.topbar :title="View::yieldContent('title', 'Ringkasan')" :identity="$identity" />
            <main class="dashboard-content" id="main-content" tabindex="-1">
                <div class="demo-banner">
                    <x-dashboard.icon name="info" /><span><strong>Pusat Layanan SiDaya</strong> — <span id="storage-description">Sistem Informasi dan Pelaporan LKS Kota Banjarbaru.</span></span>
                </div>
                <div>
                    <div id="view-content">
                        @yield('content')
                    </div>
                    <div class="q-toast" id="app-toast" role="status" aria-live="polite" @if (session('status')) data-flash="{{ session('status') }}" @endif hidden></div>
                </div>
            </main>
        </div>
    </div>
    <dialog class="detail-dialog logout-dialog" id="logout-dialog" aria-labelledby="logout-title" aria-describedby="logout-description">
        <h2 id="logout-title">Keluar dari dashboard?</h2>
        <p id="logout-description">Sesi akun akan diakhiri dan Anda kembali ke halaman login.</p>
        <form class="logout-actions" method="post" action="{{ route('logout') }}">
            @csrf
            <button class="text-button" id="cancel-logout" type="button">Batal</button><button class="export-button" id="confirm-logout" type="submit">Ya, logout</button>
        </form>
    </dialog>
    @stack('dialogs')
</body>
</html>
