<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Enums\SampleRequestStatus;

class SampleRequest extends Model
{
    protected $fillable = [
        'sample_id',
        'quantity',
        'requested_by',
        'used_for',
        'status' => SampleRequestStatus::class,
    ];

    protected $casts = [
        'sample_id' => 'integer',
        'quantity' => 'integer',
    ];

    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }

    public function returns()
    {
        return $this->hasMany(SampleReturn::class);
    }
}
