<?php


declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')
            ->where('stock', '>', 0)
            ->paginate(12);

        $allCategories = Category::with('children')
            ->whereNull('parent_id')
            ->get();

        $categories = $allCategories->filter(function ($category) {
            return $category->hasProductsRecursive();
        });

        return view('shop.index', compact('products', 'categories'));
    }
}
