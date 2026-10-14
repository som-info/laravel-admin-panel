<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin']);
    }

    public function test_customers_can_be_searched_and_filtered(): void
    {
        Customer::factory()->create(['name' => 'Sara Rahimi', 'status' => 'active']);
        Customer::factory()->create(['name' => 'Kamran Nouri', 'status' => 'lead']);

        $this->actingAs($this->user)->get('/customers?q=Sara')->assertSee('Sara Rahimi')->assertDontSee('Kamran Nouri');
        $this->actingAs($this->user)->get('/customers?status=lead')->assertSee('Kamran Nouri')->assertDontSee('Sara Rahimi');
    }

    public function test_customers_are_paginated(): void
    {
        Customer::factory()->count(20)->create();

        $this->actingAs($this->user)->get('/customers')->assertViewHas('customers', fn ($p) => $p->count() === 15 && $p->total() === 20);
    }

    public function test_customer_can_be_created_and_is_logged(): void
    {
        $this->actingAs($this->user)->post('/customers', [
            'name' => 'Negar Karimi', 'email' => 'negar@example.com', 'phone' => '09121234567', 'status' => 'active',
        ])->assertRedirect('/customers');

        $customer = Customer::where('email', 'negar@example.com')->firstOrFail();
        $this->assertDatabaseHas('activity_logs', ['subject_type' => 'Customer', 'subject_id' => $customer->id, 'action' => 'created', 'user_id' => $this->user->id]);
    }

    public function test_customer_validation(): void
    {
        Customer::factory()->create(['email' => 'taken@example.com']);

        $this->actingAs($this->user)->post('/customers', ['name' => '', 'email' => 'taken@example.com', 'phone' => 'abc', 'status' => 'vip'])
            ->assertSessionHasErrors(['name', 'email', 'phone', 'status']);
    }

    public function test_customer_can_be_updated_and_deleted(): void
    {
        $customer = Customer::factory()->create();

        $this->actingAs($this->user)->put("/customers/{$customer->id}", [
            'name' => 'Updated Name', 'email' => $customer->email, 'status' => 'inactive',
        ])->assertRedirect('/customers');
        $this->assertSame('Updated Name', $customer->fresh()->name);

        $this->actingAs($this->user)->delete("/customers/{$customer->id}")->assertRedirect();
        $this->assertModelMissing($customer);
    }
}
