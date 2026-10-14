<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? __('Dashboard') }} · {{ \App\Models\Setting::get('company_name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 text-slate-800 antialiased">
@php
    $nav = [
        ['route' => 'dashboard', 'match' => 'dashboard', 'label' => __('Dashboard'), 'icon' => 'M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10'],
        ['route' => 'customers.index', 'match' => 'customers.*', 'label' => __('Customers'), 'icon' => 'M17 20h5v-2a4 4 0 00-5-3.9M9 20H2v-2a4 4 0 014-4h3a4 4 0 014 4v2M12 7a4 4 0 11-8 0 4 4 0 018 0zm8 1a3 3 0 11-6 0 3 3 0 016 0z'],
        ['route' => 'products.index', 'match' => 'products.*', 'label' => __('Products'), 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ['route' => 'orders.index', 'match' => 'orders.*', 'label' => __('Orders'), 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
        ['route' => 'invoices.index', 'match' => 'invoices.*', 'label' => __('Invoices'), 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.6a1 1 0 01.7.3l5.4 5.4a1 1 0 01.3.7V19a2 2 0 01-2 2z'],
    ];
    if (Gate::allows('view-activity')) $nav[] = ['route' => 'activity.index', 'match' => 'activity.*', 'label' => __('Activity log'), 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'];
    if (Gate::allows('manage-settings')) $nav[] = ['route' => 'settings.edit', 'match' => 'settings.*', 'label' => __('Settings'), 'icon' => 'M10.3 4.3c.4-1.8 3-1.8 3.4 0a1.7 1.7 0 002.6 1.1c1.5-.9 3.3.8 2.4 2.4a1.7 1.7 0 001 2.5c1.8.4 1.8 3 0 3.4a1.7 1.7 0 00-1 2.6c.9 1.5-.9 3.3-2.4 2.4a1.7 1.7 0 00-2.6 1c-.4 1.8-3 1.8-3.4 0a1.7 1.7 0 00-2.5-1c-1.6.9-3.3-.9-2.4-2.4a1.7 1.7 0 00-1.1-2.6c-1.8-.4-1.8-3 0-3.4a1.7 1.7 0 001.1-2.5c-.9-1.6.8-3.3 2.4-2.4 1 .6 2.3.1 2.5-1.1zM15 12a3 3 0 11-6 0 3 3 0 016 0z'];
@endphp
<div class="min-h-screen lg:flex">
    {{-- Sidebar --}}
    <aside id="sidebar" class="fixed inset-y-0 start-0 z-40 w-64 -translate-x-full rtl:translate-x-full transform bg-slate-900 text-slate-300 transition lg:static lg:translate-x-0 rtl:lg:translate-x-0 lg:shrink-0">
        <div class="flex h-16 items-center gap-3 px-6 border-b border-white/5">
            <div class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 font-bold text-white shadow-lg shadow-indigo-500/30">N</div>
            <div>
                <div class="text-sm font-bold text-white leading-tight">{{ __('Namvar Admin') }}</div>
                <div class="text-[11px] text-slate-400">{{ __('Business control panel') }}</div>
            </div>
        </div>
        <nav class="space-y-1 p-4">
            @foreach ($nav as $item)
                @php $active = request()->routeIs($item['match']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ $active ? 'bg-indigo-600 text-white shadow-md shadow-indigo-900/40' : 'hover:bg-white/5 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
        <div class="absolute bottom-0 inset-x-0 p-4">
            <div class="rounded-xl bg-white/5 p-3 text-xs">
                <div class="font-semibold text-white">{{ auth()->user()->name }}</div>
                <div class="mt-0.5 text-slate-400">{{ __('role.'.auth()->user()->role) }}</div>
            </div>
        </div>
    </aside>
    <div id="backdrop" class="fixed inset-0 z-30 hidden bg-slate-900/50 lg:hidden"></div>

    <div class="flex-1 min-w-0">
        {{-- Top bar --}}
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between gap-4 border-b border-slate-200 bg-white/80 px-4 backdrop-blur lg:px-8">
            <div class="flex items-center gap-3">
                <button id="menu-toggle" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden" aria-label="Menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-bold text-slate-900">{{ $title ?? __('Dashboard') }}</h1>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex rounded-lg border border-slate-200 p-0.5 text-xs font-semibold">
                    <a href="{{ route('locale', 'fa') }}" class="rounded-md px-2.5 py-1 {{ app()->getLocale() === 'fa' ? 'bg-slate-900 text-white' : 'text-slate-500' }}">فا</a>
                    <a href="{{ route('locale', 'en') }}" class="rounded-md px-2.5 py-1 {{ app()->getLocale() === 'en' ? 'bg-slate-900 text-white' : 'text-slate-500' }}">EN</a>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded-lg px-3 py-1.5 text-sm font-medium text-slate-500 hover:bg-slate-100 hover:text-slate-900">{{ __('Log out') }}</button>
                </form>
            </div>
        </header>

        <main class="p-4 lg:p-8">
            @if (session('success'))
                <div class="mb-6 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
