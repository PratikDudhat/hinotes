<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use DB;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('permissions.index');
    }

    public function permissionsfilter(Request $request){
        $params = $request->all();
        $data = array();
          $columns = array(
            0 => 'id',
            1 => 'name',
        );
        $list = Permission::skip($params['start']);
                                $list->take($params['length']);
                                if(!empty($params['search']['value'])){
                                $list->where('name','like','%'.$params['search']['value'].'%');
                                }
                                $list->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir']);
                 $listrecords = $list->get();
    
        $listrecord =Permission::orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir']);
                                if(!empty($params['search']['value'])){
                                $listrecord->where('name','like','%'.$params['search']['value'].'%');
                                }
                $totalRecords = $listrecord->count();
        $user = auth()->user();
        if(!empty($listrecords)){
            foreach ($listrecords as $value) {
                $show = "";
                 $edit = "";
                 $delete ="";
                 $show .= '<a class="btn btn-sm" href="'.url('/permissions/'.$value->id).'"><i class="fa fa-eye"></i></a>';
               if ($user->can('permission-edit')){
                    $edit .='<a href="'.url('/permissions/'.$value->id.'/edit').'" class="btn btn-circle btn-sm blue"><i class="fa fa-edit"></i></a>';;
                } 
                  $url = \URL::route('permissions.destroy',$value->id);
                 if ($user->can('permission-delete')){
                    $delete .='<a class="btn btn-sm waves-effect waves-light remove-record" data-toggle="modal" data-url="'.$url.'" data-id="'.$value->id.'" data-target="#custom-width-modal"><i class="fa fa-trash-o"></i></a>';
                }
                $row = array(); 
                $row[] = $value->id;
                $row[] = $value->name;
               
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
        return view('permissions.create');
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
            'name' => 'required',
        ]);
        $userData['guard_name'] = "web";
		$userData['name'] = $request->name;
        Permission::create($userData);
        return redirect()->route('permissions.index')
                        ->with('success','Permission created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         $permissions = Permission::find($id);
         return view('permissions.show',compact('permissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $permissions = Permission::find($id);
      return view('permissions.edit',compact('permissions'));
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
        $this->validate($request,[
            'name' => 'required',
        ]);
         $permissions = Permission::find($id);
           $permissions->update($request->all());
       return redirect()->route('permissions.index')
                        ->with('success','Permission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         $permissions = Permission::find($id)->delete();
         return redirect()->route('permissions.index')
                        ->with('success','Permission deleted successfully');
    }
}
