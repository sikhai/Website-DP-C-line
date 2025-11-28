<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;

use App\Services\AttributeService;

class Design extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
        'slug',
        'images',
        'description',
        'is_featured',
        'attributes',
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
            return $this->products()
                ->where('is_featured', 1)
                ->count();
        });
    }

    // -----------------------
    // Ảnh đại diện (nếu design chưa có image)
    // -----------------------
    public function getImageAttribute()
    {
        // Chuẩn hóa: decode JSON string thành array
        $images = $this->images;
        if (is_string($images)) {
            $images = json_decode($images, true) ?? [];
        }

        // Nếu đã có images → trả về ảnh đầu tiên
        if (!empty($images) && count($images) > 0) {
            return $images[0];
        }

        // Lấy ảnh đầu tiên của product liên quan (nếu có), dùng cache
        return Cache::remember("design_{$this->id}_image", 300, function () {
            $firstProduct = $this->products()->whereNotNull('image')->first();
            return $firstProduct && $firstProduct->image ? $firstProduct->image : null;
        });
    }

    public function attributeValues()
    {
        return $this->morphToMany(
            AttributeValue::class,
            'entity',
            'entity_attribute_values',
            'entity_id',
            'attribute_value_id'
        )->withPivot('attribute_id')
            ->withTimestamps();
    }

    // Optional: convenience: get attributes as name => value list
    public function attributesMapped()
    {
        return $this->attributeValues->mapWithKeys(function ($av) {
            return [$av->attribute->name => $av->value];
        })->toArray();
    }

    public function getActiveAttributesAttribute()
    {
        return $this->attributeValues()
            ->whereHas('attribute', fn($q) => $q->where('status', 1))
            ->with('attribute')
            ->get()
            ->mapWithKeys(fn($attrValue) => [$attrValue->attribute->name => $attrValue->value])
            ->toArray();
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

        // -------------------------
        // Event: Saved (create/update)
        // -------------------------
        static::saved(function ($design) {
            // 1) Clear design-related cache
            $design->clearProductsCountCache();

            // 2) Clear collection->category cache nếu có
            if ($design->collection && $design->collection->category) {
                $design->collection->category->clearCache();
            }

            // 3) Sync attributes nếu có data mới
            $raw = $design->getRawOriginal('attributes');

            $attrs = json_decode($raw, true);

            if (!empty($attrs)) {
                AttributeService::updateAttributes($design, $attrs);
            }
        });

        // -------------------------
        // Event: Deleted
        // -------------------------
        static::deleted(function ($design) {
            // 1) Clear design-related cache
            $design->clearProductsCountCache();

            // 2) Clear collection->category cache nếu có
            if ($design->collection && $design->collection->category) {
                $design->collection->category->clearCache();
            }

            // 3) Detach all attributes
            AttributeService::detachAllAttributes($design);
        });
    }
}
