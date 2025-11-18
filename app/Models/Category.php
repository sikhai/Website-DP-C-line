<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

use App\Models\Design;
use App\Models\Accessory;

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

    public function childCategories()
    {
        return $this->hasMany(Design::class, 'parent_id');
    }

    public function collections()
    {
        return $this->hasMany(Collection::class, 'parent_id');
    }

    public function childAccessory()
    {
        return $this->hasMany(Accessory::class, 'category_id');
    }

    /**
     * Tổng số collections thuộc category.
     * @return int
     */
    public function getTotalCollectionsAttribute()
    {
        // Vì đã load quan hệ collections, chỉ cần count() trực tiếp.
        return $this->collections->count();
    }
    /**
     * Tổng số designs thuộc category.
     * @return int
     */
    public function getTotalDesignsAttribute()
    {
        return $this->collections->flatMap->designs->count();
    }

    /**
     * Tổng số products thuộc category.
     * @return int
     */
    public function getTotalProductsAttribute()
    {
        return $this->collections->flatMap(function ($collection) {
            return $collection->designs->flatMap->products;
        })->count();
    }

    public function collectionsWithProducts()
    {
        return $this->hasMany(Collection::class, 'parent_id')
            ->whereHas('designs.products');
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

    public function projects()
    {
        return $this->hasMany(Project::class, 'category_id');
    }

    protected static function boot()
    {
        parent::boot();

        // Lắng nghe sự kiện `saving` (gọi cả khi tạo và cập nhật)
        static::saving(function ($category) {
            // dd(request()->all()); // Dừng và in dữ liệu của `Category` trước khi lưu
            // dd($Category); // Dừng và in dữ liệu của `Category` trước khi lưu
        });
    }
}
