<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'project_name',
        'description',
        'images',
        'slug',
        'is_featured'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_project');
    }
}
