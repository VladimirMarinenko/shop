<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| Публичные маршруты
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    $products = Product::with('category')
        ->where('stock', '>', 0) // показываем только товары в наличии
        ->paginate(12);
    return view('shop.index', compact('products'));
})->name('home');

// Главная страница с товарами и категориями
Route::get('/', function () {
    $products = Product::with('category')->where('stock', '>', 0)->paginate(12);

    // Загружаем категории с подсчётом товаров во всей ветке
    $allCategories = Category::with('children')->whereNull('parent_id')->get();

    // Подсчитываем общее количество товаров для каждой категории
    $categories = $allCategories->filter(function ($category) {
        $total = $category->products()->count();
        foreach ($category->children as $child) {
            $total += $child->products()->count();
        }
        return $total > 0;
    });

    return view('shop.index', compact('products', 'categories'));
})->name('home');


Route::get('/category/{id}', function ($id) {
    $category = Category::with('children.products')->findOrFail($id);

    $childIds = $category->children->pluck('id')->toArray();
    $allIds = array_merge([$id], $childIds);

    $products = Product::whereIn('category_id', $allIds)->where('stock', '>', 0)->paginate(12);

    $allCategories = Category::with('children.products')->whereNull('parent_id')->get();
    $categories = $allCategories->filter(function ($cat) {
        return $cat->hasProductsRecursive();
    });

    return view('shop.index', compact('products', 'categories', 'category'));
})->name('category.products');



// Страница товара
Route::get('/product/{slug}', function ($slug) {
    $product = Product::where('slug', $slug)->firstOrFail();
    return view('shop.product', compact('product'));
})->name('product.show');

// Стандартный дашборд
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Аутентифицированные пользователи
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Корзина
    Route::prefix('/cart')->name('cart.')->group(function () {
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

/*
|--------------------------------------------------------------------------
| Административные маршруты
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('/admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('/categories', CategoryController::class);
    Route::resource('/products', ProductController::class);
    Route::resource('/orders', OrderController::class)->only(['index', 'show', 'update']);
});

/*
|--------------------------------------------------------------------------
| Тестовые маршруты
|--------------------------------------------------------------------------
*/
Route::get('/test-category', function () {
    $category = \App\Models\Category::create(['name' => 'Животные']);
    return $category;
});

require __DIR__.'/auth.php';
