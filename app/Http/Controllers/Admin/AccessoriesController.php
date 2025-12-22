<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Gate;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;
use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;

class AccessoriesController extends VoyagerBaseController
{
    public function index(Request $request)
    {
        $slug = 'accessories';
        $dataType = Voyager::model('DataType')->where('slug', $slug)->firstOrFail();

        // Tạo instance model
        $modelClass = $dataType->model_name;
        $modelInstance = app($modelClass);

        // SoftDeletes
        $usesSoftDeletes = in_array(
            'Illuminate\Database\Eloquent\SoftDeletes',
            class_uses_recursive($modelInstance)
        );

        // Server-side
        $isServerSide = isset($dataType->server_side) && $dataType->server_side;

        // Order
        $orderColumn = $dataType->order_column ?? null;
        $orderDirection = $dataType->order_direction ?? 'desc';

        // Query bắt đầu
        $query = $modelInstance->newQuery();

        // ----------------- SEARCH / FILTER -----------------
        // Hỗ trợ param giống Voyager: s (search value), key (column), filter (contains/equals)
        $searchValue = $request->get('search', $request->get('s', null));
        $searchKeyFromRequest = $request->get('key', null);
        $searchFilterFromRequest = $request->get('filter', null);

        // build searchNames từ browseRows nếu có, else từ fillable
        $searchNames = [];
        if (!empty($dataType->browseRows)) {
            foreach ($dataType->browseRows as $row) {
                $searchNames[$row->field] = $row->display_name;
            }
        } else {
            foreach ($modelInstance->getFillable() as $f) {
                if ($f !== 'id') $searchNames[$f] = ucfirst(str_replace('_', ' ', $f));
            }
        }

        // defaultSearchKey: nếu request không chỉ định, lấy key đầu của $searchNames hoặc 'id'
        $defaultSearchKey = $searchKeyFromRequest ?? (count($searchNames) ? array_key_first($searchNames) : 'id');
        $defaultSearchFilter = $searchFilterFromRequest ?? 'contains'; // giống Voyager mặc định

        // giá trị search thực tế: ưu tiên param, fallback rỗng
        $searchKey = $searchKeyFromRequest ?? $defaultSearchKey;
        $searchFilter = $searchFilterFromRequest ?? $defaultSearchFilter;

        if ($searchValue && $searchKey) {
            if ($searchFilter === 'equals') {
                $query->where($searchKey, $searchValue);
            } else {
                // contains/default
                $query->where($searchKey, 'like', '%' . $searchValue . '%');
            }
        }

        // Các filter custom khác (ví dụ status)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Nếu cần tối ưu (chỉ select cột hiển thị) -> bật ?optimize=1
        if ($request->has('optimize')) {
            $selectCols = ['id'];
            if (!empty($dataType->browseRows)) {
                foreach ($dataType->browseRows as $row) {
                    $selectCols[] = $row->field;
                }
            } else {
                $selectCols = array_merge($selectCols, $modelInstance->getFillable());
            }
            $selectCols = array_unique($selectCols);
            $query->select($selectCols);
        }

        // Order
        if ($orderColumn) {
            $query->orderBy($orderColumn, $orderDirection);
        } else {
            $query->orderBy('id', 'desc');
        }

        // Pagination
        $perPage = $dataType->pagination ?: 50;
        if ($isServerSide) {
            $dataTypeContent = $query->paginate($perPage)->appends($request->except('page'));
        } else {
            $dataTypeContent = $query->get();
        }

        // Các biến view khác
        $isModelTranslatable = is_bread_translatable($modelInstance);

        $actions = collect(Voyager::actions())
            ->map(function ($action) use ($dataType) {
                return new $action($dataType, null);
            })
            ->filter(function ($action) use ($dataType) {
                return $action->shouldActionDisplayOnDataType($dataType);
            });

        $showCheckboxColumn = false;
        try {
            if (auth()->check() && Gate::allows('delete', $modelInstance)) {
                $showCheckboxColumn = true;
            }
        } catch (\Throwable $e) {
            $showCheckboxColumn = false;
        }

        // Tạo object search giống Voyager
        $search = (object)[
            'value'  => $searchValue,
            'key'    => $searchKey,
            'filter' => $searchFilter,
        ];

        // sortableColumns fallback
        $sortableColumns = [];
        if (!empty($dataType->browseRows)) {
            foreach ($dataType->browseRows as $row) {
                if (isset($row->details) && is_array($row->details) && ($row->details['sortable'] ?? false)) {
                    $sortableColumns[] = $row->field;
                }
            }
        }

        $orderBy = $orderColumn ?? 'id';
        $orderDirection = $orderDirection ?? 'desc';

        // map sang tên biến khác mà view có thể dùng
        $sortOrder = $orderDirection;   
        $sortColumn = $orderBy;      

        $showSoftDeleted = false;
        if ($usesSoftDeletes) {
            $showSoftDeleted = (bool) $request->get('showSoftDeleted', false);
        }

        return Voyager::view("voyager::bread.browse", compact(
            'dataType',
            'dataTypeContent',
            'isServerSide',
            'usesSoftDeletes',
            'orderColumn',
            'orderDirection',
            'orderBy',
            'isModelTranslatable',
            'actions',
            'showCheckboxColumn',
            'search',
            'searchNames',
            'defaultSearchKey',
            'searchKey',
            'defaultSearchFilter',
            'sortableColumns',
            'sortOrder',
            'sortColumn',
            'showSoftDeleted',
        ));
    }
}
