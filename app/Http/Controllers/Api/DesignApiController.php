<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Design;
use Illuminate\Http\Request;

class DesignApiController extends Controller
{
    public function loadMore(Request $request)
    {
        $page = $request->page ?? 1;

        $query = Design::with('products');

        // Filter theo collection
        if ($request->collection_id) {
            $query->where('parent_id', $request->collection_id);
        }

        $designs = $query->paginate(20);

        // Render partial HTML item
        $html = view('partials.design-item', compact('designs'))->render();

        return response()->json([
            'html' => $html,
            'next_page' => $designs->nextPageUrl() ? $page + 1 : null,
        ]);
    }
}
