@extends('layouts.app')

@section('content')

<div class="breadcrumbs">
    <div class="breadcrumbs-inner">
        <div class="row m-0">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>Sponsers Management</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="#">Sponsers Management</a></li>
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
                        <strong class="card-title">Sponsers List</strong>
                        @can('sponsers-create')
                        <div class="pull-right">
                            <a class="btn btn-success" href="{{ route('sponsers.create') }}"> Create New Sponsers</a>
                        </div>
                         @endcan
                    </div>
                    
                    <div class="card-body">
                    <table id="datatable_grid" class="table table-striped table-bordered">
						<thead>
						<tr>
							 <th>No</th>
							 <th>Sponsers Name</th>
							 <th>Sponsers Logo</th>
							 <th>Sponsers Banner</th>
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
<div id="datatablelink" style="display: none;">{{ route('sponserfilter') }}</div>
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