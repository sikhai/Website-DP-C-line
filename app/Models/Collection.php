<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

use App\Models\Category;

class Collection extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'shape',
        'image',
        'images',
        'description',
        'is_featured',
        'parent_id',
    ];
    
    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}
