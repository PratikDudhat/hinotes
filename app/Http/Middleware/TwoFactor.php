<?php

namespace App\Http\Middleware;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use Closure;

class TwoFactor extends ResponseController
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        if(auth()->check() && $user->two_factor_code)
        {
            if($user->two_factor_expires_at->lt(now()))
            {
                //$user->resetTwoFactorCode();
                //auth('api')->logout();

                $success = [
                    'message' => "Unauthorized",
                    'success' => "0"
                ];
                return $this->sendResponse($success);
            }
            
        }
        return $next($request);
    }
}