<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderInvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_is_created_with_items_and_calculated_total(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create();
        $a = Product::factory()->create(['price' => 1000000]);
        $b = Product::factory()->create(['price' => 250000]);

        $response = $this->actingAs($staff)->post('/orders', [
            'customer_id' => $customer->id,
            'status' => 'pending',
            'ordered_at' => now()->toDateString(),
            'items' => [
                ['product_id' => $a->id, 'quantity' => 2],
                ['product_id' => $b->id, 'quantity' => 4],
            ],
        ]);

        $order = Order::firstOrFail();
        $response->assertRedirect("/orders/{$order->id}");
        $this->assertSame(3000000, $order->total);
        $this->assertCount(2, $order->items);
        $this->assertSame($staff->id, $order->user_id);

        $this->actingAs($staff)->get("/orders/{$order->id}")->assertOk()->assertSee($order->number);
    }

    public function test_order_validation_requires_items(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)->post('/orders', ['customer_id' => 999, 'status' => 'unknown', 'ordered_at' => now()->addWeek()->toDateString(), 'items' => []])
            ->assertSessionHasErrors(['customer_id', 'status', 'ordered_at', 'items']);
    }

    public function test_orders_can_be_filtered_by_status_and_date(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $customer = Customer::factory()->create();
        Order::create(['number' => 'ORD-AAA', 'customer_id' => $customer->id, 'status' => 'completed', 'ordered_at' => '2026-01-10']);
        Order::create(['number' => 'ORD-BBB', 'customer_id' => $customer->id, 'status' => 'pending', 'ordered_at' => '2026-03-10']);

        $this->actingAs($user)->get('/orders?status=pending')->assertSee('ORD-BBB')->assertDontSee('ORD-AAA');
        $this->actingAs($user)->get('/orders?from=2026-01-01&to=2026-01-31')->assertSee('ORD-AAA')->assertDontSee('ORD-BBB');
    }

    public function test_manager_can_issue_invoice_for_an_order_once(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $order = Order::create(['number' => 'ORD-1', 'customer_id' => Customer::factory()->create()->id, 'status' => 'completed', 'ordered_at' => now(), 'total' => 2000000]);

        $payload = ['order_id' => $order->id, 'amount' => 2000000, 'tax_rate' => 9, 'status' => 'paid', 'issued_at' => now()->toDateString(), 'due_at' => now()->addDays(10)->toDateString()];

        $this->actingAs($manager)->post('/invoices', $payload)->assertRedirect('/invoices');
        $invoice = Invoice::firstOrFail();
        $this->assertNotNull($invoice->paid_at);
        $this->assertSame(2180000, $invoice->grand_total);

        $this->actingAs($manager)->post('/invoices', $payload)->assertSessionHasErrors('order_id');
    }

    public function test_invoice_due_date_must_follow_issue_date(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $order = Order::create(['number' => 'ORD-2', 'customer_id' => Customer::factory()->create()->id, 'status' => 'pending', 'ordered_at' => now()]);

        $this->actingAs($manager)->post('/invoices', [
            'order_id' => $order->id, 'amount' => 100, 'tax_rate' => 9, 'status' => 'unpaid',
            'issued_at' => '2026-05-10', 'due_at' => '2026-05-01',
        ])->assertSessionHasErrors('due_at');
    }
}
