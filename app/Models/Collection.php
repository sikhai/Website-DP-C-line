<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

use App\Models\Category;

class Collection extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'shape',
        'image',
        'images',
        'description',
        'is_featured',
        'parent_id',
    ];

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_id')->productType();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'parent_id')->productType();
    }

    public function designs()
    {
        return $this->hasMany(Design::class, 'parent_id');
    }

    // -------------------------------
    // Tổng số products (tối ưu + cache)
    // -------------------------------
    public function getProductsCountAttribute()
    {
        return Cache::remember("collection_{$this->id}_products_count", 300, function () {
            return Product::whereHas('design', function ($q) {
                $q->where('parent_id', $this->id);
            })->count();
        });
    }

    // -------------------------------
    // Tổng số designs (tối ưu + cache)
    // -------------------------------
    public function getDesignsCountAttribute()
    {
        return Cache::remember("collection_{$this->id}_designs_count", 300, function () {
            return $this->designs()->count();
        });
    }

    // -------------------------------
    // Ảnh đại diện (tối ưu + cache)
    // -------------------------------
    public function getImageAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        return Cache::remember("collection_{$this->id}_image", 300, function () {
            return Product::whereHas('design', function ($q) {
                $q->where('parent_id', $this->id);
            })->whereNotNull('image')
                ->value('image');
        });
    }

    /**
     * Xóa cache ProductsCount cho collection này
     */
    public function clearProductsCountCache()
    {
        Cache::forget("collection_{$this->id}_products_count");
        // Nếu muốn xóa luôn designs_count và image
        Cache::forget("collection_{$this->id}_designs_count");
        Cache::forget("collection_{$this->id}_image");
    }
}
