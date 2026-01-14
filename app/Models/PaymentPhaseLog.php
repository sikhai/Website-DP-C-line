<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentPhaseLog extends Model
{
    protected $fillable = [
        'project_id',
        'payment_phase_id',
        'action',
        'changed_fields',
        'old_data',
        'new_data',
        'reason',
        'created_by',
    ];

    protected $casts = [
        'changed_fields' => 'array',
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    /* ================= RELATIONS ================= */

    public function paymentPhase()
    {
        return $this->belongsTo(PaymentPhase::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

