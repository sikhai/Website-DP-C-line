<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    protected $primaryKey = 'attribute_id';

    protected $fillable = [
        'name',
    ]; 

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_attribute_values', 'attribute_id', 'ProductID')
                    ->withPivot('Value');
    }
}
