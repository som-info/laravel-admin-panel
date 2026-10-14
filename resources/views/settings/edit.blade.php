<x-layouts.app :title="__('Settings')">
    <form method="POST" action="{{ route('settings.update') }}" class="mx-auto max-w-3xl space-y-6">
        @csrf @method('PUT')
        <x-card class="p-6">
            <h2 class="text-base font-semibold text-slate-900">{{ __('Company') }}</h2>
            <p class="mb-6 mt-1 text-sm text-slate-500">{{ __('Shown in the panel title and on invoices.') }}</p>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-field name="company_name" :label="__('Company name')" :value="$settings['company_name']" required />
                <x-field name="company_email" type="email" :label="__('Company email')" :value="$settings['company_email']" required dir="ltr" />
            </div>
        </x-card>
        <x-card class="p-6">
            <h2 class="text-base font-semibold text-slate-900">{{ __('Preferences') }}</h2>
            <p class="mb-6 mt-1 text-sm text-slate-500">{{ __('Defaults applied across the whole panel.') }}</p>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-field name="currency" :label="__('Currency')" :value="$settings['currency']" required :options="['IRT' => __('Toman'), 'USD' => 'USD', 'EUR' => 'EUR']" />
                <x-field name="tax_rate" type="number" :label="__('Default tax rate (%)')" :value="$settings['tax_rate']" required dir="ltr" />
                <x-field name="items_per_page" :label="__('Rows per page')" :value="$settings['items_per_page']" required :options="[10 => 10, 15 => 15, 25 => 25, 50 => 50]" />
                <x-field name="default_locale" :label="__('Default language')" :value="$settings['default_locale']" required :options="['fa' => 'فارسی', 'en' => 'English']" />
            </div>
        </x-card>
        <div class="flex justify-end"><button class="btn-primary">{{ __('Save settings') }}</button></div>
    </form>
</x-layouts.app>
