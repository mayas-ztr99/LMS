<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ManageUserRoleRequest;
use App\Http\Resources\Auth\UserResource;
use App\Services\Admin\RoleService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Throwable;

class RoleController extends Controller
{
    use ApiResponseTrait;

    public function assignRole(ManageUserRoleRequest $request, RoleService $service)
    {
        try {
            $user = $service->assignRole($request->validated());

            return $this->successResponse(
                new UserResource($user),
                'Role assigned successfully',
                200
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('User or role not found', 404);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (Throwable $e) {
            return $this->errorResponse('Something went wrong', 500);
        }
    }

    public function revokeRole(ManageUserRoleRequest $request, RoleService $service)
    {
        try {
            $user = $service->revokeRole($request->validated());

            return $this->successResponse(
                new UserResource($user),
                'Role revoked successfully',
                200
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('User or role not found', 404);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (Throwable $e) {
            return $this->errorResponse('Something went wrong', 500);
        }
    }

    public function updateRole(ManageUserRoleRequest $request, RoleService $service)
    {
        try {
            $user = $service->updateRole($request->validated());

            return $this->successResponse(
                new UserResource($user),
                'Role updated successfully',
                200
            );
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('User or role not found', 404);
        } catch (ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (Throwable $e) {
            return $this->errorResponse('Something went wrong', 500);
        }
    }
}