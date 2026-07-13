<?php


namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;


class OrderService
{
    /**
     * Получить список заказов с пагинацией.
     */
    public function getPaginatedOrders(int $perPage = 15): LengthAwarePaginator
    {
        return Order::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Получить заказ с деталями.
     */
    public function getOrderWithDetails(int $id): Order
    {
        return Order::with(['user', 'items.product'])->findOrFail($id);
    }

    /**
     * Обновить статус заказа и управлять остатками.
     */
    public function updateStatus(Order $order, string $newStatus): array
    {
        $oldStatus = $order->status;
        $errors = [];
        
        if ($newStatus === 'completed' && $oldStatus !== 'completed') {
            $insufficient = $this->checkStockBeforeComplete($order);
            if ($insufficient) {
                return [
                    'success' => false,
                    'error' => 'Недостаточно товара на складе для выполнения заказа!'
                ];
            }
            $this->decreaseStock($order);
        }

        if ($oldStatus === 'completed' && $newStatus !== 'completed') {
            $this->increaseStock($order);
        }

        $order->status = $newStatus;
        $order->save();

        return [
            'success' => true,
            'message' => 'Статус заказа обновлён.'
        ];
    }

    /**
     * Проверить, достаточно ли товара на складе для выполнения заказа.
     */
    private function checkStockBeforeComplete(Order $order): bool
    {
        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product && $product->stock < $item->quantity) {
                return true;
            }
        }
        return false;
    }

    /**
     * Списать товары со склада.
     */
    private function decreaseStock(Order $order): void
    {
        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product) {
                $product->stock -= $item->quantity;
                $product->save();
            }
        }
    }

    /**
     * Вернуть товары на склад.
     */
    private function increaseStock(Order $order): void
    {
        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product) {
                $product->stock += $item->quantity;
                $product->save();
            }
        }
    }

    public function createOrderFromCart(array $cart, array $customerData, int $userId): Order
    {
        $products = Product::whereIn('id', array_keys($cart))->get();
        $totalPrice = 0;
        foreach ($products as $product) {
            $totalPrice += $product->price * $cart[$product->id];
        }

        $order = Order::create([
            'user_id'       => $userId,
            'status'        => 'new',
            'total_price'   => $totalPrice,
            'total'         => $totalPrice,
            'customer_name' => $customerData['customer_name'],
            'email'         => $customerData['email'],
            'phone'         => $customerData['phone'],
            'address'       => $customerData['address'] ?? '',
            'comment'       => $customerData['comment'] ?? null,
            'delivery_date' => $customerData['delivery_date'] ?? null,
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

        return $order;
    }
}
