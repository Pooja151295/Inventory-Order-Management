<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Responses\ApiResponse;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        try {
            $products = Product::latest()->get();

            return ApiResponse::success(ProductResource::collection($products), 'Products fetched successfully');
        } catch (\Exception $e) {
            Log::error('Product index error: '.$e->getMessage());

            return ApiResponse::error('Failed to fetch products', $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created product.
     * shop_id is auto-injected before saving.
     */
    public function store(ProductRequest $request)
    {
        try {
            $product = Product::create(array_merge(
                $request->validated(),
                ['shop_id' => $request->user()->shop_id]
            ));

            return ApiResponse::success($product, 'Product created successfully', 201);
        } catch (\Exception $e) {
            Log::error('Product store error: '.$e->getMessage(), [
                'shop_id' => $request->user()->shop_id ?? null,
            ]);

            return ApiResponse::error('Failed to create product', $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        try {
            return ApiResponse::success(new ProductResource($product), 'Product retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Product show error: '.$e->getMessage(), ['product_id' => $product->id ?? null]);

            return ApiResponse::error('Failed to fetch product', $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            $product->update($request->validated());

            return ApiResponse::success(new ProductResource($product), 'Product updated successfully', 200);
        } catch (\Exception $e) {
            Log::error('Product update error: '.$e->getMessage(), ['product_id' => $product->id ?? null]);

            return ApiResponse::error('Failed to update product', $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();

            return ApiResponse::success(null, 'Product deleted successfully', 204);
        } catch (\Exception $e) {
            Log::error('Product delete error: '.$e->getMessage(), ['product_id' => $product->id ?? null]);

            return ApiResponse::error('Failed to delete product', $e->getMessage(), 500);
        }
    }
}
