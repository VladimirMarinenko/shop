<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Показать содержимое корзины
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->get();
        $totalPrice = 0;
        foreach ($products as $product) {
            $totalPrice += $product->price * $cart[$product->id];
        }
        return view('cart', compact('products', 'cart', 'totalPrice'));
    }

    /**
     * Добавить товар в корзину
     */
    public function add($productId)
    {
        $product = Product::findOrFail($productId);
        if ($product->stock <= 0) {
            return redirect()->back()->with('error', 'Товара нет в наличии!');
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            if ($cart[$productId] >= $product->stock) {
                return redirect()->back()->with('error', 'Недостаточно товара на складе!');
            }
            $cart[$productId] += 1;
        } else {
            $cart[$productId] = 1;
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Товар добавлен в корзину!');
    }

    /**
     * Удалить одну единицу товара
     */
    public function remove($productId)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$productId])) {
            if ($cart[$productId] > 1) {
                $cart[$productId] -= 1;
            } else {
                unset($cart[$productId]);
            }
            session()->put('cart', $cart);
        }
        return redirect()->route('cart.index')->with('success', 'Товар удалён из корзины.');
    }

    /**
     * Полная очистка корзины
     */
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', 'Корзина очищена.');
    }

    /**
     * Показать форму оформления заказа
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Корзина пуста.');
        }
        $products = Product::whereIn('id', array_keys($cart))->get();
        $totalPrice = 0;
        foreach ($products as $product) {
            $totalPrice += $product->price * $cart[$product->id];
        }
        return view('checkout', compact('products', 'cart', 'totalPrice'));
    }

    /**
     * Сохранить заказ
     */
    public function placeOrder(Request $request)
    {
        // Валидация
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => 'required|string|max:20',
            'comment'       => 'nullable|string|max:500',
            'delivery_date' => 'nullable|date|after:now', // дата должна быть в будущем
        ]);

        // Получаем корзину
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Корзина пуста.');
        }

        // Получаем товары и считаем сумму
        $products = Product::whereIn('id', array_keys($cart))->get();
        $totalPrice = 0;
        foreach ($products as $product) {
            $totalPrice += $product->price * $cart[$product->id];
        }

        // Создаём заказ
        $order = Order::create([
            'user_id'       => Auth::id(),
            'status'        => 'new',
            'total_price'   => $totalPrice,
            'total'         => $totalPrice,
            'customer_name' => $request->customer_name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'address'       => '',
            'comment'       => $request->comment,
            'delivery_date' => $request->delivery_date,
        ]);

        // Создаём позиции заказа
        foreach ($products as $product) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'quantity'     => $cart[$product->id],
                'price'        => $product->price,
            ]);
        }

        // Очищаем корзину
        session()->forget('cart');

        return redirect()->route('home')->with('success', 'Заказ оформлен! Спасибо за покупку.');
    }
}
