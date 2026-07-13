<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * Получить список товаров с пагинацией.
     */
    public function getPaginatedProducts(int $perPage = 10)
    {
        return Product::with('category')->paginate($perPage);
    }

    /**
     * Получить товар по ID.
     */
    public function getProductById(int $id): Product
    {
        return Product::findOrFail($id);
    }

    /**
     * Получить список категорий для выпадающего списка.
     */
    public function getCategoriesList(): array
    {
        return Category::pluck('name', 'id')->toArray();
    }

    /**
     * Создать новый товар с загрузкой изображения.
     */
    public function createProduct(array $data, ?UploadedFile $image = null): Product
    {
        if ($image) {
            $data['image'] = $image->store('products', 'public');
        }

        return Product::create($data);
    }

    /**
     * Обновить товар с возможной заменой изображения.
     */
    public function updateProduct(Product $product, array $data, ?UploadedFile $image = null): Product
    {
        if ($image) {
            // Удаляем старое изображение, если оно есть
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $image->store('products', 'public');
        }

        $product->update($data);
        return $product;
    }

    /**
     * Удалить товар и его изображение.
     */
    public function deleteProduct(Product $product): void
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
    }
}
