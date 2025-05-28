<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Design;
use App\Models\Product;
use App\Models\Project;
use App\Models\Client;

class MainController extends Controller
{
    public function home()
    {

        $categories = Category::where('is_featured', 1)->where('type', 'PRODUCT')->whereNull('parent_id')->get();
        $designs_is_trending = Design::where('is_trending', 1)
            ->with('parentCategory')
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();

        $projects_home = Project::where('is_featured', 1)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $projects_of_mounth = Project::where('is_featured', 1)
            ->where('is_trending', 1)
            ->orderBy('created_at', 'desc')
            ->first();

        $clients_home = Client::where('is_featured', 1)
            ->get();

        return view('home', compact('designs_is_trending', 'categories', 'projects_home', 'clients_home', 'projects_of_mounth'));
    }
}
