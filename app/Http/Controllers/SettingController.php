<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $settings = collect(Setting::DEFAULTS)->mapWithKeys(fn ($v, $k) => [$k => Setting::get($k, $v)]);

        return view('settings.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:120'],
            'company_email' => ['required', 'email'],
            'currency' => ['required', Rule::in(['IRT', 'USD', 'EUR'])],
            'tax_rate' => ['required', 'integer', 'between:0,50'],
            'items_per_page' => ['required', 'integer', Rule::in([10, 15, 25, 50])],
            'default_locale' => ['required', Rule::in(['fa', 'en'])],
        ]);

        Setting::put($data);
        ActivityLog::record('updated', null, 'Application settings updated');

        return back()->with('success', __('Settings saved.'));
    }
}
