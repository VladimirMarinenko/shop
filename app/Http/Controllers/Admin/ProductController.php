<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    public function index(): View
    {
        $products = $this->productService->getPaginatedProducts(10);
        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = $this->productService->getCategoriesList();
        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->productService->createProduct(
            $request->validated(),
            $request->file('image')
        );

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Товар создан!');
    }

    public function edit(int $id): View
    {
        $product = $this->productService->findProduct($id);
        $categories = $this->productService->getCategoriesList();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, int $id): RedirectResponse
    {
        $product = $this->productService->findProduct($id);

        $this->productService->updateProduct(
            $product,
            $request->validated(),
            $request->file('image')
        );

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Товар обновлён!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $product = $this->productService->findProduct($id);
        $this->productService->deleteProduct($product);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Товар удалён!');
    }
}
