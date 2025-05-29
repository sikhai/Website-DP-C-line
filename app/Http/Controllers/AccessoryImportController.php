<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\AccessoriesImport;
use Maatwebsite\Excel\Facades\Excel;

class AccessoryImportController extends Controller
{
    public function showForm()
    {
        return view('accessories.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        Excel::import(new AccessoriesImport, $request->file('file'));

        return back()->with('success', 'Nhập dữ liệu thành công!');
    }
}
