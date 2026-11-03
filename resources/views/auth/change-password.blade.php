@extends('layouts.auth')

@section('title', 'Ganti Kata Sandi')
@section('body-class', 'sidaya-login')

@push('scripts')
    @vite(['resources/js/auth/change-password.js'])
@endpush

@section('content')
    <main class="login-page">
        <div class="login-layout is-single">
            <section class="login-card" aria-labelledby="password-title">
                <header class="card-header">
                    <p class="form-eyebrow">AKUN SiDaya</p>
                    <h1 id="password-title">Ganti kata sandi</h1>
                    <p class="login-description">
                        Kata sandi baru minimal 10 karakter dan memuat huruf
                        serta angka. Sesi di perangkat lain akan dikeluarkan.
                    </p>
                </header>

                {{-- TODO(Tahap 2): kirim ke PUT /account/password (AccountPasswordController sumber). --}}
                <form id="change-password-form" novalidate>
                    <div class="field">
                        <label for="current_password">Kata sandi saat ini</label>
                        <input id="current_password" type="password" autocomplete="current-password" required aria-describedby="current-password-error">
                        <p class="field-error" id="current-password-error" role="alert" hidden></p>
                    </div>
                    <div class="field">
                        <label for="password">Kata sandi baru</label>
                        <input id="password" type="password" autocomplete="new-password" minlength="10" required aria-describedby="password-help password-error">
                        <p class="login-description" id="password-help">
                            Minimal 10 karakter, memuat huruf dan angka.
                        </p>
                        <p class="field-error" id="password-error" role="alert" hidden></p>
                    </div>
                    <div class="field">
                        <label for="password_confirmation">Ulangi kata sandi baru</label>
                        <input id="password_confirmation" type="password" autocomplete="new-password" required aria-describedby="password-confirmation-error">
                        <p class="field-error" id="password-confirmation-error" role="alert" hidden></p>
                    </div>
                    <label class="remember-row">
                        <input type="checkbox" id="show-password">
                        Tampilkan kata sandi
                    </label>
                    <button class="submit-button" type="submit">
                        Simpan kata sandi baru
                    </button>
                    <div class="login-return">
                        <a href="{{ $dashboard }}">Kembali ke dashboard</a>
                    </div>
                    <p class="form-status" id="form-status" role="status" hidden></p>
                </form>
            </section>
        </div>
    </main>
@endsection
