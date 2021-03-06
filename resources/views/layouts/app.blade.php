<!doctype html>

<html class="no-js" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('app.name', 'Rooted Admin') }}</title>
    <meta name="description" content="Ela Admin - HTML5 Admin Template">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.0/css/flag-icon.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/cs-skin-elastic.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/css/lib/datatable/dataTables.bootstrap.min.css') }}">
    <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/html5shiv/3.7.3/html5shiv.min.js"></script> -->
    <link href="https://cdn.jsdelivr.net/npm/chartist@0.11.0/dist/chartist.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/jqvmap@1.5.1/dist/jqvmap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/weathericons@2.1.0/css/weather-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.9.0/dist/fullcalendar.min.css" rel="stylesheet" />
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

   <style>
		#weatherWidget .currentDesc {
			color: #ffffff!important;
		}
        .traffic-chart {
            min-height: 335px;
        }
        #flotPie1  {
            height: 150px;
        }
        #flotPie1 td {
            padding:3px;
        }
        #flotPie1 table {
            top: 20px!important;
            right: -10px!important;
        }
        .chart-container {
            display: table;
            min-width: 270px ;
            text-align: left;
            padding-top: 10px;
            padding-bottom: 10px;
        }
        #flotLine5  {
             height: 105px;
        }

        #flotBarChart {
            height: 150px;
        }
        #cellPaiChart{
            height: 160px;
        }
		.right-panel header.header {
			background: black;
		}
		.right-panel .navbar-header {
			background-color: #000;
		}
		.select2-selection {
			min-height: 38px;
		}
		.select2-selection{
			padding: 4px;
			height: 38px !important;
			font-size: 16px;
			border: 1px solid #ced4da !important;
		}
		.right-panel .navbar-brand {
			width: 180px;
		}
    </style>
</head>

