@extends('layouts.dashboard')

@section('title', $item['title'])

@php
    use App\Support\Format;
@endphp

@section('content')
    <div class="section-heading">
        <div>
            <h1 id="view-title" tabindex="-1">{{ $item['title'] }}</h1>
            <p class="view-description">{{ $item['organizationTitle'] }}</p>
        </div>
        <div class="q-actions">
            {{-- TODO(Tahap 2): POST /notifications/{id}/unread. --}}
            <button class="text-button" type="button" data-demo-action>Tandai belum dibaca</button>
            <a class="text-button" href="{{ $backUrl }}">Kembali ke notifikasi</a>
        </div>
    </div>
    <div class="detail-primary">
        <section class="detail-card">
            <h2>Informasi pemberitahuan</h2>
            <p>{{ $item['message'] }}</p>
            <p>{{ Format::dateTime($item['at']) }}</p>
            @if ($item['url'])
                <a class="text-button" href="{{ $item['url'] }}">Buka data terkait</a>
            @endif
        </section>
        <section class="detail-card">
            <h2>Detail data terkait</h2>
            @if ($item['exists'])
                <dl class="field-grid">
                    @foreach ($item['fields'] as $key => $value)
                        <div><dt>{{ $labels[$key] ?? $key }}</dt><dd>{{ $value }}</dd></div>
                    @endforeach
                </dl>
            @else
                <p>Data terkait sudah dihapus atau merupakan riwayat sebelum migrasi. Pemberitahuan tetap tersimpan.</p>
            @endif
        </section>
    </div>
@endsection
