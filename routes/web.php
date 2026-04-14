<?php

use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminProductViewContoller;
use Illuminate\Support\Facades\Route;
  use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MyCartController;
use App\Http\Controllers\NotifyMeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductRatingController;
use App\Http\Controllers\StripeConnectController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\UserImageController;
use Intervention\Image\ImageManager;

    Route::get('/', [DashboardRedirectController::class, 'redirect']);

    Route::post('/login', [AuthController::class,'apiLogin']);
    Route::post('/register', [AuthController::class,'apiRegister']);
    Route::middleware('auth:api')->group(function(){

        Route::get('/profile', function(){
            return auth()->user();
        });

    });
    Route::post('/save-fcm', [App\Http\Controllers\FcmController::class, 'store'])
    ->middleware('auth');

    Route::middleware('guest')->group(function () {

    Route::get('/register', [AuthController::class,'showRegister'])->name('register');
    Route::post('/register', [AuthController::class,'register']);

    Route::get('/login', [AuthController::class,'showLogin'])->name('login');

    // uncomment this if you want to use JWT token based login for API
    // Route::post('/login', [AuthController::class,'apiLogin']);

    Route::post('/login', [AuthController::class,'login']);
    });
        Route::get('/auth/{provider}', [AuthController::class, 'redirectToProvider'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback'])->name('social.callback');


    Route::middleware(['auth','role:super_admin'])->group(function () {
    Route::get('/super/dashboard', function () {

        $users = \App\Models\User::paginate(10);

        return view('super.dashboard', compact('users'));
    })->name('super.dashboard');



    Route::get('/super/profile', function () {
        return view('super.profile');
    })->name('super.profile');

    Route::get('/super/admins', [AdminController::class,'adminList'])
        ->name('super.admin.list');

    Route::get('/super/admin/create', [AdminController::class,'showAdminCreate'])
        ->name('super.admin.create');

    Route::post('/super/admin/store', [AdminController::class,'storeAdmin'])
        ->name('super.admin.store');

    Route::get('/super/admin/view/{id}', [AdminController::class,'viewAdmin'])
        ->name('super.admin.view');

    Route::get('/super/admin/edit/{id}', [AdminController::class,'editAdmin'])
        ->name('super.admin.edit');

    Route::post('/super/admin/update/{id}', [AdminController::class,'updateAdmin'])
        ->name('super.admin.update');
    Route::post('/admin/{id}/deactivate', [AdminController::class, 'deactivateAdmin'])->name('super.admin.deactivate');

    Route::post('/super/admin/delete/{id}', [AdminController::class,'deleteAdmin'])
        ->name('super.admin.delete');

    Route::get('/customer/list', [CustomerController::class,'customerList'])
        ->name('customer.list');

    Route::get('/all-products', [AdminProductViewContoller::class, 'allProducts'])->name('all-products');

    Route::get('/buy-now/{product}', [OrderController::class, 'showBuyForm'])->name('buy.now');
    Route::post('/buy-now/{product}', [OrderController::class, 'placeOrder'])->name('order.place');

});

        Route::middleware(['auth', 'role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

        // Dashboard & Profile
        // Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
        Route::get('/dashboard', [NotifyMeController::class, 'adminDemandReport'])
        ->name('dashboard')
        ->middleware(['auth', 'role:admin']);

        Route::get('/stripe/connect', [StripeConnectController::class, 'createAccount'])->name('stripe.connect');

    Route::get('/stripe/return', [StripeConnectController::class, 'return'])->name('stripe.return');

    Route::get('/stripe/retry', [StripeConnectController::class, 'retry'])->name('stripe.retry');

    Route::get('/stripe/status', [StripeConnectController::class, 'status'])->name('stripe.status');


        Route::get('/profile',   fn() => view('admin.profile'))->name('profile');

        Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
        Route::get('/my-wishlist', [WishlistController::class, 'myWishlist'])->name('my-wishlist');       
        Route::get('/my-products', [AdminProductViewContoller::class, 'myProducts'])->name('my-products');
        Route::match(['put', 'patch'], '/my-products/update/{product}', [ProductController::class, 'update'])
        ->name('products.update');
        Route::post('/my-products/store', [ProductController::class, 'store'])->name('products.store');
        Route::post('/my-products/update/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/my-products/destroy/{product}', [ProductController::class, 'destroy'])
        ->name('products.destroy');    
        Route::get('/all-products', [AdminProductViewContoller::class, 'allProducts'])->name('all-products');
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');
        Route::post('/orders/update-status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::get('/products/{product}/variants',[ProductVariantController::class,'getVariants']);

        Route::get('products/{product}/images', fn(\App\Models\Product $product) => $product->images)->name('products.images');
        
        Route::delete('product-images/{productImage}', [ProductController::class, 'destroy']);
        
        Route::post('/products/variant/{variant}', [ProductVariantController::class, 'updateVariant'])->name('products.variant.update');
        
        Route::delete('/products/images/{publicId}', [ProductImageController::class, 'deleteImage'])->name('products.images.destroy');
        

        Route::get('/products/{product}/stock', [ProductVariantController::class,'addStockForm'])->name('products.stock.form');
        
        Route::post('/products/stock/store', [ProductVariantController::class,'storeStock'])->name('products.stock.store');
        Route::delete('/products/variant/{variant}', [ProductVariantController::class, 'deleteVariant'])->name('products.variant.delete');

        Route::put('/orders/{id}/quantity', [CustomerOrderController::class,'updateQuantity'])->name('orders.updateQuantity');

        Route::get('/notify-details/{sellerId}', [NotifyMeController::class, 'getNotifyDetails']);

        });

        Route::middleware(['auth','role:customer'])->group(function () {

        // Route::get('/customer/dashboard', function () {
        //     return view('customer.dashboard');
        // })->name('customer.dashboard');
        Route::get('/profile',   fn() => view('customer.profile'))->name('profile');

        Route::get('/customer/dashboard', [AdminProductViewContoller::class, 'allProducts'])->name('customer.dashboard');
    
        });

        // Allow all authenticated users (admin + customer)
        Route::middleware('auth')->group(function () {
            Route::get('/buy-now/{product}', [OrderController::class, 'showBuyForm'])->name('buy.now');
            Route::post('/buy-now/{product}', [OrderController::class, 'placeOrder'])->name('order.place');
            Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

            Route::get('/my-wishlist', [WishlistController::class, 'myWishlist'])->name('my-wishlist');
            Route::get('/my-orders', [CustomerOrderController::class, 'index'])->name('orders');
            
            Route::get('/orders/{order}/invoice', [InvoiceController::class, 'invoice'])->name('invoice');
            Route::get('/invoice/{order}/download', [InvoiceController::class, 'downloadInvoice'])->name('invoice.download');
            
            // Route::get('orders/{order}/invoice', [InvoiceController::class, 'invoice']);
            Route::post('/notify-me',[NotifyMeController::class,'store'])->name('notify.store');
            Route::get('/product/{product}', [ProductController::class, 'productDetails'])->name('product.details');
            Route::post('/product/rate', [ProductRatingController::class, 'store'])->name('product.rate.store');
            Route::post('/upload-image', [ProductRatingController::class, 'uploadImage'])->name('image.upload');
            Route::match(['get','post'],'/generate-review-image', [ProductRatingController::class, 'generateReviewImage'])
            ->name('generate.review.image');
            Route::get('/share-review/{filename}', [ProductRatingController::class, 'shareReview'])->name('share.review');
  
            Route::get('/gd-check', function () {
                return [
                    'gd_loaded' => extension_loaded('gd'),
                    'gd_info' => function_exists('gd_info') ? gd_info() : 'missing',
                ];
            });

            Route::post('/payment/{product}', [PaymentController::class, 'process'])->name('payment.process');
            Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
            Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
           Route::get('/my-cart', [MyCartController::class, 'myCart'])->name('my_cart');
           Route::post('/clear-cart', [MyCartController::class, 'clear'])->name('cart.clear');
               Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/wishlist/variants/bulk', [WishlistController::class, 'bulk'])
    ->name('wishlist.variant.bulk');

    Route::get('/my-wishlist', [WishlistController::class, 'myWishlist'])->name('my-wishlist','my_cart');


            Route::post('/logout', [AuthController::class,'logout'])->name('logout');
        });


        Route::middleware('auth')->prefix('user-images')->name('user.images.')->group(function () {

        Route::post('/store', [UserImageController::class, 'store'])
            ->name('store');

        Route::post('/update/{userImage}', [UserImageController::class, 'update'])
            ->name('update');

        Route::delete('/delete/{userImage}', [UserImageController::class, 'destroy'])
            ->name('delete');

        });

    
    

