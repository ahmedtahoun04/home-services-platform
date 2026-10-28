<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'price',
        'rating',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'rating' => 'decimal:1',
            'is_active' => 'boolean',
        ];
    }

    /** Only services visible to customers. */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Match the term against name or description. */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /** Filter by exact category (ignored when empty). */
    public function scopeCategory(Builder $query, ?string $category): Builder
    {
        return filled($category) ? $query->where('category', $category) : $query;
    }
}
