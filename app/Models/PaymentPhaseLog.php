<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentPhaseLog extends Model
{
    protected $fillable = [
        'payment_phase_id',
        'project_id',
        'action',
        'changed_fields',
        'reason',
        'old_data',
        'new_data',
        'created_by',
    ];

    protected $casts = [
        'changed_fields' => 'array',
        'old_data' => 'array',
        'new_data' => 'array',
    ];
}
