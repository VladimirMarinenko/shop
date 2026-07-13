<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceOrderRequest;
use App\Services\CartService;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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

    public function add(int $productId): RedirectResponse
    {
        $result = $this->cartService->addProduct($productId);
        return redirect()->back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
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
