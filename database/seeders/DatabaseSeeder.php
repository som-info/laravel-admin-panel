<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        ActivityLog::$enabled = false;
        mt_srand(1404);
        fake()->seed(1404);

        $users = collect([
            ['امیر نامور', 'admin@example.com', 'admin'],
            ['سارا احمدی', 'manager@example.com', 'manager'],
            ['رضا کریمی', 'staff@example.com', 'staff'],
        ])->map(fn ($u) => User::create(['name' => $u[0], 'email' => $u[1], 'role' => $u[2], 'password' => 'password', 'email_verified_at' => now()]));

        Setting::put(Setting::DEFAULTS);

        $products = collect([
            ['لپ‌تاپ ایسوس ویووبوک ۱۵', 'hardware', 38_500_000], ['مانیتور ال‌جی ۲۷ اینچ', 'hardware', 14_200_000],
            ['کیبورد مکانیکی لاجیتک', 'accessories', 4_650_000], ['ماوس بی‌سیم ام‌ایکس', 'accessories', 3_900_000],
            ['هارد اکسترنال ۲ ترابایت', 'hardware', 5_400_000], ['روتر میکروتیک hAP ax2', 'hardware', 7_800_000],
            ['لایسنس آفیس ۳۶۵ (یک‌ساله)', 'software', 2_900_000], ['آنتی‌ویروس ESET (۳ کاربر)', 'software', 1_350_000],
            ['نرم‌افزار حسابداری ابری', 'software', 9_500_000], ['پشتیبانی شبکه (ماهانه)', 'services', 12_000_000],
            ['طراحی و راه‌اندازی وب‌سایت', 'services', 45_000_000], ['سئو و بهینه‌سازی (ماهانه)', 'services', 8_500_000],
            ['هدست جبرا Evolve2', 'accessories', 6_200_000], ['وب‌کم لاجیتک C920', 'accessories', 3_100_000],
            ['پرینتر لیزری اچ‌پی', 'hardware', 11_900_000], ['یو‌پی‌اس ۱۰۰۰VA', 'hardware', 6_750_000],
            ['هاست ابری (سالانه)', 'services', 4_800_000], ['کابل شبکه Cat6 (۳۰۵ متر)', 'accessories', 5_900_000],
            ['اس‌اس‌دی سامسونگ ۱ ترابایت', 'hardware', 4_350_000], ['نصب و پیکربندی سرور', 'services', 15_000_000],
        ])->map(fn ($p, $i) => Product::create([
            'name' => $p[0],
            'sku' => strtoupper(substr($p[1], 0, 3)).'-'.str_pad((string) (101 + $i), 4, '0', STR_PAD_LEFT),
            'category' => $p[1],
            'price' => $p[2],
            'stock' => $p[1] === 'services' ? 999 : [3, 7, 24, 58, 112, 0, 41][$i % 7],
            'is_active' => $i !== 17,
            'description' => null,
        ]));

        $first = ['محمد', 'علی', 'زهرا', 'مریم', 'حسین', 'نگار', 'مهدی', 'الهام', 'کامران', 'پریسا', 'بهنام', 'شیرین', 'آرش', 'لیلا', 'فرهاد', 'نازنین', 'سینا', 'مهسا', 'پویا', 'یاسمن'];
        $last = ['رضایی', 'محمدی', 'حسینی', 'جعفری', 'صادقی', 'موسوی', 'رحیمی', 'کاظمی', 'نوری', 'شریفی', 'طاهری', 'اکبری', 'فراهانی', 'یزدانی', 'باقری'];
        $companies = ['فناوران پارس', 'داده‌پردازان نوین', 'بازرگانی آریا', 'گروه صنعتی سپهر', 'هلدینگ آفتاب', 'نرم‌افزار رایان', 'پخش البرز', 'مهندسی آرمان', 'تجارت الکترونیک ستاره', null, null];
        $cities = ['دبی', 'استانبول', 'تورنتو', 'برلین', 'لندن', 'ونکوور', 'هامبورگ', 'آمستردام'];
        $latin = ['mohammad', 'ali', 'zahra', 'maryam', 'hossein', 'negar', 'mahdi', 'elham', 'kamran', 'parisa', 'behnam', 'shirin', 'arash', 'leila', 'farhad', 'nazanin', 'sina', 'mahsa', 'pouya', 'yasaman'];

        $customers = collect(range(1, 48))->map(function ($i) use ($first, $last, $companies, $cities, $latin) {
            $f = $i % count($first);

            return Customer::create([
                'name' => $first[$f].' '.$last[($i * 7) % count($last)],
                'email' => $latin[$f].'.'.$i.'@example.com',
                'phone' => '09'.mt_rand(10, 39).mt_rand(1000000, 9999999),
                'company' => $companies[$i % count($companies)],
                'city' => $cities[$i % count($cities)],
                'status' => $i % 9 === 0 ? 'inactive' : ($i % 5 === 0 ? 'lead' : 'active'),
                'created_at' => now()->subDays(200 - $i * 3),
            ]);
        });

        // ~140 orders over the last six months, growing over time
        $dates = collect(range(1, 140))
            ->map(fn () => Carbon::today()->subDays((int) floor(180 * (1 - sqrt(mt_rand(0, 1000) / 1000)))))
            ->sort()->values();

        foreach ($dates as $idx => $date) {
            $n = $idx + 1;
            $age = $date->diffInDays(now());
            $status = match (true) {
                mt_rand(1, 100) <= 7 => 'cancelled',
                $age > 20 => 'completed',
                $age > 10 => ['shipped', 'completed'][mt_rand(0, 1)],
                default => ['pending', 'processing', 'shipped'][mt_rand(0, 2)],
            };

            $order = Order::create([
                'number' => 'ORD-'.str_pad((string) (1000 + $n), 5, '0', STR_PAD_LEFT),
                'customer_id' => $customers->random()->id,
                'user_id' => $users->random()->id,
                'status' => $status,
                'ordered_at' => $date,
                'created_at' => $date,
            ]);

            foreach ($products->random(mt_rand(1, 3)) as $product) {
                $order->items()->create(['product_id' => $product->id, 'quantity' => mt_rand(1, 4), 'unit_price' => $product->price]);
            }
            $order->recalculateTotal();
        }

        $i = 0;
        Order::where('status', '!=', 'cancelled')->orderBy('ordered_at')->get()->each(function (Order $order) use (&$i) {
            if ($order->status === 'pending') {
                return;
            }
            $due = $order->ordered_at->copy()->addDays(14);
            $status = $order->status === 'completed' && mt_rand(1, 10) <= 8 ? 'paid' : ($due->isPast() ? 'overdue' : 'unpaid');
            Invoice::create([
                'number' => 'INV-'.$order->ordered_at->format('Y').'-'.str_pad((string) ++$i, 4, '0', STR_PAD_LEFT),
                'order_id' => $order->id,
                'amount' => $order->total,
                'tax_rate' => 9,
                'status' => $status,
                'issued_at' => $order->ordered_at,
                'due_at' => $due,
                'paid_at' => $status === 'paid' ? $order->ordered_at->copy()->addDays(mt_rand(1, 12)) : null,
            ]);
        });

        ActivityLog::$enabled = true;
        $recent = Order::latest('id')->take(12)->get();
        foreach ($recent as $k => $order) {
            $user = $users[$k % 3];
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => $k % 4 === 0 ? 'updated' : 'created',
                'subject_type' => 'Order',
                'subject_id' => $order->id,
                'description' => sprintf('Order "%s" %s', $order->number, $k % 4 === 0 ? 'updated' : 'created'),
                'ip_address' => '192.168.1.'.(10 + $k),
                'created_at' => now()->subMinutes(15 + $k * 47),
            ]);
        }
        foreach ($users as $k => $user) {
            ActivityLog::create(['user_id' => $user->id, 'action' => 'login', 'description' => $user->name.' signed in', 'ip_address' => '192.168.1.'.(50 + $k), 'created_at' => now()->subMinutes(5 + $k * 90)]);
        }
    }
}
