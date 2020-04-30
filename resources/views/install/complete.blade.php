@extends('install.layouts.master')

@section('title', trans('general.install_complete'))
@section('container')
    <p class="paragraph">{!! trans('general.install_complete_msg') !!}</p>
    <div class="form-group">
        <a href="{{ url('/') }}"
           class="btn btn-info pull-right">{{ trans('general.install_login') }}</a>
    </div>
@endsection