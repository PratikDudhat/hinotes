@extends('layouts.app')

@section('content')
<div class="breadcrumbs">
    <div class="breadcrumbs-inner">
        <div class="row m-0">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>HashTags Management</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="#">HashTags Management</a></li>
                            <li class="active">List</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="animated fadeIn">
        <div class="row">
            <div class="col-md-12">
                @if ($message = Session::get('success'))
                <div class="alert alert-success">
                  <p>{{ $message }}</p>
                </div>
                @endif
            </div>    
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <strong class="card-title">HashTags List</strong>
                        @can('tags-create')
                        <div class="pull-right">
                            <a class="btn btn-success" href="{{ route('tags.create') }}"> Create New HashTags</a>
                        </div>
                         @endcan
                    </div>
                    
                    <div class="card-body">
                    <table id="datatable_grid" class="table table-striped table-bordered">
						<thead>
						<tr>
							 <th>No</th>
							 <th>Title</th>
							 <th>Tag Logo</th>
							 <th width="150px">Action</th>
						</tr>
					  </thead>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- .animated -->
</div><!-- .content -->
<div id="datatablelink" style="display: none;">{{ route('tagfilter') }}</div>
<form action="" method="POST" class="remove-record-model">
    <div id="custom-width-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog" style="width:55%;">
            <div class="modal-content" style="text-align: center;">                
                <div class="modal-body">
					<p></p>
                     <h5>Are you sure you wants to delete?</h5>
					<p></p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Yes, Delete it!</button>
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-dismiss="modal">No Cancel!</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection