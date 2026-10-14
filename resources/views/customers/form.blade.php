@php $editing = $customer->exists; @endphp
<x-layouts.app :title="$editing ? __('Edit customer') : __('New customer')">
    <form method="POST" action="{{ $editing ? route('customers.update', $customer) : route('customers.store') }}" class="mx-auto max-w-3xl">
        @csrf @if($editing) @method('PUT') @endif
        <x-card class="p-6">
            <h2 class="text-base font-semibold text-slate-900">{{ __('Customer details') }}</h2>
            <p class="mb-6 mt-1 text-sm text-slate-500">{{ __('Contact information used on orders and invoices.') }}</p>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-field name="name" :label="__('Full name')" :value="$customer->name" required />
                <x-field name="email" type="email" :label="__('Email')" :value="$customer->email" required dir="ltr" />
                <x-field name="phone" :label="__('Phone')" :value="$customer->phone" dir="ltr" />
                <x-field name="company" :label="__('Company')" :value="$customer->company" />
                <x-field name="city" :label="__('City')" :value="$customer->city" />
                <x-field name="status" :label="__('Status')" :value="$customer->status" required
                    :options="collect(\App\Models\Customer::STATUSES)->mapWithKeys(fn ($s) => [$s => __('status.'.$s)])" />
                <x-field name="notes" type="textarea" :label="__('Notes')" :value="$customer->notes" class="sm:col-span-2" />
            </div>
        </x-card>
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('customers.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
            <button class="btn-primary">{{ $editing ? __('Save changes') : __('Create customer') }}</button>
        </div>
    </form>
</x-layouts.app>
