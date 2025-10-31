<?php

// app/Services/OrderService.php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Handles the complex process of validating stock, creating an order,
     * and decrementing product inventory, all within a database transaction.
     *
     * @param  \App\Models\User  $user  The authenticated user placing the order.
     * @param  array  $items  The array of ['product_id' => 1, 'quantity' => 2]
     *
     * @throws \Exception If stock is insufficient or a database error occurs.
     */
    public function placeOrder(\App\Models\User $user, array $items): Order
    {
        $orderItems = collect($items);
        $totalAmount = 0;
        $orderProducts = [];
        $productIds = $orderItems->pluck('product_id')->unique()->toArray();
        $shopId = $user->shop_id;

        return DB::transaction(function () use ($user, $shopId, $orderItems, &$totalAmount, &$orderProducts, $productIds) {
            $products = Product::whereIn('id', $productIds)
                ->where('shop_id', $shopId) // Explicit security check
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($orderItems as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];

                if (! $products->has($productId)) {
                    throw new \Exception("Product ID {$productId} not found or belongs to another shop.");
                }

                $product = $products->get($productId);

                if ($product->stock < $quantity) {
                    throw new \Exception("Insufficient stock for product: {$product->name}. Requested: {$quantity}, Available: {$product->stock}");
                }

                $totalAmount += $product->price * $quantity;

                $orderProducts[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price_at_order' => $product->price,
                ];

                $product->stock -= $quantity;
                $product->save();
            }

            $order = Order::create([
                'shop_id' => $shopId,
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'status' => OrderStatus::PENDING->value,
            ]);
            foreach ($orderProducts as $itemData) {
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);
            }

            return $order;
        });
    }
}
