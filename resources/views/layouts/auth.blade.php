<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1a2536">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') — SiDaya</title>
    @vite(['resources/css/entries/auth.css'])
    @stack('scripts')
</head>
<body class="@yield('body-class')">
    @yield('content')
</body>
</html>
