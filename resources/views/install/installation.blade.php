@extends('install.layouts.master')

@section('title', trans('general.install_installation'))
@section('container')
    <p class="paragraph">{{ trans('general.install_msg') }}</p>
    {!! Form::open(array('url' => url('install/installation'), 'method' => 'post', 'name' => 'form',"enctype"=>"multipart/form-data")) !!}
    <div class="form-group">

        <button type="submit" class="btn btn-info pull-right"> {{ trans('general.install_install') }}</button>

    </div>
    {!! Form::close() !!}
@endsection