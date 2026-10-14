<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory, LogsActivity;

    public const STATUSES = ['unpaid', 'paid', 'overdue', 'void'];

    protected $fillable = ['number', 'order_id', 'amount', 'tax_rate', 'status', 'issued_at', 'due_at', 'paid_at'];

    protected function casts(): array
    {
        return ['issued_at' => 'date', 'due_at' => 'date', 'paid_at' => 'date', 'amount' => 'integer', 'tax_rate' => 'integer'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getTaxAttribute(): int
    {
        return (int) round($this->amount * $this->tax_rate / 100);
    }

    public function getGrandTotalAttribute(): int
    {
        return $this->amount + $this->tax;
    }

    public static function nextNumber(): string
    {
        return 'INV-'.now()->format('Y').'-'.str_pad((string) ((static::max('id') ?? 0) + 1), 4, '0', STR_PAD_LEFT);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn ($q) => $q->where(fn ($q) => $q
            ->where('number', 'like', "%{$term}%")
            ->orWhereHas('order.customer', fn ($c) => $c->where('name', 'like', "%{$term}%"))));
    }
}
