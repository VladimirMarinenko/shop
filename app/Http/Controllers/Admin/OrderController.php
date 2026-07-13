<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:new,processing,completed,cancelled',
        ]);

        if ($request->status == 'completed' && $order->status != 'completed') {
            $insufficientStock = false;

            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    if ($product->stock < $item->quantity) {
                        $insufficientStock = true;
                        break;
                    }
                }
            }

            if ($insufficientStock) {
                return redirect()->route('admin.orders.show', $order->id)
                    ->with('error', 'Недостаточно товара на складе для выполнения заказа!');
            }

            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->stock -= $item->quantity;
                    $product->save();
                }
            }
        }

        if ($order->status == 'completed' && $request->status != 'completed') {
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->stock += $item->quantity;
                    $product->save();
                }
            }
        }
        
        $order->status = $request->status;
        $order->save();

        return redirect()->route('admin.orders.show', $order->id)
            ->with('success', 'Статус заказа обновлён.');
    }
}
