<?php

namespace App\Services;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TenantService
{
    /**
     * Creates a new Shop and associates a new User as its owner atomically.
     *
     * @param  array  $data  Validated data from the TenantRegistrationRequest.
     * @return User The newly created User (Shop Owner).
     *
     * @throws \Exception If the transaction fails.
     */
    public function registerTenantAndOwner(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $shop = Shop::create([
                'name' => $data['shop_name'],
            ]);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'shop_id' => $shop->id,
            ]);

            return $user;
        });
    }
}
