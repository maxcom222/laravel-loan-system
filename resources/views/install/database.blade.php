@extends('install.layouts.master')

@section('title', trans('general.install_database'))
@section('container')
    {!! Form::open(array('url' => url('install/database'), 'method' => 'post', 'name' => 'form',"enctype"=>"multipart/form-data")) !!}
    <div class="form-group">
        {!! Form::label('host',trans('general.install_host'),array('class'=>'')) !!}
        {!! Form::text('host', null, array('class' => 'form-control','required'=>'required')) !!}
    </div>
    <div class="form-group">
        {!! Form::label('username',trans('general.install_username'),array('class'=>'')) !!}
        {!! Form::text('username', null, array('class' => 'form-control','required'=>'required')) !!}
    </div>
    <div class="form-group">
        {!! Form::label('password',trans('general.install_password'),array('class'=>'')) !!}
        {!! Form::text('password', null, array('class' => 'form-control')) !!}
    </div>
    <div class="form-group">
        {!! Form::label('name',trans('general.install_name'),array('class'=>'')) !!}
        {!! Form::text('name', null, array('class' => 'form-control','required'=>'required')) !!}
    </div>
    <div class="form-group">
        {!! Form::label('port',trans('general.install_port'),array('class'=>'')) !!}
        {!! Form::text('port', 3306, array('class' => 'form-control','required'=>'required')) !!}
    </div>
    <div class="form-group">

        <button type="submit" class="btn btn-info pull-right"> {{ trans('general.install_next') }}</button>

    </div>
    {!! Form::close() !!}
@endsection