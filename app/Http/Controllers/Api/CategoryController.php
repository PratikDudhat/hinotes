<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use Validator;

class CategoryController extends ResponseController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->guard = "api";
    }

    public function index()
    {
       $category = Category::select('id','category_name','category_logo')->get();
       if(!$category){
         $success = [
                'message' =>"Category list not found",
                'success' => false
            ];
       }
       $success = [
                'message' =>"Category get successfully.",
                'success' => true,
                'data' => $category,
            ];
        return $this->sendResponse($success);
    }
      
}
