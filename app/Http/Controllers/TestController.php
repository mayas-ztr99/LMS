<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;

class TestController extends Controller
{
    use ApiResponseTrait;
    public function test(){
       // return $this->successResponse(['name'=>'test user'],'everythings work .');
        return $this->errorResponse('something went wrong',400);
    }
}
