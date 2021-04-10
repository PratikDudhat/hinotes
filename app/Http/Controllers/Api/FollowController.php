<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Follower;
use App\User;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use Validator;

class FollowController extends ResponseController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api',['except'=>['suggested_users']]);
        $this->guard = "api";
    }

     public function follows(Request $request){
        $user = auth($this->guard)->user();
        $validator = Validator::make($request->all(), [
            'follow_for' => 'required',
        ]);
		
        if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success);    
        }
		
		if($request->get('follow_for') == $user->id){
			$message = "You can't follow yourself";
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success);    
		}
        $follow = Follower::where("follow_for",$request->get('follow_for'))->where('follow_by',$user->id)->first();
        if(!$follow){
            $form_data = ["follow_for"=>$request->get('follow_for'),"follow_by"=>$user->id];
            Follower::create($form_data);
            $success = [
                'message' =>"Followed successfully.",
                'success' => true,
            ];
           return $this->sendResponse($success);
        }
        else
        {
            $follow->delete();
            $success = [
                'message' =>"Unfollowed successfully.",
                'success' => true,
            ];
           return $this->sendResponse($success);
        }
    }
	
	public function suggested_users(Request $request)
	{
		$limit = 10;
		$nextpage = (($request->page??1)+1);
		$latitude = $request->get('latitude');
		$longitude = $request->get('longitude');
		
		$user = auth($this->guard)->user();
		
		$circle_radius = 100;
		if(!empty($latitude) && !empty($longitude)){
				//DB::enableQueryLog();
				if(isset($user->id) && !empty($user->id))
				{
					$user = User::select(DB::raw("users.id,users.username,users.avatar, ( 3959 * acos( cos( radians('$latitude') ) * cos( radians( users.latitude ) ) * cos( radians( users.longitude ) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians( users.latitude ) ) ) ) AS distance"))
					->withCount(['followers'])
					->where('users.id','!=',$user->id)
					->havingRaw('distance < '.$circle_radius)
					->orderBy('distance','ASC')
					->limit($limit)->offset((($request->page??1)-1)*$limit)
					->get();
				} else{
					$user = User::select(DB::raw("users.id,users.username,users.avatar, ( 3959 * acos( cos( radians('$latitude') ) * cos( radians( users.latitude ) ) * cos( radians( users.longitude ) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians( users.latitude ) ) ) ) AS distance"))
					->withCount(['followers'])
					->havingRaw('distance < '.$circle_radius)
					->orderBy('distance','ASC')
					->limit($limit)->offset((($request->page??1)-1)*$limit)
					->get();
				}
				
				//dd(DB::getQueryLog());
				if(($user->isEmpty()))
				{
					$success = [
						'message' => "No User",
						'nextpage' => $nextpage,
						'success' => true,
						'data' =>  []
					];
				}
				else
				{
					$success = [
						'message' => "Search User successfully",
						'nextpage' => $nextpage,
						'success' => true,
						'data' =>  $user
					];
				}
		} else {
			$success = [
				'message' => "No User",
				'nextpage' => $nextpage,
				'success' => true,
				'data' =>  []
			];
		}
        return $this->sendResponse($success);
    }
	
	public function search(Request $request,$name)
	{
		$user = auth($this->guard)->user();
		if(isset($request->search_user_id) && !empty($request->search_user_id)){
			$user_id = $request->search_user_id;
		} else{
			$user_id = $user->id;
		}
		$limit = 10;
		$nextpage = (($request->page??1)+1);
		$listtype = $request->listtype;
		if(!empty($name)){
			if($listtype == 'follow'){
			$user = User::select('users.id','users.username','users.avatar', \DB::raw('CONCAT("http://192.241.137.149","",users.avatar) as profileUrl'),\DB::raw('COUNT(fs.id) as follow_status'),\DB::raw('COUNT(fw.id) as following_status'))
				->join('followers','followers.follow_by','=','users.id')
				->leftJoin("followers as fs",function($join) use ($user){
				$join->on("fs.follow_for","=","users.id")
					->where('fs.follow_by',$user->id);
				})
				->leftJoin("followers as fw",function($join) use ($user){
				$join->on("fw.follow_by","=","users.id")
					->where('fw.follow_for',$user->id);
				})
				->withCount(['followers'])
				->where('users.username','like','%'.$name.'%')
				->where('followers.follow_for',$user_id)
				->where('users.id','!=',$user_id)
				->groupBy('users.id')
				->limit($limit)->offset((($request->page??1)-1)*$limit)
				->get();
			}
			if($listtype == 'following'){
				$user = User::select('users.id','users.username','users.avatar', \DB::raw('CONCAT("http://192.241.137.149","",users.avatar) as profileUrl'),\DB::raw('COUNT(fs.id) as follow_status'),\DB::raw('COUNT(fw.id) as following_status'))
				->join('followers','followers.follow_for','=','users.id')
				->leftJoin("followers as fs",function($join) use ($user){
				$join->on("fs.follow_for","=","users.id")
					->where('fs.follow_by',$user->id);
				})
				->leftJoin("followers as fw",function($join) use ($user){
				$join->on("fw.follow_by","=","users.id")
					->where('fw.follow_for',$user->id);
				})
				->withCount(['followers'])
				->where('users.username','like','%'.$name.'%')
				->where('followers.follow_by',$user_id)
				->where('users.id','!=',$user_id)
			   ->groupBy('users.id')
			   ->limit($limit)->offset((($request->page??1)-1)*$limit)
			   ->get();
			}
			
			$success = [
				'message' => "Search User successfully",
				'success' => true,
				'nextpage' => $nextpage,
				'data' =>  $user
			];
		} else {
			$success = [
				'message' => "No User",
				'success' => false
			];
		}
        return $this->sendResponse($success);
    }
	
    public function following_list_by_user(Request $request)
	{	
		$user_id = $request->get('user_id');
		$user = auth($this->guard)->user();
		
		$limit = 10;
		$nextpage = (($request->page??1)+1);
		$following = User::select('users.id','users.username','users.avatar', \DB::raw('CONCAT("http://192.241.137.149","",users.avatar) as profileUrl'),\DB::raw('COUNT(fs.id) as follow_status'),\DB::raw('COUNT(fw.id) as following_status'))
			->join('followers','followers.follow_for','=','users.id')
			->leftJoin("followers as fs",function($join) use ($user){
				$join->on("fs.follow_for","=","users.id")
					->where('fs.follow_by',$user->id);
			})
			->leftJoin("followers as fw",function($join) use ($user){
				$join->on("fw.follow_by","=","users.id")
					->where('fw.follow_for',$user->id);
			})
			->withCount(['followers'])
			->where('followers.follow_by',$user_id)
			->where('users.id','!=',$user_id)
			->groupBy('users.id')
			->limit($limit)->offset((($request->page??1)-1)*$limit)
			->get();
			
		$success = [
			'message' => "Following",
			'success' => true,
			'nextpage' => $nextpage,
			'data' =>  $following
		];
        return $this->sendResponse($success); 
    }

    public function follows_list_by_user(Request $request)
	{
		$user_id = $request->get('user_id');
		$user = auth($this->guard)->user();
		$limit = 10;
		$nextpage = (($request->page??1)+1);
		$followers = User::select('users.id','users.username','users.avatar', \DB::raw('CONCAT("http://192.241.137.149","",users.avatar) as profileUrl'),\DB::raw('COUNT(fs.id) as follow_status'),\DB::raw('COUNT(fw.id) as following_status'))
			->join('followers','followers.follow_by','=','users.id')
			->leftJoin("followers as fs",function($join) use ($user){
				$join->on("fs.follow_for","=","users.id")
					->where('fs.follow_by',$user->id);
			})
			->leftJoin("followers as fw",function($join) use ($user){
				$join->on("fw.follow_by","=","users.id")
					->where('fw.follow_for',$user->id);
			})
			->withCount(['followers'])
			->where('followers.follow_for',$user_id)
			->where('users.id','!=',$user_id)
			->groupBy('users.id')
			->limit($limit)->offset((($request->page??1)-1)*$limit)
			->get();
		
		$success = [
			'message' => "My Followers",
			'success' => true,
			'nextpage' => $nextpage,
			'data' =>  $followers
		];
        return $this->sendResponse($success); 
    }

    public function search_suggested_users(Request $request)
	{
		$limit = 10;
		$nextpage = (($request->page??1)+1);
		$latitude = $request->get('latitude');
		$longitude = $request->get('longitude');
		$name = $request->get('name');
		$user = auth($this->guard)->user();
		$circle_radius = 100;
		if(!empty($latitude) && !empty($longitude)){
				//DB::enableQueryLog();
				if( !empty($user->id))
				{
					$users = User::select(DB::raw("users.id,users.username,users.avatar, ( 3959 * acos( cos( radians('$latitude') ) * cos( radians( users.latitude ) ) * cos( radians( users.longitude ) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians( users.latitude ) ) ) ) AS distance"));
							$users->withCount(['followers']);
							if(!empty($name)){
							$users->where('users.username','like','%'.$name.'%');	
							}
							$user->where('users.id','!=',$user->id);
							$users->havingRaw('distance < '.$circle_radius);
							$users->orderBy('distance','ASC');
							$users->limit($limit)->offset((($request->page??1)-1)*$limit);
				   $userlist = $users->get();
				  
				} else{
					$users = User::select(DB::raw("users.id,users.username,users.avatar, ( 3959 * acos( cos( radians('$latitude') ) * cos( radians( users.latitude ) ) * cos( radians( users.longitude ) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians( users.latitude ) ) ) ) AS distance"));
							$users->withCount(['followers']);
							if(!empty($name)){
							$user->where('users.username','like','%'.$name.'%');	
							}
							$users->havingRaw('distance < '.$circle_radius);
							$users->orderBy('distance','ASC');
							$users->limit($limit)->offset((($request->page??1)-1)*$limit);
				   $userlist = $users->get();
				}
				
				if(($userlist->isEmpty()))
				{
					$success = [
						'message' => "No User",
						'success' => false,
						'data' =>  []
					];
				}
				else
				{
					$success = [
						'message' => "Search User successfully",
						'nextpage' => $nextpage,
						'success' => true,
						'data' =>  $userlist
					];
				}
		} else {
			$success = [
				'message' => "No User",
				'success' => false,
				'data' =>  []
			];
		}
        return $this->sendResponse($success);
    }
}
