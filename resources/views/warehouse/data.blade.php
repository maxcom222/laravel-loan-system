@extends('layouts.master')
@section('title'){{trans_choice('general.warehouse',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title"> {{trans_choice('general.warehouse',2)}}</h6>

            <div class="heading-elements">
                @if(Sentinel::hasAccess('stock.create'))
                    <a href="{{ url('warehouse/create') }}"
                       class="btn btn-info btn-sm">{{trans_choice('general.add',1)}} {{trans_choice('general.warehouse',1)}}</a>
                @endif
            </div>
        </div>
        <div class="panel-body">
            <table id="" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>{{ trans_choice('general.name',1) }}</th>
                    <th>{{ trans_choice('general.note',2) }}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>{{ $key->name }}</td>
                        <td>{{ $key->notes }}</td>
                        <td>
                            <ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        @if(Sentinel::hasAccess('stock.view'))
                                            <li><a href="{{ url('warehouse/'.$key->id.'/show') }}"><i
                                                            class="fa fa-search"></i> {{ trans_choice('general.detail',1) }}
                                                </a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('stock.update'))
                                            <li><a href="{{ url('warehouse/'.$key->id.'/edit') }}"><i
                                                            class="fa fa-edit"></i> {{ trans('general.edit') }} </a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('stock.delete'))
                                            <li><a href="{{ url('warehouse/'.$key->id.'/delete') }}"
                                                   class="delete"><i
                                                            class="fa fa-trash"></i> {{ trans('general.delete') }} </a>
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
        <!-- /.panel-body -->
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')

@endsection
