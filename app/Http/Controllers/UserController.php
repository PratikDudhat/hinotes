<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\SocialUsers;
use Spatie\Permission\Models\Role;
use DB;
use Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = User::where('user_type','!=','superadmin')->orderBy('id','DESC')->get();
        return view('users.index',compact('data'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function userfilter(Request $request){
        $params = $request->all();
        $data = array();
          $columns = array(
            0 => 'id',
            1 => 'username',
            3 => 'email',
            4 => 'avatar',
            5 => 'country_code',
            6 => 'phone',
            7 => 'email_verified',
            8 => 'role',
        );
         $list = User::skip($params['start']);
                        $list->take($params['length']);
                        if(!empty($params['search']['value'])){
                            if($params['search']['value'] == "verified"){
                            $list->where('email_verified',1);
                            }else if($params['search']['value'] == "unverified"){
                            $list->where('email_verified',0);
                            }else{
                            $list->where('username','like','%'.$params['search']['value'].'%');    
                            $list->orWhere('email','like','%'.$params['search']['value'].'%');    
                            $list->orWhere('country_code','like','%'.$params['search']['value'].'%');    
                            $list->orWhere('phone','like','%'.$params['search']['value'].'%');    
                            }
                        }
                        $list->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir']);
         $listrecords = $list->get();
        $listrecord = User::orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir']);
                        if(!empty($params['search']['value'])){
                            if($params['search']['value'] == "verified"){
                            $listrecord->where('email_verified',1);
                            }else if($params['search']['value'] == "unverified"){
                            $listrecord->where('email_verified',0);
                            }else{
                            $listrecord->where('username','like','%'.$params['search']['value'].'%');    
                            $listrecord->orWhere('email','like','%'.$params['search']['value'].'%');    
                            $listrecord->orWhere('country_code','like','%'.$params['search']['value'].'%');    
                            $listrecord->orWhere('phone','like','%'.$params['search']['value'].'%');    
                            }
                        }
        $totalRecords = $listrecord->count();
        $user = auth()->user();
        if(!empty($listrecords)){
            foreach ($listrecords as $value) {
                 $show = "";
                 $edit = "";
                 $delete ="";
                 $show .= '<a class="btn btn-sm" href="'.url('/users/'.$value->id).'"><i class="fa fa-eye"></i></a>';
                if ($user->can('user-edit')){
                    $edit .='<a href="'.url('/users/'.$value->id.'/edit').'" class="btn btn-circle btn-sm blue"><i class="fa fa-edit"></i></a>';;
                } 
                  $url = \URL::route('users.destroy',$value->id);
               if ($user->can('user-delete')){
                    $delete .='<a class="btn btn-sm waves-effect waves-light remove-record" data-toggle="modal" data-url="'.$url.'" data-id="'.$value->id.'" data-target="#custom-width-modal"><i class="fa fa-trash-o"></i></a>';
                }
                $row = array(); 
				if(!empty($value->avatar)){
					$row[] = '<td class="avatar"><div class="round-img"><img class="rounded-circle" src="'.$value->avatar.'" style="width: 50px;height:50px;" alt=""></div></td>';
				} else{
					$row[] = '<td class="avatar"><div class="round-img"><img class="rounded-circle" src="#" alt=""></div></td>';
				}
                $row[] = $value->username;
                $row[] = $value->email;
                $row[] = $value->country_code;
                $row[] = $value->phone;
                if( $value->email_verified == 1){
                $row[] = "verified";
                }
                else{
                 $row[] = "unverified";  
                }
                $row[] = $edit."".$delete;
                $data[] = $row;
            } 
        }
        $json_data = array(
                "draw"            => intval( $params['draw'] ), 
                 "recordsTotal"    => intval( $totalRecords ),  
                "recordsFiltered" => intval($totalRecords),  
                "data"            => $data
                );
        echo json_encode($json_data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name','name')->all();
        $countries = DB::table('countries')->get();
        return view('users.create',compact('roles','countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
			'username' => 'required',
            'phone' => 'required',
            'country_code' => 'required',
			'avatar' =>  'required|mimes:jpeg,jpg,png,svg',
            'roles' => 'required',
        ]);

        $input = $request->all();
		if($request->hasFile('avatar')) {   
		   $file = $request->file('avatar');
		   $fullName=  time().'.'.$file->getClientOriginalExtension();;
		  // $fullName= $file->getClientOriginalName();
		  $avatar = '/userprofile/'.$fullName;
		   $fileName = $file->move(public_path('userprofile/'),$fullName);
		}
		$input['avatar'] = isset($avatar)?$avatar:"";
		$roles = $request->input('roles');
		foreach($roles as $r){
			if($r == "Admin"){
				$input['user_type'] = "admin";
			}
			if($r == "User"){
				$input['user_type'] = "user";
			}
			if($r == "Super Admin"){
				$input['user_type'] = "superadmin";
			}
		}
		
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $user->assignRole($request->input('roles'));
	
        return redirect()->route('users.index')
                        ->with('success','User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return view('users.show',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::with('social')->find($id);
        $roles = Role::pluck('name','name')->all();
		$countries = DB::table('countries')->get();
        $userRole = $user->roles->pluck('name','name')->all();
		//dd($user);
        return view('users.edit',compact('user','roles','userRole','countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'username' => 'required',
            'phone' => 'required',
            'country_code' => 'required',
            'roles' => 'required',
        ]);

        $input = $request->all();
		//dd($input);
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = array_except($input,array('password'));    
        }
		
		$user = User::find($id);
		if(!empty($input['social_id']) && !empty($input['social_platform'])){
			$socialuser = SocialUsers::select('id','social_id','social_platform')->where("user_id",$id)->get();
			
			foreach($socialuser as $key=>$value){
				$form_data = ["social_id"=>$input["social_id"][$key],"social_platform"=>$input["social_platform"][$key]];
				$value->update($form_data);
			}
		}
		if($request->hasFile('avatar')) {   
		$this->validate($request,[
            'avatar' =>  'required|mimes:jpeg,jpg,png,svg',
        ]);
		   $file = $request->file('avatar');
		   $fullName=  time().'.'.$file->getClientOriginalExtension();;
		  // $fullName= $file->getClientOriginalName();
		   $path = '/userprofile/'.$fullName;
		   $fileName = $file->move(public_path('userprofile/'),$fullName);
		}
		$roles = $request->input('roles');
		foreach($roles as $r){
			if($r == "Admin"){
				$input['user_type'] = "admin";
			}
			if($r == "User"){
				$input['user_type'] = "user";
			}
			if($r == "Super Admin"){
				$input['user_type'] = "superadmin";
			}
		}
		$user_data = ["name" => $input["name"],"email" => $input["email"],"username" => $input["username"],"phone" => $input["phone"],"country_code" => $input["country_code"],"user_type" => $input["user_type"],"password" => isset($input["password"])?$input["password"]:$user->password,"avatar" => isset($path)?$path:$user->avatar];
		$user->update($user_data);

	
        DB::table('model_has_roles')->where('model_id',$id)->delete();

        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')->with('success','User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
		SocialUsers::where('user_id',$user->id)->delete();
		$user->delete();
        return redirect()->route('users.index')
                        ->with('success','User deleted successfully');
    }
}