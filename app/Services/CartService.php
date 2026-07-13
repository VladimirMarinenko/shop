<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Получить корзину из сессии.
     */
    public function getCart(): array
    {
        return session()->get('cart', []);
    }

    /**
     * Обновить корзину в сессии.
     */
    public function updateCart(array $cart): void
    {
        session()->put('cart', $cart);
    }

    /**
     * Очистить корзину.
     */
    public function clearCart(): void
    {
        session()->forget('cart');
    }

    /**
     * Получить товары из корзины с дополнительной информацией.
     */
    public function getCartProducts(): array
    {
        $cart = $this->getCart();
        if (empty($cart)) {
            return ['products' => collect(), 'cart' => $cart, 'totalPrice' => 0];
        }

        $products = Product::whereIn('id', array_keys($cart))->get();
        $totalPrice = 0;
        foreach ($products as $product) {
            $totalPrice += $product->price * $cart[$product->id];
        }

        return [
            'products'    => $products,
            'cart'        => $cart,
            'totalPrice'  => $totalPrice,
        ];
    }

    /**
     * Добавить товар в корзину.
     */
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
        $this->updateCart($cart);

        return ['success' => true, 'message' => 'Товар добавлен в корзину!'];
    }

    /**
     * Удалить одну единицу товара из корзины.
     */
    public function removeProduct(int $productId): void
    {
        $cart = $this->getCart();
        if (isset($cart[$productId])) {
            if ($cart[$productId] > 1) {
                $cart[$productId] -= 1;
            } else {
                unset($cart[$productId]);
            }
            $this->updateCart($cart);
        }
    }

    /**
     * Проверить корзину на валидность перед оформлением заказа.
     *
     * @throws \Exception
     */
    public function validateCartForCheckout(): array
    {
        $cart = $this->getCart();
        if (empty($cart)) {
            throw new \Exception('Корзина пуста.');
        }

        $products = Product::whereIn('id', array_keys($cart))->get();
        if ($products->isEmpty()) {
            $this->clearCart();
            throw new \Exception('Товары в корзине больше не доступны. Корзина очищена.');
        }

        // Проверяем количество и наличие на складе
        foreach ($products as $product) {
            $quantity = $cart[$product->id] ?? 0;
            if ($quantity <= 0) {
                unset($cart[$product->id]);
                $this->updateCart($cart);
                throw new \Exception("Количество товара '{$product->name}' должно быть больше нуля.");
            }
            if ($product->stock <= 0) {
                throw new \Exception("Товар '{$product->name}' отсутствует на складе!");
            }
            if ($quantity > $product->stock) {
                throw new \Exception("Недостаточно товара '{$product->name}' на складе! Доступно: {$product->stock} шт.");
            }
        }

        return ['products' => $products, 'cart' => $cart];
    }

    /**
     * Оформить заказ.
     */
    public function placeOrder(array $data): Order
    {
        $validated = $this->validateCartForCheckout();
        $products = $validated['products'];
        $cart = $validated['cart'];

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
