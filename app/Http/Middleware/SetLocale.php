<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale');

        if (! in_array($locale, ['fa', 'en'], true)) {
            try {
                $locale = Setting::get('default_locale', 'fa');
            } catch (\Throwable) {
                $locale = 'fa';
            }
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
