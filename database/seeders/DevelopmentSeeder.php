<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'shop_id' => null,
            'name' => 'Global Super Admin',
            'email' => 'superadmin@system.com',
            'password' => Hash::make('password'),
        ]);

        $shopA = Shop::factory()->create(['name' => 'Widget Central']);
        User::factory()->create([
            'shop_id' => $shopA->id,
            'name' => 'Shop A Admin',
            'email' => 'shopa@system.com',
            'password' => Hash::make('password'),
        ]);

        Product::factory(5)->create(['shop_id' => $shopA->id]);

        $shopB = Shop::factory()->create(['name' => 'Tech Hub']);

        User::factory()->create([
            'shop_id' => $shopB->id,
            'name' => 'Shop B Admin',
            'email' => 'shopb@system.com',
            'password' => Hash::make('password'),
        ]);

        Product::factory(5)->create(['shop_id' => $shopB->id]);

    }
}
