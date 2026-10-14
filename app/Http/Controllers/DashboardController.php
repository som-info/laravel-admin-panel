<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $months = collect(range(5, 0))->map(fn ($i) => now()->startOfMonth()->subMonths($i));

        $revenue = $months->map(fn ($m) => (int) Order::where('status', '!=', 'cancelled')
            ->whereBetween('ordered_at', [$m->copy()->startOfMonth(), $m->copy()->endOfMonth()])
            ->sum('total'));

        $orderCounts = $months->map(fn ($m) => Order::whereBetween('ordered_at', [$m->copy()->startOfMonth(), $m->copy()->endOfMonth()])->count());

        $statusBreakdown = collect(Order::STATUSES)->mapWithKeys(fn ($s) => [__('status.'.$s) => Order::where('status', $s)->count()]);

        return view('dashboard', [
            'stats' => [
                'revenue' => (int) Order::where('status', '!=', 'cancelled')->where('ordered_at', '>=', now()->startOfMonth())->sum('total'),
                'orders' => Order::count(),
                'customers' => Customer::count(),
                'unpaid' => (int) Invoice::whereIn('status', ['unpaid', 'overdue'])->sum('amount'),
                'low_stock' => Product::where('stock', '<', 10)->count(),
            ],
            'chart' => [
                'labels' => $months->map(fn ($m) => __('month.'.$m->format('n')))->values(),
                'revenue' => $revenue->values(),
                'orders' => $orderCounts->values(),
                'statusLabels' => $statusBreakdown->keys(),
                'statusData' => $statusBreakdown->values(),
            ],
            'recentOrders' => Order::with('customer')->latest('ordered_at')->latest('id')->take(6)->get(),
            'activity' => ActivityLog::with('user')->latest()->take(6)->get(),
        ]);
    }
}
