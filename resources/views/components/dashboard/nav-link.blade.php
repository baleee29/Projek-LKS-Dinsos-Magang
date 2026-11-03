@props(['href', 'label', 'icon' => null, 'active' => false, 'child' => false, 'count' => null, 'unread' => false])
@php
    $ariaLabel = $unread ? ($count === null ? "{$label}, jumlah belum tersedia" : "{$label}, ".($count ?: 0).' belum dibaca') : null;
    $showCount = $count !== null && $count !== '' && (! $unread || $count > 0);
@endphp
<a href="{{ $href }}" @class(['nav-item', 'is-active' => $active, 'nav-child' => $child]) @if ($active) aria-current="page" @endif @if ($ariaLabel) aria-label="{{ $ariaLabel }}" @endif>
    @if ($icon)
        <x-dashboard.icon :name="$icon" />
    @else
        <span class="nav-dot" aria-hidden="true"></span>
    @endif
    <span>{{ $label }}</span>
    @if ($showCount)
        <span class="psm-nav-count">{{ $count }}</span>
    @endif
</a>
