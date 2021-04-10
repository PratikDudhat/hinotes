<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Challange;
use App\ChallangeTag;
use App\Tag;
use App\Category;
use App\User;
use App\JoinChallange;
use App\ChallangeLike;
use App\ChallangeVote;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use Validator;

class ChallangesController extends ResponseController
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

    public function updatetag(){
		/* $challanges = Challange::select('id')->get();
		$challangestag = ChallangeTag::select('challange_id')->groupBy('challange_id')->get();
       
		$chid = $chtid = [];
		foreach($challanges as $value){
			$chid[] = $value->id;
		}
		foreach($challangestag as $value){
			$chtid[] = $value->challange_id;
		}
		$result = array_diff($chid,$chtid);
		
		foreach($result as $id){
			//Challange::where('id',$id)->delete();
		} */
		
		/* $tags = [1,2,3,4,5,6,7,8,9,10,11,12,13,14];
		foreach($challanges as $challange){
				$random = rand(0,3);
				shuffle($tags);
				for($i=0;$i<$random;$i++){
					$form_data1 = ["challange_id" => $challange->id,"tags_id" => $tags[$i]];
					$challangetag = ChallangeTag::create($form_data1);
				}
		} */
		$success = [
			'message' =>"successfully.",
			'success' => true,
		];
        return $this->sendResponse($success);
    }
	
	public function index(){
		$challange = Challange::with('challangeTag','users')
					->select('challanges.*','categories.category_name')
					->join('categories','categories.id','=','challanges.category_id')
					->orderBy('challanges.id', 'DESC')
					->paginate(10);
       
			$success = [
                'message' =>"Challange get successfully.",
                'data' => $challange,
                'success' => true,
            ];
        return $this->sendResponse($success);
    }
	
	public function challangebycategory(Request $request){
		
		$category_id = $request->get('category_id');
		//\DB::enableQueryLog();

		$limit = 10;
		$challange = Tag::select('tags.*')
				  ->with(['challangevideo' => function ($query) use ($category_id) {
                        $query->with('users','challangeTag') 
                        ->select('challanges.*')
                        ->where('challanges.category_id', $category_id);
                  }])
				  ->whereHas('challangevideo' , function ($query ) use ($category_id) {
                        $query 
                        ->select('challanges.*')
                        ->where('challanges.category_id', $category_id);
                  })
				  ->limit($limit)->offset((($request->page??1)-1)*$limit)
				  ->get()
				  ->transform(function($query){ 
						$query->video = $query->challangevideo->take(9); // take only 10 query
						$query->userjoin = $query->challangevideo->count();
						unset($query->challangevideo);
						return $query; 
					}
				);
		$success = [
			'message' =>"Challange successfully.",
			'success' => true,
			'nextpage' => ($request->page+1),
			'data' => $challange,
		];
        return $this->sendResponse($success);
    }

    public function load_more_tag_by_video(Request $request)
    {
        $category_id = $request->get('category_id');
        $tags_id = $request->get('tags_id');
        $custom = collect(['message' => 'Category by video','success'=>true]);

        $challange = Challange::select('challanges.id','challanges.thumbnail','challanges.video_file','challanges.totalvotes','challanges.totallikes','challanges.followers')
                        ->leftjoin('challange_tags','challange_tags.challange_id','=','Challanges.id')
                        ->leftjoin('tags','challange_tags.tags_id','=','tags.id')
                        ->leftjoin('categories','categories.id','=','Challanges.category_id')
                        ->where('Challanges.category_id',$category_id)
                        ->where('challange_tags.tags_id',$tags_id)
                        ->paginate(10);
      $success = $custom->merge($challange);
        return $this->sendResponse($success);
    }

    public function discover_challange(Request $request)
	{
		// \DB::enableQueryLog();
		/* $challange = Challange::select('tags.id','tags.title', \DB::raw('CONCAT("http://192.241.137.149","",tags.tag_logo) as tag_logo'),'challanges.id as challanges_id','challanges.video_file','challanges.thumbnail','challanges.is_lock','challanges.latitude','challanges.longitude','challanges.totalvotes','challanges.totallikes','challanges.created_at','challanges.updated_at','challanges.deleted_at','sponsers.sponsers_name as sponser','sponsers.sponsers_logo',\DB::raw('COUNT(challange_tags.id) as userjoin'))
		->join('challange_tags','challange_tags.challange_id','=','challanges.id')
		->join('tags','tags.id','=','challange_tags.tags_id')
		->join('sponsers','sponsers.id','=','challanges.sponser_id')
		->groupBy('challange_tags.tags_id')	
		->orderBy('challanges.totalvotes','DESC')
		->get(); */
		
		$user = auth($this->guard)->user();
		if(!empty($request->get('latitude')) && !empty($request->get('longitude'))){
			User::where('id',$user->id)->update(['latitude'=>$request->get('latitude'),'longitude'=>$request->get('longitude')]);
		}
		
		$challange = Tag::select('tags.id','tags.title', \DB::raw('CONCAT("http://192.241.137.149","",tags.tag_logo) as tag_logo'),'challanges.id as challanges_id','challanges.totalvotes','challanges.totallikes','tags.created_at','tags.updated_at','tags.deleted_at','sponsers.sponsers_name as sponser','sponsers.sponsers_logo',\DB::raw('COUNT(challange_tags.id) as userjoin'))
		->join('challange_tags','challange_tags.tags_id','=','tags.id')
		->join('challanges','challanges.id','=','challange_tags.challange_id')
		->join('sponsers','sponsers.id','=','challanges.sponser_id')
		->groupBy('challange_tags.tags_id')	
		->orderBy('challanges.totalvotes','DESC')
		->get();
		//dd(\DB::getQueryLog());
		
        $success = [
            'message' =>"Discover Challanges.",
            'success' => true,
            'data' => $challange,
        ];
        return $this->sendResponse($success);
    }
	
	public function tag_by_challange(Request $request)
	{
        $validator = Validator::make($request->all(), [
            'tags_id' => 'required',
        ]);
		
		$limit = 12;
		$tags = Tag::select('tags.id','tags.title', \DB::raw('CONCAT("http://192.241.137.149","",tags.tag_logo) as tag_logo'),'tags.created_at','tags.updated_at',\DB::raw('COUNT(challange_tags.id) as userjoin'))->join('challange_tags','challange_tags.tags_id','=','tags.id')->where('challange_tags.deleted_at',null)->where('tags.id',$request->get('tags_id'))->groupBy('challange_tags.tags_id')->first();
        
		$challange = Challange::with('challangeTag','users')->select('challanges.*')
                    ->join('challange_tags','challange_tags.challange_id','=','challanges.id')
					->where('challange_tags.tags_id',$request->get('tags_id'))
                    ->groupBy('challanges.id')
					->orderBy('challanges.totalvotes','DESC')
					->limit($limit)->offset((($request->page??1)-1)*$limit)
					->get();
				  
		$tags->video = $challange;	  
		
        $success = [
            'message' =>"Tag Challanges",
            'success' => true,
            'nextpage' => ($request->page+1),
            'data' => $tags,
        ];
        return $this->sendResponse($success);
    }
	
    public function createChallange(Request $request)
    {
        $user = auth($this->guard)->user();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'tags_id' => 'required',
            'thumbnail' => 'required|mimes:jpeg,jpg,png',
            'video_file' =>  'required|mimes:mp4,3gp,mov,avi',
            'is_lock' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        if($request->get('is_lock') == 1){
             $validator = Validator::make($request->all(), [
                'password' => 'required',
             ]);
        }
        $password = \Hash::make($request->get('password'));
        if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success);    
        }
        if($request->hasFile('thumbnail')) {  
			$file1 = $request->file('thumbnail');
			$new_name1 = time().rand().'.'.$file1->getClientOriginalExtension();
			$thumbnail = '/Videos/thumbnail/'.$new_name1;
			$fileName1 = $file1->move(public_path('Videos/thumbnail/'),$new_name1);
		}  
        if($request->hasFile('video_file')) {  
			$file = $request->file('video_file');
			$new_name = time().rand().'.'.$file->getClientOriginalExtension();
			$video = '/Videos/'.$new_name;
			$fileName = $file->move(public_path('Videos/'),$new_name);
		}    
		
		$userData= [];
        $userData['user_id'] = $user->id;
        if(!empty($request->get('category_id'))){
            $userData['category_id'] = $request->get('category_id');
        }
        if(!empty($thumbnail)){
            $userData['thumbnail'] = $thumbnail;
        }
        if(!empty($video)){
            $userData['video_file'] = $video;
        }
        if(!empty($request->get('password'))){
            $userData['password'] = \Hash::make($request->get('password'));
        }
        if(!empty($request->get('is_lock'))){
            $userData['is_lock'] = $request->get('is_lock');
        }
        if(!empty($request->get('latitude'))){
            $userData['latitude'] = $request->get('latitude');
        }
        if(!empty($request->get('longitude'))){
            $userData['longitude'] = $request->get('longitude');
        }
		$challange = Challange::create($userData);

		$tags = explode(',', $request->get('tags_id'));
		foreach($tags as $tag){
			$form_data1 = ["challange_id" => $challange->id,"tags_id" => $tag];
			$challangetag = ChallangeTag::create($form_data1);
		}

		$challange->challangetag = ChallangeTag::select('challange_tags.id','challange_tags.tags_id','tags.title')->join('tags','challange_tags.tags_id','=','tags.id')->where('challange_id',$challange->id)->get();
		
		$success = [
			'message' =>"Challange created successfully.",
			'data' => $challange,
			'success' => true,
		];
        return $this->sendResponse($success);
    }
     
    public function verify_challenge(Request $request)
    {
		$validator = Validator::make($request->all(), [
            'challange_id' => 'required',
            'password' => 'required',
        ]);
		$challange = Challange::where('id',$request->get('challange_id'))->first();
        if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success);    
        }
		
		if(\Hash::check($request->get('password'), $challange->password)) { 
			$success = [
				'message' =>"Password verified",
				'success' => true,
			];
		} else {
			$success = [
				'message' =>"Invalid password",
				'success' => false,
			];
		}
		return $this->sendResponse($success);
	}
		
	public function updateChallange(Request $request, $id)
    {
        $user = auth($this->guard)->user();
        $oldchallange = Challange::where('id',$id)->where('user_id',$user->id)->first();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'tags_id' => 'required',
            'is_lock' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        if($request->get('is_lock') == 1){
             $validator = Validator::make($request->all(), [
                'password' => 'required',
             ]);
        }
        if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success);    
        }
        if(!empty($request->get('password'))){
             $password = \Hash::make($request->get('password'));
        }
        if($request->hasFile('thumbnail')) {  
             $validator = Validator::make($request->all(), [
               'thumbnail' => 'required|mimes:jpeg,jpg,png',
            ]); 
            if($validator->fails()){
                $message = $validator->errors()->first();
                $success = [
                    'message' => $message,
                    'success' => false
                ];
                return $this->sendResponse($success);    
            } 
           $file1 = $request->file('thumbnail');
           $new_name1 = time().rand().'.'.$file1->getClientOriginalExtension();
           //$fullName= $file->getClientOriginalName();
           $thumbnail = '/Videos/thumbnail/'.$new_name1;
       
           $fileName1 = $file1->move(public_path('Videos/thumbnail/'),$new_name1);
       }  
        if($request->hasFile('video_file')) {
            $validator = Validator::make($request->all(), [
               'video_file' => 'required|mimes:mp4,3gp,mov,avi', 
            ]); 
            if($validator->fails()){
                $message = $validator->errors()->first();
                $success = [
                    'message' => $message,
                    'success' => false
                ];
                return $this->sendResponse($success);    
            } 
           $file = $request->file('video_file');
           $new_name = time().rand().'.'.$file->getClientOriginalExtension();
           $video = '/Videos/'.$new_name;
       
           $fileName = $file->move(public_path('Videos/'),$new_name);
       }    
      
            $userData= [];
            $userData['user_id'] = $user->id;
            if(!empty($request->get('category_id'))){
                $userData['category_id'] = $request->get('category_id');
            }
            if(!empty($thumbnail)){
                $userData['thumbnail'] = $thumbnail;
            }
            if(!empty($video)){
                $userData['video_file'] = $video;
            }
            if(!empty($request->get('password'))){
                $userData['password'] = \Hash::make($request->get('password'));
            }
            $userData['is_lock'] = $request->get('is_lock');
            if(!empty($request->get('latitude'))){
                $userData['latitude'] = $request->get('latitude');
            }
            if(!empty($request->get('longitude'))){
                $userData['longitude'] = $request->get('longitude');
            }
            $challange = Challange::where('id',$id)->update($userData);
            ChallangeTag::where('challange_id',$challange)->delete();
            $tags = explode(',', $request->get('tags_id'));
            foreach($tags as $tag){
             $form_data1 = ["challange_id" => $challange,
                            "tags_id" => $tag,
                        ];
            $challangetag = ChallangeTag::where('challange_id',$challange)->create($form_data1);
            }
           ChallangeTag::select('id','tags_id')->where('challange_id',$challange)->get();
           if(!$challange){
             $success = [
                    'message' =>"Challange not found",
                    'success' => false
                ];
           }
            $success = [
                'message' =>"Challange updated successfully.",
                'success' => true,
            ];
        return $this->sendResponse($success);
    } 

    public function joinChallange(Request $request){
        $user = auth($this->guard)->user();
        $validator = Validator::make($request->all(), [
            //'status' => 'required',
			'challange_id' => 'required',
        ]);
         if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success);    
        }
        $form_data = ["user_id" => $user->id,"status" => $request->get('status')];
        $joinchallange = JoinChallange::create($form_data);
         if(!$joinchallange){
             $success = [
                    'message' =>"Issue while Join Challenge.",
                    'success' => false
                ];
        } else {
            $success = [
                'message' =>"Challange Join successfully.",
                'success' => true,
            ];
		}
        return $this->sendResponse($success);
    }

    public function addlikechallange(Request $request){
        $user = auth($this->guard)->user();
        $validator = Validator::make($request->all(), [
            'challange_id' => 'required',
        ]);
         if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success);    
        }
        $like = ChallangeLike::where("challange_id",$request->get('challange_id'))->where('like_by',$user->id)->first();
        if(!$like){
            $form_data = ["challange_id"=>$request->get('challange_id'),"like_by"=>$user->id,"status"=>1];
            ChallangeLike::create($form_data);
			Challange::where('id',$request->get('challange_id'))->increment('totallikes');
            $success = [
                'message' =>"Liked successfully.",
                'success' => true,
            ];
           return $this->sendResponse($success);
        }
        else
        {
			Challange::find($like->challange_id)->decrement('totallikes');
            $like->delete();
            $success = [
                'message' =>"Unlike successfully.",
                'success' => true,
            ];
           return $this->sendResponse($success);
        }
    }

    public function votechallange(Request $request){
       $user = auth($this->guard)->user();
        $validator = Validator::make($request->all(), [
            'challange_id' => 'required',
        ]);
         if($validator->fails()){
            $message = $validator->errors()->first();
            $success = [
                'message' => $message,
                'success' => false
            ];
            return $this->sendResponse($success);    
        }
        $votes = ChallangeVote::where("challange_id",$request->get('challange_id'))->where('vote_by',$user->id)->first();
        if(!$votes){
            $form_data = ["challange_id"=>$request->get('challange_id'),"vote_by"=>$user->id,"status"=>1];
            ChallangeVote::create($form_data);
			Challange::where('id',$request->get('challange_id'))->increment('totalvotes');
            $success = [
                'message' =>"Voted successfully.",
                'success' => true,
            ];
           return $this->sendResponse($success);
        }
        else
        {
			if(Challange::where('id',$request->get('challange_id'))->first()){
				Challange::find($votes->challange_id)->decrement('totalvotes',1);
				$votes->delete();
				$success = [
					'message' =>"Vote remove successfully.",
					'success' => true,
				];
			} else{
				$success = [
					'message' =>"Challange Not found.",
					'success' => false,
				];
			}
           return $this->sendResponse($success);
        }
    }
}
