<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(): View
    {
        $products = $this->getProductQuery()->paginate(12);
        $categories = $this->getActiveCategories();

        return view('shop.index', compact('products', 'categories'));
    }

    public function category(Category $category): View
    {
        $categoryIds = $this->getCategoryIdsWithChildren($category);

        $products = $this->getProductQuery()
            ->whereIn('category_id', $categoryIds)
            ->paginate(12);

        $categories = $this->getActiveCategories();

        return view('shop.index', compact('products', 'categories', 'category'));
    }

    public function newProducts(): View
    {
        $products = $this->getProductQuery()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->paginate(12);

        $categories = $this->getActiveCategories();
        $pageTitle = 'Новое поступление';

        return view('shop.index', compact('products', 'categories', 'pageTitle'));
    }

    public function show(string $slug): View
    {
        $product = Product::with('category')->where('slug', $slug)->firstOrFail();
        return view('shop.product', compact('product'));
    }

    public function search(Request $request): View|RedirectResponse
    {
        $query = $request->input('q');

        if (empty($query)) {
            return redirect()->route('home');
        }

        $products = $this->getProductQuery()
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%")
                    ->orWhere('slug', 'LIKE', "%{$query}%")
                    ->orWhereHas('category', function ($cat) use ($query) {
                        $cat->where('name', 'LIKE', "%{$query}%");
                    });
            })
            ->paginate(12)
            ->appends(['q' => $query]);

        $categories = $this->getActiveCategories();
        $pageTitle = "Результаты поиска: «{$query}»";

        return view('shop.index', compact('products', 'categories', 'pageTitle'));
    }

    private function getProductQuery()
    {
        return Product::with('category')->where('stock', '>', 0);
    }

    private function getCategoryIdsWithChildren(Category $category): array
    {
        $childIds = $category->children->pluck('id')->toArray();
        return array_merge([$category->id], $childIds);
    }

    private function getActiveCategories()
    {
        return Category::with('children')
            ->whereNull('parent_id')
            ->get()
            ->filter(fn($cat) => $cat->hasProductsRecursive());
    }
}
