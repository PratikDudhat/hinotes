<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use App\SocialUsers;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use Validator;

class AuthController extends ResponseController
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'create', 'forgotPassword', 'doResetPassword', 'doVerifyEmail','doResendCode','social_login','doVerifyToken']]);
        $this->guard = "api";
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required'
        ]);

        if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success);
        }

        $user = User::where('email', request('email'))->first();

        if (!$user) {
            $success = [
                'message' => "Invalid email.",
                'success' => false
            ];
            return $this->sendResponse($success);
        }
        
        if (!$token = $this->attemptLogin($user)) {
            $success = [
                'message' => "Invalid email or password.",
                'success' => false
            ];
            return $this->sendResponse($success);
        }
		
		$verificationtoken = rand(100000, 999999);
      
        if(!$user){
			
			$success = [
                'message' => "Wrong or non-existing email.",
                'success' => false
            ];
            return $this->sendResponse($success);
        }

		if(isset($user->email_verified) && empty($user->email_verified)){
			DB::table('password_resets')->where('email', $request->get('email'))->delete();
			DB::table('password_resets')->insert([
				'email' => $request->get('email'),
				'token' => $verificationtoken,
				'created_at' => Carbon::now()
			]);
			$user->sendVerificationNotification($verificationtoken);
			$success = [
				'access_token' => $token,
				'token_type' => 'bearer',
				'expires_in' => 0,
				'message' => "Verification code send Successfully.",
				'success' => true,
				'data' => $user,
			];
			return $this->sendResponse($success);
		}
		
        $user = auth($this->guard)->user();
		//with('media')
		$user = User::find($user->id);
        $success = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => 0,
            'data' => $user,
            'message' => "Login Successfully",
            'success' => true,
        ];
        return $this->sendResponse($success);
    }
	
	public function create(Request $request)
    {
		$validator = Validator::make($request->all(), [
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'country_code' => 'required',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|min:6',
            'country_name' => 'required',
        ]);

        if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success);
        }
		
        $userData = $request->only(['email', 'country_code','phone','username']);
        $userData['password'] = bcrypt($request->get('password'));
		$userData['user_type'] = "user";
		$userData['country'] = $request->get('country_name');
        $user = User::create($userData);
		$user->assignRole([2]);
		
		if (!$token = $this->attemptLogin($user)) {
            $success = [
                'message' => "Unauthorized",
                'success' => false
            ];
            return $this->sendResponse($success);
        }
		
		
		$verificationtoken = rand(100000, 999999);
	  
		DB::table('password_resets')->where('email', $request->get('email'))->delete();
		DB::table('password_resets')->insert([
			'email' => $request->get('email'),
			'token' => $verificationtoken,
			'created_at' => Carbon::now()
		]);
		$user->sendVerificationNotification($verificationtoken);
		$success = [
			'step' => 1,
			'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth($this->guard)->factory()->getTTL() * 60,
            'data' => $user,			
			'message' => "Verification code has been send successfully to ".$request->get('email'),
			'success' => true,
		];
		return $this->sendResponse($success);
    }

    public function attemptLogin(User $user)
    {
        if (!$token = auth($this->guard)->attempt(['email' => request('email'), 'password' => request('password')])) {
            $token = auth($this->guard)->attempt(['email' => request('email'), 'password' => request('password')]);
        }   
        return $token;
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth($this->guard)->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth($this->guard)->logout(true);
        $success = [
            'message' => 'Successfully logged out.',
			'success' =>true,
        ];
        return $this->sendResponse($success);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $success = [
			'message' => 'refresh success',
			'success' => true,
            'access_token' => auth($this->guard)->refresh(),
            'token_type' => 'bearer'
        ];
        return $this->sendResponse($success);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success);       
        }

        $token = rand(100000, 999999);
        $user = User::where('email', $request->get('email'))->first();

        if (!$user){
			$success = [
                'message' => "Fail to send password recovery code.",
                'success' => false
            ];
            return $this->sendResponse($success);
        }

        DB::table('password_resets')->where('email', $request->get('email'))->delete();
        DB::table('password_resets')->insert([
            'email' => $request->get('email'),
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
        $user->sendPasswordResetNotification($token);

        $success = [
            'message' => "Password recovery code has been send successfully to ".$request->get('email'),
			'success' => true,	
        ];
        return $this->sendResponse($success);
        
    }

    public function doResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false,
            ];
            return $this->sendResponse($success);       
        }
        
        $user = User::where('email', $request->get('email'))->first();
        $user->password = Hash::make($request->get('password'));
        $user->save();

        DB::table('password_resets')->where('email', $request->get('email'))->delete();

        //event(new PasswordReset($user));
        
        $success = [
            'message' => "Password update successfully.",
			'success' => true,	
        ];
        return $this->sendResponse($success);
    }
	
	public function doVerifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:password_resets,email',            
            'token' => 'required'
        ]);

        if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false,
            ];
            return $this->sendResponse($success);       
        }
        
        if(!DB::table('password_resets')->where([['email', $request->get('email')], ['token', $request->get('token')]])->first()){
			$success = [
                'message' => "Verification code not valid.",
                'success' => false,	
            ];
            return $this->sendResponse($success);
        }

        $user = User::where('email', $request->get('email'))->first();
        $user->email_verified = 1;
        $user->save();

        DB::table('password_resets')->where('email', $request->get('email'))->delete();

        //event(new PasswordReset($user));
        
        $success = [
            'message' => "User Verified successfully.",
			'success' => true,	
        ];
        return $this->sendResponse($success);
    }
	
	
	public function doVerifyToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:password_resets,email',            
            'token' => 'required'
        ]);

        if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false,
            ];
            return $this->sendResponse($success);       
        }
        
        if(!DB::table('password_resets')->where([['email', $request->get('email')], ['token', $request->get('token')]])->first()){
			$success = [
                'message' => "Verification code not valid.",
                'success' => false,	
            ];
            return $this->sendResponse($success);
        }

        DB::table('password_resets')->where('email', $request->get('email'))->delete();
        
        $success = [
            'message' => "Token Verified successfully.",
			'success' => true,	
        ];
        return $this->sendResponse($success);
    }
	
	
	

    public function doResendCode(Request $request){

    	 $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success);       
        }

        $token = rand(100000, 999999);
        $user = User::where('email', $request->get('email'))->first();

        if (!$user){
			$success = [
                'message' => "Fail to send v  erification code.",
                'success' => false
            ];
            return $this->sendResponse($success);
        }

        DB::table('password_resets')->where('email', $request->get('email'))->delete();
        DB::table('password_resets')->insert([
            'email' => $request->get('email'),
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
        $user->sendPasswordResetNotification($token);

        $success = [
            'message' => "Verification code has been send successfully to ".$request->get('email'),
			'success' => true,	
        ];
        return $this->sendResponse($success);
    }

    public function social_login(Request $request)
    {
        $validator = Validator::make($request->all(), [
			// 'name' => 'required|string',
            'username' => 'required|string',
            'country_code' => 'required|string',
            'phone' => 'required|string|unique:users,phone',
            'email' => 'required|email',
            'social_platform' => 'required|string',
            'social_id' => 'required|string',
        ]);

        if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success);
        }
        
        $user = User::where("email", $request->get('email'))->first();
        if(!$user){
        	$userData = $request->only(['email', 'country_code','phone','username']);
	        $userData['password'] = bcrypt($request->get('password'));
			$userData['user_type'] = "user";
	        $user = User::create($userData);
	        $form_data = ["user_id"=>$user->id,"social_id"=>$request->get('social_id'),"social_platform"=> $request->get('social_platform'),"created_at"=>date("Y-m-d H:i:s"),"updated_at"=>date("Y-m-d H:i:s")];
			DB::table('social_users')->insert($form_data);

			$token = JWTAuth::fromUser($user);
			$user->social_id = $request->get('social_id');
			$user->social_platform = $request->get('social_platform');
			$success = [
				'access_token' => $token,
				'token_type' => 'bearer',
				'expires_in' => 0,
				'data' => $user,
				'message' => "Login Successfully",
				'success' => true,
			];
			return $this->sendResponse($success);
        }
        else
		{
			$socialuser = SocialUsers::where('user_id',$user->id)->where('social_id',$request->get('social_id'))->where('social_platform',$request->get('social_platform'))->first();
			if(!empty($socialuser)){
				 $token = JWTAuth::fromUser($user);
				 $user->social_id = $request->get('social_id');
				 $user->social_platform = $request->get('social_platform');
				 $success = [
					'access_token' => $token,
					'token_type' => 'bearer',
					'expires_in' => 0,
					'data' => $user,
					'message' => "Login Successfully",
					'success' => true,
				];
				return $this->sendResponse($success);

			}else{
				$form_data = ["user_id"=>$user->id,"social_id"=>$request->get('social_id'),"social_platform"=> $request->get('social_platform'),"created_at"=>date("Y-m-d H:i:s"),"updated_at"=>date("Y-m-d H:i:s")];
				DB::table('social_users')->insert($form_data);
				$token = JWTAuth::fromUser($user);
				 $user->social_id = $request->get('social_id');
				 $user->social_platform = $request->get('social_platform');
				 $success = [
					'access_token' => $token,
					'token_type' => 'bearer',
					'expires_in' => 0,
					'data' => $user,
					'message' => "Login Successfully",
					'success' => true,
				];
				return $this->sendResponse($success);
			}
		}
    }
}
