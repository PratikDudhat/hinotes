@extends('layouts.app')

@section('content')
<div class="breadcrumbs">
    <div class="breadcrumbs-inner">
        <div class="row m-0">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>Users Management</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li><a href="#">Users Management</a></li>
                            <li class="active">Edit</li>
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
                        <strong class="card-title">Edit User</strong>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('users.index') }}"> Back</a>
                        </div>
                    </div>
                    <div class="card-body">
                        {!! Form::model($user, ['method' => 'PATCH','route' => ['users.update', $user->id],'enctypt'=>'multipart/form-data','files'=> true ]) !!}
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Name:</strong>
									<input type="text" name="name" class="form-control" placeholder="Name" value="{{$user->name}}">
                                </div>
                            </div>
							<div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>UserName:</strong>
									<input type="text" name="username" class="form-control" placeholder="User Name" value="{{$user->username}}">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Email:</strong>
									<input type="text" name="email" class="form-control" placeholder="Email" value="{{$user->email}}">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Password:</strong>
									<input type="password" name="password" class="form-control" placeholder="Password">
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Confirm Password:</strong>
									<input type="password" name="confirm-password" class="form-control" placeholder="Confirm Password">
                                </div>
                            </div>
							<div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Country:</strong>
									<select class="form-control bselect2" name="country_code">
									@foreach($countries as $cou)
										<option value="+{{ $cou->phonecode }}"   @if ($cou->phonecode == $user->country_code)
                                                 selected="selected"
                                               @endif>(+{{ $cou->phonecode }}) {{ $cou->nicename }}</option>
									@endforeach
									</select>
                                </div>
                            </div>
							<div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Phone Number:</strong>
									<input type="number" name="phone" class="form-control" placeholder="Phone Number" value="{{$user->phone}}">
                                </div>
                            </div>
							<div class="col-xs-12 col-sm-12 col-md-10">
                                <div class="form-group">
                                   <strong>Profile Pic:</strong>
                                   <input type="file" name="avatar" class="form-control">
                                </div>
                            </div>
							<div class="col-xs-12 col-sm-12 col-md-2">
                                <div class="form-group">
									@if($user->avatar != "")
										<img src="{{$user->avatar}}" width="100px">
									@endif
                                </div>
                            </div>
							@if(!empty($user->social))
								@foreach($user->social as $social)
								<div class="col-xs-12 col-sm-12 col-md-6">
									<div class="form-group">
										<strong>Social Id:</strong>
										<input type="text" name="social_id[]" class="form-control" value="{{ $social->social_id}}" placeholder="123456789****">
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-6">
									<div class="form-group">
										<strong>Social Type:</strong>
										<select name="social_platform[]" class="form-control">
										  <option value="Facebook" {{ "Facebook" ==  $social->social_platform ? 'selected' : '' }} >Facebook</option>
										  <option value="Google" {{ "Google" == $social->social_platform ? 'selected' : '' }} >Google</option>
										</select>
									</div>
								</div>
								@endforeach
							@else
								<div class="col-xs-12 col-sm-12 col-md-12">
									<div class="form-group">
										<strong>Social Id:</strong>
										<input type="text" name="social_id" class="form-control" placeholder="123456789****">
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-12">
									<div class="form-group">
										<strong>Social Type:</strong>
										<select name="social_platform" class="form-control" multiple>
											<option value="Facebook">Facebook</option>
											<option value="Google">Google</option>
										</select>
									</div>
								</div>
							@endif
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Role:</strong>
                                    {!! Form::select('roles[]', $roles,$userRole, array('class' => 'form-control')) !!}
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