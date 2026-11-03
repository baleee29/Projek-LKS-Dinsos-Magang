@extends('layouts.auth')

@section('title', $heading)
@section('body-class', 'sidaya-login')

@section('content')
    <main class="login-page">
        <div class="login-layout is-single">
            <section class="login-card" aria-labelledby="error-title">
                <header class="card-header">
                    <p class="form-eyebrow">KODE {{ $code }}</p>
                    <h1 id="error-title">{{ $heading }}</h1>
                    <p class="login-description">{{ $description }}</p>
                </header>
                <div class="login-return">
                    <a href="/login">Kembali ke halaman login</a>
                </div>
            </section>
        </div>
    </main>
@endsection
