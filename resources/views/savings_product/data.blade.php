@extends('layouts.master')
@section('title')
    {{trans_choice('general.saving',2)}} {{trans_choice('general.product',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.saving',2)}} {{trans_choice('general.product',2)}}</h6>

            <div class="heading-elements">
                @if(Sentinel::hasAccess('savings.products'))
                    <a href="{{ url('saving/savings_product/create') }}"
                       class="btn btn-info btn-sm">{{trans_choice('general.add',1)}} {{trans_choice('general.saving',2)}} {{trans_choice('general.product',1)}}</a>
                @endif
            </div>
        </div>
        <div class="panel-body ">
            <div class="table-responsive">
                <table id="data-table" class="table table-striped table-condensed table-hover">
                    <thead>
                    <tr >
                        <th>{{trans_choice('general.product',1)}}</th>
                        <th>{{trans_choice('general.interest_rate_per_annum',1)}}</th>
                        <th>{{trans_choice('general.interest_posting_frequency',1)}}</th>
                        <th>{{trans_choice('general.minimum',1)}} {{trans_choice('general.balance',1)}}</th>
                        <th>{{ trans_choice('general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>{{ $key->name }}</td>
                            <td>{{ $key->interest_rate }}</td>
                            <td>
                                @if($key->interest_posting==1)
                                    {{trans_choice('general.every_1_month',1)}}
                                @endif
                                @if($key->interest_posting==2)
                                    {{trans_choice('general.every_2_month',1)}}
                                @endif
                                @if($key->interest_posting==3)
                                    {{trans_choice('general.every_3_month',1)}}
                                @endif
                                @if($key->interest_posting==4)
                                    {{trans_choice('general.every_4_month',1)}}
                                @endif
                                @if($key->interest_posting==5)
                                    {{trans_choice('general.every_6_month',1)}}
                                @endif
                                @if($key->interest_posting==6)
                                    {{trans_choice('general.every_12_month',1)}}

                                @endif
                            </td>
                            <td>{{ $key->minimum_balance }}</td>
                            <td>
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu9"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            @if(Sentinel::hasAccess('savings.products'))
                                                <li><a href="{{ url('saving/savings_product/'.$key->id.'/edit') }}"><i
                                                                class="fa fa-edit"></i> {{ trans('general.edit') }} </a>
                                                </li>
                                            @endif
                                            @if(Sentinel::hasAccess('savings.products'))
                                                <li><a href="{{ url('saving/savings_product/'.$key->id.'/delete') }}"
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
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            autoWidth: false,
            columnDefs: [{
                orderable: false,
                width: '100px',
                targets: [4]
            }],
            "order": [[0, "desc"]],
            language: {
                "lengthMenu": "{{ trans('general.lengthMenu') }}",
                "zeroRecords": "{{ trans('general.zeroRecords') }}",
                "info": "{{ trans('general.info') }}",
                "infoEmpty": "{{ trans('general.infoEmpty') }}",
                "search": "{{ trans('general.search') }}:",
                "infoFiltered": "{{ trans('general.infoFiltered') }}",
                "paginate": {
                    "first": "{{ trans('general.first') }}",
                    "last": "{{ trans('general.last') }}",
                    "next": "{{ trans('general.next') }}",
                    "previous": "{{ trans('general.previous') }}"
                }
            },
        });
    </script>
@endsection
