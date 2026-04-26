<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\UserResource;
use App\Services\Auth\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function register(RegisterRequest $request, AuthService $service)
    {
        $user = $service->register($request->validated());

        return $this->successResponse(
            new UserResource($user),
            'User registered successfully',
            201
        );
    }

    public function login(LoginRequest $request, AuthService $service)
    {
        $result = $service->login($request->validated());

        return $this->successResponse([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], 'Login successful');
    }

    public function logout(Request $request, AuthService $service)
    {
        $service->logout($request->user());

        return $this->successResponse(null, 'Logged out successfully');
    }
}
