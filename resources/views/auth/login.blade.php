<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Sign in') }} · {{ __('Namvar Admin') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-50 antialiased">
<div class="grid min-h-screen lg:grid-cols-2">
    <div class="relative hidden overflow-hidden bg-gradient-to-br from-indigo-700 via-indigo-600 to-violet-700 p-12 text-white lg:flex lg:flex-col lg:justify-between">
        <div class="absolute -top-24 -end-24 h-96 w-96 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute bottom-0 start-0 h-72 w-72 rounded-full bg-violet-400/20 blur-3xl"></div>
        <div class="relative flex items-center gap-3">
            <div class="grid h-11 w-11 place-items-center rounded-xl bg-white/15 text-xl font-bold backdrop-blur">N</div>
            <span class="text-lg font-bold">{{ __('Namvar Admin') }}</span>
        </div>
        <div class="relative max-w-md">
            <h2 class="text-4xl font-extrabold leading-tight">{{ __('Run your whole business from one clean panel.') }}</h2>
            <p class="mt-4 text-indigo-100">{{ __('Customers, products, orders and invoices with role-based access, live reports and a full activity trail.') }}</p>
            <div class="mt-10 grid grid-cols-3 gap-4 text-center">
                @foreach ([['3', __('User roles')], ['4', __('Modules')], ['2', __('Languages')]] as [$n, $l])
                    <div class="rounded-2xl bg-white/10 p-4 backdrop-blur"><div class="text-2xl font-bold">{{ $n }}</div><div class="text-xs text-indigo-100">{{ $l }}</div></div>
                @endforeach
            </div>
        </div>
        <div class="relative text-xs text-indigo-200">© {{ date('Y') }} {{ __('Namvar Admin') }}</div>
    </div>

    <div class="flex items-center justify-center p-6">
        <div class="w-full max-w-sm">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">{{ __('Welcome back') }}</h1>
                    <p class="mt-1 text-sm text-slate-500">{{ __('Sign in to continue to your dashboard.') }}</p>
                </div>
                <div class="flex rounded-lg border border-slate-200 p-0.5 text-xs font-semibold">
                    <a href="{{ route('locale', 'fa') }}" class="rounded-md px-2.5 py-1 {{ app()->getLocale() === 'fa' ? 'bg-slate-900 text-white' : 'text-slate-500' }}">فا</a>
                    <a href="{{ route('locale', 'en') }}" class="rounded-md px-2.5 py-1 {{ app()->getLocale() === 'en' ? 'bg-slate-900 text-white' : 'text-slate-500' }}">EN</a>
                </div>
            </div>
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <x-field name="email" type="email" :label="__('Email')" required autofocus dir="ltr" value="admin@example.com" />
                <x-field name="password" type="password" :label="__('Password')" required dir="ltr" />
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600"> {{ __('Remember me') }}
                </label>
                <button class="btn-primary w-full py-2.5">{{ __('Sign in') }}</button>
            </form>
            <div class="mt-8 rounded-xl border border-dashed border-slate-300 bg-white p-4 text-xs text-slate-500">
                <div class="mb-2 font-semibold text-slate-700">{{ __('Demo accounts') }} <span class="font-normal">({{ __('password') }}: <code dir="ltr">password</code>)</span></div>
                <div dir="ltr" class="space-y-1 font-mono">
                    <div>admin@example.com · manager@example.com · staff@example.com</div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
