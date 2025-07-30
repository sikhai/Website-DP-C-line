<?php
namespace App\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class AttributesFormField extends AbstractHandler
{
    protected $codename = 'attributes';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('vendor.voyager.formfields.attributes', compact(
            'row', 'dataType', 'dataTypeContent', 'options'
        ));
    }
}
