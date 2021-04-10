<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use Validator;

class TagController extends ResponseController
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
       $tags = Tag::select('id','title')->get();
       if(!$tags){
         $success = [
                'message' =>"Hash Tag list not found",
                'success' => false
            ];
       }
       $success = [
                'message' =>"Hash Tag get successfully.",
                'success' => true,
                'data' => $tags,
            ];
        return $this->sendResponse($success);
    }
      
}
