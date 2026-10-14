<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_with_seeded_data(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@example.com')->first();

        $this->actingAs($admin)->get('/')
            ->assertOk()
            ->assertViewHas('stats', fn ($s) => $s['customers'] === 48 && $s['orders'] === 140)
            ->assertViewHas('chart', fn ($c) => count($c['labels']) === 6);
    }

    public function test_every_list_page_loads_for_admin(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@example.com')->first();

        foreach (['/customers', '/products', '/orders', '/invoices', '/activity', '/settings', '/orders/create', '/invoices/create'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }
}
