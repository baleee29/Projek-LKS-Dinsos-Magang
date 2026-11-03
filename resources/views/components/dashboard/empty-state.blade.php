@props(['title', 'description', 'variant' => 'workspace', 'icon' => null])
@if ($variant === 'lks')
    <div class="lks-empty"><x-dashboard.icon :name="$icon ?? 'users'" /><h2>{{ $title }}</h2><p>{{ $description }}</p></div>
@else
    <div class="workspace-empty"><x-dashboard.icon :name="$icon ?? 'file'" /><h3>{{ $title }}</h3><p>{{ $description }}</p>{{ $slot }}</div>
@endif
