<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function getCart(): array
    {
        return session()->get('cart', []);
    }

    public function addProduct(int $productId): array
    {
        $product = Product::findOrFail($productId);
        $cart = $this->getCart();

        if ($product->stock <= 0) {
            return ['success' => false, 'message' => 'Товара нет в наличии!'];
        }

        if (isset($cart[$productId]) && $cart[$productId] >= $product->stock) {
            return ['success' => false, 'message' => 'Недостаточно товара на складе!'];
        }

        $cart[$productId] = isset($cart[$productId]) ? $cart[$productId] + 1 : 1;
        session()->put('cart', $cart);

        return ['success' => true, 'message' => 'Товар добавлен в корзину!'];
    }

    public function removeProduct(int $productId): void
    {
        $cart = $this->getCart();
        if (isset($cart[$productId])) {
            if ($cart[$productId] > 1) {
                $cart[$productId] -= 1;
            } else {
                unset($cart[$productId]);
            }
            session()->put('cart', $cart);
        }
    }

    public function clearCart(): void
    {
        session()->forget('cart');
    }

    public function getProductsWithCart(): array
    {
        $cart = $this->getCart();
        if (empty($cart)) {
            return ['products' => collect(), 'cart' => [], 'totalPrice' => 0];
        }

        $products = Product::whereIn('id', array_keys($cart))->get();
        $totalPrice = 0;
        foreach ($products as $product) {
            $totalPrice += $product->price * $cart[$product->id];
        }

        return [
            'products' => $products,
            'cart' => $cart,
            'totalPrice' => $totalPrice,
        ];
    }

    public function placeOrder(array $data): Order
    {
        $cart = $this->getCart();
        $products = Product::whereIn('id', array_keys($cart))->get();
        $totalPrice = 0;
        foreach ($products as $product) {
            $totalPrice += $product->price * $cart[$product->id];
        }

        $order = Order::create([
            'user_id'       => Auth::id(),
            'status'        => 'new',
            'total_price'   => $totalPrice,
            'total'         => $totalPrice,
            'customer_name' => $data['customer_name'],
            'email'         => $data['email'],
            'phone'         => $data['phone'],
            'address'       => '',
            'comment'       => $data['comment'] ?? null,
            'delivery_date' => $data['delivery_date'] ?? null,
        ]);

        foreach ($products as $product) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'quantity'     => $cart[$product->id],
                'price'        => $product->price,
            ]);
        }

        $this->clearCart();
        return $order;
    }
}
