@extends('client.auth')
@section('title')
    {{ trans('general.register') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-5 col-lg-offset-3">
            <div class="panel  registration-form">
                <div class="panel-body">
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
                                <i class="icon-plus3"></i>
                            @endif
                        </div>
                    </div>
                    {!! Form::open(array('url' => url('client/register'), 'method' => 'post', 'name' => 'form','class'=>'register-form')) !!}
                    <div class="text-center">
                        <h5 class="content-group-lg">{{ trans('general.register') }}
                            <small class="display-block">{{ trans('general.register_account_msg') }}</small>
                        </h5>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group has-feedback">
                                {!! Form::text('first_name', null, array('class' => 'form-control', 'placeholder'=>trans_choice('general.first_name',1),'required'=>'required','id'=>'first_name')) !!}

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group has-feedback">
                                {!! Form::text('last_name', null, array('class' => 'form-control', 'placeholder'=>trans_choice('general.last_name',1),'required'=>'required','id'=>'last_name')) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group ">
                                {!! Form::select('gender',array('Male'=>trans_choice('general.male',1),'Female'=>trans_choice('general.female',1)),'Male', array('class' => 'form-control','required'=>'required')) !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group ">
                                {!! Form::number('mobile', null, array('class' => 'form-control', 'placeholder'=>trans_choice('general.mobile',1),'required'=>'required','id'=>'mobile')) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group ">
                                {!! Form::text('dob',null, array('class' => 'form-control date-picker', 'placeholder'=>trans_choice('general.dob',1),'required'=>'required')) !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group ">
                                {!! Form::select('working_status',array('Employee'=>trans_choice('general.Employee',1),'Owner'=>trans_choice('general.Owner',1),'Student'=>trans_choice('general.Student',1),'Overseas Worker'=>trans_choice('general.Overseas Worker',1),'Pensioner'=>trans_choice('general.Pensioner',1),'Unemployed'=>trans_choice('general.Unemployed',1)),null, array('class' => 'form-control',)) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group ">
                                {!! Form::email('email', null, array('class' => 'form-control', 'placeholder'=>trans_choice('general.email',1),'required'=>'required','id'=>'email')) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group ">
                                {!! Form::text('username', null, array('class' => 'form-control', 'placeholder'=>trans_choice('general.username',1),'required'=>'required','id'=>'username')) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::password('password', array('class' => 'form-control', 'placeholder'=>trans('general.password'),'required'=>'required','id'=>'password')) !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::password('repeat_password', array('class' => 'form-control', 'placeholder'=>trans('general.repeat_password'),'required'=>'required','id'=>'repeat_password')) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <label class="checkbox-inline">
                                <input type="checkbox" name="terms" class="styled" checked="checked" id="terms"
                                       required>
                                {{ trans('general.accept_terms') }}
                            </label>
                        </div>
                    </div>

                    <div class="text-right">
                        <a href="{{url('client')}}" class="btn btn-link"><i
                                    class="icon-arrow-left13 position-left"></i> {{ trans('general.back') }}
                            {{ trans('general.to') }} {{ trans('general.login') }}
                        </a>
                        <button type="submit" class="btn bg-teal-400 "> {{ trans('general.register') }}
                        </button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

@endsection
@section('footer-scripts')
    <script>
        $.validator.addMethod('mypassword', function (value, element) {
                return this.optional(element) || (value.match(/[a-zA-Z]/) && value.match(/[0-9]/));
            },
            'Password must contain at least one numeric and one alphabetic character.');
        $(".register-form").validate({
            rules: {
                field: {
                    required: true,
                    step: 10
                },
                password: {
                    required: true,
                    minlength: 6,
                    mypassword: true
                },
                repeat_password: {
                    required: true,
                    minlength: 6,
                    equalTo: "#password"
                }
            }, highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            errorElement: 'span',
            errorClass: 'help-block',
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });
    </script>
@endsection