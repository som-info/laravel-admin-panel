<div data-item class="grid grid-cols-12 items-start gap-3">
    <div class="col-span-7">
        <select name="items[{{ $index }}][product_id]" class="input" required>
            <option value="" data-price="0">{{ __('Select a product') }}</option>
            @foreach ($products as $p)
                <option value="{{ $p->id }}" data-price="{{ $p->price }}" @selected((string) $productId === (string) $p->id)>{{ $p->name }} — {{ number_format($p->price) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-span-3"><input type="number" min="1" name="items[{{ $index }}][quantity]" value="{{ $quantity }}" class="input" dir="ltr" required></div>
    <div class="col-span-2 pt-2 text-end"><button type="button" data-remove class="text-sm text-rose-500 hover:text-rose-700">{{ __('Remove') }}</button></div>
</div>
