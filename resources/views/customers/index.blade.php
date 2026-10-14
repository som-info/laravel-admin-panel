<x-layouts.app :title="__('Customers')">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">{{ __(':count customers in total', ['count' => number_format($customers->total())]) }}</p>
        <a href="{{ route('customers.create') }}" class="btn-primary">+ {{ __('New customer') }}</a>
    </div>
    <x-card class="overflow-hidden">
        <x-filters :placeholder="__('Search name, email, company or phone...')">
            <select name="status" class="input sm:w-44">
                <option value="">{{ __('All statuses') }}</option>
                @foreach (\App\Models\Customer::STATUSES as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ __('status.'.$s) }}</option>
                @endforeach
            </select>
        </x-filters>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="table-head"><tr><th>{{ __('Name') }}</th><th>{{ __('Company') }}</th><th>{{ __('Phone') }}</th><th>{{ __('City') }}</th><th>{{ __('Orders') }}</th><th>{{ __('Status') }}</th><th></th></tr></thead>
                <tbody class="table-body">
                    @forelse ($customers as $customer)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="grid h-9 w-9 place-items-center rounded-full bg-indigo-50 text-sm font-bold text-indigo-600">{{ mb_substr($customer->name, 0, 1) }}</div>
                                    <div><div class="font-semibold text-slate-900">{{ $customer->name }}</div><div class="text-xs text-slate-400" dir="ltr">{{ $customer->email }}</div></div>
                                </div>
                            </td>
                            <td>{{ $customer->company ?? '—' }}</td>
                            <td><span dir="ltr">{{ $customer->phone }}</span></td>
                            <td>{{ $customer->city }}</td>
                            <td>{{ $customer->orders_count }}</td>
                            <td><x-badge :status="$customer->status" /></td>
                            <td><div class="flex items-center justify-end gap-4">
                                <a href="{{ route('customers.edit', $customer) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">{{ __('Edit') }}</a>
                                <x-delete-button :action="route('customers.destroy', $customer)" />
                            </div></td>
                        </tr>
                    @empty
                        <x-empty />
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 p-4">{{ $customers->links() }}</div>
    </x-card>
</x-layouts.app>
