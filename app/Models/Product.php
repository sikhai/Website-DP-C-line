<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, Translatable, SoftDeletes;

    public $timestamps = true;

    protected $casts = [
        'images' => 'array',
    ];

    protected $fillable = [
        'name',
        'title',
        'product_code',
        'qr_code_path',
        'description',
        'short_description',
        'price',
        'meter',
        'tax',
        'stock_quantity',
        'color',
        'image',
        'get_image_url',
        'images',
        'keywords',
        'slug',
        'status',
        'is_featured',
        'is_trending',
        'file',
        'category_id'
    ];

    protected $translatable = ['name', 'short_description', 'description', 'keywords', 'slug'];

    public function design()
    {
        return $this->belongsTo(Design::class, 'category_id');
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attribute', 'product_id', 'attribute_id')
            ->withTimestamps();
    }

    public function warehouseTransactions()
    {
        return $this->hasMany(WarehouseProductTransaction::class);
    }

    public function getStockAttribute()
    {
        return WarehouseProductTransaction::stock($this->id, Product::class, $this->unit_id ?? null);
    }

    public function getDisplayNameAttribute()
    {
        return $this->name;
    }


    public function getAttributeValueByName(string $name)
    {
        $attributesRelation = $this->getRelationValue('attributes');

        // Kiểm tra nếu relation không tồn tại hoặc rỗng thì trả về null luôn
        if (!$attributesRelation || $attributesRelation->isEmpty()) {
            return null;
        }

        // Có thể lấy $attributesRelation[0], nhưng nên lặp qua hết để chắc chắn
        foreach ($attributesRelation as $attribute) {
            $values = json_decode($attribute->value, true);

            if (is_array($values)) {
                foreach ($values as $val) {
                    if (isset($val['name']) && $val['name'] === $name) {
                        return $val['value'];
                    }
                }
            }
        }

        // Không tìm thấy attribute có tên như yêu cầu thì trả về null
        return null;
    }

    public function getParsedAttributesAttribute()
    {
        $parsed = [];

        // Lấy quan hệ nếu đã được load, nếu chưa thì load thủ công
        $attributesRelation = $this->relationLoaded('attributes')
            ? $this->getRelation('attributes')
            : $this->attributes()->get();

        foreach ($attributesRelation as $attribute) {
            $json = json_decode($attribute->value, true);

            if (is_array($json)) {
                foreach ($json as $item) {
                    if (is_array($item) && isset($item['name'], $item['value'])) {
                        $parsed[$item['name']] = $item['value'];
                    }
                }
            }
        }

        return $parsed;
    }

    public function getLabelAttribute(): string
    {
        $name = $this->name ?? '';
        $code = $this->product_code ?? '';

        if ($name && $code) {
            return "{$name} ({$code})";
        }

        return $name ?: $code;
    }
}
