@extends('layouts.master')
@section('title') {{ trans_choice('general.borrower',1) }} {{ trans_choice('general.group',2) }}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{ trans_choice('general.borrower',1) }} {{ trans_choice('general.group',2) }}</h6>

            <div class="heading-elements">
                <a href="{{ url('borrower/group/create') }}"
                   class="btn btn-info btn-sm">{{ trans_choice('general.add',1) }} {{ trans_choice('general.borrower',1) }} {{ trans_choice('general.group',1) }}</a>
            </div>
        </div>
        <div class="panel-body">
            <table id="" class="table table-striped table-condensed table-hover">
                <thead>
                <tr>
                    <th>{{ trans_choice('general.name',1) }}</th>
                    <th>{{ trans_choice('general.borrower',2) }}</th>
                    <th>{{ trans_choice('general.note',2) }}</th>
                    <th class="text-center">{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>{{ $key->name }}</td>
                        <td>{{ \App\Models\BorrowerGroupMember::where('borrower_group_id',$key->id)->count() }}</td>
                        <td>{!!   $key->notes !!}</td>
                        <td class="text-center">
                            <ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a href="{{ url('borrower/group/'.$key->id.'/show') }}"><i
                                                    class="fa fa-search"></i> {{ trans_choice('general.detail',2) }} </a></li>
                                    <li><a href="{{ url('borrower/group/'.$key->id.'/edit') }}"><i
                                                    class="fa fa-edit"></i> {{ trans('general.edit') }} </a></li>
                                    <li><a href="{{ url('borrower/group/'.$key->id.'/delete') }}"
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
