<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseExport extends Model
{
    protected $fillable = [
        'export_code',
        'user_id',
        'project_id',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(
            WarehouseProductTransaction::class,
            'warehouse_export_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
