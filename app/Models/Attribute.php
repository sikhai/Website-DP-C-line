<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = ['name', 'type', 'status'];

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_attribute', 'attribute_id', 'product_id')
            ->withTimestamps();
    }

    public static function storeName($name, $type = 'product', $status = true, $value = '')
    {
        $attribute = self::firstOrNew(['name' => $name]);
        $attribute->type = $type;
        $attribute->status = $status;
        $attribute->value = $value ?? '';
        $attribute->save();

        return $attribute;
    }
}
