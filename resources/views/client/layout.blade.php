<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> @yield('title')</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet"
          type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/themes/limitless/css/icons/icomoon/styles.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/themes/limitless/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/themes/limitless/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/themes/limitless/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/themes/limitless/css/colors.css') }}">
    <link href="{{ asset('assets/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/bootstrap-toastr/toastr.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/bootstrap-touchspin/bootstrap.touchspin.min.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/plugins/fullcalendar/fullcalendar.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/fancybox/jquery.fancybox.css') }}"
          rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/amcharts/plugins/export/export.css') }}"
          rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}"
          rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/plugins/datepicker/bootstrap-datepicker3.min.css') }}" rel="stylesheet"
          type="text/css"/>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery 2.2.3 -->
    <script src="{{ asset('assets/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

    <script src="{{ asset('assets/plugins/bootstrap-toastr/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/jQueryUi/jquery-ui.min.js') }}" type="text/javascript"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.6 -->
    <script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datepicker/bootstrap-datepicker.min.js') }}"
            type="text/javascript"></script>
    {{--Start Page header level scripts--}}
    @yield('page-header-scripts')
    {{--End Page level scripts--}}
    <style>
        .description-block {
            display: block;
            margin: 10px 0;
            text-align: center;
        }

        .description-block.margin-bottom {
            margin-bottom: 25px;
        }

        .description-block > .description-header {
            margin: 0;
            padding: 0;
            font-weight: 600;
            font-size: 16px;
        }

        .description-block > .description-text {
            text-transform: uppercase;
        }
    </style>
</head>
<body class="">
<!-- Main navbar -->
<div class="navbar navbar-inverse bg-indigo">
    <div class="navbar-header">
        <a class="navbar-brand"
           href="{{url('dashboard')}}">{{ \App\Models\Setting::where('setting_key','company_name')->first()->setting_value }}</a>
        <ul class="nav navbar-nav pull-right visible-xs-block">
            <li><a data-toggle="collapse" data-target="#navbar-mobile" class="legitRipple " aria-expanded="false"><i
                            class="icon-tree5"></i></a></li>
        </ul>

    </div>

    <div class="navbar-collapse collapse" id="navbar-mobile">
        <ul class="nav navbar-nav">
            <li class="@if(Request::is('client_dashboard')) active @endif"><a
                        href="{{url('client_dashboard')}}">{{trans_choice('general.dashboard',1)}}
                    <span class="sr-only">(current)</span></a>
            </li>
            @if(\App\Models\Setting::where('setting_key','allow_client_apply')->first()->setting_value==1)
                <li class="dropdown @if(Request::is('client/application/*')) active @endif">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                       aria-expanded="false"> {{trans_choice('general.loan',1)}} {{trans_choice('general.application',2)}}
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            <a href="{{url('client/application/data')}}">{{trans_choice('general.my',1)}} {{trans_choice('general.application',2)}}</a>
                        </li>
                        <li>
                            <a href="{{url('client/application/create')}}">{{trans_choice('general.apply',1)}}</a>
                        </li>
                    </ul>
                </li>
            @endif
            <li class="@if(Request::is('client/saving/*')) active @endif"><a
                        href="{{url('client/saving/data')}}">{{trans_choice('general.saving',2)}} {{trans_choice('general.account',2)}}
                </a>
            </li>
        </ul>
        <ul class="nav navbar-nav navbar-right">


            <li class="dropdown dropdown-user">
                <a class="dropdown-toggle" data-toggle="dropdown">
                    <img src="{{ asset('assets/themes/limitless/images/user.png') }}"
                         class=" " alt="">
                    <span> {{ $borrower->first_name }} {{ $borrower->last_name }}</span>
                    <i class="caret"></i>
                </a>

                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="{{ url('client_profile') }}"><i class="icon-user-check"></i>
                            <span>{{trans_choice('general.profile',1)}}</span></a></li>
                    <li><a href="{{ url('client_logout') }}"><i class="icon-switch2"></i>
                            <span>{{trans_choice('general.logout',1)}}</span></a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<!-- Page container -->
