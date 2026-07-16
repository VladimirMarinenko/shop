<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function getPaginatedProducts(int $perPage = 10)
    {
        return Product::with('category')->paginate($perPage);
    }

    public function getCategoriesList(): array
    {
        return Category::pluck('name', 'id')->toArray();
    }

    public function findProduct(int $id): Product
    {
        return Product::findOrFail($id);
    }

    public function createProduct(array $data, $image = null): Product
    {
        if ($image) {
            $path = $image->store('products', 'public');
            $data['image'] = $path;
        }

        return Product::create($data);
    }

    public function updateProduct(Product $product, array $data, $image = null): Product
    {
        if ($image) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $image->store('products', 'public');
            $data['image'] = $path;
        }

        $product->update($data);
        return $product;
    }

    public function deleteProduct(Product $product): void
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
    }

    public function getFilteredProducts(?string $search = null, int $perPage = 25)
    {
        $query = Product::with('category');

        if (!empty($search)) {
            $search = trim($search);
            // Ищем по ID (точное совпадение) или по названию (частичное)
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }
}
