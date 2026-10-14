<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_crud_and_filters(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post('/products', [
            'name' => 'Mechanical Keyboard', 'sku' => 'ACC-0001', 'category' => 'accessories',
            'price' => 4500000, 'stock' => 3, 'is_active' => '1',
        ])->assertRedirect('/products');

        $product = Product::where('sku', 'ACC-0001')->firstOrFail();
        $this->assertTrue($product->is_active);

        Product::factory()->create(['name' => 'Big Server', 'stock' => 80]);
        $this->actingAs($admin)->get('/products?stock=low')->assertSee('Mechanical Keyboard')->assertDontSee('Big Server');
        $this->actingAs($admin)->get('/products?category=accessories')->assertSee('Mechanical Keyboard')->assertDontSee('Big Server');

        $this->actingAs($admin)->put("/products/{$product->id}", [
            'name' => 'Mechanical Keyboard Pro', 'sku' => 'ACC-0001', 'category' => 'accessories', 'price' => 5000000, 'stock' => 10,
        ])->assertRedirect('/products');
        $this->assertFalse($product->fresh()->is_active);
        $this->assertSame(5000000, $product->fresh()->price);
    }

    public function test_product_validation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Product::factory()->create(['sku' => 'DUP-1']);

        $this->actingAs($admin)->post('/products', ['name' => '', 'sku' => 'DUP-1', 'category' => 'food', 'price' => -1, 'stock' => 'x'])
            ->assertSessionHasErrors(['name', 'sku', 'category', 'price', 'stock']);
    }
}
