@extends('layouts.master')
@section('title'){{trans_choice('general.loan',1)}} {{trans_choice('general.disbursed_by',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.loan',1)}} {{trans_choice('general.disbursed_by',1)}}</h6>

            <div class="heading-elements">
                <a href="{{ url('loan/loan_disbursed_by/create') }}"
                   class="btn btn-info btn-sm">{{trans_choice('general.add',1)}} {{trans_choice('general.disbursed_by',1)}}</a>
            </div>
        </div>
        <div class="panel-body">
            <table id="" class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>{{trans_choice('general.name',1)}}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>{{ $key->name }}</td>
                        <td>
                            <ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <li><a href="{{ url('loan/loan_disbursed_by/'.$key->id.'/edit') }}"><i
                                                        class="fa fa-edit"></i> {{ trans('general.edit') }} </a></li>
                                        <li><a href="{{ url('loan/loan_disbursed_by/'.$key->id.'/delete') }}"
                                               class="delete"><i
                                                        class="fa fa-trash"></i> {{ trans('general.delete') }} </a></li>
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
