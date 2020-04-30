@extends('layouts.master')
@section('title')
    {{trans_choice('general.expense',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.expense',2)}}</h6>

            <div class="heading-elements">
                @if(Sentinel::hasAccess('expenses.create'))
                    <a href="{{ url('expense/create') }}"
                       class="btn btn-info btn-sm">{{trans_choice('general.add',1)}} {{trans_choice('general.expense',2)}}</a>
                @endif
            </div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table id="data-table" class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>{{trans_choice('general.expense',1)}} {{trans_choice('general.type',1)}}</th>
                        <th>{{trans_choice('general.amount',1)}}</th>
                        <th>{{trans_choice('general.date',1)}}</th>
                        <th>{{trans_choice('general.recurring',1)}}</th>
                        <th>{{trans_choice('general.description',1)}}</th>
                        <th>{{trans_choice('general.file',2)}}</th>
                        <th>{{ trans_choice('general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>
                                @if(!empty($key->expense_type))
                                    {{$key->expense_type->name}}
                                @endif
                            </td>
                            <td>{{ $key->amount }}</td>
                            <td>{{ $key->date }}</td>
                            <td>
                                @if($key->recurring==1)
                                    {{trans_choice('general.yes',1)}}
                                @else
                                    {{trans_choice('general.no',1)}}
                                @endif
                            </td>
                            <td>{{ $key->notes }}</td>
                            <td>
                                <ul class="">
                                    @foreach(unserialize($key->files) as $k=>$value)
                                        <li><a href="{!!asset('uploads/'.$value)!!}"
                                               target="_blank">{!!  $value!!}</a></li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu9"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            @if(Sentinel::hasAccess('expenses.update'))
                                                <li><a href="{{ url('expense/'.$key->id.'/edit') }}"><i
                                                                class="fa fa-edit"></i> {{ trans('general.edit') }} </a>
                                                </li>
                                            @endif
                                            @if(Sentinel::hasAccess('expenses.delete'))
                                                <li><a href="{{ url('expense/'.$key->id.'/delete') }}"
                                                       class="delete"><i
                                                                class="fa fa-trash"></i> {{ trans('general.delete') }}
                                                    </a>
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
        <!-- /.panel-body -->
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')
    <script>

        $('#data-table').DataTable({
            "order": [[2, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": [6]}
            ],
            "language": {
                "lengthMenu": "{{ trans('general.lengthMenu') }}",
                "zeroRecords": "{{ trans('general.zeroRecords') }}",
                "info": "{{ trans('general.info') }}",
                "infoEmpty": "{{ trans('general.infoEmpty') }}",
                "search": "{{ trans('general.search') }}",
                "infoFiltered": "{{ trans('general.infoFiltered') }}",
                "paginate": {
                    "first": "{{ trans('general.first') }}",
                    "last": "{{ trans('general.last') }}",
                    "next": "{{ trans('general.next') }}",
                    "previous": "{{ trans('general.previous') }}"
                }
            },
            responsive: false
        });
    </script>
@endsection