<div class="page-container">
    <div class="page-content">

        <div class="content-wrapper">
            <div class="page-header page-header-default">
                <div class="page-header-content">
                    <div class="page-title">
                        <h4><i class="icon-arrow-left52 position-left"></i> <span
                                    class="text-semibold">{{trans_choice('general.home',1)}}</span> -
                            @yield('title')</h4>
                    </div>
                    <div class="heading-elements">
                        <div class="heading-btn-group">


                        </div>
                    </div>
                </div>
                <div class="breadcrumb-line">
                    <ul class="breadcrumb">
                        <li><a href="{{ url('dashboard') }}"><i class="icon-home2 position-left"></i> Home</a></li>
                        <li class="active">@yield('title')</li>
                    </ul>
                </div>
            </div>
            <!-- /page header -->
            <div class="content">

                <section class="">
                    @if(Session::has('flash_notification.message'))
                        <script>toastr.{{ Session::get('flash_notification.level') }}('{{ Session::get("flash_notification.message") }}', 'Response Status')</script>
                    @endif
                    @if (isset($_REQUEST['msg']))
                        <div class="alert alert-success">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ $_REQUEST['msg'] }}
                        </div>
                    @endif
                    @if (isset($msg))
                        <div class="alert alert-success">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ $msg }}
                        </div>
                    @endif
                    @if (isset($error))
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ $error }}
                        </div>
                    @endif
                    @if (isset($_REQUEST['error']))
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ $_REQUEST['error'] }}
                        </div>
                    @endif
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @yield('content')
                </section>
                <!-- /.content -->
                <!-- Footer -->
                <div class="footer text-muted">
                    Copyright &copy; {{ date("Y") }} by <a
                            href="{{ \App\Models\Setting::where('setting_key','company_website')->first()->setting_value }}"
                            target="_blank">{{ \App\Models\Setting::where('setting_key','company_name')->first()->setting_value }}</a>
                </div>
                <!-- /footer -->
            </div>
        </div>
        <!-- /content area -->
    </div>
    <!-- /page content -->
</div>


<script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap-confirmation/bootstrap-confirmation.min.js') }}"
        type="text/javascript"></script>
<script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"
        type="text/javascript"></script>
<script src="{{ asset('assets/plugins/jquery-validation/additional-methods.min.js') }}"
        type="text/javascript"></script>
<script src="{{ asset('assets/plugins/moment/js/moment.min.js') }}"
        type="text/javascript"></script>
<script src="{{ asset('assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"
        type="text/javascript"></script>
<script src="{{ asset('assets/plugins/bootstrap-touchspin/bootstrap.touchspin.min.js') }}"
        type="text/javascript"></script>
<script src="{{ asset('assets/plugins/tinymce/tinymce.min.js') }}"
        type="text/javascript"></script>
<script src="{{ asset('assets/plugins/fancybox/jquery.fancybox.js') }}"
        type="text/javascript"></script>
<script src="{{ asset('assets/plugins/jquery.numeric.js') }}"></script>

<script src="{{ asset('assets/themes/limitless/js/plugins/loaders/pace.min.js') }}"></script>
<script src="{{ asset('assets/themes/limitless/js/plugins/loaders/blockui.min.js') }}"></script>
<script src="{{ asset('assets/themes/limitless/js/core/app.js') }}"></script>
<script src="{{ asset('assets/themes/limitless/js/plugins/ui/ripple.min.js') }}"></script>
<script src="{{ asset('assets/themes/limitless/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
<!-- SlimScroll 1.3.0 -->
<script src="{{ asset('assets/themes/limitless/js/plugins/tables/datatables/datatables.min.js') }}"></script>

@yield('footer-scripts')
<!-- ChartJS 1.0.1 -->
<script src="{{ asset('assets/themes/limitless/js/custom.js') }}"></script>

</body>
</html>