@extends('layouts.dashboard')

@section('title', 'Notifikasi')

@php
    use App\Support\Format;
    $tabs = ['all' => 'Semua', 'unread' => 'Belum dibaca', 'read' => 'Sudah dibaca'];
@endphp

@section('content')
    <x-dashboard.section-heading title="Notifikasi" :description="$admin ? 'Data masuk, pembaruan, penghapusan, dan perubahan status dari seluruh LKS.' : 'Pengajuan dan pembaruan status laporan organisasi Anda.'" :eyebrow="$eyebrow">
        <button class="text-button" type="button" id="mark-notifications-read" data-action-url="/notifications/read-all" @disabled($unreadCount === 0)>Tandai semua dibaca</button>
    </x-dashboard.section-heading>
    <section class="q-notification-panel" aria-label="{{ $admin ? 'Seluruh notifikasi Admin' : 'Pemberitahuan laporan' }}">
        <div class="q-notification-toolbar">
            <div class="q-notification-tabs" role="group" aria-label="{{ $admin ? 'Status dibaca' : 'Filter status dibaca' }}">
                @foreach ($tabs as $key => $title)
                    <button type="button" @class(['is-current' => $filters['filter'] === $key]) aria-pressed="{{ $filters['filter'] === $key ? 'true' : 'false' }}" data-href="{{ request()->fullUrlWithQuery(['filter' => $key === 'all' ? null : $key, 'page' => null]) }}">{{ $title }} <span>{{ $counts[$key] }}</span></button>
                @endforeach
            </div>
            <p id="notification-summary" role="status" aria-live="polite" aria-atomic="true" tabindex="-1">{{ $unreadCount }} belum dibaca dari {{ $all->count() }} notifikasi</p>
        </div>
        <form class="q-notification-search" method="get" data-filter-form>
            <input type="hidden" name="filter" value="{{ $filters['filter'] === 'all' ? '' : $filters['filter'] }}">
            <label class="list-search"><x-dashboard.icon name="search" /><span class="sr-only">Cari notifikasi</span><input type="search" name="q" value="{{ $filters['q'] }}" placeholder="{{ $admin ? 'Cari aktivitas atau nama lembaga…' : 'Cari laporan…' }}" @if ($filters['q']) data-autofocus @endif></label>
            <a class="text-button" href="{{ request()->url() }}">Hapus filter</a>
            @if ($admin)
                <a class="text-button" href="{{ request()->fullUrl() }}">Muat ulang</a>
            @endif
        </form>
        <div id="notification-results">
            @if ($notifications->count())
                <ul class="q-notification-list">
                    @foreach ($notifications as $item)
                        <li @class(['q-notification-item', 'is-unread' => ! $item['read']])>
                            <a class="q-notification-link" href="/{{ auth()->user()?->peran }}/notification-detail/{{ $item['id'] }}">
                                <span class="q-notification-dot" aria-hidden="true"></span>
                                <div class="q-notification-content">
                                    <div class="q-notification-heading"><h2>{{ $item['title'] }}</h2><time datetime="{{ $item['at'] }}">{{ Format::dateTime($item['at']) }}</time></div>
                                    <p class="q-notification-context">@if ($admin)LKS · {{ $item['organizationTitle'] }}@else{{ $item['periodLabel'] }}@endif</p>
                                    <p class="q-notification-message">{{ $item['message'] }}</p>
                                    <span class="q-notification-read-label">{{ $item['read'] ? 'Sudah dibaca' : 'Belum dibaca' }}</span>
                                </div>
                                <x-dashboard.icon name="arrow" />
                            </a>
                        </li>
                    @endforeach
                </ul>
                <x-dashboard.pagination :paginator="$notifications" class="q-notification-pagination" />
            @else
                <div class="q-notification-empty">
                    <h2>{{ $admin ? 'Belum ada notifikasi sesuai pilihan' : ($all->isEmpty() ? 'Belum ada notifikasi laporan' : 'Tidak ada notifikasi sesuai pilihan') }}</h2>
                    <p>{{ $admin ? 'Pemberitahuan muncul setelah data atau perubahan berhasil disimpan.' : ($all->isEmpty() ? 'Notifikasi mencatat pengiriman laporan dan pembaruan yang telah dikirim.' : 'Ubah pencarian atau filter untuk melihat pemberitahuan lainnya.') }}</p>
                </div>
            @endif
        </div>
    </section>
@endsection
