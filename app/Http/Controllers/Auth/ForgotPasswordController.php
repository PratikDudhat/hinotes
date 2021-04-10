<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
	
	public function passwordRequest(Request $request){
		
		$user = DB::table('users')->where('email', '=', $request->email)->first();
		
		//Check if the user exists
		if(empty($user)) {
			return redirect()->back()->withErrors(['email' => trans('User does not exist')]);
		}

		$response = Password::broker()->sendResetLink($request->only('email'));

		if($response == Password::RESET_LINK_SENT) {
			return redirect()->back()->withErrors(['success' => trans('A reset link has been sent to your email address.')]);
		} else {
			return redirect()->back()->withErrors(['email' => trans('A Network Error occurred. Please try again.')]);
		}
	}	
}
