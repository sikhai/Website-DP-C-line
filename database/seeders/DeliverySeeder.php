<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeliveryVendor;
use App\Models\DeliveryFreightType;
use App\Models\DeliveryFreightRate;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        // Insert Freight Types
        $freightTypes = [
            ['type' => 'air_freight', 'name' => 'Air Freight'],
            ['type' => 'ocean_freight', 'name' => 'Ocean Freight'],
        ];

        $freightTypeIds = [];
        foreach ($freightTypes as $type) {
            $freightType = DeliveryFreightType::create($type);
            $freightTypeIds[$type['type']] = $freightType->id;
        }

        // Vendors & Rates
        $vendors = [
            [
                'name' => 'TOPLI',
                'point_of_origin' => 'China - ExW Beijing',
                'air_freight_rates' => [
                    ['from' => 1, 'to' => 10, 'rate' => 750000],
                    ['from' => 10, 'to' => 45, 'rate' => 400000],
                    ['from' => 45, 'to' => 100, 'rate' => 330000],
                    ['from' => 100, 'to' => 150, 'rate' => 310000],
                ],
                'ocean_freight_rates' => [
                    ['from' => 150, 'to' => 1000, 'rate' => 17852440]
                ],
            ],
            [
                'name' => 'SIC',
                'point_of_origin' => 'China - ExW Hangzou',
                'air_freight_rates' => [
                    ['from' => 1, 'to' => 10, 'rate' => 730840],
                    ['from' => 10, 'to' => 45, 'rate' => 390000],
                    ['from' => 45, 'to' => 100, 'rate' => 377302],
                    ['from' => 100, 'to' => 150, 'rate' => 186686]
                ],
                'ocean_freight_rates' => [
                    ['from' => 150, 'to' => 1000, 'rate' => 17126000]
                ],
            ],
            [
                'name' => 'Cherry',
                'point_of_origin' => 'China - ExW Hangzou',
                'air_freight_rates' => [
                    ['from' => 1, 'to' => 10, 'rate' => 730840],
                    ['from' => 10, 'to' => 45, 'rate' => 390000],
                    ['from' => 45, 'to' => 100, 'rate' => 377302],
                    ['from' => 100, 'to' => 150, 'rate' => 186686]
                ],
                'ocean_freight_rates' => [
                    ['from' => 150, 'to' => 1000, 'rate' => 17126000]
                ],
            ],
            [
                'name' => 'DAVIS',
                'point_of_origin' => 'China - ExW',
                'air_freight_rates' => [
                    ['from' => 1, 'to' => 10, 'rate' => 730840],
                    ['from' => 10, 'to' => 45, 'rate' => 390000],
                    ['from' => 45, 'to' => 100, 'rate' => 377302],
                    ['from' => 100, 'to' => 150, 'rate' => 186686]
                ],
                'ocean_freight_rates' => [
                    ['from' => 150, 'to' => 1000, 'rate' => 17776000]
                ],
            ],
            [
                'name' => 'ELASTRON',
                'point_of_origin' => 'China',
                'air_freight_rates' => [
                    ['from' => 1, 'to' => 10, 'rate' => 730840],
                    ['from' => 10, 'to' => 45, 'rate' => 390000],
                    ['from' => 45, 'to' => 100, 'rate' => 377302],
                    ['from' => 100, 'to' => 150, 'rate' => 186686]
                ],
                'ocean_freight_rates' => [
                    ['from' => 150, 'to' => 1000, 'rate' => 15306000]
                ],
            ],
            [
                'name' => 'GOLDHOUSE',
                'point_of_origin' => 'Thailand - ExW Bangkok',
                'air_freight_rates' => [
                    ['from' => 1, 'to' => 10, 'rate' => 668624],
                    ['from' => 10, 'to' => 45, 'rate' => 340000],
                    ['from' => 45, 'to' => 100, 'rate' => 260000]
                ]
            ],
            [
                'name' => 'NITAS',
                'point_of_origin' => 'Thailand - ExW Bangkok',
                'air_freight_rates' => [
                    ['from' => 1, 'to' => 10, 'rate' => 668624],
                    ['from' => 10, 'to' => 45, 'rate' => 340000],
                    ['from' => 45, 'to' => 100, 'rate' => 260000]
                ]
            ],
            [
                'name' => 'SWAVELLE',
                'point_of_origin' => 'USA - Exw NY',
                'air_freight_rates' => [
                    ['from' => 1, 'to' => 10, 'rate' => 905916],
                    ['from' => 10, 'to' => 45, 'rate' => 608356],
                    ['from' => 45, 'to' => 100, 'rate' => 500000]
                ],
                'ocean_freight_rates' => [
                    ['from' => 150, 'to' => 1000, 'rate' => 22000000]
                ],
            ],
            [
                'name' => 'LEATHERFABS',
                'point_of_origin' => 'Turkey - ExW Istanbul',
                'air_freight_rates' => [
                    ['from' => 1, 'to' => 10, 'rate' => 1526316],
                    ['from' => 10, 'to' => 45, 'rate' => 501820],
                    ['from' => 45, 'to' => 100, 'rate' => 450000]
                ],
                'ocean_freight_rates' => [
                    ['from' => 150, 'to' => 1000, 'rate' => 24016000]
                ],
            ],
            [
                'name' => 'TOSUN',
                'point_of_origin' => 'Turkey - ExW Istanbul',
                'air_freight_rates' => [
                    ['from' => 1, 'to' => 10, 'rate' => 1526316],
                    ['from' => 10, 'to' => 45, 'rate' => 501820],
                    ['from' => 45, 'to' => 100, 'rate' => 450000]
                ]
            ],
            [
                'name' => 'FAB&FABS',
                'point_of_origin' => 'Sing - ExW Sing',
                'air_freight_rates' => [
                    ['from' => 1, 'to' => 10, 'rate' => 750000],
                    ['from' => 10, 'to' => 45, 'rate' => 400000],
                    ['from' => 45, 'to' => 100, 'rate' => 330000],
                    ['from' => 100, 'to' => 150, 'rate' => 310000]
                ],
                'ocean_freight_rates' => [
                    ['from' => 150, 'to' => 1000, 'rate' => 14500000]
                ],
            ],
            [
                'name' => 'ULTRAFABRICS',
                'point_of_origin' => 'Exw - NY',
                'air_freight_rates' => [
                    ['from' => 1, 'to' => 10, 'rate' => 905916],
                    ['from' => 10, 'to' => 45, 'rate' => 608356],
                    ['from' => 45, 'to' => 100, 'rate' => 500000]
                ],
                'ocean_freight_rates' => [
                    ['from' => 150, 'to' => 1000, 'rate' => 22000000]
                ],
            ],
            [
                'name' => 'MUNZERT',
                'point_of_origin' => 'ExW - Germany',
                'air_freight_rates' => [
                    ['from' => 1, 'to' => 10, 'rate' => 910000],
                    ['from' => 10, 'to' => 45, 'rate' => 500000],
                    ['from' => 45, 'to' => 100, 'rate' => 650000]
                ]
            ],
            [
                'name' => 'ELITEX',
                'point_of_origin' => 'China - FOB Shanghai',
                'ocean_freight_rates' => [
                    ['from' => 150, 'to' => 1000, 'rate' => 8500000]
                ],
            ],
            [
                'name' => 'MERMET',
                'point_of_origin' => 'China - FOB Shanghai',
                'ocean_freight_rates' => [
                    ['from' => 150, 'to' => 1000, 'rate' => 8500000]
                ],
            ],
            [
                'name' => 'FERRARI SING',
                'point_of_origin' => 'Sing - ExW Sing',
                'air_freight_rates' => [
                    ['from' => 1, 'to' => 10, 'rate' => 750000],
                    ['from' => 10, 'to' => 45, 'rate' => 400000],
                    ['from' => 45, 'to' => 100, 'rate' => 330000],
                    ['from' => 100, 'to' => 150, 'rate' => 310000]
                ],
                'ocean_freight_rates' => [
                    ['from' => 150, 'to' => 1000, 'rate' => 14500000]
                ],
            ],
            [
                'name' => 'FERRARI FRANCH',
                'point_of_origin' => 'Franch - FOB',
                'ocean_freight_rates' => [
                    ['from' => 150, 'to' => 1000, 'rate' => 85000000]
                ],
            ],

        ];

        foreach ($vendors as $vendorData) {
            $vendor = DeliveryVendor::create([
                'name' => $vendorData['name'],
                'point_of_origin' => $vendorData['point_of_origin'],
            ]);

            // Air Freight
            if (isset($vendorData['air_freight_rates'])) {
                foreach ($vendorData['air_freight_rates'] as $rate) {
                    DeliveryFreightRate::create([
                        'vendor_id' => $vendor->id,
                        'freight_type_id' => $freightTypeIds['air_freight'],
                        'weight_from' => $rate['from'],
                        'weight_to' => $rate['to'],
                        'rate' => $rate['rate'],
                    ]);
                }
            }

            // Ocean Freight
            if (isset($vendorData['ocean_freight_rates'])) {
                foreach ($vendorData['ocean_freight_rates'] as $rate) {
                    DeliveryFreightRate::create([
                        'vendor_id' => $vendor->id,
                        'freight_type_id' => $freightTypeIds['ocean_freight'],
                        'weight_from' => $rate['from'],
                        'weight_to' => $rate['to'],
                        'rate' => $rate['rate'],
                    ]);
                }
            }
        }
    }
}
