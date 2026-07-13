<?php


declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(int $id): View
    {
        $category = Category::with('children.products')->findOrFail($id);

        $childIds = $category->children->pluck('id')->toArray();
        $allIds = array_merge([$id], $childIds);

        $products = Product::whereIn('category_id', $allIds)
            ->where('stock', '>', 0)
            ->paginate(12);

        $allCategories = Category::with('children.products')
            ->whereNull('parent_id')
            ->get();

        $categories = $allCategories->filter(function ($cat) {
            return $cat->hasProductsRecursive();
        });

        return view('shop.index', compact('products', 'categories', 'category'));
    }
}
