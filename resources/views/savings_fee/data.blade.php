@extends('layouts.master')
@section('title')
    {{trans_choice('general.saving',2)}} {{trans_choice('general.fee',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.saving',2)}} {{trans_choice('general.fee',2)}}</h6>

            <div class="heading-elements">
                @if(Sentinel::hasAccess('savings.fees'))
                    <a href="{{ url('saving/savings_fee/create') }}"
                       class="btn btn-info btn-sm">{{trans_choice('general.add',1)}} {{trans_choice('general.saving',2)}} {{trans_choice('general.fee',1)}}</a>
                @endif
            </div>
        </div>
        <div class="panel-body ">
            <div class="table-responsive">
                <table id="data-table" class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr >
                        <th>{{trans_choice('general.product',1)}}</th>
                        <th>{{trans_choice('general.fee_posting_frequency',1)}}</th>
                        <th>{{trans_choice('general.amount',1)}} </th>
                        <th>{{ trans_choice('general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>{{ $key->name }}</td>
                            <td>
                                @if($key->fees_posting==1)
                                    {{trans_choice('general.every_1_month',1)}}
                                @endif
                                @if($key->fees_posting==2)
                                    {{trans_choice('general.every_2_month',1)}}
                                @endif
                                @if($key->fees_posting==3)
                                    {{trans_choice('general.every_3_month',1)}}
                                @endif
                                @if($key->fees_posting==4)
                                    {{trans_choice('general.every_4_month',1)}}
                                @endif
                                @if($key->fees_posting==5)
                                    {{trans_choice('general.every_6_month',1)}}
                                @endif
                                @if($key->fees_posting==6)
                                    {{trans_choice('general.every_12_month',1)}}

                                @endif
                            </td>
                            <td>{{ $key->amount }}</td>
                            <td>
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu9"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            @if(Sentinel::hasAccess('savings.fees'))
                                                <li><a href="{{ url('saving/savings_fee/'.$key->id.'/edit') }}"><i
                                                                class="fa fa-edit"></i> {{ trans('general.edit') }} </a>
                                                </li>
                                            @endif
                                            @if(Sentinel::hasAccess('savings.fees'))
                                                <li><a href="{{ url('saving/savings_fee/'.$key->id.'/delete') }}"
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

            "order": [[0, "asc"]],
            "columnDefs": [
                {"orderable": false, "targets": [3]}
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
