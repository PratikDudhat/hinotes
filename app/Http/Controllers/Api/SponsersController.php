<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Sponsers;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use Validator;

class SponsersController extends ResponseController
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
       $sponsers = Sponsers::get();
       if(!$sponsers){
         $success = [
                'message' =>"Sponsers list not found",
                'success' => false
            ];
       }
       $success = [
                'message' =>"Sponsers list",
                'success' => true,
                'data' => $sponsers,
            ];
        return $this->sendResponse($success);
    }
      
}
