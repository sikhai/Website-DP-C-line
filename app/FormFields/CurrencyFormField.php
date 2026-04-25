<?php

namespace App\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;
use App\Models\Currency;

class CurrencyFormField extends AbstractHandler
{
    protected $codename = 'currency';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        $currencies = Currency::all();

        $selected = old($row->field, $dataTypeContent->{$row->field} ?? null);

        return view('formfields.currency', compact(
            'row',
            'dataType',
            'dataTypeContent',
            'currencies',
            'selected'
        ));
    }
}