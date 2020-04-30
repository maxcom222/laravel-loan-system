@extends('install.layouts.master')

@section('title', trans('general.start_install'))
@section('container')
    <p class="paragraph">{{ trans('general.install_welcome') }}</p>
    <div class="form-group">
        <a href="{{ url('install/requirements') }}"
           class="btn btn-info pull-right">{{ trans('general.install_next') }}</a>
    </div>
@endsection