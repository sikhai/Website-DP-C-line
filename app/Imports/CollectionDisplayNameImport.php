<?php

namespace App\Imports;

use App\Models\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CollectionDisplayNameImport implements WithHeadingRow, ToCollection
{
    private array $rows = [];

    public function collection(\Illuminate\Support\Collection $collection)
    {
        Log::info('=== BẮT ĐẦU IMPORT DISPLAY_NAME CHO COLLECTIONS ===');

        foreach ($collection as $i => $row) {
            $slug         = trim($row['slug'] ?? '');
            $displayName  = trim($row['display_name'] ?? '');

            if (!$slug) {
                $status = 'thiếu slug';
                Log::warning("Dòng " . ($i + 2) . ": bỏ qua do thiếu slug");
            } else {
                $updated = Collection::where('slug', $slug)->update(['display_name' => $displayName ?: null]);

                if ($updated) {
                    $status = 'đã cập nhật';
                    Log::info("Dòng " . ($i + 2) . ": slug=[{$slug}] display_name=[{$displayName}] => OK");
                } else {
                    $status = 'không tìm thấy slug';
                    Log::warning("Dòng " . ($i + 2) . ": slug=[{$slug}] => không tìm thấy collection");
                }
            }

            $this->rows[] = [
                'row'          => $i + 2,
                'slug'         => $slug,
                'display_name' => $displayName,
                'status'       => $status,
            ];
        }

        Log::info('=== KẾT THÚC IMPORT DISPLAY_NAME CHO COLLECTIONS ===');
    }

    public function getRows(): array
    {
        return $this->rows;
    }
}
