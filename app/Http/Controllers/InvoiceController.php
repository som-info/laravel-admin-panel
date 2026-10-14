<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $invoices = Invoice::query()
            ->with('order.customer')
            ->search($request->string('q')->trim()->value())
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest('issued_at')->latest('id')
            ->paginate((int) Setting::get('items_per_page', 15))
            ->withQueryString();

        $totals = [
            'paid' => (int) Invoice::where('status', 'paid')->sum('amount'),
            'unpaid' => (int) Invoice::where('status', 'unpaid')->sum('amount'),
            'overdue' => (int) Invoice::where('status', 'overdue')->sum('amount'),
        ];

        return view('invoices.index', compact('invoices', 'totals'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('manage-invoices');
        $order = Order::find($request->integer('order'));

        return view('invoices.form', [
            'invoice' => new Invoice([
                'order_id' => $order?->id,
                'amount' => $order?->total,
                'tax_rate' => (int) Setting::get('tax_rate', 9),
                'status' => 'unpaid',
                'issued_at' => now(),
                'due_at' => now()->addDays(14),
            ]),
            'orders' => $this->availableOrders(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-invoices');
        Invoice::create([...$this->validated($request), 'number' => Invoice::nextNumber()]);

        return redirect()->route('invoices.index')->with('success', __('Invoice created successfully.'));
    }

    public function edit(Invoice $invoice): View
    {
        Gate::authorize('manage-invoices');

        return view('invoices.form', ['invoice' => $invoice, 'orders' => $this->availableOrders($invoice)]);
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        Gate::authorize('manage-invoices');
        $invoice->update($this->validated($request, $invoice));

        return redirect()->route('invoices.index')->with('success', __('Invoice updated successfully.'));
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        Gate::authorize('delete-records');
        $invoice->delete();

        return back()->with('success', __('Invoice deleted.'));
    }

    private function availableOrders(?Invoice $invoice = null)
    {
        return Order::with('customer')
            ->where(fn ($q) => $q->doesntHave('invoice')->when($invoice, fn ($q) => $q->orWhere('id', $invoice->order_id)))
            ->latest('id')->get();
    }

    private function validated(Request $request, ?Invoice $invoice = null): array
    {
        $data = $request->validate([
            'order_id' => ['required', 'exists:orders,id', Rule::unique('invoices')->ignore($invoice)],
            'amount' => ['required', 'integer', 'min:0'],
            'tax_rate' => ['required', 'integer', 'between:0,50'],
            'status' => ['required', Rule::in(Invoice::STATUSES)],
            'issued_at' => ['required', 'date'],
            'due_at' => ['required', 'date', 'after_or_equal:issued_at'],
        ]);
        $data['paid_at'] = $data['status'] === 'paid' ? ($invoice?->paid_at ?? now()) : null;

        return $data;
    }
}
