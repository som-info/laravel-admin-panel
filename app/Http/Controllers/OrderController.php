<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with('customer')
            ->withCount('items')
            ->search($request->string('q')->trim()->value())
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('ordered_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('ordered_at', '<=', $request->to))
            ->latest('ordered_at')->latest('id')
            ->paginate((int) Setting::get('items_per_page', 15))
            ->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load('customer', 'items.product', 'invoice', 'user');

        return view('orders.show', compact('order'));
    }

    public function create(): View
    {
        return view('orders.form', $this->formData(new Order([
            'status' => 'pending',
            'ordered_at' => now(),
        ])));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $order = DB::transaction(function () use ($data, $request) {
            $order = Order::create([
                ...collect($data)->except('items')->all(),
                'number' => Order::nextNumber(),
                'user_id' => $request->user()->id,
            ]);
            $this->syncItems($order, $data['items']);

            return $order;
        });

        return redirect()->route('orders.show', $order)->with('success', __('Order created successfully.'));
    }

    public function edit(Order $order): View
    {
        return view('orders.form', $this->formData($order->load('items')));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($order, $data) {
            $order->update(collect($data)->except('items')->all());
            $order->items()->delete();
            $this->syncItems($order, $data['items']);
        });

        return redirect()->route('orders.show', $order)->with('success', __('Order updated successfully.'));
    }

    public function destroy(Order $order): RedirectResponse
    {
        Gate::authorize('delete-records');
        $order->delete();

        return redirect()->route('orders.index')->with('success', __('Order deleted.'));
    }

    private function formData(Order $order): array
    {
        return [
            'order' => $order,
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'products' => Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'price']),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'status' => ['required', Rule::in(Order::STATUSES)],
            'ordered_at' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:1000'],
        ], [], [
            'items.*.product_id' => __('product'),
            'items.*.quantity' => __('quantity'),
        ]);
    }

    private function syncItems(Order $order, array $items): void
    {
        $prices = Product::whereIn('id', collect($items)->pluck('product_id'))->pluck('price', 'id');

        foreach ($items as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $prices[$item['product_id']],
            ]);
        }

        $order->recalculateTotal();
    }
}