<body>
    <!-- Left Panel -->
    <aside id="left-panel" class="left-panel">
        <nav class="navbar navbar-expand-sm navbar-default">
            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">
                   <li class="{{ (Request::route()->getName() == 'home.index') ? 'active' : '' }}">
                        <a href="{{ route('home.index') }}"><i class="menu-icon fa fa-laptop"></i>Dashboard </a>
                    </li>
                    @can('user-list')
                    <li class="{{ (Request::route()->getName() == 'users.index') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}"> <i class="menu-icon fa fa-users"></i>Manage Users </a>
                    </li>
					@endcan
					@can('category-list')
					<li class="{{ (Request::route()->getName() == 'categories.index') ? 'active' : '' }}">
                        <a href="{{ route('categories.index') }}"> <i class="menu-icon fa fa-list-alt"></i>Manage Category </a>
                    </li>
                    @endcan
					@can('sponsers-list')
					<li class="{{ (Request::route()->getName() == 'sponsers.index') ? 'active' : '' }}">
                        <a href="{{ route('sponsers.index') }}"> <i class="menu-icon fa fa-black-tie"></i>Manage Sponsers </a>
                    </li>
				   @endcan
                   @can('tags-list')
                   <li class="{{ (Request::route()->getName() == 'tags.index') ? 'active' : '' }}">
                        <a href="{{ route('tags.index') }}"> <i class="menu-icon fa fa-tags"></i>Manage HashTags </a>
                    </li>
                   @endcan
                  @can('challanges-list')
                   <li class="{{ (Request::route()->getName() == 'challanges.index') ? 'active' : '' }}">
                        <a href="{{ route('challanges.index') }}"> <i class="menu-icon fa fa-tasks"></i>Manage Challanges </a>
                    </li>
                   @endcan
                   @can('role-list')
                   <li class="{{  (Request::route()->getName() == 'roles.index') ? 'active' : '' }}">
                        <a href="{{ route('roles.index') }}"> <i class="menu-icon fa fa-wrench"></i>Manage Role </a>
                    </li>
                    @endcan
                    @can('permission-list')
                    <li class="{{ (Request::route()->getName() == 'permissions.index') ? 'active' : '' }}">
                        <a href="{{ route('permissions.index') }}"> <i class="menu-icon fa fa-lock"></i>Manage Permission</a>
                    </li>
                    @endcan
                </ul>
            </div><!-- /.navbar-collapse -->
        </nav>
    </aside>
    <!-- /#left-panel -->
    <!-- Right Panel -->
    <div id="right-panel" class="right-panel">
        <!-- Header-->
        <header id="header" class="header">
            <div class="top-left">
                <div class="navbar-header">
                    <a class="navbar-brand" href="{{ url('home') }}"><img src="{{ asset('images/logo3.png') }}" alt="Logo"></a>
                    <a class="navbar-brand hidden" href="./"><img src="{{ asset('images/logo3.png') }}" alt="Logo"></a>
                    <a id="menuToggle" class="menutoggle"><i class="fa fa-bars"></i></a>
                </div>
            </div>
            <div class="top-right">
                <div class="header-menu">
                    <div class="header-left">
                        <!--<button class="search-trigger"><i class="fa fa-search"></i></button>
                        <div class="form-inline">
                            <form class="search-form">
                                <input class="form-control mr-sm-2" type="text" placeholder="Search ..." aria-label="Search">
                                <button class="search-close" type="submit"><i class="fa fa-close"></i></button>
                            </form>
                        </div>

                        <div class="dropdown for-notification">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="notification" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bell"></i>
                                <span class="count bg-danger">3</span>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="notification">
                                <p class="red">You have 3 Notification</p>
                                <a class="dropdown-item media" href="#">
                                    <i class="fa fa-check"></i>
                                    <p>Server #1 overloaded.</p>
                                </a>
                                <a class="dropdown-item media" href="#">
                                    <i class="fa fa-info"></i>
                                    <p>Server #2 overloaded.</p>
                                </a>
                                <a class="dropdown-item media" href="#">
                                    <i class="fa fa-warning"></i>
                                    <p>Server #3 overloaded.</p>
                                </a>
                            </div>
                        </div>

                        <div class="dropdown for-message">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="message" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-envelope"></i>
                                <span class="count bg-primary">4</span>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="message">
                                <p class="red">You have 4 Mails</p>
                                <a class="dropdown-item media" href="#">
                                    <span class="photo media-left"><img alt="avatar" src="images/avatar/1.jpg"></span>
                                    <div class="message media-body">
                                        <span class="name float-left">Jonathan Smith</span>
                                        <span class="time float-right">Just now</span>
                                        <p>Hello, this is an example msg</p>
                                    </div>
                                </a>
                                <a class="dropdown-item media" href="#">
                                    <span class="photo media-left"><img alt="avatar" src="images/avatar/2.jpg"></span>
                                    <div class="message media-body">
                                        <span class="name float-left">Jack Sanders</span>
                                        <span class="time float-right">5 minutes ago</span>
                                        <p>Lorem ipsum dolor sit amet, consectetur</p>
                                    </div>
                                </a>
                                <a class="dropdown-item media" href="#">
                                    <span class="photo media-left"><img alt="avatar" src="images/avatar/3.jpg"></span>
                                    <div class="message media-body">
                                        <span class="name float-left">Cheryl Wheeler</span>
                                        <span class="time float-right">10 minutes ago</span>
                                        <p>Hello, this is an example msg</p>
                                    </div>
                                </a>
                                <a class="dropdown-item media" href="#">
                                    <span class="photo media-left"><img alt="avatar" src="images/avatar/4.jpg"></span>
                                    <div class="message media-body">
                                        <span class="name float-left">Rachel Santos</span>
                                        <span class="time float-right">15 minutes ago</span>
                                        <p>Lorem ipsum dolor sit amet, consectetur</p>
                                    </div>
                                </a>
                            </div>
                        </div>-->
                    </div>

                    <div class="user-area dropdown float-right">
                        <a href="#" class="dropdown-toggle active" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="user-avatar rounded-circle" src="{{ asset('images/admin.jpg') }}" alt="User Avatar">
                        </a>

                        <div class="user-menu dropdown-menu">
                            <a class="nav-link" href="#"><i class="fa fa- user"></i>My Profile</a>

                            <!--<a class="nav-link" href="#"><i class="fa fa- user"></i>Notifications <span class="count">13</span></a>-->

                            <!--<a class="nav-link" href="#"><i class="fa fa -cog"></i>Settings</a>-->

                            <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-power -off"></i>{{ __('Logout') }}</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                				@csrf
                			</form>
                        </div>
                    </div>

                </div>
            </div>
        </header>
        <!-- /#header -->
         <!-- .content -->
          
          @yield('content')
    
         <!-- /.content -->
        <div class="clearfix"></div>
        <!-- Footer -->
        <footer class="site-footer">
            <div class="footer-inner bg-white">
                <div class="row">
                    <div class="col-sm-6">
                        Copyright &copy; <?php echo date('Y') ?> Hinotes Admin
                    </div>
                    <div class="col-sm-6 text-right">
                        Designed by <a href="#">Hinotes</a>
                    </div>
                </div>
            </div>
        </footer>
        <!-- /.site-footer -->
    </div>
    <!-- /#right-panel -->
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
    <script src="{{ ('assets/js/main.js') }}"></script>

	<script src="{{ asset('assets/js/lib/data-table/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/data-table/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/data-table/dataTables.buttons.min.js') }}"></script>
	
    <!--  Chart js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.7.3/dist/Chart.bundle.min.js"></script>

    <!--Chartist Chart-->
    <script src="https://cdn.jsdelivr.net/npm/chartist@0.11.0/dist/chartist.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartist-plugin-legend@0.6.2/chartist-plugin-legend.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flot-pie@1.0.0/src/jquery.flot.pie.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flot-spline@0.0.1/js/jquery.flot.spline.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/simpleweather@3.1.0/jquery.simpleWeather.min.js"></script>
    <script src="assets/js/init/weather-init.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/moment@2.22.2/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.9.0/dist/fullcalendar.min.js"></script>
    <script src="{{ ('assets/js/init/fullcalendar-init.js') }}"></script>
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    <!--Local Stuff-->    
<script type="text/javascript" language="javascript">
   jQuery(document).ready(function () {
         jQuery("#bootstrap-data-table").dataTable();


       jQuery.ajaxSetup({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
        });
   });
