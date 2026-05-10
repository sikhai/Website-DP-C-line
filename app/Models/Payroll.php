<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Payroll extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'year',
        'base_salary',
        'total_working_days',
        'actual_working_days',
        'official_work_salary',
        'hourly_salary',
        'allowance_total',
        'overtime_total',
        'bonus_total',
        'gross_salary',
        'note',
        'status',
        'confirmed_by',
        'confirmed_at',
        'paid_by',
        'paid_at',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'total_working_days' => 'decimal:2',
        'actual_working_days' => 'decimal:2',
        'official_work_salary' => 'decimal:2',
        'hourly_salary' => 'decimal:2',
        'allowance_total' => 'decimal:2',
        'overtime_total' => 'decimal:2',
        'bonus_total' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function allowances()
    {
        return $this->hasMany(PayrollAllowance::class);
    }

    public function overtimes()
    {
        return $this->hasMany(PayrollOvertime::class);
    }

    public function bonuses()
    {
        return $this->hasMany(PayrollBonus::class);
    }

    public function businessTrips()
    {
        return $this->hasMany(BusinessTrip::class);
    }

    public function paymentAttachments()
    {
        return $this->hasMany(PayrollPaymentAttachment::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function isLocked(): bool
    {
        return in_array($this->status, ['confirmed', 'paid'], true);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function markAsPaid(int $userId): void
    {
        if (! $this->paymentAttachments()->exists()) {
            throw ValidationException::withMessages([
                'payment_attachments' => 'At least one payment attachment is required.',
            ]);
        }

        $this->update([
            'status' => 'paid',
            'paid_by' => $userId,
            'paid_at' => now(),
        ]);
    }
}
