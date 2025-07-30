<?php

namespace App\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;
use App\Helpers\DeliveryVendorHelper;

use App\Models\Attribute;

class AttributesFormField extends AbstractHandler
{
    protected $codename = 'attributes';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        // Khởi tạo helper và gọi phương thức getSuppliers
        $deliveryVendorHelper = new DeliveryVendorHelper();
        $suppliers = $deliveryVendorHelper->getSuppliers();

        $designAttributes = Attribute::where('type', 'design')->pluck('name')->unique();

        return view('vendor.voyager.formfields.attributes', compact(
            'row',
            'dataType',
            'dataTypeContent',
            'options',
            'suppliers',
            'designAttributes'
        ));
    }
}
