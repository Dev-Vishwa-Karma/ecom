<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    protected $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->index($request);
        return view('admin.my-products', $data);
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(StoreProductRequest $request)
    {
        $result = $this->service->store($request);
        return response()->json($result);
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $result = $this->service->update($request, $product);
        return response()->json($result);
    }

    public function destroy(Product $product)
    {
        $this->service->destroy($product);
        return back()->with('success', 'Product Deleted');
    }

    public function productDetails(Product $product)
    {
        $data = $this->service->productDetails($product);
        return view('product-details', $data);
    }
}