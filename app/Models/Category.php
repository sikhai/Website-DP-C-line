<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'CategoryID';

    protected $fillable = [
        'CategoryName',
        'Description',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories', 'CategoryID', 'ProductID');
    }
}
