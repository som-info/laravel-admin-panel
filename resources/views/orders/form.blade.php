@php
    $editing = $order->exists;
    $items = old('items', $order->items->map(fn ($i) => ['product_id' => $i->product_id, 'quantity' => $i->quantity])->all() ?: [['product_id' => '', 'quantity' => 1]]);
@endphp
<x-layouts.app :title="$editing ? __('Edit order').' '.$order->number : __('New order')">
    <form method="POST" action="{{ $editing ? route('orders.update', $order) : route('orders.store') }}" class="mx-auto grid max-w-5xl gap-6 lg:grid-cols-3">
        @csrf @if($editing) @method('PUT') @endif
        <div class="space-y-6 lg:col-span-2">
            <x-card class="p-6">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">{{ __('Line items') }}</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ __('Prices are taken from the product catalog.') }}</p>
                    </div>
                    <button type="button" id="add-item" class="btn-secondary">+ {{ __('Add item') }}</button>
                </div>
                <div class="mb-2 grid grid-cols-12 gap-3 text-xs font-semibold text-slate-500"><div class="col-span-7">{{ __('Product') }}</div><div class="col-span-3">{{ __('Quantity') }}</div></div>
                <div id="order-items" class="space-y-3">
                    @foreach ($items as $i => $item)
                        @include('orders._item', ['index' => $i, 'productId' => $item['product_id'] ?? '', 'quantity' => $item['quantity'] ?? 1])
                    @endforeach
                </div>
                @if ($errors->has('items') || $errors->has('items.*'))
                    <p class="mt-3 text-xs text-rose-600">{{ $errors->first('items') ?: collect($errors->get('items.*'))->flatten()->first() }}</p>
                @endif
                <template id="item-template">@include('orders._item', ['index' => '__INDEX__', 'productId' => '', 'quantity' => 1])</template>
                <div class="mt-6 flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                    <span class="text-sm font-medium text-slate-600">{{ __('Estimated total') }}</span>
                    <span class="text-lg font-bold text-slate-900"><span id="order-total">0</span> {{ __('Toman') }}</span>
                </div>
            </x-card>
        </div>
        <div class="space-y-6">
            <x-card class="space-y-5 p-6">
                <h2 class="text-base font-semibold text-slate-900">{{ __('Order details') }}</h2>
                <x-field name="customer_id" :label="__('Customer')" :value="$order->customer_id" required
                    :options="collect(['' => __('Select a customer')])->union($customers->pluck('name', 'id'))" />
                <x-field name="status" :label="__('Status')" :value="$order->status" required
                    :options="collect(\App\Models\Order::STATUSES)->mapWithKeys(fn ($s) => [$s => __('status.'.$s)])" />
                <x-field name="ordered_at" type="date" :label="__('Order date')" :value="$order->ordered_at?->format('Y-m-d')" required dir="ltr" />
                <x-field name="notes" type="textarea" :label="__('Notes')" :value="$order->notes" />
            </x-card>
            <div class="flex gap-3">
                <a href="{{ $editing ? route('orders.show', $order) : route('orders.index') }}" class="btn-secondary flex-1">{{ __('Cancel') }}</a>
                <button class="btn-primary flex-1">{{ $editing ? __('Save changes') : __('Create order') }}</button>
            </div>
        </div>
    </form>
</x-layouts.app>
