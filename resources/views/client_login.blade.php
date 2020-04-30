@extends('client.auth')
@section('title')
    {{ trans('login.login') }}
@endsection

@section('content')
    <div style="width: 100%;height: 100px;box-shadow: 0px 0px 15px -6px rgb(113, 113, 113);background-color: white;margin-top: 25px">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div style="padding-top: 10px;">
                        @if(!empty(\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value))
                            <img src="{{ url(asset('uploads/'.\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value)) }}"
                                 class="" height="72"/>
                        @else
                            <h3>{{ \App\Models\Setting::where('setting_key','company_name')->first()->setting_value }}</h3>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container"
         style="@if(!empty(\App\Models\Setting::where('setting_key','client_login_background')->first()->setting_value))
                 background-image: url({{asset('uploads/'.\App\Models\Setting::where('setting_key','client_login_background')->first()->setting_value)}}); min-height: 400px;
         @else
                 background-image: url({{asset('assets/dist/img/login_image.jpg')}}); min-height: 400px;

         @endif
                 ">
        @if(Session::has('flash_notification.message'))
            <script>toastr.{{ Session::get('flash_notification.level') }}('{{ Session::get("flash_notification.message") }}', 'Response Status')</script>
        @endif
        @if (isset($msg))
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{ $msg }}
                    </div>
                </div>
            </div>
        @endif
        @if (isset($error))
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-error">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{ $error }}
                    </div>
                </div>
            </div>
        @endif
        @if (count($errors) > 0)
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-md-6">
                <div style="background-color: rgba(255, 255, 255, 0.8); border-radius: 8px; margin: 40px 0 0 5px; padding: 15px; width: 100%;">
                    <p class="login-box-msg">{{ \App\Models\Setting::where('setting_key','welcome_note')->first()->setting_value }}</p>
                    @if(\App\Models\Setting::where('setting_key', 'allow_client_login')->first()->setting_value == 1)
                        {!! Form::open(array('url' => url('client'), 'method' => 'post', 'name' => 'form','class'=>'login-form')) !!}


                        <div class="form-group has-feedback">
                            {!! Form::text('username', null, array('class' => 'form-control', 'placeholder'=>trans('general.username'),'required'=>'required')) !!}
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        </div>
                        <div class="form-group has-feedback">
                            {!! Form::password('password', array('class' => 'form-control', 'placeholder'=>trans('general.password'),'required'=>'required')) !!}
                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        </div>
                        <div class="row">
                            <div class="col-xs-8">
                                <div class="checkbox icheck">
                                    <label>
                                        <input type="checkbox" name="remember"
                                               value="1"> {{ trans('general.remember') }}
                                    </label>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-xs-4">
                                <button type="submit"
                                        class="btn btn-primary btn-block btn-flat">{{ trans('general.login') }}</button>
                            </div>
                            <!-- /.col -->
                        </div>
                        <a href="javascript:;" id="forget-password">{{ trans('general.forgot_password') }}</a><br>
                        @if(\App\Models\Setting::where('setting_key','allow_self_registration')->first()->setting_value==1)
                            <a href="javascript:;" id="register-btn">{{ trans('general.register_account_msg') }}</a><br>
                        @endif
                        {!! Form::close() !!}
                        {!! Form::open(array('url' => url('client_reset'), 'method' => 'post', 'name' => 'form','class'=>'forget-form ')) !!}
                        <p class="login-box-msg">{{ trans('general.forgot_password_msg') }}</p>

                        <div class="form-group has-feedback">
                            {!! Form::email('email', null, array('class' => 'form-control', 'placeholder'=>trans_choice('general.email',1),'required'=>'required')) !!}
                            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                        </div>
                        <div class="row">
                            <div class="col-xs-8">
                                <div class="">
                                    <a href="javascript:;" class="btn btn-primary  btn-flat" id="back-btn"><i
                                                class="fa fa-backward"></i> {{ trans('general.back') }}</a>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-xs-4">
                                <button type="submit"
                                        class="btn btn-primary btn-block btn-flat">{{ trans('general.reset') }}</button>
                            </div>
                            <!-- /.col -->
                        </div>
                        {!! Form::close() !!}
                        {!! Form::open(array('url' => url('client_register'), 'method' => 'post', 'name' => 'form','class'=>'register-form ')) !!}
                        <p class="login-box-msg">{{ trans('general.register_account_msg') }}</p>
                        <div class="form-group">
                            {!! Form::label('first_name',trans_choice('general.first_name',1),array('class'=>'')) !!}
                            {!! Form::text('first_name',null, array('class' => 'form-control', 'placeholder'=>trans_choice('general.first_name',1),'required'=>'required')) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('last_name',trans_choice('general.last_name',1),array('class'=>'')) !!}
                            {!! Form::text('last_name',null, array('class' => 'form-control', 'placeholder'=>trans_choice('general.last_name',1),'required'=>'required')) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('gender',trans_choice('general.gender',1),array('class'=>'')) !!}
                            {!! Form::select('gender',array('Male'=>trans_choice('general.Male',1),'Female'=>trans_choice('general.Female',1)),'Male', array('class' => 'form-control','required'=>'required')) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('mobile',trans_choice('general.mobile',1),array('class'=>'')) !!}
                            {!! Form::text('mobile',null, array('class' => 'form-control', 'placeholder'=>trans_choice('general.numbers_only',1))) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('email',trans_choice('general.email',1),array('class'=>'')) !!}
                            {!! Form::email('email',null, array('class' => 'form-control', 'placeholder'=>trans_choice('general.email',1),'required'=>'required')) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('dob',trans_choice('general.dob',1),array('class'=>'')) !!}
                            {!! Form::text('dob',null, array('class' => 'form-control date-picker', 'placeholder'=>"yyyy-mm-dd",'required'=>'required')) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('working_status',trans_choice('general.working_status',1),array('class'=>'')) !!}
                            {!! Form::select('working_status',array('Employee'=>trans_choice('general.Employee',1),'Owner'=>trans_choice('general.Owner',1),'Student'=>trans_choice('general.Student',1),'Overseas Worker'=>trans_choice('general.Overseas Worker',1),'Pensioner'=>trans_choice('general.Pensioner',1),'Unemployed'=>trans_choice('general.Unemployed',1)),null, array('class' => 'form-control',)) !!}
                        </div>
                        <p class="bg-navy disabled color-palette">{{trans_choice('general.login',1)}} {{trans_choice('general.detail',2)}}</p>

                        <div class="form-group">
                            {!! Form::label('username',trans_choice('general.username',1),array('class'=>'')) !!}
                            {!! Form::text('username',null, array('class' => 'form-control', 'placeholder'=>"",'required'=>'required')) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('password',trans_choice('general.password',1),array('class'=>'')) !!}
                            {!! Form::password('password', array('class' => 'form-control', 'placeholder'=>"",'required'=>'required')) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('repeatpassword',trans_choice('general.repeat_password',1),array('class'=>'')) !!}
                            {!! Form::password('repeatpassword', array('class' => 'form-control', 'placeholder'=>"",'required'=>'required')) !!}
                        </div>
                        <div class="row">
                            <div class="col-xs-8">
                                <div class="">
                                    <a href="javascript:;" class="btn btn-primary  btn-flat" id="register-back-btn"><i
                                                class="fa fa-backward"></i> {{ trans('general.back') }}</a>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-xs-4">
                                <button type="submit"
                                        class="btn btn-primary btn-block btn-flat">{{ trans('general.register') }}</button>
                            </div>
                            <!-- /.col -->
                        </div>
                        {!! Form::close() !!}
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div style="padding-top: 10px;color: #fff">
                    {{trans_choice('general.accept_terms_and_conditions',1)}}
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            jQuery('#forget-password').click(function () {
                jQuery('.login-form').hide();
                jQuery('.forget-form').show();
            });
            jQuery('#register-btn').click(function () {
                jQuery('.login-form').hide();
                jQuery('.register-form').show();
            });

            jQuery('#back-btn').click(function () {
                jQuery('.login-form').show();
                jQuery('.forget-form').hide();
            });
            jQuery('#register-back-btn').click(function () {
                jQuery('.login-form').show();
                jQuery('.register-form').hide();
            });

        });
    </script>
@endsection