</script>

    <script type="text/javascript">
        jQuery(document).ready(function(){
         var table;
            table = jQuery('#datatable_grid').DataTable({
                 "bProcessing": true,
                 "serverSide": true,
                 "searching": true,
                 "orderable": true,
                 "order": [[ 0, "ASC" ]],
                 "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                /* "columnDefs": [
                  { "targets":0, "orderable": true },
                 ],*/
                 "ajax":{
                    url :$('#datatablelink').text(), // json datasource
                    type: "post",  // type of method  ,GET/POST/DELETE
                    error: function(){
                      $("#datatable_grid_processing").css("display","none");
                    }
                  },
            });
             
        });
    </script>
      <script type="text/javascript" language="javascript">
      jQuery(document).on("click",".remove-record",function() {
                var id = jQuery(this).attr('data-id');
                var url = jQuery(this).attr('data-url');
                var token = jQuery('meta[name="csrf-token"]').attr('content');
                jQuery(".remove-record-model").attr("action",url);
                jQuery('body').find('.remove-record-model').append('<input name="_token" type="hidden" value="'+ token +'">');
                jQuery('body').find('.remove-record-model').append('<input name="_method" type="hidden" value="DELETE">');
                jQuery('body').find('.remove-record-model').append('<input name="id" type="hidden" value="'+ id +'">');
            });

            jQuery('.remove-data-from-delete-form').click(function() {
                jQuery('body').find('.remove-record-model').find( "input" ).remove();
            });
            jQuery('.modal').click(function() {
                // $('body').find('.remove-record-model').find( "input" ).remove();
            });
            jQuery('.bselect2').select2();

    </script>
</body>
</html>