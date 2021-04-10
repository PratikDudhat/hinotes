<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;

class CategoryController extends Controller
{
	  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:category-list|category-create|category-edit|category-delete', ['only' => ['index','show']]);
         $this->middleware('permission:category-create', ['only' => ['create','store']]);
         $this->middleware('permission:category-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:category-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      // $categories = Category::get();
	   return view('admin.categories.index');
    }

     public function categoryfilter(Request $request){
        $params = $request->all();
        $data = array();
          $columns = array(
            0 => 'id',
            1 => 'category_name',
            2 => 'category_logo',
        );
         $list = Category::skip($params['start']);
                            $list->take($params['length']);
                            $list->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir']);
                            if(!empty($params['search']['value'])){
                            $list->where('category_name','like','%'.$params['search']['value'].'%');
                            }
            $listrecords = $list->get();

      $listrecord = Category::orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir']);
                            if(!empty($params['search']['value'])){
                            $listrecord->where('category_name','like','%'.$params['search']['value'].'%');
                            }   
            $totalRecords = $listrecord->count();
        $user = auth()->user();
        if(!empty($listrecords)){
            foreach ($listrecords as $value) {
                $show = "";
                 $edit = "";
                 $delete ="";
                 $show .= '<a class="btn btn-sm" href="'.url('/categories/'.$value->id).'"><i class="fa fa-eye"></i></a>';
                if ($user->can('category-edit')){
                    $edit .='<a href="'.url('/categories/'.$value->id.'/edit').'" class="btn btn-circle btn-sm blue"><i class="fa fa-edit"></i></a>';;
                } 
                  $url = \URL::route('categories.destroy',$value->id);
                if ($user->can('category-delete')){
                    $delete .='<a class="btn btn-sm waves-effect waves-light remove-record" data-toggle="modal" data-url="'.$url.'" data-id="'.$value->id.'" data-target="#custom-width-modal"><i class="fa fa-trash-o"></i></a>';
                }
                $row = array(); 
                $row[] = $value->id;
                $row[] = $value->category_name;
                $row[] = '<img src="'.$value->category_logo.'" width="50px">';
               
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
       return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		//dd($request->all());
        $this->validate($request,[
            'category_name' => 'required',
            'category_logo' =>  'required|mimes:svg',
        ]);
		if($request->hasFile('category_logo')) { 
           $file = $request->file('category_logo');
           $fullName=  time().'.'.$file->getClientOriginalExtension();;
          // $fullName= $file->getClientOriginalName();
           $path = '/category_logo/'.$fullName;
           $fileName = $file->move(public_path('category_logo/'),$fullName);
        }
		 
			$form_data = ["category_name" =>$request["category_name"],"category_logo"=>$path];
        Category::create($form_data);
        return redirect()->route('categories.index')
                        ->with('success','Category created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
         return view('admin.categories.show',compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
       return view('admin.categories.edit',compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
		$this->validate($request,[
            'category_name' => 'required',
        ]);
		if($request->hasFile('category_logo')) {   
		$this->validate($request,[
            'category_logo' =>  'required|mimes:svg',
        ]);
		$clogo = public_path($category->category_logo);
            unlink($clogo);	  
		   $file = $request->file('category_logo');
		   $fullName=  time().'.'.$file->getClientOriginalExtension();
		  // $fullName= $file->getClientOriginalName();
		   $path = '/category_logo/'.$fullName;
		   $fileName = $file->move(public_path('category_logo/'),$fullName);
		}
		if(!empty($path)){
		$form_data = ["category_name" =>$request["category_name"],"category_logo"=>$path];
        $category->update($form_data);
		}
		else{
        $category->update($request->all());
		}
        return redirect()->route('categories.index')
                        ->with('success','Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')
                        ->with('success','Category deleted successfully');
    }
}
