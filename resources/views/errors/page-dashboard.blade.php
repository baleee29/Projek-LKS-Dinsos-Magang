@extends('layouts.dashboard')

@section('title', $heading)

@section('content')
    <div class="section-heading">
        <div>
            <p class="eyebrow">{{ $role === 'admin' ? 'Dinas Sosial Kota Banjarbaru' : $identity['organization'] }} · Kode {{ $code }}</p>
            <h1 id="view-title" tabindex="-1">{{ $heading }}</h1>
            <p class="view-description">{{ $description }}</p>
        </div>
        <div class="q-actions">
            <a class="export-button" href="/{{ $role }}/overview">Ke ringkasan</a>
        </div>
    </div>
@endsection
