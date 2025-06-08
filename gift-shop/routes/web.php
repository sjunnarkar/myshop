<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CustomizationTemplateController;
use App\Http\Controllers\Shop\ProductController as ShopProductController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\PageController;
use App\Http\Requests\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Profile\OrderController as ProfileOrderController;

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

// Authentication Routes
Auth::routes(['verify' => true]);

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/')->with('status', 'Your email has been verified!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Frontend Routes
Route::get('/', [ShopProductController::class, 'index'])->name('home');
Route::get('/shop', [ShopProductController::class, 'index'])->name('shop');
Route::get('/products', [ShopProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ShopProductController::class, 'show'])->name('products.show');
Route::get('/categories/{category:slug}', [ShopProductController::class, 'category'])->name('categories.show');

// Legal Pages
Route::get('/terms', function () {
    return view('terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

// Cart Routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{product}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{product}', [CartController::class, 'remove'])->name('remove');
    Route::post('/coupon', [CartController::class, 'applyCoupon'])->name('coupon');
});

// Checkout Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
});

// Product Reviews Routes
Route::prefix('products')->name('products.')->group(function () {
    Route::get('{product:slug}/reviews', [ProductReviewController::class, 'index'])->name('reviews.index');
    Route::post('{product:slug}/reviews', [ProductReviewController::class, 'store'])->name('reviews.store');
    Route::patch('{product:slug}/reviews/{review}', [ProductReviewController::class, 'update'])->name('reviews.update');
    Route::delete('{product:slug}/reviews/{review}', [ProductReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Analytics Routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [Admin\AnalyticsController::class, 'index'])->name('index');
        Route::get('/sales', [Admin\AnalyticsController::class, 'sales'])->name('sales');
        Route::get('/customers', [Admin\AnalyticsController::class, 'customers'])->name('customers');
        Route::get('/products', [Admin\AnalyticsController::class, 'products'])->name('products');
        Route::get('/marketing', [Admin\AnalyticsController::class, 'marketing'])->name('marketing');
        Route::get('/operations', [Admin\AnalyticsController::class, 'operations'])->name('operations');
        
        // API endpoints for chart data
        Route::get('/api/sales-trends', [Admin\AnalyticsController::class, 'salesTrends'])->name('api.sales-trends');
        Route::get('/api/category-performance', [Admin\AnalyticsController::class, 'categoryPerformance'])->name('api.category-performance');
        Route::get('/api/customer-metrics', [Admin\AnalyticsController::class, 'customerMetrics'])->name('api.customer-metrics');
        Route::get('/api/product-performance', [Admin\AnalyticsController::class, 'productPerformance'])->name('api.product-performance');
    });

    Route::resources([
        'categories' => CategoryController::class,
        'products' => ProductController::class,
        'orders' => OrderController::class,
        'customization-templates' => CustomizationTemplateController::class,
    ]);

    // Reviews management routes
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [Admin\ProductReviewController::class, 'index'])->name('index');
        Route::get('{review}', [Admin\ProductReviewController::class, 'show'])->name('show');
        Route::post('{review}/approve', [Admin\ProductReviewController::class, 'approve'])->name('approve');
        Route::post('{review}/reject', [Admin\ProductReviewController::class, 'reject'])->name('reject');
        Route::delete('{review}', [Admin\ProductReviewController::class, 'destroy'])->name('destroy');
        Route::post('bulk/approve', [Admin\ProductReviewController::class, 'bulkApprove'])->name('bulk.approve');
        Route::post('bulk/reject', [Admin\ProductReviewController::class, 'bulkReject'])->name('bulk.reject');
        Route::post('bulk/delete', [Admin\ProductReviewController::class, 'bulkDestroy'])->name('bulk.destroy');
    });

    // Additional admin routes
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status.update');

    // Inventory management routes
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/alerts', [InventoryController::class, 'alerts'])->name('inventory.alerts');
    Route::get('/inventory/export', [InventoryController::class, 'export'])->name('inventory.export');
    Route::get('/inventory/{inventory}', [InventoryController::class, 'show'])->name('inventory.show');
    Route::post('/inventory/{inventory}/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
    Route::post('/inventory/batch-update', [InventoryController::class, 'batchUpdate'])->name('inventory.batch-update');

    // CMS Pages Management
    Route::resource('pages', PageController::class);
    Route::post('pages/update-order', [PageController::class, 'updateOrder'])->name('pages.update-order');
});

