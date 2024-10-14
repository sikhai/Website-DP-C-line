<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    public function show()
    {
        // Lấy danh sách tất cả các project, có thể phân trang hoặc lấy theo điều kiện
        $projects = Project::with('products')->get(); // Nếu cần chỉ lấy các project nhất định, có thể dùng where hoặc take

        // Trả về view và truyền dữ liệu projects
        return view('project.show', compact('projects'));
    }
}
