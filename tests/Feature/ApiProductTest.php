<?php

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

    $token = $this->user->createToken('test_token')->plainTextToken;
    $this->withHeader('Authorization', 'Bearer '.$token);
});

test('it fetches all products', function () {
    $products = Product::factory()->count(3)->create(['shop_id' => $this->user->shop_id]);

    $response = $this->getJson('/api/products');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => ['id', 'name', 'sku', 'price', 'stock', 'created_at', 'updated_at'],
            ],
        ])
        ->assertJsonCount(3, 'data')
        ->assertJsonFragment([
            'id' => $products[0]->id,
            'name' => $products[0]->name,
        ]);
});

test('it creates a product with valid data', function () {
    $payload = [
        'name' => 'Test Product',
        'sku' => 'TESTSKU01',
        'price' => 9.99,
        'stock' => 10,
    ];

    $response = $this->postJson('/api/products', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'data' => [
                'name' => 'Test Product',
                'sku' => 'TESTSKU01',
                'price' => 9.99,
                'stock' => 10,
            ],
        ]);

    $this->assertDatabaseHas('products', [
        'name' => 'Test Product',
        'sku' => 'TESTSKU01',
        'shop_id' => $this->user->shop_id,
    ]);
});

test('it fails to create product with invalid data', function () {
    $payload = [
        'name' => '',
        'sku' => '',
        'price' => -5,
        'stock' => -10,
    ];

    $response = $this->postJson('/api/products', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'sku', 'price', 'stock']);
});

test('it shows a single product', function () {
    $product = Product::factory()->create(['shop_id' => $this->user->shop_id]);

    $response = $this->getJson("/api/products/{$product->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->price,
                'stock' => $product->stock,
            ],
        ]);
});

test('it updates a product with valid data', function () {
    $product = \App\Models\Product::withoutEvents(function () {
        return \App\Models\Product::factory()->create(['shop_id' => $this->user->shop_id]);
    });

    $payload = [
        'name' => 'Updated Name',
        'price' => 19.99,
        'stock' => 25,
    ];

    $response = $this->putJson("/api/products/{$product->id}", $payload);
    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'name' => 'Updated Name',
                'price' => 19.99,
                'stock' => 25,
            ],
        ]);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Updated Name',
        'price' => 19.99,
        'stock' => 25,
    ]);
});

test('it fails to update product with invalid data', function () {
    $product = Product::factory()->create(['shop_id' => $this->user->shop_id]);

    $payload = [
        'name' => '',
        'price' => -10,
        'stock' => -5,
    ];

    $response = $this->putJson("/api/products/{$product->id}", $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'price', 'stock']);
});
