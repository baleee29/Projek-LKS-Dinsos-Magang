<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $description }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#102a43">
    <title>{!! $title !!}</title>
    <link rel="preload" href="/assets/fonts/plus-jakarta-sans-400.woff2" as="font" type="font/woff2" crossorigin>
    @vite(['resources/css/entries/public-'.$entry.'.css', 'resources/js/public/entries/'.$entry.'.js'])
</head>
<body @if($bodyId) id="{{ $bodyId }}" @endif @if($bodyClass) class="{{ $bodyClass }}" @endif>
    <a class="skip-link" href="#konten">Lewati ke konten</a>
    @php($publicBrand = $publicBrand ?? 'Dayasos')
    @include('components.public.navigation')
    @yield('content')
    @include('components.public.footer')
    @if($showInfoDialog)
        @include('components.public.info-dialog')
    @endif
    @yield('dialogs')
</body>
</html>
