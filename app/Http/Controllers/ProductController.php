<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->search($request->string('q')->trim()->value())
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->category))
            ->when($request->stock === 'low', fn ($q) => $q->where('stock', '<', 10))
            ->when($request->stock === 'out', fn ($q) => $q->where('stock', 0))
            ->orderBy($request->sort === 'price' ? 'price' : 'created_at', 'desc')
            ->paginate((int) Setting::get('items_per_page', 15))
            ->withQueryString();

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        Gate::authorize('manage-catalog');

        return view('products.form', ['product' => new Product(['is_active' => true, 'category' => 'hardware'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-catalog');
        Product::create($this->validated($request));

        return redirect()->route('products.index')->with('success', __('Product created successfully.'));
    }

    public function edit(Product $product): View
    {
        Gate::authorize('manage-catalog');

        return view('products.form', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        Gate::authorize('manage-catalog');
        $product->update($this->validated($request, $product));

        return redirect()->route('products.index')->with('success', __('Product updated successfully.'));
    }

    public function destroy(Product $product): RedirectResponse
    {
        Gate::authorize('delete-records');
        $product->delete();

        return back()->with('success', __('Product deleted.'));
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'sku' => ['required', 'string', 'max:40', 'alpha_dash', Rule::unique('products')->ignore($product)],
            'category' => ['required', Rule::in(Product::CATEGORIES)],
            'price' => ['required', 'integer', 'min:0', 'max:100000000000'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        return [...$data, 'is_active' => $request->boolean('is_active')];
    }
}
