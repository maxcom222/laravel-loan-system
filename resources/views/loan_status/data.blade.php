@extends('layouts.master')
@section('title') {{ trans_choice('general.loan',1) }} {{ trans_choice('general.status',1) }}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h3 class="panel-title">{{ trans_choice('general.loan',1) }} {{ trans_choice('general.status',1) }}</h3>

            <div class="heading-elements">
                <a href="{{ url('loan/loan_status/create') }}"
                   class="btn btn-info btn-sm">{{ trans_choice('general.add',1) }} {{ trans_choice('general.loan',1) }} {{ trans_choice('general.status',1) }}</a>
            </div>
        </div>
        <div class="panel-body">
            <table id="" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>{{ trans_choice('general.name',1) }}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>{{ $key->name }}</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-info btn-flat dropdown-toggle"
                                        data-toggle="dropdown" aria-expanded="false">
                                    {{ trans('general.choose') }} <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="{{ url('loan/loan_status/'.$key->id.'/edit') }}"><i
                                                    class="fa fa-edit"></i> {{ trans('general.edit') }} </a></li>
                                    <li><a href="{{ url('loan/loan_status/'.$key->id.'/delete') }}"
                                           class="delete"><i
                                                    class="fa fa-trash"></i> {{ trans('general.delete') }} </a></li>
                                </ul>
                            </div>
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
