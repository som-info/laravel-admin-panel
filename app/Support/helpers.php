<?php

use App\Models\Setting;

if (! function_exists('money')) {
    /** Format an integer amount (in Toman) with the configured currency label. */
    function money(int|float|null $amount): string
    {
        $label = app()->getLocale() === 'fa' ? 'تومان' : (Setting::get('currency') === 'IRT' ? 'Toman' : Setting::get('currency'));

        return number_format((float) $amount).' '.$label;
    }
}

if (! function_exists('status_color')) {
    function status_color(string $status): string
    {
        return match ($status) {
            'active', 'completed', 'paid' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            'processing', 'shipped', 'lead' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
            'pending', 'unpaid' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            'overdue', 'cancelled' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
            default => 'bg-slate-100 text-slate-600 ring-slate-500/20',
        };
    }
}

if (! function_exists('money_short')) {
    /** Compact amount for summary cards, e.g. "1.8 billion Toman". */
    function money_short(int|float|null $amount): string
    {
        $fa = app()->getLocale() === 'fa';
        [$value, $unit] = match (true) {
            $amount >= 1_000_000_000 => [$amount / 1_000_000_000, $fa ? 'میلیارد' : 'B'],
            $amount >= 1_000_000 => [$amount / 1_000_000, $fa ? 'میلیون' : 'M'],
            default => [$amount, ''],
        };

        return rtrim(rtrim(number_format((float) $value, 1), '0'), '.').($unit ? ' '.$unit : '').' '.($fa ? 'تومان' : 'Toman');
    }
}

if (! function_exists('money_card')) {
    /** Compact amount as HTML with a muted currency suffix, for summary cards. */
    function money_card(int|float|null $amount): string
    {
        $parts = explode(' ', money_short($amount));
        $currency = array_pop($parts);

        return e(implode(' ', $parts)).' <span class="text-sm font-medium text-slate-400">'.e($currency).'</span>';
    }
}
