@extends('client.auth')
@section('title')
    {{ trans('general.login') }}
@endsection

@section('content')
    <div class="panel panel-body login-form">
        @if(Session::has('flash_notification.message'))
            <script>toastr.{{ Session::get('flash_notification.level') }}('{{ Session::get("flash_notification.message") }}', 'Response Status')</script>
        @endif
        @if (isset($msg))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {{ $msg }}
            </div>
        @endif
        @if (isset($error))
            <div class="alert alert-error">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {{ $error }}
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
        <div class="text-center">
            <h3>{{ \App\Models\Setting::where('setting_key','company_name')->first()->setting_value }}</h3>
            <div class="icon-object border-slate-300 text-slate-300">
                @if(!empty(\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value))
                    <img src="{{ url(asset('uploads/'.\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value)) }}"
                         class="" height="72"/>
                @else
                    <i class="icon-reading"></i>
                @endif
            </div>
        </div>
        @if(\App\Models\Setting::where('setting_key', 'allow_client_login')->first()->setting_value == 1)
            {!! Form::open(array('url' => url('client'), 'method' => 'post', 'name' => 'form','class'=>'c-login-form')) !!}
            <div class="text-center">
                <h5 class="content-group-lg">{{ trans('general.login') }}
                    <small class="display-block">{{ \App\Models\Setting::where('setting_key','welcome_note')->first()->setting_value }}</small>
                </h5>

            </div>
            <div class="form-group has-feedback has-feedback-left">
                {!! Form::text('username', null, array('class' => 'form-control', 'placeholder'=>trans_choice('general.username',1),'required'=>'required',"id"=>'username')) !!}
                <div class="form-control-feedback">
                    <i class="icon-user text-muted"></i>
                </div>
            </div>
            <div class="form-group has-feedback has-feedback-left">
                {!! Form::password('password', array('class' => 'form-control', 'placeholder'=>trans('general.password'),'required'=>'required')) !!}
                <div class="form-control-feedback">
                    <i class="icon-lock2 text-muted"></i>
                </div>
            </div>
            <div class="form-group login-options">
                <div class="row">
                    <div class="col-sm-6">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="remember" class="styled">
                            {{ trans('general.remember') }}
                        </label>
                    </div>

                    <div class="col-sm-6 text-right">
                        <a href="{{url('client/forgot_password')}}"
                           id="forget-password">{{ trans('general.forgot_password') }}</a>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn bg-pink-400 btn-block">{{ trans('general.login') }} <i
                            class="icon-arrow-right14 position-right"></i></button>
            </div>
            {!! Form::close() !!}
            @if(\App\Models\Setting::where('setting_key','allow_self_registration')->first()->setting_value==1)
                <div class="content-divider text-muted form-group">
                    <span>{{ trans('general.register_account_msg') }}</span>
                </div>
                <a href="{{url('client/register')}}"
                   class="btn btn-default btn-block content-group legitRipple">{{ trans('general.register') }}</a>
            @endif
        @endif
    </div>
@endsection
