<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Eloquent\Model;

class AttributeService
{
    /**
     * Attach attributes to any entity (Design/Product/Vendor...)
     * @param Model $entity
     * @param array $attributes ['Supplier'=>'TOPLI', 'Usage'=>'wallpaper']
     */
    public static function attachAttributesToEntity(Model $entity, array $attributes)
    {
        foreach ($attributes as $name => $value) {
            if ($value === null || trim($value) === '') continue;

            // 1) ensure Attribute exists
            $attribute = Attribute::updateOrCreate(
                ['name' => $name],
                ['type' => strtolower(class_basename($entity)), 'status' => true, 'value' => '']
            );

            // 2) ensure AttributeValue exists
            $attrValue = AttributeValue::firstOrCreate([
                'attribute_id' => $attribute->id,
                'value' => (string) $value,
            ]);

            // 3) attach to entity
            $entity->attributeValues()->syncWithoutDetaching([
                $attrValue->id => ['attribute_id' => $attribute->id]
            ]);
        }
    }

    /**
     * Detach all attributes from an entity
     */
    public static function detachAllAttributes(Model $entity)
    {
        $entity->attributeValues()->detach();
    }

    /**
     * Update attributes: replace old values with new values
     */
    public static function updateAttributes(Model $entity, array $attributes)
    {
        // detach old
        static::detachAllAttributes($entity);
        // attach new
        static::attachAttributesToEntity($entity, $attributes);
    }
}
