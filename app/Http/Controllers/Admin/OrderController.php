<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function index()
    {
        $orders = $this->orderService->getPaginatedOrders(15);
        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = $this->orderService->getOrderWithDetails($id);
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:new,processing,completed,cancelled',
        ]);

        $order = $this->orderService->getOrderWithDetails($id);
        $result = $this->orderService->updateStatus($order, $request->status);

        if (!$result['success']) {
            return redirect()
                ->route('admin.orders.show', $order->id)
                ->with('error', $result['error']);
        }

        return redirect()
            ->route('admin.orders.show', $order->id)
            ->with('success', $result['message']);
    }
}
