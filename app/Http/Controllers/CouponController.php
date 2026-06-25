<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Coupon\ApplyCouponRequest;
use App\Http\Requests\Coupon\StoreCouponRequest;
use App\Http\Requests\Coupon\UpdateCouponRequest;
use App\Models\Coupon;
use App\Services\CouponService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Throwable;

class CouponController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private CouponService $couponService)
    {
    }

    public function index()
    {
        try {
            $coupons = $this->couponService->getAllCoupons();

            return $this->successResponse('Coupons fetched successfully.', $coupons);
        } catch (Throwable $e) {
            return $this->errorResponse('Error occurred while fetching coupons.', 500,  ['message' => $e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine()]);
        }
    }

    public function store(StoreCouponRequest $request)
    {
        try {
            $coupon = $this->couponService->createCoupon($request->validated());

            return $this->successResponse('Coupon created successfully.', $coupon, 201);
        } catch (Throwable $e) {
            return $this->errorResponse('Error occurred while creating the coupon.', 500, ['message' => $e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine()]);
        }
    }

    public function show(Coupon $coupon)
    {
        try {
            return $this->successResponse('Coupon fetched successfully.', $coupon);
        } catch (Throwable $e) {
            return $this->errorResponse('Error occurred while fetching the coupon.', 500,  ['message' => $e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine()]);
        }
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        try {
            $updatedCoupon = $this->couponService->updateCoupon($coupon, $request->validated());

            return $this->successResponse('Coupon updated successfully.', $updatedCoupon);
        } catch (Throwable $e) {
            return $this->errorResponse('Error occurred while updating the coupon.', 500,  ['message' => $e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine()]);
        }
    }

    public function destroy(Coupon $coupon)
    {
        try {
            $this->couponService->deleteCoupon($coupon);

            return $this->successResponse('Coupon deleted successfully.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error occurred while deleting the coupon.', 500,  ['message' => $e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine()]);
        }
    }

    public function preview(ApplyCouponRequest $request)
    {
        try {
            $data = $this->couponService->previewFinalPrice(
                $request->validated('course_id'),
                $request->validated('coupon_code')
            );

            return $this->successResponse('Final price previewed successfully.', $data);
        } catch (ValidationException $e) {
            return $this->errorResponse('Coupon validation failed.', 422, $e->errors());
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Course not found.', 404, null);
        } catch (Throwable $e) {
            return $this->errorResponse('Error occurred while calculating the final price.', 500,  ['message' => $e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine()]);
        }
    }
}
