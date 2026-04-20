<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    
    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#7C3AED">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="CP Review">
    <link rel="apple-touch-icon" href="/icon-512.png">
    <link rel="icon" type="image/png" href="/favicon.png">

    <title>CP Review Care - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @if(session()->has('impersonate_tenant_id'))
    <div class="bg-red-600 text-white py-2 px-4 flex justify-between items-center text-xs font-bold uppercase tracking-widest sticky top-0 z-[9999] shadow-lg">
        <div class="flex items-center gap-2">
            <span>🕵️ MODO SUPORTE (SIMULANDO TENANT)</span>
        </div>
        <a href="{{ route('admin.stop-impersonation') }}" class="bg-white text-red-600 px-3 py-1 rounded-lg hover:bg-gray-100 transition">
            PARAR E VOLTAR
        </a>
    </div>
    @endif
    @yield('content')
</body>
</html>
