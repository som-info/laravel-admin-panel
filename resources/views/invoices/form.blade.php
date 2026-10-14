@php $editing = $invoice->exists; @endphp
<x-layouts.app :title="$editing ? __('Edit invoice').' '.$invoice->number : __('New invoice')">
    <form method="POST" action="{{ $editing ? route('invoices.update', $invoice) : route('invoices.store') }}" class="mx-auto max-w-3xl">
        @csrf @if($editing) @method('PUT') @endif
        <x-card class="p-6">
            <h2 class="text-base font-semibold text-slate-900">{{ __('Invoice details') }}</h2>
            <p class="mb-6 mt-1 text-sm text-slate-500">{{ __('Each order can have one invoice. Tax is added on top of the amount.') }}</p>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-field name="order_id" :label="__('Order')" :value="$invoice->order_id" required class="sm:col-span-2"
                    :options="collect(['' => __('Select an order')])->union($orders->mapWithKeys(fn ($o) => [$o->id => $o->number.' — '.$o->customer->name.' ('.number_format($o->total).')']))" />
                <x-field name="amount" type="number" :label="__('Amount (Toman)')" :value="$invoice->amount" required min="0" dir="ltr" />
                <x-field name="tax_rate" type="number" :label="__('Tax rate (%)')" :value="$invoice->tax_rate" required min="0" max="50" dir="ltr" />
                <x-field name="issued_at" type="date" :label="__('Issue date')" :value="$invoice->issued_at?->format('Y-m-d')" required dir="ltr" />
                <x-field name="due_at" type="date" :label="__('Due date')" :value="$invoice->due_at?->format('Y-m-d')" required dir="ltr" />
                <x-field name="status" :label="__('Status')" :value="$invoice->status" required
                    :options="collect(\App\Models\Invoice::STATUSES)->mapWithKeys(fn ($s) => [$s => __('status.'.$s)])" />
            </div>
        </x-card>
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('invoices.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
            <button class="btn-primary">{{ $editing ? __('Save changes') : __('Create invoice') }}</button>
        </div>
    </form>
</x-layouts.app>
