<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory, LogsActivity;

    public const STATUSES = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

    protected $fillable = ['number', 'customer_id', 'user_id', 'status', 'total', 'ordered_at', 'notes'];

    protected function casts(): array
    {
        return ['ordered_at' => 'date', 'total' => 'integer'];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public static function nextNumber(): string
    {
        return 'ORD-'.str_pad((string) ((static::max('id') ?? 0) + 1001), 5, '0', STR_PAD_LEFT);
    }

    public function recalculateTotal(): void
    {
        $this->forceFill(['total' => $this->items()->get()->sum(fn ($i) => $i->quantity * $i->unit_price)])->saveQuietly();
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn ($q) => $q->where(fn ($q) => $q
            ->where('number', 'like', "%{$term}%")
            ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$term}%"))));
    }
}
