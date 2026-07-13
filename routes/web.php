<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Shop\CategoryController as ShopCategoryController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController as ShopProductController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Публичные маршруты
Route::get('/', [ShopController::class, 'index'])->name('home');
Route::get('/category/{category}', [ShopController::class, 'category'])->name('category.products');
Route::get('/new-products', [ShopController::class, 'newProducts'])->name('products.new');
Route::get('/product/{slug}', [ShopController::class, 'show'])->name('product.show');
Route::get('/search', [ShopController::class, 'search'])->name('shop.search');

// Стандартный дашборд (для пользователей)
Route::get('/dashboard', function () {
    return view('dashboard');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Маршруты для аутентифицированных пользователей
Route::middleware(['auth'])->group(function (): void {
    // Корзина
    Route::prefix('/cart')
        ->name('cart.')
        ->group(function (): void {
            Route::get('/', [CartController::class, 'index'])->name('index');
            Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
            Route::delete('/remove/{product}', [CartController::class, 'remove'])->name('remove');
            Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
            Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
            Route::post('/order', [CartController::class, 'placeOrder'])->name('placeOrder');
        });

    // Профиль
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Административные маршруты
Route::middleware(['auth', 'admin'])
    ->prefix('/admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('/categories', CategoryController::class);
        Route::resource('/products', ProductController::class);
        Route::resource('/orders', OrderController::class)->only(['index', 'show', 'update']);
    });

// Тестовые маршруты (можно удалить в продакшене)
if (app()->environment('local')) {
    Route::get('/test-category', function () {
        $category = \App\Models\Category::create(['name' => 'Животные']);

        return $category;
    });
}

// Подключение маршрутов аутентификации Breeze
require __DIR__ . '/auth.php';
