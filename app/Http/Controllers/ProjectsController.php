<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    public function show()
    {

        $title_head = 'Our Projects';

        $categories = Category::where('is_featured', 1)->get();
        $projects = Project::with('products')->get();

        // Trả về view và truyền dữ liệu projects
        return view('project.show', compact('projects', 'categories', 'title_head'));
        
    }

    public function detail($project_slug)
    {

        $title_head = 'Our Projects';

        $categories = Category::where('is_featured', 1)->get();
        $projects = Project::with('products')->where('slug',$project_slug)->get();

        if (!$projects) {
            abort(404);
        }

        return view('project.detail', compact('projects', 'categories', 'title_head'));
    }
}
