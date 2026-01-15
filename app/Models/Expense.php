<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'amount',
        'expense_date',
        'planned_pay_date',
        'category',
        'status',
        'note',
        'created_by',
        'approved_by',
        'approved_at',
        'paid_by',
        'paid_at',
    ];

    protected $casts = [
        'expense_date'     => 'date',
        'planned_pay_date' => 'date',
        'approved_at'      => 'datetime',
        'paid_at'          => 'datetime',
        'amount'           => 'decimal:2',
    ];

    /* ===== relations ===== */

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /* ===== domain logic ===== */

    public function approve(int $userId): void
    {
        if ($this->status !== 'draft') {
            throw new \RuntimeException('Chi phí không ở trạng thái đề xuất');
        }

        $this->update([
            'status'      => 'approved',
            'approved_by'=> $userId,
            'approved_at'=> now(),
        ]);
    }

    public function markAsPaid(int $userId): void
    {
        if ($this->status !== 'approved') {
            throw new \RuntimeException('Chi phí chưa được duyệt');
        }

        $this->update([
            'status'  => 'paid',
            'paid_by' => $userId,
            'paid_at' => now(),
        ]);
    }
}

