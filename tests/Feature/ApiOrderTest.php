<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->shop = Shop::factory()->create();
    $this->user = User::factory()->create([
        'shop_id' => $this->shop->id,
    ]);

    $this->actingAs($this->user);
});

test('it fetches paginated orders', function () {
    $orders = Order::factory()->count(3)->create([
        'user_id' => $this->user->id,
        'shop_id' => $this->shop->id,
    ]);

    foreach ($orders as $order) {
        $product = Product::factory()->create([
            'shop_id' => $this->shop->id,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 10.00,
            'shop_id' => $this->shop->id,
        ]);
    }

    $response = $this->getJson('/api/orders');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'orders' => [
                    '*' => [
                        'id',
                        'user',
                        'items',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'pagination' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                ],
            ],
        ]);
});

test('it shows a single order', function () {
    $order = Order::factory()->create([
        'user_id' => $this->user->id,
        'shop_id' => $this->shop->id,
    ]);

    $product = Product::factory()->create([
        'shop_id' => $this->shop->id,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => 9.99,
        'shop_id' => $this->shop->id,
    ]);

    $response = $this->getJson("/api/orders/{$order->id}");

    $response->assertStatus(200)
        ->assertJsonFragment([
            'id' => $order->id,
        ]);
});

test('it places a new order', function () {
    $product = Product::factory()->create([
        'shop_id' => $this->shop->id,
        'price' => 15.00,
        'stock' => 10,
    ]);

    $payload = [
        'items' => [
            [
                'product_id' => $product->id,
                'quantity' => 2,
            ],
        ],
    ];

    $response = $this->postJson('/api/orders', $payload);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'user',
                'items',
                'created_at',
                'updated_at',
            ],
        ]);

    $this->assertDatabaseHas('orders', [
        'user_id' => $this->user->id,
        'shop_id' => $this->shop->id,
    ]);

    $this->assertDatabaseHas('order_items', [
        'product_id' => $product->id,
        'quantity' => 2,
        'shop_id' => $this->shop->id,
    ]);
});

test('it fails to place order when stock is insufficient', function () {
    $product = Product::factory()->create([
        'shop_id' => $this->shop->id,
        'price' => 20.00,
        'stock' => 1,
    ]);

    $payload = [
        'items' => [
            [
                'product_id' => $product->id,
                'quantity' => 5,
            ],
        ],
    ];

    $response = $this->postJson('/api/orders', $payload);

    $response->assertStatus(409)
        ->assertJson([
            'success' => false,
        ]);
});
