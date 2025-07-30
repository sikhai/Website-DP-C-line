<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    protected $fillable = ['attribute_id', 'value'];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public static function storeValue(Attribute $attribute, string $value): void
    {
        if (!empty($value)) {
            static::firstOrCreate([
                'attribute_id' => $attribute->id,
                'value' => $value
            ]);
        }
    }
}
