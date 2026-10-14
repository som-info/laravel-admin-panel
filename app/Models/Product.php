<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, LogsActivity;

    public const CATEGORIES = ['hardware', 'software', 'services', 'accessories'];

    protected $fillable = ['name', 'sku', 'category', 'price', 'stock', 'is_active', 'description'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'price' => 'integer', 'stock' => 'integer'];
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn ($q) => $q->where(fn ($q) => $q
            ->where('name', 'like', "%{$term}%")
            ->orWhere('sku', 'like', "%{$term}%")));
    }
}
