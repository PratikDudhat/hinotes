<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sponsers;

class SponsersController extends Controller
{
	  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:sponsers-list|sponsers-create|sponsers-edit|sponsers-delete', ['only' => ['index','show']]);
         $this->middleware('permission:sponsers-create', ['only' => ['create','store']]);
         $this->middleware('permission:sponsers-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:sponsers-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	   return view('admin.sponsers.index');
    }

     public function sponserfilter(Request $request){
        $params = $request->all();
        $data = array();
          $columns = array(
            0 => 'id',
            1 => 'sponsers_name',
            2 => 'sponsers_logo',
            3 => 'sponsers_banner',
        );
        $list = Sponsers::skip($params['start']);
                            $list->take($params['length']);
                            $list->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir']);
                            if(!empty($params['search']['value'])){
                            $list->where('sponsers_name','like','%'.$params['search']['value'].'%');
                            }
            $listrecords = $list->get();
          
            $listrecord =Sponsers::orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir']);
                            if(!empty($params['search']['value'])){
                            $listrecord->where('sponsers_name','like','%'.$params['search']['value'].'%');
                            }
            $totalRecords = $listrecord->count();
        $user = auth()->user();
        if(!empty($listrecords)){
            foreach ($listrecords as $value) {
                $show = "";
                 $edit = "";
                 $delete ="";
                 $show .= '<a class="btn btn-sm" href="'.url('/sponsers/'.$value->id).'"><i class="fa fa-eye"></i></a>';
                if ($user->can('sponsers-edit')){
                    $edit .='<a href="'.url('/sponsers/'.$value->id.'/edit').'" class="btn btn-circle btn-sm blue"><i class="fa fa-edit"></i></a>';;
                } 
                  $url = \URL::route('sponsers.destroy',$value->id);
                if($user->can('sponsers-delete')){
                    $delete .='<a class="btn btn-sm waves-effect waves-light remove-record" data-toggle="modal" data-url="'.$url.'" data-id="'.$value->id.'" data-target="#custom-width-modal"><i class="fa fa-trash-o"></i></a>';
                }
                $row = array(); 
                $row[] = $value->id;
                $row[] = $value->sponsers_name;
                $row[] = '<img src="'.$value->sponsers_logo.'" width="50px">';
                $row[] = '<img src="'.$value->sponsers_banner.'" width="50px">';
               
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
         return view('admin.sponsers.create');
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
            'sponsers_name' => 'required',
            'sponsers_logo' =>  'required|mimes:svg',
            'sponsers_banner' =>  'required|mimes:svg',
        ]);
		if($request->hasFile('sponsers_logo')) {   	
           $file1 = $request->file('sponsers_logo');
           $fullName1 =  time().'.'.$file1->getClientOriginalExtension();;
          // $fullName= $file->getClientOriginalName();
           $sponsers_logo = '/sponsers_logo/'.$fullName1;
           $fileName1 = $file1->move(public_path('sponsers_logo/'),$fullName1);
        }
		if($request->hasFile('sponsers_banner')) {   	
           $file2 = $request->file('sponsers_banner');
           $fullName2 =  time().'.'.$file2->getClientOriginalExtension();;
          // $fullName2 = $file2->getClientOriginalName();
           $sponsers_banner = '/sponsers_banner/'.$fullName2;
           $fileName2 = $file2->move(public_path('sponsers_banner/'),$fullName2);
        }
		 
			$form_data = ["sponsers_name" =>$request["sponsers_name"],"sponsers_logo"=>$sponsers_logo,"sponsers_banner"=>$sponsers_banner];
        Sponsers::create($form_data);
        return redirect()->route('sponsers.index')
                        ->with('success','Sponsers created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
		$sponsers = Sponsers::find($id);
         return view('admin.sponsers.show',compact('sponsers'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
		$sponsers = Sponsers::find($id);
      return view('admin.sponsers.edit',compact('sponsers'));
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
            'sponsers_name' => 'required',
        ]);
		$sponsers = Sponsers::find($id);
		if($request->hasFile('sponsers_logo')) { 
			$this->validate($request,[
				'sponsers_logo' =>'required|mimes:svg',
			]);
            $slogo = public_path($sponsers->sponsers_logo);
            unlink($slogo);
           $file1 = $request->file('sponsers_logo');
           $fullName1 =  time().'.'.$file1->getClientOriginalExtension();;
          // $fullName= $file->getClientOriginalName();
           $sponsers_logo = '/sponsers_logo/'.$fullName1;
		   
           $fileName1 = $file1->move(public_path('sponsers_logo/'),$fullName1);
		   
        }
		if(!empty($sponsers_logo)){
          $sponsers->update(["sponsers_logo"=>$sponsers_logo]);
		}
		if($request->hasFile('sponsers_banner')) { 
			$this->validate($request,[
				'sponsers_banner' => 'required|mimes:svg',
			]);		
			$sbanner = public_path($sponsers->sponsers_banner);
            unlink($sbanner);		
           $file2 = $request->file('sponsers_banner');
           $fullName2 =  time().'.'.$file2->getClientOriginalExtension();
          // $fullName2 = $file2->getClientOriginalName();
           $sponsers_banner2 = '/sponsers_banner/'.$fullName2;
           $fileName2 = $file2->move(public_path('sponsers_banner/'),$fullName2);
		
        }
		if(!empty($sponsers_banner2)){
          $sponsers->update(array('sponsers_banner' => $sponsers_banner2));
		}
        $sponsers->update(array('sponsers_name' => $request["sponsers_name"]));
        return redirect()->route('sponsers.index')
                        ->with('success','Sponsers updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
		 $sponsers = Sponsers::find($id);		
         $sponsers->delete();

        return redirect()->route('sponsers.index')
                        ->with('success','Sponsers deleted successfully');
    }
}
