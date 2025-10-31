<?php

namespace App\FormFields;

use App\Models\Accessory;
use TCG\Voyager\FormFields\AbstractHandler;

class VendorAttributesFormField extends AbstractHandler
{
    protected $codename = 'vendor_attributes';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        // Lấy tên field trong BREAD
        $fieldName = $row->field;

        // Dữ liệu cũ (khi validate fail) hoặc từ model hiện tại
        $attributes = old($fieldName, $dataTypeContent->{$fieldName} ?? []);

        // Danh sách phụ kiện
        $accessories = Accessory::select('id', 'name')->orderBy('name')->get();

        // Encode JSON (an toàn khi đưa vào HTML)
        $jsonAttributes = json_encode($attributes, JSON_HEX_APOS | JSON_HEX_QUOT);

        return view('vendor.voyager.formfields.vendor_attributes', [
            'row' => $row,
            'dataType' => $dataType,
            'dataTypeContent' => $dataTypeContent,
            'options' => $options,
            'fieldName' => $fieldName,
            'accessories' => $accessories,
            'jsonAttributes' => $jsonAttributes,
        ]);
    }
}
