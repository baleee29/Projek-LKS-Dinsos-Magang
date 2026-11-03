@props(['status', 'variant' => 'pill', 'label' => null])
@php($tone = \App\Support\Format::statusClass($status))
@if ($variant === 'lks')
    <span class="lks-status lks-status-{{ $tone }}">{{ $label ?? $status }}</span>
@else
    <span class="status-pill status-{{ $tone }}">{{ $label ?? $status }}</span>
@endif
