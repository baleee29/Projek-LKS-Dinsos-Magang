@isset($back)
    <a class="admin-correspondence-button" href="/admin/{{ $back }}">Kembali ke daftar</a>
@endisset
<header class="admin-correspondence-heading"><div><small>Karang Taruna</small><h1 id="view-title" tabindex="-1">{{ $title }}</h1><p>{{ $description }}</p></div><div class="admin-correspondence-actions">{!! $action ?? '' !!}</div></header>
