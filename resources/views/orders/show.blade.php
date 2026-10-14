<x-layouts.app :title="__('Order').' '.$order->number">
    <div class="mx-auto max-w-5xl">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold text-slate-900" dir="ltr">{{ $order->number }}</h2>
                <x-badge :status="$order->status" />
            </div>
            <div class="flex gap-2">
                @if (! $order->invoice)
                    @can('manage-invoices')<a href="{{ route('invoices.create', ['order' => $order->id]) }}" class="btn-secondary">{{ __('Create invoice') }}</a>@endcan
                @endif
                <a href="{{ route('orders.edit', $order) }}" class="btn-primary">{{ __('Edit order') }}</a>
            </div>
        </div>
        <div class="grid gap-6 lg:grid-cols-3">
            <x-card class="overflow-hidden lg:col-span-2">
                <table class="min-w-full">
                    <thead class="table-head"><tr><th>{{ __('Product') }}</th><th>{{ __('Unit price') }}</th><th>{{ __('Quantity') }}</th><th>{{ __('Subtotal') }}</th></tr></thead>
                    <tbody class="table-body">
                        @foreach ($order->items as $item)
                            <tr><td class="font-medium">{{ $item->product->name }}</td><td>{{ money($item->unit_price) }}</td><td>{{ $item->quantity }}</td><td class="font-semibold">{{ money($item->unit_price * $item->quantity) }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="flex justify-between border-t border-slate-100 bg-slate-50 px-4 py-4 font-bold"><span>{{ __('Total') }}</span><span>{{ money($order->total) }}</span></div>
            </x-card>
            <div class="space-y-6">
                <x-card class="p-5 text-sm">
                    <h3 class="mb-3 font-semibold text-slate-900">{{ __('Customer') }}</h3>
                    <p class="font-medium">{{ $order->customer->name }}</p>
                    <p class="text-slate-500">{{ $order->customer->company }}</p>
                    <p class="mt-2 text-slate-500" dir="ltr">{{ $order->customer->email }}</p>
                </x-card>
                <x-card class="space-y-2 p-5 text-sm">
                    <div class="flex justify-between"><span class="text-slate-500">{{ __('Order date') }}</span><span dir="ltr">{{ $order->ordered_at->format('Y-m-d') }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">{{ __('Created by') }}</span><span>{{ $order->user?->name ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">{{ __('Invoice') }}</span>
                        @if ($order->invoice)<span class="flex items-center gap-2"><span dir="ltr">{{ $order->invoice->number }}</span><x-badge :status="$order->invoice->status" /></span>@else<span>—</span>@endif
                    </div>
                    @if ($order->notes)<p class="border-t border-slate-100 pt-3 text-slate-600">{{ $order->notes }}</p>@endif
                </x-card>
            </div>
        </div>
    </div>
</x-layouts.app>
