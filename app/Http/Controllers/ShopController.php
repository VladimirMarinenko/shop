<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Requests\SearchRequest;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('search');
        $categoryId = $request->input('category');
        $page = $request->input('page', 1);

        $productsQuery = Product::with('category')->where('stock', '>', 0);

        if (!empty($query)) {
            $productsQuery->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%")
                    ->orWhere('slug', 'LIKE', "%{$query}%")
                    ->orWhereHas('category', function ($cat) use ($query) {
                        $cat->where('name', 'LIKE', "%{$query}%");
                    });
            });
        }

        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }

        $products = $productsQuery->paginate(12, ['*'], 'page', $page);
        $categories = $this->getActiveCategories();
        if ($request->ajax()) {
            return view('shop.partials.products', compact('products'));
        }

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
        $similar = collect();
        if ($product->category_id) {
            $similar = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->limit(4)
                ->get();
        }

        $recommended = $similar;
        $title = 'Похожие товары';

        if ($similar->count() < 4) {
            $excludeIds = $similar->pluck('id')->push($product->id)->toArray();
            $random = Product::whereNotIn('id', $excludeIds)
                ->inRandomOrder()
                ->limit(4 - $similar->count())
                ->get();
            $recommended = $similar->merge($random);
        }

        if ($recommended->isEmpty()) {
            $recommended = null;
        }
        if ($recommended) {
            $title = $similar->isNotEmpty() ? 'Похожие товары' : 'Возможно, вас заинтересует';
        }

        return view('shop.product', compact('product', 'recommended', 'title'));
    }

    public function search(SearchRequest $request): View|RedirectResponse
    {
        if (!$request->hasValidQuery()) {
            return redirect()->route('home');
        }

        $escapedQuery = $request->getEscapedQuery();

        $products = $this->getProductQuery()
            ->where(function ($q) use ($escapedQuery) {
                $q->where('name', 'LIKE', "%{$escapedQuery}%")
                    ->orWhere('description', 'LIKE', "%{$escapedQuery}%")
                    ->orWhere('slug', 'LIKE', "%{$escapedQuery}%")
                    ->orWhereHas('category', function ($cat) use ($escapedQuery) {
                        $cat->where('name', 'LIKE', "%{$escapedQuery}%");
                    });
            })
            ->paginate(12)
            ->appends(['q' => $request->input('q')]); // исходный запрос для отображения

        $categories = $this->getActiveCategories();
        $pageTitle = "Результаты поиска: «{$request->input('q')}»";

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

    public function loadProducts(Request $request)
    {
        $query = $request->input('search');
        $categoryId = $request->input('category');
        $page = $request->input('page', 1);

        $productsQuery = Product::with('category')->where('stock', '>', 0);

        if (!empty($query)) {
            $productsQuery->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%")
                    ->orWhere('slug', 'LIKE', "%{$query}%")
                    ->orWhereHas('category', function ($cat) use ($query) {
                        $cat->where('name', 'LIKE', "%{$query}%");
                    });
            });
        }

        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }

        $products = $productsQuery->paginate(12, ['*'], 'page', $page);
        return view('shop.partials.products', compact('products'))->render();
    }
}
