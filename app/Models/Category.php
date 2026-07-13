<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
      "name",
      "parent_id",
    ];

    public function parent(): BelongsTo
    {
        return  $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Проверяет, есть ли товары в этой категории или её подкатегориях.
     */
    public function hasProductsRecursive(): bool
    {
        if ($this->products()->where('stock', '>', 0)->exists()) {
            return true;
        }

        foreach ($this->children as $child) {
            if ($child->hasProductsRecursive()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Получает количество товаров во всей ветке.
     */
    public function totalProductsCount(): int
    {
        $count = $this->products()->count();
        foreach ($this->children as $child) {
            $count += $child->totalProductsCount();
        }
        return $count;
    }
}

