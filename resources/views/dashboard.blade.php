<x-layouts.app :title="__('Dashboard')">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900">{{ __('Hello, :name', ['name' => auth()->user()->name]) }} 👋</h2>
        <p class="mt-1 text-sm text-slate-500">{{ __("Here's what's happening with your business today.") }}</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $cards = [
                [__('Revenue this month'), money_card($stats['revenue']), 'from-indigo-500 to-violet-500', 'M12 8c-1.7 0-3 .9-3 2s1.3 2 3 2 3 .9 3 2-1.3 2-3 2m0-8c1.1 0 2.1.4 2.6 1M12 8V7m0 1v8m0 0v1m0-1c-1.1 0-2.1-.4-2.6-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                [__('Total orders'), number_format($stats['orders']), 'from-sky-500 to-cyan-500', 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
                [__('Customers'), number_format($stats['customers']), 'from-emerald-500 to-teal-500', 'M17 20h5v-2a4 4 0 00-5-3.9M9 20H2v-2a4 4 0 014-4h3a4 4 0 014 4v2M12 7a4 4 0 11-8 0 4 4 0 018 0z'],
                [__('Outstanding invoices'), money_card($stats['unpaid']), 'from-amber-500 to-orange-500', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.6l5.4 5.4V19a2 2 0 01-2 2z'],
            ];
        @endphp
        @foreach ($cards as [$label, $value, $gradient, $icon])
            <x-card class="p-5">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
                    <div class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br {{ $gradient }} text-white shadow-lg">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                    </div>
                </div>
                <p class="mt-3 whitespace-nowrap text-2xl font-bold text-slate-900">{!! $value !!}</p>
            </x-card>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <x-card class="p-5 xl:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="font-semibold text-slate-900">{{ __('Revenue & orders') }}</h3>
                <span class="text-xs text-slate-400">{{ __('Last 6 months') }}</span>
            </div>
            <div class="h-72"><canvas id="revenueChart"></canvas></div>
        </x-card>
        <x-card class="p-5">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="font-semibold text-slate-900">{{ __('Orders by status') }}</h3>
                @if ($stats['low_stock'])
                    <a href="{{ route('products.index', ['stock' => 'low']) }}" class="rounded-full bg-rose-50 px-2 py-0.5 text-xs font-medium text-rose-600">{{ __(':count low stock', ['count' => $stats['low_stock']]) }}</a>
                @endif
            </div>
            <div class="h-72"><canvas id="statusChart"></canvas></div>
        </x-card>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <x-card class="overflow-hidden xl:col-span-2">
            <div class="flex items-center justify-between p-5">
                <h3 class="font-semibold text-slate-900">{{ __('Recent orders') }}</h3>
                <a href="{{ route('orders.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">{{ __('View all') }}</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="table-head"><tr><th>{{ __('Order') }}</th><th>{{ __('Customer') }}</th><th>{{ __('Date') }}</th><th>{{ __('Status') }}</th><th>{{ __('Total') }}</th></tr></thead>
                    <tbody class="table-body">
                        @foreach ($recentOrders as $order)
                            <tr>
                                <td><a href="{{ route('orders.show', $order) }}" class="font-semibold text-indigo-600" dir="ltr">{{ $order->number }}</a></td>
                                <td>{{ $order->customer->name }}</td>
                                <td class="text-slate-500" dir="ltr">{{ $order->ordered_at->format('Y-m-d') }}</td>
                                <td><x-badge :status="$order->status" /></td>
                                <td class="font-medium">{{ money($order->total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
        <x-card class="p-5">
            <h3 class="mb-4 font-semibold text-slate-900">{{ __('Latest activity') }}</h3>
            <ul class="space-y-4">
                @forelse ($activity as $log)
                    <li class="flex gap-3">
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $log->action === 'deleted' ? 'bg-rose-500' : ($log->action === 'created' ? 'bg-emerald-500' : 'bg-indigo-500') }}"></span>
                        <div class="min-w-0">
                            <p class="truncate text-sm text-slate-700" dir="auto">{{ $log->description }}</p>
                            <p class="text-xs text-slate-400">{{ $log->user?->name ?? __('System') }} · {{ $log->created_at->diffForHumans() }}</p>
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-slate-400">{{ __('No records found.') }}</li>
                @endforelse
            </ul>
        </x-card>
    </div>

    <script>
        window.dashboardChart = @json($chart + ['revenueLabel' => __('Revenue'), 'ordersLabel' => __('Orders')]);
    </script>
</x-layouts.app>
