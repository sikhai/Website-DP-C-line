<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use TCG\Voyager\Facades\Voyager;
use App\Models\Project;

class ProjectController extends VoyagerBaseController
{
    /**
     * Hiển thị danh sách project trong Voyager.
     */
    public function index(Request $request)
    {
        return parent::index($request);
    }

    /**
     * Hiển thị form tạo mới project.
     */
    public function create(Request $request)
    {
        $slug = $this->getSlug($request);

        // Lấy thông tin DataType từ Voyager
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->firstOrFail();

        // Kiểm tra quyền
        $this->authorize('add', app($dataType->model_name));

        // Tạo một instance mới của model hoặc trả về false nếu không có model
        $dataTypeContent = !empty($dataType->model_name) ? new $dataType->model_name() : null;

        // Kiểm tra nếu có model thì thiết lập thuộc tính col_width
        if ($dataTypeContent) {
            foreach ($dataType->addRows as $key => $row) {
                $dataType->addRows[$key]['col_width'] = $row->details->width ?? 100;
            }
        }

        // Kiểm tra xem model có hỗ trợ đa ngôn ngữ không
        $isModelTranslatable = $dataTypeContent ? is_bread_translatable($dataTypeContent) : false;

        return Voyager::view('voyager::bread.edit-add', compact('dataType', 'isModelTranslatable', 'dataTypeContent'));
    }


    /**
     * Lưu project mới vào database.
     */
    public function store(Request $request)
    {
        // Tạo project mới
        $project = new Project();
        $project->name = $request->input('name');
        $project->slug = $request->input('slug');
        $project->short_description = $request->input('short_description');

        // Xử lý hình ảnh và caption
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('public/uploads/projects');
                $images[] = [
                    'path' => str_replace('public/', '', $path),
                    'caption' => ''
                ];
            }
        }

        $project->images_with_captions = json_encode($images);
        $project->save();

        return redirect()->route('voyager.projects.index')->with('success', 'Project đã được tạo thành công!');
    }

    /**
     * Hiển thị form chỉnh sửa project.
     */
    public function edit(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        // Lấy thông tin DataType từ Voyager
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->firstOrFail();

        // Kiểm tra quyền
        $dataTypeContent = app($dataType->model_name)->findOrFail($id);
        $this->authorize('edit', $dataTypeContent);

        // Kiểm tra nếu có model thì thiết lập thuộc tính col_width
        foreach ($dataType->editRows as $key => $row) {
            $dataType->editRows[$key]['col_width'] = $row->details->width ?? 100;
        }

        // Kiểm tra xem model có hỗ trợ đa ngôn ngữ không
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        return Voyager::view('voyager::bread.edit-add', compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    }


    /**
     * Cập nhật project và lưu hình ảnh + caption.
     */
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $requestData = $request->all();

        $requestData['is_featured'] = $request->has('is_featured') ? 1 : 0;

        $imagesWithCaptions = [];

        // Xử lý hình ảnh cũ
        if ($request->has('images_with_captions.existing')) {
            foreach ($request->input('images_with_captions.existing') as $image) {
                $imagesWithCaptions[] = [
                    'path' => $image['path'],
                    'caption' => $image['caption'] ?? '',
                ];
            }
        }

        // Xử lý hình ảnh mới
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('public/uploads/projects');
                $caption = $request->input("new_images.$index.caption") ?? '';

                $imagesWithCaptions[] = [
                    'path' => str_replace('public/', '', $path),
                    'caption' => $caption,
                ];
            }
        }

        $requestData['images_with_captions'] = json_encode($imagesWithCaptions);

        $project->update($requestData);

        return redirect()->route('voyager.projects.index')->with([
            'message' => 'Cập nhật thành công!',
            'alert-type' => 'success'
        ]);
    }


    /**
     * Xóa project.
     */
    public function destroy(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        // Xóa ảnh trong storage
        if ($project->images_with_captions) {
            $images = json_decode($project->images_with_captions, true);
            foreach ($images as $image) {
                Storage::delete('public/' . $image['path']);
            }
        }

        // Xóa project
        $project->delete();

        return redirect()->route('voyager.projects.index')->with('success', 'Project đã được xóa!');
    }

    /**
     * Xóa ảnh khỏi project.
     */
    public function destroyImage($id, $imageIndex)
    {
        $project = Project::findOrFail($id);
        $images = json_decode($project->images_with_captions, true);

        if (isset($images[$imageIndex])) {
            Storage::delete('public/' . $images[$imageIndex]['path']);
            unset($images[$imageIndex]);
            $project->images_with_captions = json_encode(array_values($images));
            $project->save();
        }

        return redirect()->back()->with('success', 'Xóa ảnh thành công!');
    }
}
