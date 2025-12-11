<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WarehouseProductTransaction extends Model
{
    const TYPE_IMPORT = 'IMPORT';
    const TYPE_EXPORT = 'EXPORT';

    protected $fillable = [
        'item_id',
        'item_type',
        'unit_id',
        'quantity',
        'extras',
        'type',
        'warehouse_import_id',
        'warehouse_export_id',
        'user_id',
    ];

    protected $casts = [
        'extras' => 'array',
    ];

    public function item(): MorphTo
    {
        return $this->morphTo();
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function import(): BelongsTo
    {
        return $this->belongsTo(WarehouseImport::class, 'warehouse_import_id');
    }

    public function export(): BelongsTo
    {
        return $this->belongsTo(WarehouseExport::class, 'warehouse_export_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ============================
    // TỐI ƯU SCOPES
    // ============================
    public function scopeForItem($query, $itemId, $itemType)
    {
        return $query->where('item_id', $itemId)
            ->where('item_type', $itemType);
    }

    // ============================
    // TỒN KHO (CHỈ 1 QUERY)
    // ============================
    public static function stock($itemId, $itemType, $unitId = null)
    {
        $query = self::forItem($itemId, $itemType);

        if ($unitId) {
            $query->where('unit_id', $unitId);
        }

        $result = $query->selectRaw("
                SUM(CASE WHEN type = 'IMPORT' THEN quantity ELSE 0 END) AS total_in,
                SUM(CASE WHEN type = 'EXPORT' THEN quantity ELSE 0 END) AS total_out
            ")
            ->first();

        return ($result->total_in ?? 0) - ($result->total_out ?? 0);
    }

    public function getCategoryNameAttribute()
    {
        $item = $this->item;

        if (!$item) {
            return null;
        }

        // Nếu là Accessory
        if ($item instanceof Accessory) {
            return $item->category->name ?? null;
        }

        // Nếu là Product
        if ($item instanceof Product) {
            return $item->design?->collection?->category?->name ?? null;
        }

        return null;
    }
}
