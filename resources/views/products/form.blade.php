@php $editing = $product->exists; @endphp
<x-layouts.app :title="$editing ? __('Edit product') : __('New product')">
    <form method="POST" action="{{ $editing ? route('products.update', $product) : route('products.store') }}" class="mx-auto max-w-3xl">
        @csrf @if($editing) @method('PUT') @endif
        <x-card class="p-6">
            <h2 class="text-base font-semibold text-slate-900">{{ __('Product details') }}</h2>
            <p class="mb-6 mt-1 text-sm text-slate-500">{{ __('Pricing and inventory for the catalog.') }}</p>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-field name="name" :label="__('Product name')" :value="$product->name" required class="sm:col-span-2" />
                <x-field name="sku" label="SKU" :value="$product->sku" required dir="ltr" />
                <x-field name="category" :label="__('Category')" :value="$product->category" required
                    :options="collect(\App\Models\Product::CATEGORIES)->mapWithKeys(fn ($c) => [$c => __('category.'.$c)])" />
                <x-field name="price" type="number" :label="__('Price (Toman)')" :value="$product->price" required min="0" dir="ltr" />
                <x-field name="stock" type="number" :label="__('Stock')" :value="$product->stock ?? 0" required min="0" dir="ltr" />
                <x-field name="description" type="textarea" :label="__('Description')" :value="$product->description" class="sm:col-span-2" />
                <label class="flex items-center gap-2 text-sm text-slate-700 sm:col-span-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active)) class="rounded border-slate-300 text-indigo-600">
                    {{ __('Active and available for orders') }}
                </label>
            </div>
        </x-card>
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('products.index') }}" class="btn-secondary">{{ __('Cancel') }}</a>
            <button class="btn-primary">{{ $editing ? __('Save changes') : __('Create product') }}</button>
        </div>
    </form>
</x-layouts.app>
