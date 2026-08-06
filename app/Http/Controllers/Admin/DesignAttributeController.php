<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;

class DesignAttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::where('type', 'design')
            ->select('id', 'name', 'status')
            ->orderByRaw("CASE LOWER(name) WHEN 'supplier' THEN 1 WHEN 'thinkness' THEN 2 ELSE 0 END")
            ->orderByRaw('LOWER(name)')
            ->get();

        return response()->json([
            'success' => true,
            'attributes' => $attributes,
        ]);
    }

    public function toggleStatus(Request $request, $id)
    {
        $attribute = Attribute::where('type', 'design')->findOrFail($id);

        if ($request->has('status')) {
            $attribute->status = $request->boolean('status') ? 1 : 0;
        } else {
            $attribute->status = $attribute->status == 1 ? 0 : 1;
        }

        $attribute->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'attribute' => $attribute,
        ]);
    }
}
