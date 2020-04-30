@extends('layouts.master')
@section('title')
    {{trans_choice('general.role',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.role',2)}}</h6>

            <div class="heading-elements">
                <a href="{{ url('user/role/create') }}" class="btn btn-info btn-xs">
                    {{trans_choice('general.add',1)}} {{trans_choice('general.role',1)}}
                </a>
            </div>
        </div>
        <div class="panel-body">
            <table class="table responsive table-bordered table-hover table-stripped" id="">
                <thead>
                <tr>
                    <th>{{trans_choice('general.name',1)}}</th>
                    <th>{{trans('general.slug')}}</th>
                    <th>{{trans_choice('general.action',1)}}</th>
                </tr>
                </thead>

                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>{{ $key->name }}</td>
                        <td>{{ $key->slug}}</td>
                        <td>
                            <ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        <li>
                                            <a href="{{ url('user/role/'.$key->id.'/edit') }}"><i
                                                        class="fa fa-edit"></i>
                                                {{ trans('general.edit') }}</a>
                                        </li>
                                        @if($key->id!=1)
                                            <li>
                                                <a href="{{ url('user/role/'.$key->id.'/delete') }}"
                                                   class="delete"><i
                                                            class="fa fa-trash"></i>
                                                    {{ trans('general.delete') }}</a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            </ul>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
