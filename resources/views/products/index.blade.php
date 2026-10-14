<x-layouts.app :title="__('Products')">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">{{ __(':count products in catalog', ['count' => number_format($products->total())]) }}</p>
        @can('manage-catalog')<a href="{{ route('products.create') }}" class="btn-primary">+ {{ __('New product') }}</a>@endcan
    </div>
    <x-card class="overflow-hidden">
        <x-filters :placeholder="__('Search name or SKU...')">
            <select name="category" class="input sm:w-44">
                <option value="">{{ __('All categories') }}</option>
                @foreach (\App\Models\Product::CATEGORIES as $c)
                    <option value="{{ $c }}" @selected(request('category') === $c)>{{ __('category.'.$c) }}</option>
                @endforeach
            </select>
            <select name="stock" class="input sm:w-40">
                <option value="">{{ __('Any stock') }}</option>
                <option value="low" @selected(request('stock') === 'low')>{{ __('Low stock') }}</option>
                <option value="out" @selected(request('stock') === 'out')>{{ __('Out of stock') }}</option>
            </select>
        </x-filters>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="table-head"><tr><th>{{ __('Product') }}</th><th>SKU</th><th>{{ __('Category') }}</th><th>{{ __('Price') }}</th><th>{{ __('Stock') }}</th><th>{{ __('Status') }}</th><th></th></tr></thead>
                <tbody class="table-body">
                    @forelse ($products as $product)
                        <tr>
                            <td class="font-semibold text-slate-900">{{ $product->name }}</td>
                            <td class="font-mono text-xs text-slate-500" dir="ltr">{{ $product->sku }}</td>
                            <td>{{ __('category.'.$product->category) }}</td>
                            <td class="font-medium">{{ money($product->price) }}</td>
                            <td><span class="{{ $product->stock < 10 ? 'font-semibold text-rose-600' : '' }}">{{ number_format($product->stock) }}</span></td>
                            <td><x-badge :status="$product->is_active ? 'active' : 'inactive'" /></td>
                            <td><div class="flex items-center justify-end gap-4">
                                @can('manage-catalog')<a href="{{ route('products.edit', $product) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">{{ __('Edit') }}</a>@endcan
                                <x-delete-button :action="route('products.destroy', $product)" />
                            </div></td>
                        </tr>
                    @empty
                        <x-empty />
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 p-4">{{ $products->links() }}</div>
    </x-card>
</x-layouts.app>
