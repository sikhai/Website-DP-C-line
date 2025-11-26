<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;

class Design extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
        'slug',
        'images',
        'description',
        'is_featured',
    ];

    protected $appends = ['image'];

    // -----------------------
    // Quan hệ
    // -----------------------
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class, 'parent_id');
    }

    // -----------------------
    // Tổng sản phẩm (cache)
    // -----------------------
    public function getTotalProductsAttribute()
    {
        return Cache::remember("design_{$this->id}_products_count", 300, function () {
            return $this->products()->count();
        });
    }

    // -----------------------
    // Ảnh đại diện (nếu design chưa có image)
    // -----------------------
    public function getImageAttribute()
    {
        // Nếu đã có image trong design → trả về luôn
        if (!empty($this->images)) {
            return $this->images;
        }

        return Cache::remember("design_{$this->id}_image", 300, function () {
            $firstProduct = $this->products()->whereNotNull('image')->first();
            return $firstProduct ? $firstProduct->image : null;
        });
    }

    // -----------------------
    // Xóa cache thủ công
    // -----------------------
    public function clearProductsCountCache()
    {
        Cache::forget("design_{$this->id}_products_count");
        Cache::forget("design_{$this->id}_image");
    }

    // -----------------------
    // Boot model
    // -----------------------
    protected static function boot()
    {
        parent::boot();

        // Clear cache khi Product liên quan thay đổi
        static::saved(function ($design) {
            $design->clearProductsCountCache();
        });

        static::deleted(function ($design) {
            $design->clearProductsCountCache();
        });
    }
}
