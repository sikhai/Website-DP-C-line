<?php 

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Services\ProductService;

class PerformanceController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    // Hàm lấy thuộc tính
    public function getAttributes(): array
    {
        // Logic để lấy thuộc tính
        $attributes = $this->productService->getAttributesWithProductCount();
        return $attributes;
    }

    // API để kiểm tra thời gian thực hiện hàm getAttributes()
    public function getAttributesPerformance(): JsonResponse
    {
        $startTime = microtime(true);

        $result_attributes = $this->getAttributes();

        $executionTime = microtime(true) - $startTime;

        return response()->json([
            'execution_time' => number_format($executionTime, 6) . ' seconds',
            'data' => $result_attributes
        ]);
    }

    // API để kiểm tra thời gian thực hiện hàm getProductsByAttribute()
    public function getProductsByAttributePerformance(): JsonResponse
    {
        $startTime = microtime(true);

        $result_attributes = $this->getAttributes();
        $result_attributes = $this->productService->getProductsByAttribute($result_attributes);

        $executionTime = microtime(true) - $startTime;

        return response()->json([
            'execution_time' => number_format($executionTime, 6) . ' seconds',
            'data' => $result_attributes
        ]);
    }
}
