<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TenantRegistrationRequest;
use App\Http\Resources\TenantResource;
use App\Http\Responses\ApiResponse;
use App\Services\TenantService;

class TenantRegistrationController extends Controller
{
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function register(TenantRegistrationRequest $request)
    {
        try {
            $user = $this->tenantService->registerTenantAndOwner($request->validated());
            $user->load('shop');

            $token = $user->createToken('auth_token')->plainTextToken;

            return ApiResponse::success(
                [
                    'user' => new TenantResource($user),
                    'token' => $token,
                ],
                'Tenant and owner successfully registered.',
                201
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Registration failed. '.$e->getMessage()
            );
        }
    }
}
