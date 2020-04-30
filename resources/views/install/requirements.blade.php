@extends('install.layouts.master')

@section('title', trans('general.install_requirements'))
@section('container')

    <ul class="list-group">
        @foreach($requirements as $extention => $enabled)
            <li class="list-group-item">
                {{ $extention }}
                @if($enabled)
                    <span class="badge badge1"><i class="fa fa-check"></i></span>
                @else
                    <span class="badge badge2"><i class="fa fa-times"></i></span>
                @endif
            </li>
        @endforeach
    </ul>

    <div class="form-group">
        @if($next)
            <a href="{{ url('install/permissions') }}"
               class="btn btn-info pull-right">{{ trans('general.install_next') }}</a>
        @else
            <div class="alert alert-danger">{{ trans('general.install_check') }}</div>
            <a class="btn btn-info pull-right" href="{{ Request::url() }}">
                {{trans('general.refresh')}}
                <i class="fa fa-refresh"></i></a>
        @endif

    </div>
@endsection