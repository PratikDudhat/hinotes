<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Challange;
use App\Sponsers;
use App\Category;
use App\User;

class ChallangesController extends Controller
{
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:challanges-list|challanges-create|challanges-edit|challanges-delete', ['only' => ['index','show']]);
         $this->middleware('permission:challanges-create', ['only' => ['create','store']]);
         $this->middleware('permission:challanges-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:challanges-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         return view('admin.challanges.index');
    }

    public function challangesfilter(Request $request){
        $params = $request->all();
        $data = array();
          $columns = array(
            0 => 'id',
            1 => 'user_id',
            2 => 'category_id',
            3 => 'sponser_id',
            4 => 'thumbnail',
            5 => 'is_lock',
        );
            $list = Challange::skip($params['start']);
                            $list->take($params['length']);
                            $list->select('challanges.*','users.username','categories.category_name','sponsers.sponsers_name');
                            $list->leftjoin('users','users.id','=','challanges.user_id'); 
                            $list->leftjoin('categories','categories.id','=','challanges.category_id');
                            $list->leftjoin('sponsers','sponsers.id','=','challanges.sponser_id');
                            if(!empty($params['search']['value']) && $params['search']['value'] != "Public" && $params['search']['value'] != "Private"){
                            $list->where('users.username','like','%'.$params['search']['value'].'%');
                            $list->orWhere('categories.category_name','like','%'.$params['search']['value'].'%');
                            $list->orWhere('sponsers.sponsers_name','like','%'.$params['search']['value'].'%');
                            }
                            if(!empty($params['search']['value']) && $params['search']['value'] == "Public"){
                            $list->where('challanges.is_lock',0);
                            }
                            if(!empty($params['search']['value']) && $params['search']['value'] == "Private"){
                            $list->where('challanges.is_lock',1);
                            }
                            $list->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir']);
             $listrecords = $list->get();

            $listrecord =Challange::select('challanges.*','users.username','categories.category_name','sponsers.sponsers_name');
                            $listrecord->leftjoin('users','users.id','=','challanges.user_id'); 
                            $listrecord->leftjoin('categories','categories.id','=','challanges.category_id');
                            $listrecord->leftjoin('sponsers','sponsers.id','=','challanges.sponser_id');
                            if(!empty($params['search']['value']) && $params['search']['value'] != "Public" && $params['search']['value'] != "Private"){
                            $listrecord->where('users.username','like','%'.$params['search']['value']);
                            $listrecord->where('categories.category_name','like','%'.$params['search']['value']);
                            $listrecord->where('sponsers.sponsers_name','like','%'.$params['search']['value']);
                            }
                            if(!empty($params['search']['value']) && $params['search']['value'] == "Public"){
                            $listrecord->where('challanges.is_lock',0);
                            }
                            if(!empty($params['search']['value']) && $params['search']['value'] == "Private"){
                            $listrecord->where('challanges.is_lock',1);
                            }
             $totalRecords = $listrecord->count();
        $user = auth()->user();
        if(!empty($listrecords)){
            foreach ($listrecords as $value) {
                $show = "";
                 $edit = "";
                 $delete ="";
                 $show .= '<a class="btn btn-sm" href="'.url('/challanges/'.$value->id).'"><i class="fa fa-eye"></i></a>';
                 if ($user->can('challanges-edit')){
                    $edit .='<a href="'.url('/challanges/'.$value->id.'/edit').'" class="btn btn-circle btn-sm blue"><i class="fa fa-edit"></i></a>';;
                 } 
                  $url = \URL::route('challanges.destroy',$value->id);
                 if ($user->can('challanges-delete')){
                    $delete .='<a class="btn btn-sm waves-effect waves-light remove-record" data-toggle="modal" data-url="'.$url.'" data-id="'.$value->id.'" data-target="#custom-width-modal"><i class="fa fa-trash-o"></i></a>';
                }
                $row = array(); 
                $row[] = $value->id;
                if($value->username){
                    $row[] = $value->username;
                }else{
                    $row[] = "N/A";
                }
                if($value->category_name){
                    $row[] = $value->category_name;
                }else{
                    $row[] = "N/A";
                }
                if($value->sponsers_name){
                    $row[] = $value->sponsers_name;
                }else{
                    $row[] = "N/A";
                }
                $row[] =  '<img src="'.$value->thumbnail.'" width="50px">';
                if($value->is_lock == 0){
                $row[] = "Public";
                }else{
                   $row[] = "Private"; 
                }
                $row[] =  $show;
                $data[] = $row;
            } 
        }
        $json_data = array(
                "draw"            => intval( $params['draw'] ), 
                "recordsTotal"    => intval($totalRecords),  
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
         return view('admin.challanges.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /* $this->validate($request,[
            'title' => 'required',
            'tag_logo' =>  'required|mimes:png,jpg,jpeg',
        ]);
        
        if($request->hasFile('tag_logo')) { 
           $file = $request->file('tag_logo');
           $fullName=  time().'.'.$file->getClientOriginalExtension();;
           $path = '/tag_logo/'.$fullName;
           $fileName = $file->move(public_path('tag_logo/'),$fullName);
        }
         
        $form_data = ["title" =>$request["title"],"tag_logo"=>$path];
        Challange::create($form_data);*/
        
        return redirect()->route('challanges.index')->with('success','Challanges created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         $challanges = Challange::find($id);
         $user = User::select('username')->where('id',$challanges->user_id)->first();
         $category = Category::select('category_name')->where('id',$challanges->category_id)->first();
         $sponsers = Sponsers::select('sponsers_name')->where('id',$challanges->sponser_id)->first();
         return view('admin.challanges.show',compact('challanges','user','category','sponsers'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $challanges = Challange::find($id);
        return view('admin.challanges.edit',compact('challanges'));
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
      /*  $this->validate($request,[
            'title' => 'required',
        ]);
        $challanges = Challange::find($id);
        if($request->hasFile('tag_logo')) {   
            $this->validate($request,[
                'tag_logo' =>  'required|mimes:png,jpg,jpeg',
            ]);
            if(!empty($challanges->tag_logo)){
                $clogo = public_path($challanges->tag_logo);
                unlink($clogo);   
            }
            
            $file = $request->file('tag_logo');
            $fullName=  time().'.'.$file->getClientOriginalExtension();
            $path = '/tag_logo/'.$fullName;
            $fileName = $file->move(public_path('tag_logo/'),$fullName);
        }
        if(!empty($path)){
            $form_data = ["title" =>$request["title"],"tag_logo"=>$path];
            $challanges->update($form_data);
        } else{
            $challanges->update($request->all());
        }*/
        return redirect()->route('challanges.index')
                    ->with('success','Challanges updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       $challanges = Challange::find($id)->delete();
         return redirect()->route('challanges.index')
                        ->with('success','Challanges deleted successfully');
    }
}
