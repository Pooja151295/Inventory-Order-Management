<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Responses\ApiResponse;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $orders = Order::with('items.product:id,name,sku', 'user:id,name,email')
                ->latest()
                ->paginate(15);

            return ApiResponse::success([
                'orders' => OrderResource::collection($orders),
                'pagination' => [
                    'total' => $orders->total(),
                    'per_page' => $orders->perPage(),
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                ],
            ], 'Orders fetched successfully');
        } catch (\Exception $e) {
            Log::error('Order index error: '.$e->getMessage());

            return ApiResponse::error('Failed to fetch orders', $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource (Order Management Show).
     * Fails with 404 if order does not belong to the current shop.
     */
    public function show(Order $order)
    {
        try {
            $order->load('items.product:id,name,sku', 'user:id,name,email');

            return ApiResponse::success(new OrderResource($order), 'Order retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Order show error: '.$e->getMessage());

            return ApiResponse::error('Failed to fetch order', $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage (Order Placement).
     */
    public function store(StoreOrderRequest $request)
    {
        try {
            $order = $this->orderService->placeOrder(
                $request->user(),
                $request->validated('items')
            );

            return ApiResponse::success(new OrderResource($order), 'Order placed successfully', 201);
        } catch (\Exception $e) {
            Log::error('Order placement failed: '.$e->getMessage(), [
                'user_id' => $request->user()->id,
            ]);

            if (str_contains($e->getMessage(), 'stock') || str_contains($e->getMessage(), 'shop')) {
                return ApiResponse::error($e->getMessage(), null, 409);
            }

            return ApiResponse::error(
                'An internal error occurred during order placement.',
                $e->getMessage(),
                500
            );
        }
    }
}
