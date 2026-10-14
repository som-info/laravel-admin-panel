<x-layouts.app :title="__('Orders')">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">{{ __(':count orders found', ['count' => number_format($orders->total())]) }}</p>
        <a href="{{ route('orders.create') }}" class="btn-primary">+ {{ __('New order') }}</a>
    </div>
    <x-card class="overflow-hidden">
        <x-filters :placeholder="__('Search order number or customer...')">
            <select name="status" class="input sm:w-40">
                <option value="">{{ __('All statuses') }}</option>
                @foreach (\App\Models\Order::STATUSES as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ __('status.'.$s) }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ request('from') }}" class="input sm:w-40" title="{{ __('From') }}">
            <input type="date" name="to" value="{{ request('to') }}" class="input sm:w-40" title="{{ __('To') }}">
        </x-filters>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="table-head"><tr><th>{{ __('Order') }}</th><th>{{ __('Customer') }}</th><th>{{ __('Date') }}</th><th>{{ __('Items') }}</th><th>{{ __('Total') }}</th><th>{{ __('Status') }}</th><th></th></tr></thead>
                <tbody class="table-body">
                    @forelse ($orders as $order)
                        <tr>
                            <td><a href="{{ route('orders.show', $order) }}" class="font-semibold text-indigo-600" dir="ltr">{{ $order->number }}</a></td>
                            <td>{{ $order->customer->name }}</td>
                            <td class="text-slate-500"><span dir="ltr">{{ $order->ordered_at->format('Y-m-d') }}</span></td>
                            <td>{{ $order->items_count }}</td>
                            <td class="font-medium">{{ money($order->total) }}</td>
                            <td><x-badge :status="$order->status" /></td>
                            <td><div class="flex items-center justify-end gap-4">
                                <a href="{{ route('orders.edit', $order) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">{{ __('Edit') }}</a>
                                <x-delete-button :action="route('orders.destroy', $order)" />
                            </div></td>
                        </tr>
                    @empty
                        <x-empty />
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 p-4">{{ $orders->links() }}</div>
    </x-card>
</x-layouts.app>
