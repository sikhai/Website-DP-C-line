<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'ProductID';

    protected $fillable = [
        'ProductName',
        'ProductCode',
        'Description',
        'Price',
        'StockQuantity',
        'ImageURL',
        'Keywords',
        'Status',
        'IsFeatured',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'ProductID', 'CategoryID');
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attribute_values', 'ProductID', 'AttributeID')
                    ->withPivot('Value');
    }
}
