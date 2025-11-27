<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'shape',
        'unit',
        'image',
        'images',
        'description',
        'is_featured',
        'parent_id'
    ];

    // -----------------------------
    // QUAN HỆ
    // -----------------------------

    public function collections()
    {
        return $this->hasMany(Collection::class, 'parent_id');
    }

    public function designs()
    {
        return $this->hasManyThrough(
            Design::class,
            Collection::class,
            'parent_id',   // collection.parent_id = category.id
            'parent_id',   // design.parent_id = collection.id
            'id',
            'id'
        );
    }

    public function products()
    {
        return $this->hasManyThrough(
            Product::class,
            Design::class,
            'parent_id',    // design.parent_id = collection.id
            'category_id',  // product.category_id = design.id
            'id',
            'id'
        );
    }

    // -----------------------------
    // CACHE: COLLECTION COUNT
    // -----------------------------
    public function getTotalCollectionsAttribute()
    {
        return Cache::remember("category_{$this->id}_collections_count", 300, function () {
            return $this->collections()->count();
        });
    }

    // -----------------------------
    // CACHE: DESIGNS COUNT
    // -----------------------------
    public function getTotalDesignsAttribute()
    {
        return Cache::remember("category_{$this->id}_designs_count", 300, function () {
            return $this->designs()->count();
        });
    }

    // -----------------------------
    // CACHE: PRODUCTS COUNT
    // -----------------------------
    public function getTotalProductsAttribute()
    {
        return Cache::remember("category_{$this->id}_products_count", 300, function () {
            return Product::whereHas('design', function ($q) {
                $q->whereHas('collection', function ($q2) {
                    $q2->where('parent_id', $this->id);
                });
            })->count();
        });
    }

    /**
     * Scope a query to only include active users.
     */
    #[Scope]
    public function scopeAccessoryType(Builder $query)
    {
        return $query->where('type', 'ACCESSORY');
    }
    #[Scope]
    public function scopeProductType(Builder $query)
    {
        return $query->where('type', 'PRODUCT');
    }

    // -----------------------------
    // Xóa cache
    // -----------------------------
    public function clearCache()
    {
        Cache::forget("category_{$this->id}_collections_count");
        Cache::forget("category_{$this->id}_designs_count");
        Cache::forget("category_{$this->id}_products_count");
    }

    // -----------------------------
    // BOOT MODEL
    // -----------------------------
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($category) {
            $category->clearCache();
        });

        static::deleted(function ($category) {
            $category->clearCache();
        });
    }
}
