<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeliveryVendorFreightSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // 1. Tạo vendor: Vận chuyển trong nước
        $vendorId = DB::table('delivery_vendors')->insertGetId([
            'name' => 'Vận chuyển trong nước',
            'point_of_origin' => 'Hồ Chí Minh',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. Tạo freight type: nội địa
        $freightTypeId = DB::table('delivery_freight_types')->insertGetId([
            'type' => 'domestic',
            'name' => 'Nội địa',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. Tạo freight rate: 1-1000kg, rate = 0
        DB::table('delivery_freight_rates')->insert([
            'vendor_id' => $vendorId,
            'freight_type_id' => $freightTypeId,
            'weight_from' => 1,
            'weight_to' => 1000,
            'rate' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
