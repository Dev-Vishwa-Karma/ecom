<?php

namespace app\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProductService{
    public function index ($request){

    $query = Product::with(['images','user']);

    if ($request->search){
        $query->where('name','like', '%', $request->search . '%' );

    }

    $products = $query->latest()->paginate(8);

    $productsDataJson = json_encode(
        $products->keyBy('id')->map(function($p){

        return[
        'id' => $p->id,
        'name'=> $p->name,
        'price' => $p->price,
        'description' => $p->description ?? '',
        'images'  => $p->images->pluck('images')->toArray(),
                ];

        })->toArray()
    );
    return compact('products', 'productsDataJson');
    }

    public function store($request){
        $product = Product::create([
            'user_id'  => auth()->id(),
            'name' => $request->name,
            'description' => $request->description ?? null,

        ]);

        foreach($request->file('images') as $file){
            $uploaded = Cloudinary::upload(
                $file->getRealPath(),
                ['folder' => 'product']
            );

            $product->images()->create([
                'image'=> $uploaded->getSecurePath(),
                'public_id' => $uploaded->getPublicId() ,
                
                ]);
        }

        foreach ($request->variants as $variantData){
            ProductVariant::create([
                'product_id' => $product->id,
                 'color' => $variantData['color']??null,
                 'size' => $variantData['size']??null,
                 'gender'  => $variantData['gender']??null,
                 'price'   => $variantData['price'],
                 'quantity' => $variantData['quantity'],
                ]);

        }
        return[
            'success' => true,
            'message' => 'Product Created with variants'
        ];
    }

    public function update ($request , $product){

    abort_unless($product->user_id  === auth () -> id (), 403);
    $product->update([          
        'name' => $request->name,
        'description' => $request->description ?? $product -> description,
        ]);

        if($request->hasFile('images')){
            foreach($request->file('images') as $file){
            $uploaded = Cloudinary::upload(
                $file->getRealPath(),
                ['folder'=>'products']
            );
            $product->images()->create([
                'image'=> $uploaded->getSecurePath(),
                'public_id' => $uploaded->getPublicId(),
            ]);
            }
        }

        return[
            'success'=>true,
            'message'=> 'Product updated successfully',
        ];


    }

    public function destroy ($product)
    {
        abort_unless($product->user_id === auth()->id(),403);

        foreach ($product->images as $img){
            Cloudinary::destroy($img->public_id);
        }

        $product->delete();
        return true;
    }

    public function productDetails($product)
    {
        $product->load (['images','variants','user']);

        $products = Product::with(['images', 'variants','user'])
        ->where('id','!=', $product->id)
        ->latest()
        ->take(8)
        ->get();

        return compact('product','products');

    }

}