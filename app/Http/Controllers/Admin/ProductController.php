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
use App\Models\DeliveryVendor;
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
        $dataType = DataType::where('slug', 'products')->first();
        return parent::index($request, $dataType);
    }

    public function create(Request $request)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $this->authorize('add', app($dataType->model_name));

        $dataTypeContent = (strlen($dataType->model_name) != 0) ? new $dataType->model_name() : false;

        foreach ($dataType->addRows as $key => $row) {
            $dataType->addRows[$key]['col_width'] = $row->details->width ?? 100;
        }

        $this->removeRelationshipField($dataType, 'add');
        $isModelTranslatable = is_bread_translatable($dataTypeContent);
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'add', $isModelTranslatable);

        $Suppliers = $this->getSuppliers();

        // $attributes_name = $this->getUniqueAttributeNames();
        // $result_attributes = $this->productService->getAttributesWithProductCount();
        // $status_attributes = $this->productService->getStatusAttributes($result_attributes);

        $view = 'layouts.admin.products.edit-add';

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'Suppliers'));
    }

    public function store(Request $request)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $this->authorize('add', app($dataType->model_name));
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();

        // Handle multiple images
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $this->saveWebpImage($image, 'product_');
            }
        }

        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());

        if (!empty($imagePaths)) {
            $data->images = json_encode($imagePaths);
        }

        // Handle single image
        if ($request->hasFile('image')) {
            $data->image = $this->saveWebpImage($request->file('image'), 'main_');
        }

        $data->save();

        // Handle attributes
        if ($request->has('attributes')) {
            $attributes = $request->input('attributes');
            $attributesJson = json_encode($attributes);
            $attribute = Attribute::firstOrCreate(['value' => $attributesJson]);
            $data->attributes()->syncWithoutDetaching([$attribute->id]);
            Cache::forget('attributes_with_product_count');
        }

        event(new BreadDataAdded($dataType, $data));

        if (!$request->has('_tagging')) {
            if (auth()->user()->can('browse', $data)) {
                return redirect()->route("voyager.{$dataType->slug}.index")->with([
                    'message' => __('voyager::generic.successfully_added_new') . " {$dataType->getTranslatedAttribute('display_name_singular')}",
                    'alert-type' => 'success',
                ]);
            } else {
                return redirect()->back();
            }
        } else {
            return response()->json(['success' => true, 'data' => $data]);
        }
    }

    public function edit(Request $request, $id)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);
            $query = $model->query();

            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $query = $query->withTrashed();
            }

            if ($dataType->scope && method_exists($model, 'scope' . ucfirst($dataType->scope))) {
                $query = $query->{$dataType->scope}();
            }

            $dataTypeContent = $query->findOrFail($id);
        } else {
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }

        foreach ($dataType->editRows as $key => $row) {
            $dataType->editRows[$key]['col_width'] = $row->details->width ?? 100;
        }

        $this->removeRelationshipField($dataType, 'edit');
        $this->authorize('edit', $dataTypeContent);

        $isModelTranslatable = is_bread_translatable($dataTypeContent);
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'edit', $isModelTranslatable);

        $Suppliers = $this->getSuppliers();

        // $attributes = $dataTypeContent->attributes->pluck('value')->last();
        // $attributes = json_decode($attributes, true);

        // $result_attributes = $this->productService->getAttributesWithProductCount();
        // $status_attributes = $this->productService->getStatusAttributes($result_attributes);

        $view = 'layouts.admin.products.edit-add';

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'Suppliers'));
    }

    public function update(Request $request, $id)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $id = $id instanceof \Illuminate\Database\Eloquent\Model ? $id->{$id->getKeyName()} : $id;

        $model = app($dataType->model_name);
        $query = $model->query();
        if ($dataType->scope && method_exists($model, 'scope' . ucfirst($dataType->scope))) {
            $query = $query->{$dataType->scope}();
        }
        if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
            $query = $query->withTrashed();
        }

        $data = $query->findOrFail($id);
        $this->authorize('edit', $data);

        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();

        // Handle multiple images
        $imagePaths = json_decode($data->images, true) ?: [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $this->saveWebpImage($image, 'product_');
            }
        }

        if ($request->has('remove_images')) {
            $removeIndexes = $request->input('remove_images');
            foreach ($removeIndexes as $index) {
                if (isset($imagePaths[$index])) {
                    Storage::disk('public')->delete($imagePaths[$index]);
                    unset($imagePaths[$index]);
                }
            }
            $imagePaths = array_values($imagePaths);
        }

        $to_remove = $dataType->editRows->where('type', 'image')->filter(function ($item) use ($request) {
            return $request->hasFile($item->field);
        });

        $original_data = clone $data;

        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

        if (!empty($imagePaths)) {
            $data->images = json_encode($imagePaths);
        }

        // Handle single image
        if ($request->hasFile('image')) {
            if ($data->image) {
                Storage::disk('public')->delete($data->image);
            }
            $data->image = $this->saveWebpImage($request->file('image'), 'main_');
        }

        $data->save();
        $this->deleteBreadImages($original_data, $to_remove);

        if ($request->has('attributes')) {
            $attributes = $request->input('attributes');
            $attributesJson = json_encode($attributes);
            $attribute_id = $request->input('attribute_id');

            Cache::forget('attributes_with_product_count');

            if ($attribute_id) {
                $existingAttribute = Attribute::find($attribute_id);
                if ($existingAttribute) {
                    $existingAttribute->update(['value' => $attributesJson]);
                } else {
                    $newAttribute = Attribute::create(['value' => $attributesJson]);
                    $data->attributes()->syncWithoutDetaching([$newAttribute->id]);
                }
            } else {
                $newAttribute = Attribute::create(['value' => $attributesJson]);
                $data->attributes()->syncWithoutDetaching([$newAttribute->id]);
            }
        }

        event(new BreadDataUpdated($dataType, $data));

        if (auth()->user()->can('browse', app($dataType->model_name))) {
            return redirect()->route("voyager.{$dataType->slug}.index")->with([
                'message' => __('voyager::generic.successfully_updated') . " {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
        }

        return redirect()->back();
    }

    public function getUniqueAttributeNames()
    {
        $attributes = Attribute::all();
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
        return array_unique($names);
    }

    public function checkProductCode(Request $request)
    {
        $productCode = $request->input('product_code');
        $exists = Product::where('product_code', $productCode)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function updateStatusAttributes(Request $request)
    {
        $updatedItems = $request->input('items');
        $cacheKey = 'status_attributes';
        Cache::put($cacheKey, $updatedItems, 720);

        $statusAttributes = json_encode($updatedItems);
        $setting = Setting::where('key', $cacheKey)->first();

        if ($setting) {
            $setting->value = $statusAttributes;
            $setting->save();
        } else {
            Setting::create([
                'key' => $cacheKey,
                'value' => $statusAttributes,
                'display_name' => 'Status Attributes',
                'type' => 'text',
                'order' => 1,
                'group' => 'General',
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Status updated successfully!']);
    }

    private function saveWebpImage($image, $prefix = 'image_')
    {
        $filename = uniqid($prefix) . '.webp';

        $webpImage = Image::make($image)
            ->resize(1200, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('webp', 50);

        $path = 'products/' . $filename;
        Storage::disk('public')->put($path, (string) $webpImage);
        return $path;
    }

    protected function getSuppliers()
    {
        return DeliveryVendor::whereHas('freightRates.freightType', function ($query) {
            $query->where('type', '!=', 'domestic');
        })->with(['freightRates' => function ($query) {
            $query->whereHas('freightType', function ($q) {
                $q->where('type', '!=', 'domestic');
            });
        }])->get();
    }
}
