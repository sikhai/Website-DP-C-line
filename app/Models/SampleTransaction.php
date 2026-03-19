<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Enums\SampleTransactionType;

class SampleTransaction extends Model
{
    protected $fillable = [
        'sample_id',
        'type' => SampleTransactionType::class,
        'quantity',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'sample_id' => 'integer',
        'quantity' => 'integer',
    ];

    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }
}
