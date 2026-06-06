<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class CheckUserRole
{
    use ApiResponseTrait;

    public function handle(Request $request, Closure $next, string $role)
    {
        if (! $request->user()) {
            return $this->errorResponse(
                'Unauthenticated',401
            );
        }
        if (!$request->user()->hasRole($role)) {
            return $this->errorResponse(
                'You do not have permission to access this resource',403
            );
        }
        return $next($request);
    }
}
