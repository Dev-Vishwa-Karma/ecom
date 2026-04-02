<?php

namespace App\Http\Controllers;

use App\Services\ProductImageService;

class ProductImageController extends Controller
{
    protected $service;

    public function __construct(ProductImageService $service)
    {
        $this->service = $service;
    }

    public function deleteImage($public_id)
    {
        $result = $this->service->deleteImage($public_id);

        return response()->json($result);
    }
}