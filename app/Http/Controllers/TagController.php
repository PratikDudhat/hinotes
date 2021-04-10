<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tag;

class TagController extends Controller
{
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:tags-list|tags-create|tags-edit|tags-delete', ['only' => ['index','show']]);
         $this->middleware('permission:tags-create', ['only' => ['create','store']]);
         $this->middleware('permission:tags-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:tags-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
       return view('admin.tags.index');
    }

     public function tagfilter(Request $request){
        $params = $request->all();
        $data = array();
          $columns = array(
            0 => 'id',
            1 => 'title',
        );
          $list = Tag::skip($params['start']);
                        $list->take($params['length']);
                        $list->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir']);
                        if(!empty($params['search']['value'])){
                        $list->where('title','like','%'.$params['search']['value'].'%');
                        }
         $listrecords = $list->get();
         
         $listrecord =Tag::orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir']);
                        if(!empty($params['search']['value'])){
                        $listrecord->where('title','like','%'.$params['search']['value'].'%');
                        }
        $totalRecords = $listrecord->count();
        $user = auth()->user();
        if(!empty($listrecords)){
            foreach ($listrecords as $value) {
                $show = "";
                 $edit = "";
                 $delete ="";
                 $show .= '<a class="btn btn-sm" href="'.url('/tags/'.$value->id).'"><i class="fa fa-eye"></i></a>';
               if ($user->can('tags-edit')){
                    $edit .='<a href="'.url('/tags/'.$value->id.'/edit').'" class="btn btn-circle btn-sm blue"><i class="fa fa-edit"></i></a>';;
                } 
                  $url = \URL::route('tags.destroy',$value->id);
                 if ($user->can('tags-delete')){
                    $delete .='<a class="btn btn-sm waves-effect waves-light remove-record" data-toggle="modal" data-url="'.$url.'" data-id="'.$value->id.'" data-target="#custom-width-modal"><i class="fa fa-trash-o"></i></a>';
                }
                $row = array(); 
                $row[] = $value->id;
                $row[] = $value->title;
                $row[] =  '<img src="'.$value->tag_logo.'" width="50px">';
               
                $row[] = $edit."".$delete;
               
                //$sumqty += $value->qty;
                //$totalunitprice += ($value->qty * $value->unit_price);
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
         return view('admin.tags.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
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
        Tag::create($form_data);
		
        return redirect()->route('tags.index')->with('success','Tag created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tags = Tag::find($id);
         return view('admin.tags.show',compact('tags'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tags = Tag::find($id);
		return view('admin.tags.edit',compact('tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $this->validate($request,[
            'title' => 'required',
        ]);
		$tags = Tag::find($id);
		if($request->hasFile('tag_logo')) {   
			$this->validate($request,[
				'tag_logo' =>  'required|mimes:png,jpg,jpeg',
			]);
			if(!empty($tags->tag_logo)){
				$clogo = public_path($tags->tag_logo);
				unlink($clogo);	  
			}
			
			$file = $request->file('tag_logo');
			$fullName=  time().'.'.$file->getClientOriginalExtension();
			$path = '/tag_logo/'.$fullName;
			$fileName = $file->move(public_path('tag_logo/'),$fullName);
		}
		if(!empty($path)){
			$form_data = ["title" =>$request["title"],"tag_logo"=>$path];
			$tags->update($form_data);
		} else{
			$tags->update($request->all());
		}
		return redirect()->route('tags.index')
					->with('success','Tags updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         $tags = Tag::find($id)->delete();
         return redirect()->route('tags.index')
                        ->with('success','Tags deleted successfully');
    }
}
