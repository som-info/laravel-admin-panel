<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_reach_admin_areas(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)->get('/settings')->assertForbidden();
        $this->actingAs($staff)->get('/activity')->assertForbidden();
        $this->actingAs($staff)->get('/products/create')->assertForbidden();
        $this->actingAs($staff)->get('/invoices/create')->assertForbidden();
    }

    public function test_staff_cannot_delete_records(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = Customer::factory()->create();

        $this->actingAs($staff)->delete("/customers/{$customer->id}")->assertForbidden();
        $this->assertModelExists($customer);
    }

    public function test_manager_can_manage_catalog_but_not_settings(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $product = Product::factory()->create();

        $this->actingAs($manager)->get("/products/{$product->id}/edit")->assertOk();
        $this->actingAs($manager)->get('/activity')->assertOk();
        $this->actingAs($manager)->get('/settings')->assertForbidden();
        $this->actingAs($manager)->delete("/products/{$product->id}")->assertRedirect();
        $this->assertModelMissing($product);
    }

    public function test_admin_can_update_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put('/settings', [
            'company_name' => 'Acme Ltd', 'company_email' => 'ops@acme.test', 'currency' => 'USD',
            'tax_rate' => 10, 'items_per_page' => 25, 'default_locale' => 'en',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('settings', ['key' => 'company_name', 'value' => 'Acme Ltd']);
        $this->assertDatabaseHas('settings', ['key' => 'items_per_page', 'value' => '25']);
    }
}