// Profile Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    
    // Address Routes
    Route::get('/profile/addresses/create', [ProfileController::class, 'createAddress'])->name('profile.addresses.create');
    Route::post('/profile/addresses', [ProfileController::class, 'storeAddress'])->name('profile.addresses.store');
    Route::get('/profile/addresses/{address}/edit', [ProfileController::class, 'editAddress'])->name('profile.addresses.edit');
    Route::patch('/profile/addresses/{address}', [ProfileController::class, 'updateAddress'])->name('profile.addresses.update');
    Route::delete('/profile/addresses/{address}', [ProfileController::class, 'destroyAddress'])->name('profile.addresses.destroy');
    Route::post('/profile/addresses/preferences', [ProfileController::class, 'updateAddressPreferences'])->name('profile.addresses.update-preferences');
    
    // Wishlist Routes
    Route::post('/wishlist', [ProfileController::class, 'addToWishlist'])->name('wishlist.add');
    Route::delete('/wishlist', [ProfileController::class, 'removeFromWishlist'])->name('wishlist.remove');

    // Order Management Routes
    Route::get('/profile/orders', [ProfileOrderController::class, 'index'])->name('profile.orders.index');
    Route::get('/profile/orders/{order}', [ProfileOrderController::class, 'show'])->name('profile.orders.show');
    Route::get('/profile/orders/{order}/details', [ProfileOrderController::class, 'details'])->name('profile.orders.details');
    Route::post('/profile/orders/{order}/cancel', [ProfileOrderController::class, 'cancel'])->name('profile.orders.cancel');

    // Payment Routes
    Route::post('/payment/razorpay/order/{order}', [PaymentController::class, 'createRazorpayOrder']);
    Route::post('/payment/razorpay/success/{order}', [PaymentController::class, 'handleRazorpaySuccess']);
    Route::post('/payment/razorpay/failure/{order}', [PaymentController::class, 'handleRazorpayFailure']);
    Route::post('/payment/paypal/{order}', [PaymentController::class, 'processPayPal']);
});

// Newsletter Routes
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/verify/{token}', [NewsletterController::class, 'verify'])->name('newsletter.verify');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::get('/newsletter/preferences/{subscriber}', [NewsletterController::class, 'preferences'])->name('newsletter.preferences');
Route::post('/newsletter/preferences/{subscriber}', [NewsletterController::class, 'updatePreferences'])->name('newsletter.preferences.update');

// Wishlist Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/wishlists', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::get('/wishlists/create', [WishlistController::class, 'create'])->name('wishlist.create');
    Route::post('/wishlists', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::get('/wishlists/{wishlist}/edit', [WishlistController::class, 'edit'])->name('wishlist.edit');
    Route::put('/wishlists/{wishlist}', [WishlistController::class, 'update'])->name('wishlist.update');
    Route::delete('/wishlists/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::post('/wishlists/add-product', [WishlistController::class, 'addProduct'])->name('wishlist.add-product');
    Route::delete('/wishlists/{wishlist}/products/{product}', [WishlistController::class, 'removeProduct'])->name('wishlist.remove-product');
    Route::post('/wishlists/{wishlist}/add-all-to-cart', [WishlistController::class, 'addAllToCart'])->name('wishlist.add-all-to-cart');
    Route::post('/wishlists/{wishlist}/share', [WishlistController::class, 'generateShareToken'])->name('wishlist.share');
});

// Public wishlist routes (no auth required)
Route::get('/wishlists/{wishlist}', [WishlistController::class, 'show'])->name('wishlist.show');

// CMS Pages Routes
Route::get('/page/{slug}', [PageController::class, 'show'])->name('pages.show');
