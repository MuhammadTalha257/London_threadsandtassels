<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StorefrontController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'index'])->name('store.index');
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/help', [HelpController::class, 'show'])->name('help');

// A tiny, dependency-free XML sitemap — kept as a closure since it's just three
// route groups (home, shop, product pages) and doesn't warrant its own controller.
Route::get('/sitemap.xml', function () {
    $urls = collect([
        ['loc' => url('/'), 'priority' => '1.0'],
        ['loc' => url('/shop'), 'priority' => '0.9'],
        ['loc' => url('/help'), 'priority' => '0.5'],
    ]);

    try {
        Product::query()->available()->get(['slug', 'updated_at'])->each(function ($product) use ($urls) {
            $urls->push(['loc' => url('/suits/'.$product->slug), 'priority' => '0.8', 'lastmod' => $product->updated_at?->toAtomString()]);
        });
    } catch (\Throwable $e) {
        // Products table not migrated yet — ship the static pages only.
    }

    return response()
        ->view('sitemap', ['urls' => $urls])
        ->header('Content-Type', 'application/xml');
})->name('sitemap');
Route::get('/suits/{product:slug}', [StorefrontController::class, 'show'])->name('products.show');
Route::get('/bag', [CartController::class, 'index'])->name('cart.index');
Route::post('/bag/{product}', [CartController::class, 'store'])->name('cart.store');
Route::patch('/bag/{key}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/bag/{key}', [CartController::class, 'destroy'])->name('cart.destroy');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:6,1');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'registerStore']);
});
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');
Route::middleware('auth')->group(function (): void {
    Route::get('/account', [AccountController::class, 'show'])->name('account');
    Route::patch('/account', [AccountController::class, 'update'])->name('account.update');
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/orders/{order}', [CheckoutController::class, 'show'])->name('orders.show');
});
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function (): void {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::resource('products', AdminProductController::class);
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::resource('promotions', PromotionController::class)->except('show');
    Route::resource('campaigns', CampaignController::class)->except('show');
    Route::resource('categories', CategoryController::class)->except('show');
});
