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
                            <li class="active">Add</li>
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
            @if (count($errors) > 0)
              <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                   @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                   @endforeach
                </ul>
              </div>
            @endif
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <strong class="card-title">Create New Sponsers</strong>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('sponsers.index') }}"> Back</a>
                        </div>
                    </div>
                    <div class="card-body">
                    {!! Form::open(array('route' => 'sponsers.store','method'=>'POST','enctypt'=>'multipart/form-data','files'=> true,'autocomplete'=>'off')) !!}
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Sponsers Name:</strong>
                              <input type="text" name="sponsers_name" class="form-control" placeholder="Sponsers Name">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Sponsers Logo:</strong>(SVG only)
                               <input type="file" name="sponsers_logo" class="form-control">
                            </div>
                        </div>
						<div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Sponsers Banner:</strong>(SVG only)
                               <input type="file" name="sponsers_banner" class="form-control">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div><!-- .animated -->
</div><!-- .content -->
@endsection