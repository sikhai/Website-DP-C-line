<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\CollectionDisplayNameImport;
use Maatwebsite\Excel\Facades\Excel;

class CollectionImportController extends Controller
{
    public function showForm()
    {
        return view('collections.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        $import = new CollectionDisplayNameImport;
        Excel::import($import, $request->file('file'));

        return back()->with([
            'success' => 'Đã đọc file thành công! Kiểm tra log bên dưới.',
            'rows'    => $import->getRows(),
        ]);
    }
}
