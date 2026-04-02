<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductViewService;

class AdminProductViewContoller extends Controller
{
    protected $service;

    public function __construct(ProductViewService $service)
    {
        $this->service = $service;
    }

    public function myProducts(Request $request)
    {
        $data = $this->service->getMyProducts($request);

        return view('admin.my-products', $data);
    }

    public function allProducts(Request $request)
    {
        $data = $this->service->getAllProducts($request);

        return view('all-products', $data);
    }
}