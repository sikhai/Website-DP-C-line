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

    // morph relation: entities that use this attribute value
    public function entities()
    {
        // you can list common models: designs, products, vendors, ...
        return $this->morphedByMany(Design::class, 'entity', 'entity_attribute_values', 'attribute_value_id', 'entity_id')
            ->withPivot('attribute_id')
            ->withTimestamps();
    }

    // If you want helper shortcuts for other models, add:
    public function products()
    {
        return $this->morphedByMany(Product::class, 'entity', 'entity_attribute_values', 'attribute_value_id', 'entity_id')
            ->withPivot('attribute_id')
            ->withTimestamps();
    }
}
