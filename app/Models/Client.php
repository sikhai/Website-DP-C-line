<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

use App\Models\Design;

class Client extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_featured',
    ];
}
