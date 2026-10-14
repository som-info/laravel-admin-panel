@props(['placeholder' => __('Search...')])
<form method="GET" class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center">
    <div class="relative flex-1">
        <svg class="pointer-events-none absolute top-1/2 start-3 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 21l-4.3-4.3M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/></svg>
        <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ $placeholder }}" class="input ps-9">
    </div>
    {{ $slot }}
    <div class="flex gap-2">
        <button class="btn-secondary">{{ __('Filter') }}</button>
        @if (collect(request()->except('page'))->filter()->isNotEmpty())
            <a href="{{ url()->current() }}" class="btn-ghost">{{ __('Reset') }}</a>
        @endif
    </div>
</form>
