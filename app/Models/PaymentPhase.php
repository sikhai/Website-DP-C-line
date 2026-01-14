<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentPhase extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'amount',
        'paid_amount',
        'due_date',
        'note',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function logs()
    {
        return $this->hasMany(PaymentPhaseLog::class);
    }

    /* ===== STATUS (TÍNH ĐỘNG) ===== */

    public function getStatusAttribute()
    {
        if ($this->paid_amount >= $this->amount) {
            return 'paid';
        }

        if (now()->gt($this->due_date)) {
            return 'overdue';
        }

        if (now()->toDateString() === $this->due_date->toDateString()) {
            return 'due';
        }

        return 'pending';
    }
}
