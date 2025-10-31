<?php

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shop_id' => Shop::factory(),
            'name' => fake()->unique()->words(2, true).' Widget',
            'sku' => strtoupper(fake()->unique()->bothify('###-???-##')),
            'price' => fake()->randomFloat(2, 10, 500),
            'stock' => fake()->numberBetween(10, 200),
        ];
    }
}
