<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockRequest;
use App\Http\Requests\UpdateVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\ProductVariantService;

class ProductVariantController extends Controller
{
    protected $service;

    public function __construct(ProductVariantService $service)
    {
        $this->service = $service;
    }

    public function addStockForm(Product $product)
    {
        $variants = $product->variants;
        return view('admin.product-stock', compact('product','variants'));
    }

    public function storeStock(StoreStockRequest $request)
    {
        $this->service->storeStock($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully'
        ]);
    }

    public function updateVariant(UpdateVariantRequest $request, ProductVariant $variant)
    {
        $this->service->updateVariant($request, $variant);

        return response()->json([
            'success' => true,
            'message' => 'Variant updated'
        ]);
    }

    public function getVariants(Product $product)
    {
        return response()->json(
            $this->service->getVariants($product)
        );
    }
    public function deleteVariant(ProductVariant $variant)
    {
        $this->service->deleteVariant($variant);

        return response()->json([
            'success' => true,
            'message' => 'Variant deleted successfully'
        ]);
    }
}