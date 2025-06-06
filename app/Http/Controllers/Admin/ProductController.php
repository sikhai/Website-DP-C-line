<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\DataType;
use Illuminate\Database\Eloquent\SoftDeletes;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataUpdated;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use TCG\Voyager\Models\Setting;

use App\Models\Attribute;

use App\Models\Product;

use App\Services\ProductService;

class ProductController extends VoyagerBaseController
{

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        // Get the DataType for 'products'
        $dataType = DataType::where('slug', 'products')->first();

        // Call parent method to handle common Voyager functionalities
        return parent::index($request, $dataType);
    }
    //***************************************
    //
    //                   /\
    //                  /  \
    //                 / /\ \
    //                / ____ \
    //               /_/    \_\
    //
    //
    // Add a new item of our Data Type BRE(A)D
    //
    //****************************************
    public function create(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        $dataTypeContent = (strlen($dataType->model_name) != 0)
            ? new $dataType->model_name()
            : false;

        foreach ($dataType->addRows as $key => $row) {
            $dataType->addRows[$key]['col_width'] = $row->details->width ?? 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'add');

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'add', $isModelTranslatable);

        //get all name attributes

        $attributes_name = $this->getUniqueAttributeNames();

        $result_attributes = $this->productService->getAttributesWithProductCount();

        $status_attributes = $this->productService->getStatusAttributes($result_attributes);

        $view = 'layouts.admin.products.edit-add';

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'attributes_name', 'result_attributes', 'status_attributes'));
    }

    /**
     * POST BRE(A)D - Store data.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();

        // Handle multiple file upload for images
        $imagePaths = [];
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            foreach ($images as $image) {
                $imagePath = $image->store('products', 'public');
                $webpImagePath = str_replace('.' . $image->getClientOriginalExtension(), '.webp', $imagePath);
                $image = Image::make(Storage::disk('public')->path($imagePath))->encode('webp', 70);
                Storage::disk('public')->put($webpImagePath, (string) $image);
                $imagePaths[] = $webpImagePath;
            }
        }

        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());

        if (!empty($imagePaths)) {
            $data->images = json_encode($imagePaths);
            $data->save();
        }

        // Handle attributes
        if ($request->has('attributes')) {
            $attributes = $request->input('attributes');
            $attributesJson = json_encode($attributes);
            $attribute = \App\Models\Attribute::firstOrCreate(['value' => $attributesJson]);
            $data->attributes()->syncWithoutDetaching([$attribute->id]);

            // Xóa cache để lần sau sẽ tạo lại cache mới
            Cache::forget('attributes_with_product_count');
        }

        event(new BreadDataAdded($dataType, $data));

        if (!$request->has('_tagging')) {
            if (auth()->user()->can('browse', $data)) {
                $redirect = redirect()->route("voyager.{$dataType->slug}.index");
            } else {
                $redirect = redirect()->back();
            }

            return $redirect->with([
                'message'    => __('voyager::generic.successfully_added_new') . " {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
        } else {
            return response()->json(['success' => true, 'data' => $data]);
        }
    }


    //***************************************
    //                ______
    //               |  ____|
    //               | |__
    //               |  __|
    //               | |____
    //               |______|
    //
    //  Edit an item of our Data Type BR(E)AD
    //
    //****************************************

    public function edit(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);
            $query = $model->query();

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $query = $query->withTrashed();
            }
            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope' . ucfirst($dataType->scope))) {
                $query = $query->{$dataType->scope}();
            }
            $dataTypeContent = call_user_func([$query, 'findOrFail'], $id);
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }

        foreach ($dataType->editRows as $key => $row) {
            $dataType->editRows[$key]['col_width'] = isset($row->details->width) ? $row->details->width : 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'edit');

        // Check permission
        $this->authorize('edit', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'edit', $isModelTranslatable);

        // Lấy danh sách các attribute liên kết với sản phẩm
        $attributes = $dataTypeContent->attributes->pluck('value')->last();
        $attributes = json_decode($attributes, true);

        $result_attributes = $this->productService->getAttributesWithProductCount();

        $status_attributes = $this->productService->getStatusAttributes($result_attributes);

        $view = 'layouts.admin.products.edit-add';

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'attributes', 'status_attributes'));
    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $id = $id instanceof \Illuminate\Database\Eloquent\Model ? $id->{$id->getKeyName()} : $id;

        $model = app($dataType->model_name);
        $query = $model->query();
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope' . ucfirst($dataType->scope))) {
            $query = $query->{$dataType->scope}();
        }
        if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
            $query = $query->withTrashed();
        }

        $data = $query->findOrFail($id);

        // Check permission
        $this->authorize('edit', $data);

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();

        // Handle multiple file upload for images
        $imagePaths = json_decode($data->images, true) ?: [];
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            foreach ($images as $image) {
                $imagePath = $image->store('products', 'public');
                $webpImagePath = str_replace('.' . $image->getClientOriginalExtension(), '.webp', $imagePath);
                $image = Image::make(Storage::disk('public')->path($imagePath))->encode('webp', 70);
                Storage::disk('public')->put($webpImagePath, (string) $image);
                $imagePaths[] = $webpImagePath;
            }
        }

        // Handle removal of existing images
        if ($request->has('remove_images')) {
            $removeIndexes = $request->input('remove_images');
            foreach ($removeIndexes as $index) {
                if (isset($imagePaths[$index])) {
                    Storage::disk('public')->delete($imagePaths[$index]);
                    unset($imagePaths[$index]);
                }
            }
            $imagePaths = array_values($imagePaths); // Reindex array
        }

        // Update the data record with the new image paths
        $data->images = json_encode($imagePaths);
        $data->save();

        // Get fields with images to remove before updating and make a copy of $data
        $to_remove = $dataType->editRows->where('type', 'image')
            ->filter(function ($item, $key) use ($request) {
                return $request->hasFile($item->field);
            });

        $original_data = clone ($data);

        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

        if (!empty($imagePaths)) {
            $data->images = json_encode($imagePaths);
            $data->save();
        }

        // Delete Images
        $this->deleteBreadImages($original_data, $to_remove);

        // Handle attributes
        if ($request->has('attributes')) {
            $attributes = $request->input('attributes');
            $attributesJson = json_encode($attributes);

            $attribute_id = $request->input('attribute_id');

            // Xóa cache để lần sau sẽ tạo lại cache mới
            Cache::forget('attributes_with_product_count');

            if ($attribute_id) {
                // Update existing attribute or create new one if not found
                $existingAttribute = \App\Models\Attribute::find($attribute_id);
                if ($existingAttribute) {
                    $existingAttribute->update(['value' => $attributesJson]);
                } else {
                    $newAttribute = \App\Models\Attribute::create(['value' => $attributesJson]);
                    $data->attributes()->syncWithoutDetaching([$newAttribute->id]);
                }
            } else {
                // Create new attribute
                $newAttribute = \App\Models\Attribute::create(['value' => $attributesJson]);
                $data->attributes()->syncWithoutDetaching([$newAttribute->id]);
            }
        }


        event(new BreadDataUpdated($dataType, $data));

        if (auth()->user()->can('browse', app($dataType->model_name))) {
            $redirect = redirect()->route("voyager.{$dataType->slug}.index");
        } else {
            $redirect = redirect()->back();
        }

        return $redirect->with([
            'message'    => __('voyager::generic.successfully_updated') . " {$dataType->getTranslatedAttribute('display_name_singular')}",
            'alert-type' => 'success',
        ]);
    }

    public function getUniqueAttributeNames()
    {
        // Lấy tất cả các giá trị từ bảng attributes
        $attributes = Attribute::all();

        // Thu thập các giá trị của 'name' từ trường 'value'
        $names = [];
        foreach ($attributes as $attribute) {
            $values = json_decode($attribute->value, true);
            if (is_array($values)) {
                foreach ($values as $value) {
                    if (isset($value['name'])) {
                        $names[] = $value['name'];
                    }
                }
            }
        }

        // Loại bỏ các giá trị trùng lặp
        $uniqueNames_Attribute = array_unique($names);

        return $uniqueNames_Attribute;
    }

    public function checkProductCode(Request $request)
    {
        $productCode = $request->input('product_code');

        $exists = Product::where('product_code', $productCode)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function updateStatusAttributes(Request $request)
    {
        // Lấy dữ liệu từ AJAX
        $updatedItems = $request->input('items'); // Mảng các mục với trạng thái đã thay đổi

        // Lấy key cache
        $cacheKey = 'status_attributes';

        // Cập nhật cache
        Cache::put($cacheKey, $updatedItems, 720); // Lưu giá trị mới vào cache (720 phút)

        // Cập nhật giá trị vào cơ sở dữ liệu
        $statusAttributes = json_encode($updatedItems); // Chuyển đổi mảng thành chuỗi JSON
        $setting = Setting::where('key', $cacheKey)->first();

        if ($setting) {
            // Cập nhật bản ghi trong bảng settings
            $setting->value = $statusAttributes;
            $setting->save();
        } else {
            // Nếu chưa tồn tại, tạo mới bản ghi
            Setting::create([
                'key'          => $cacheKey,
                'value'        => $statusAttributes,
                'display_name' => 'Status Attributes',
                'type'         => 'text',
                'order'        => 1,
                'group'        => 'General',
            ]);
        }

        // Trả về phản hồi JSON
        return response()->json(['success' => true, 'message' => 'Status updated successfully!']);
    }
}
