<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use App\UserTokens;
use App\Media;
use App\Challange;
use App\Follower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use Image;
use Validator;

class UsersController extends ResponseController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['create']]);
        $this->guard = "api";
    }

    public function index(Request $request)
    {
		$success = [
            'message' => 'User'
        ];
        return $this->sendResponse($success);
		
    }
	
    public function updateGender(Request $request)
    {
        $user = auth($this->guard)->user();
        //dd($user);
        $validator = Validator::make($request->except('password'), [
            'gender' => 'required',
        ]);

        if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success);    
        }
        $userData = $request->except('_method');
       
        $user->update($userData);
		$user = User::with('media')->find($user->id);
        $success = [
            'message' => 'Profile updated',
            'success' => true,
            'data' => $user,
        ];
        return $this->sendResponse($success);
    }
    
	public function updateType(Request $request)
    {
        $user = auth($this->guard)->user();
        //dd($user);
        $validator = Validator::make($request->all(), [
            'user_type' => 'required',
        ]);

        if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success);    
        }
        $userData = $request->except('_method');
       
        $user->update($userData);
		$user = User::with('media')->find($user->id);
        $success = [
            'message' => 'Profile updated',
            'success' => true,
            'data' => $user,
        ];
        return $this->sendResponse($success);
    }
    
    public function updateLocation(Request $request)
    {
        $user = auth($this->guard)->user();
        //dd($user);
        $validator = Validator::make($request->except('password'), [
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success);    
        }
        $userData = $request->except('_method');
       
        $user->update($userData);
		$user = User::with('media')->find($user->id);
        $success = [
            'message' => 'Location updated',
            'success' => true,
            'data' => $user,
        ];
        return $this->sendResponse($success);
    }
    
    public function updateProfile(Request $request)
    {
        $user = auth($this->guard)->user();
        //dd($user);
        $validator = Validator::make($request->except('password'), [
            'name' => 'required',
            'city' => 'required',
            'birth_date' => 'required',
            'height' => 'required',
            'eye_color' => 'required',
            'hair_color' => 'required',
            //'tatto' => 'required',
        ]);

        if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success);    
        }
        $userData = $request->except('_method');
		if($image = $request->file('profile_pic')) {
			
       	    // Define upload path
            $destinationPath = public_path('/user_media/'); // upload path
            
                $input['media_name'] = time().rand().'.'.$image->getClientOriginalExtension();
                
                $thumbdestinationPath = public_path('/thumbnail/');
                $imgth = Image::make($image->getRealPath());
                $imgth->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($thumbdestinationPath.'/'.$input['media_name']);
                
				// Upload Orginal Image           
	            $image->move($destinationPath, $input['media_name']);
				$input['media_path'] = '/public/user_media/'.$input['media_name'];
				$userData['avatar'] = $input['media_path'];
        }
		$user->update($userData);
	    if($files = $request->file('photos')) {
			$photocount = Media::where('media_type',1)->where('user_id',$user->id)->count();
			$uploadphotos  = count($files);
			$limit = $uploadphotos + $photocount;
			if($limit >= 5 ){
				$message = "Maximum 4 photos upload";
				$success = [
					'message' => $message,
					'success' => false
				];
				return $this->sendResponse($success);    
			}
       	    // Define upload path
            $destinationPath = public_path('/user_media/'); // upload path
            foreach($files as $img) {
                $input['media_name'] = time().rand().'.'.$img->getClientOriginalExtension();
                
                $thumbdestinationPath = public_path('/thumbnail/');
                $imgth = Image::make($img->getRealPath());
                $imgth->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($thumbdestinationPath.'/'.$input['media_name']);
                
				// Upload Orginal Image           
	            
	            $img->move($destinationPath, $input['media_name']);
	        	// Save In Database
				$input['user_id'] = auth($this->guard)->user()->id;
                $input['media_type'] = 1;
                $input['media_path'] = '/public/user_media/'.$input['media_name'];
                Media::create($input);
			}
        }
        
        if($vfiles = $request->file('videos')) {
			$videocount = Media::where('media_type',2)->where('user_id',$user->id)->count();
       	    $uploadvideos  = count($vfiles);
			$limit = $uploadvideos + $videocount;
			if($limit >= 5 ){
				$message = "Maximum 4 video upload";
				$success = [
					'message' => $message,
					'success' => false
				];
				return $this->sendResponse($success);    
			}
			// Define upload path
            $destinationPath = public_path('/user_media/'); // upload path
            foreach($vfiles as $vimg) {
                $input['media_name'] = time().rand().'.'.$vimg->getClientOriginalExtension();
                
				// Upload Orginal Image           
	            $vimg->move($destinationPath, $input['media_name']);
	        	// Save In Database
				$input['user_id'] = auth($this->guard)->user()->id;
                $input['media_type'] = 2;
                $input['media_path'] = '/public/user_media/'.$input['media_name'];
                Media::create($input);
			}
        }
		
		$user = User::with('media')->find($user->id);
        $success = [
            'message' => 'Profile updated',
            'success' => true,
            'data' => $user,
        ];
        return $this->sendResponse($success);
    }
	
	public function updateCustomerProfile(Request $request)
    {
        $user = auth($this->guard)->user();
        //dd($user);
        $validator = Validator::make($request->except('password'), [
            'phone' => 'required',
        ]);

        if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success);    
        }
        $userData = $request->except('_method');
		
	    if($files = $request->file('profile_pic')) {
			
       	    // Define upload path
            $destinationPath = public_path('/user_media/'); // upload path
            
			$input['media_name'] = time().rand().'.'.$files->getClientOriginalExtension();
			
			$thumbdestinationPath = public_path('/thumbnail/');
			$imgth = Image::make($files->getRealPath());
			$imgth->resize(100, 100, function ($constraint) {
				$constraint->aspectRatio();
			})->save($thumbdestinationPath.'/'.$input['media_name']);
			
			// Upload Orginal Image           
			$files->move($destinationPath, $input['media_name']);
			$input['media_path'] = '/public/user_media/'.$input['media_name'];
			$userData['avatar'] = $input['media_path'];
        }
        
        $user->update($userData);
		$user = User::with('media')->find($user->id);
        $success = [
            'message' => 'Profile updated',
            'success' => true,
            'data' => $user,
        ];
        return $this->sendResponse($success);
    }
    
    public function show($id)
    {
		$userlogin = auth($this->guard)->user();
		
        $user = User::with('media')->find($id);
        if($user){
			
			$challange = Challange::with('challangeTag','users')
                ->select('challanges.*')
				->where('challanges.user_id',$id)
				->orderBy('challanges.id', 'DESC')
                ->get();
			$user->video = $challange;
			$user->video_count = count($challange);
			$user->followers = Follower::where('follow_for',$id)->count();
			$user->following = Follower::where('follow_by',$id)->count();
			$user->profile_like = "1k";
	
			$follow = Follower::where('follow_for',$id)->where('follow_by',$userlogin->id)->count();
			
			$user->follow_status = false;
			if($follow > 0){
				$user->follow_status = true;
			}
            $success = [
                'message' => 'Profile success',
                'success' => true,
                'data' => $user,
            ];
        } else {
            $success = [
                'message' => 'No data',
                'success' => false
            ];
        }
        return $this->sendResponse($success);
    }

    public function destroy()
    {
		$user = auth($this->guard)->user();	
        auth()->logout(true);
        $user->delete();
        return ['status' => 'ok'];
    }

    public function updatePassword(Request $request){
         $user = auth($this->guard)->user();
        //dd($user);
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success);    
        }

        if($user->email == $request['email']){
          $user->update(["password"=> \Hash::make($request['password'])]);
             $success = [
                'message' => "Password update successfully",
                'success' => true
            ];
            return $this->sendResponse($success); 
        }
        else{
            $success = [
                'message' => "Fail to update password.",
                'success' => false
            ];
            return $this->sendResponse($success); 
        }
    } 

    public function updateUserProfile(Request $request){
        $user = auth($this->guard)->user();
       
        if($request->hasFile('avatar')) { 
             $validator = Validator::make($request->all(), [
                'avatar' => 'required|mimes:jpeg,jpg,png',
             ]);
           
            if($validator->fails()){
                $message = $validator->errors()->first();
                $success = [
                    'message' => $message,
                    'success' => false
                ];
                return $this->sendResponse($success);    
            }  
           $file = $request->file('avatar');
           
            $image_path = $user->avatar;
            if(isset($image_path)){
               unlink(public_path($image_path));
            }
            // Define upload path
            $destinationPath = public_path('/userprofile/'); // upload path
            
                $input['avatar'] = time().rand().'.'.$file->getClientOriginalExtension();
                
                $thumbdestinationPath = public_path('/thumbnail/');
                $imgth = Image::make($file->getRealPath());
                $imgth->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($thumbdestinationPath.'/'.$input['avatar']);
                
                // Upload Orginal Image           
                $file->move($destinationPath, $input['avatar']);
                $avatar = '/userprofile/'.$input['avatar'];
        }
          $user->update(["avatar"=> isset($avatar)?$avatar:$user->avatar]);
             $success = [
                'message' => "Profilepic update successfully",
                'success' => true
            ];
            return $this->sendResponse($success); 
    }   

    public function update_Profile(Request $request){
        $user = auth($this->guard)->user();
       
	    $avatar = "";
        if($request->hasFile('avatar')) { 
			$validator = Validator::make($request->all(), [
				'avatar' => 'required|mimes:jpeg,jpg,png',
			]);

			if($validator->fails()){
			$message = $validator->errors()->first();
			$success = [
				'message' => $message,
				'success' => false
			];
			return $this->sendResponse($success);    
			}  
			$file = $request->file('avatar');

			$image_path = $user->avatar;
			if(isset($image_path)){
				unlink(public_path($image_path));
			}
			// Define upload path
			$destinationPath = public_path('/userprofile/'); // upload path

			$input['avatar'] = time().rand().'.'.$file->getClientOriginalExtension();

			$thumbdestinationPath = public_path('/thumbnail/');
			$imgth = Image::make($file->getRealPath());
			$imgth->resize(100, 100, function ($constraint) {
				$constraint->aspectRatio();
			})->save($thumbdestinationPath.'/'.$input['avatar']);

			// Upload Orginal Image           
			$file->move($destinationPath, $input['avatar']);
			$avatar = '/userprofile/'.$input['avatar'];
        }
		$userData = [];
		if(!empty($avatar)){
			$userData['avatar'] = $avatar;
		}
		if(isset($request->username) && !empty($request->username)){
			$userData['username'] = $request->username;
		}
		if(isset($request->country_name) && !empty($request->country_name)){
			$userData['country'] = $request->country_name;
		}
		if(isset($request->country_code) && !empty($request->country_code)){
			$userData['country_code'] = $request->country_code;
		}
		if(isset($request->description) && !empty($request->description)){
			$userData['description'] = $request->description;
		}
		if(isset($request->phone) && !empty($request->phone)){
			$userData['phone'] = $request->phone;
		}
		if(!empty($userData))	{
			$user->update($userData);
		}
		
		$success = [
			'message' => "Profile update successfully",
			'success' => true,
			'data' => $user
		];
		return $this->sendResponse($success); 
    }   

    public function updateUserToken(Request $request){
        $user = auth($this->guard)->user();
      
        $validator = Validator::make($request->all(), [
            'platform' => 'required',
            'push_token' => 'required',
        ]);

        if($validator->fails()){
        $message = $validator->errors()->first();
        $success = [
            'message' => $message,
            'success' => false
        ];
        return $this->sendResponse($success);    
        }
        $usertoken = UserTokens::where('user_id',$user->id)->where('platform',$request->get('platform'))->first();
        if($usertoken){
            $form_data = ["token"=>$request->get('push_token'),"platform"=>$request->get('platform')];
            $usertoken->update($form_data);
             $success = [
                'message' => "Token update successfully",
                'success' => true
            ];
            return $this->sendResponse($success); 
        }
        else{
             $form_data = ["user_id"=>$user->id,"token"=>$request->get('push_token'),"platform"=>$request->get('platform')];
              $userTokan =  UserTokens::create($form_data);
              if($userTokan){
                 $success = [
                    'message' => "Token update successfully",
                    'success' => true
                ];
                return $this->sendResponse($success); 
              }
              else{
                 $success = [
                    'message' => "Fail to update token.",
                    'success' => true
                ];
                return $this->sendResponse($success); 
              }  
        }
    }

    public function changepassword(Request $request){
        $user = auth($this->guard)->user();
      
        $validator = Validator::make($request->all(), [
            'new_password' => 'required',
            'old_password' => 'required',
        ]);

        if($validator->fails()){
        $message = $validator->errors()->first();
        $success = [
            'message' => $message,
            'success' => false
        ];
        return $this->sendResponse($success);    
        }
        if(!\Hash::check($request->get('old_password'),$user->password)){
            $success = [
                'message' => "Old password mismatch",
                'success' => false
            ];
           return $this->sendResponse($success); 
        }
		else
		{
             $user->update(["password"=> \Hash::make($request['new_password'])]);
             $success = [
                'message' => "Password Changed successfully",
                'success' => true
            ];
            return $this->sendResponse($success); 
        }
        
    }

       public function sendNotification()
    {
        $firebaseToken = User::whereNotNull('device_token')->pluck('device_token')->all();
          
        $SERVER_API_KEY = ;
  
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => "Hinotes",
                "body" => "Hi",  
            ]
        ];
        $dataString = json_encode($data);
    
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
               
        $response = curl_exec($ch);
  
        //dd($response);
    }
}
