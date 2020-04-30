@extends('layouts.master')
@section('title')
    {{ trans_choice('general.add',1) }} {{ trans_choice('general.user',1) }}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{ trans_choice('general.add',1) }} {{ trans_choice('general.user',1) }}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => 'user/store','class'=>'',"enctype" => "multipart/form-data")) !!}
        <div class="panel-body">
            <div class="form-group">
                {!!  Form::label(trans('general.first_name'),null,array('class'=>'control-label')) !!}
                {!! Form::text('first_name','',array('class'=>'form-control','required'=>'required')) !!}
            </div>
            <div class="form-group">
                {!!  Form::label(trans('general.last_name'),null,array('class'=>'control-label')) !!}
                {!! Form::text('last_name','',array('class'=>'form-control','required'=>'required')) !!}
            </div>
            <div class="form-group">
                {!!  Form::label(trans('general.gender'),null,array('class'=>' control-label')) !!}
                {!! Form::select('gender', array('Male' =>trans('general.male'), 'Female' => trans('general.female')),null,array('class'=>'form-control')) !!}
            </div>
            <div class="form-group">
                {!!  Form::label(trans_choice('general.phone',1),null,array('class'=>'control-label')) !!}
                {!! Form::text('phone',null,array('class'=>'form-control')) !!}
            </div>
            <div class="form-group ">
                {!!  Form::label(trans_choice('general.email',1),null,array('class'=>'control-label')) !!}
                {!! Form::email('email','',array('class'=>'form-control','required'=>'required')) !!}
            </div>
            <div class="form-group">
                {!!  Form::label(trans('general.password'),null,array('class'=>'control-label')) !!}
                {!! Form::password('password',array('class'=>'form-control','required'=>'required')) !!}
            </div>
            <div class="form-group">
                {!!  Form::label(trans('general.repeat_password'),null,array('class'=>'control-label')) !!}
                {!! Form::password('rpassword',array('class'=>'form-control','required'=>'required')) !!}
            </div>
            <div class="form-group">
                {!!  Form::label(trans_choice('general.role',1),null,array('class'=>' control-label')) !!}
                {!! Form::select('role', $role,'Client',array('class'=>'form-control')) !!}
            </div>
            <div class="form-group">
                {!!  Form::label(trans('general.address'),null,array('class'=>'control-label')) !!}
                {!! Form::textarea('address','',array('class'=>'form-control wysihtml5','rows'=>'3')) !!}
            </div>
            <div class="form-group">
                {!!  Form::label(trans_choice('general.note',2),null,array('class'=>'control-label')) !!}

                {!! Form::textarea('notes','',array('class'=>'form-control wysihtml5','rows'=>'3')) !!}
            </div>
        </div>
        <!-- /.panel-body -->
        <div class="panel-footer">
            <div class="heading-elements">
                <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
@endsection
@section('footer-scripts')
    <script src="{{ asset('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
@endsection