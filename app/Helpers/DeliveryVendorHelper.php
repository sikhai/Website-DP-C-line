<?php

namespace App\Helpers;

use App\Models\DeliveryVendor;

class DeliveryVendorHelper
{
    public function getSuppliers()
    {
        return DeliveryVendor::whereHas('freightRates.freightType', function ($query) {
        })->with(['freightRates' => function ($query) {
            $query->whereHas('freightType', function ($q) {
            });
        }])->get();
    }
}
