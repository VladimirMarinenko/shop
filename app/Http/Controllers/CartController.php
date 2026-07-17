<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceOrderRequest;
use App\Services\CartService;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index(): View
    {
        $data = $this->cartService->getCartProducts();
        return view('cart', $data);
    }

    public function add(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $cart = session()->get('cart', []);

        // Проверка наличия на складе
        if ($product->stock <= 0) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Товара нет в наличии!']);
            }
            return redirect()->back()->with('error', 'Товара нет в наличии!');
        }

        // Проверка, есть ли уже этот товар в корзине
        if (isset($cart[$productId])) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Товар уже добавлен в корзину!']);
            }
            return redirect()->back()->with('error', 'Товар уже добавлен в корзину!');
        }

        // Добавляем товар (только 1 единицу)
        $cart[$productId] = 1;
        session()->put('cart', $cart);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Товар добавлен в корзину!',
                'cart_count' => array_sum($cart)
            ]);
        }

        return redirect()->back()->with('success', 'Товар добавлен в корзину!');
    }

    public function remove(int $productId): RedirectResponse
    {
        $this->cartService->removeProduct($productId);
        return redirect()->route('cart.index')->with('success', 'Товар удалён из корзины.');
    }

    public function clear(): RedirectResponse
    {
        $this->cartService->clearCart();
        return redirect()->route('cart.index')->with('success', 'Корзина очищена.');
    }

    public function checkout(): View|RedirectResponse
    {
        try {
            $this->cartService->validateCartForCheckout();
            $data = $this->cartService->getCartProducts();
            return view('checkout', $data);
        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }
    }

    public function placeOrder(PlaceOrderRequest $request): RedirectResponse
    {
        try {
            $order = $this->cartService->placeOrder($request->validated());
            return redirect()->route('home')->with('success', 'Заказ #' . $order->id . ' оформлен! Спасибо за покупку.');
        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }
    }
}
