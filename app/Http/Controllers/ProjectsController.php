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

        $categories = Category::where('is_featured', 1)->whereNull('parent_id')->get();
        $projects = Project::with('products')->get();

        // Trả về view và truyền dữ liệu projects
        return view('project.show', compact('projects', 'categories', 'title_head'));
        
    }

    public function detail($project_slug)
    {

        $categories = Category::where('is_featured', 1)->whereNull('parent_id')->get();
        $project = Project::with(['products.category.parentCategory'])->where('slug',$project_slug)->first();
        $project_other = Project::where('id', '<>', $project->id)
                        ->inRandomOrder()
                        ->first();

        if (!$project) {
            abort(404);
        }

        return view('project.detail', compact('project', 'categories', 'project_other'));
    }
}
