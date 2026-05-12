<?php

namespace App\Models;

use App\Enums\LeaveRequestStatus;
use App\Enums\LeaveRequestType;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $fillable = [
        'user_id',
        'approved_by',
        'start_date',
        'end_date',
        'days_count',
        'type',
        'status',
        'reason',
        'manager_note',
        'attachment_path',
        'attachment_disk',
        'attachment_original_name',
        'approved_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'days_count' => 'decimal:2',
        'type' => LeaveRequestType::class,
        'status' => LeaveRequestStatus::class,
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === LeaveRequestStatus::Pending;
    }
}
