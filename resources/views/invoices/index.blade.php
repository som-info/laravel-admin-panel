<x-layouts.app :title="__('Invoices')">
    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        @foreach (['paid' => 'text-emerald-600', 'unpaid' => 'text-amber-600', 'overdue' => 'text-rose-600'] as $key => $color)
            <x-card class="p-5">
                <p class="text-sm text-slate-500">{{ __('status.'.$key) }}</p>
                <p class="mt-1 text-xl font-bold {{ $color }}">{!! money_card($totals[$key]) !!}</p>
            </x-card>
        @endforeach
    </div>
    <div class="mb-4 flex justify-end">
        @can('manage-invoices')<a href="{{ route('invoices.create') }}" class="btn-primary">+ {{ __('New invoice') }}</a>@endcan
    </div>
    <x-card class="overflow-hidden">
        <x-filters :placeholder="__('Search invoice number or customer...')">
            <select name="status" class="input sm:w-40">
                <option value="">{{ __('All statuses') }}</option>
                @foreach (\App\Models\Invoice::STATUSES as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ __('status.'.$s) }}</option>
                @endforeach
            </select>
        </x-filters>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="table-head"><tr><th>{{ __('Invoice') }}</th><th>{{ __('Customer') }}</th><th>{{ __('Order') }}</th><th>{{ __('Issued') }}</th><th>{{ __('Due') }}</th><th>{{ __('Amount') }}</th><th>{{ __('Status') }}</th><th></th></tr></thead>
                <tbody class="table-body">
                    @forelse ($invoices as $invoice)
                        <tr>
                            <td class="font-semibold text-slate-900"><span dir="ltr">{{ $invoice->number }}</span></td>
                            <td>{{ $invoice->order->customer->name }}</td>
                            <td><a href="{{ route('orders.show', $invoice->order) }}" class="text-indigo-600" dir="ltr">{{ $invoice->order->number }}</a></td>
                            <td class="text-slate-500"><span dir="ltr">{{ $invoice->issued_at->format('Y-m-d') }}</span></td>
                            <td class="text-slate-500"><span dir="ltr">{{ $invoice->due_at->format('Y-m-d') }}</span></td>
                            <td class="font-medium">{{ money($invoice->grand_total) }}</td>
                            <td><x-badge :status="$invoice->status" /></td>
                            <td><div class="flex items-center justify-end gap-4">
                                @can('manage-invoices')<a href="{{ route('invoices.edit', $invoice) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">{{ __('Edit') }}</a>@endcan
                                <x-delete-button :action="route('invoices.destroy', $invoice)" />
                            </div></td>
                        </tr>
                    @empty
                        <x-empty />
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 p-4">{{ $invoices->links() }}</div>
    </x-card>
</x-layouts.app>
