@extends('layouts.master')
@section('title'){{trans_choice('general.loan',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">

                @if(isset($_REQUEST['status']))
                    @if($_REQUEST['status']=='pending')
                        {{trans_choice('general.loan',2)}}  {{trans_choice('general.pending',1)}} {{trans_choice('general.approval',1)}}
                    @endif
                    @if($_REQUEST['status']=='approved')
                        {{trans_choice('general.loan',2)}}  {{trans_choice('general.awaiting',1)}} {{trans_choice('general.disbursement',1)}}
                    @endif
                    @if($_REQUEST['status']=='disbursed')
                        {{trans_choice('general.loan',2)}}  {{trans_choice('general.disbursed',1)}}
                    @endif
                    @if($_REQUEST['status']=='declined')
                        {{trans_choice('general.loan',2)}} {{trans_choice('general.declined',1)}}
                    @endif
                    @if($_REQUEST['status']=='withdrawn')
                        {{trans_choice('general.loan',2)}} {{trans_choice('general.withdrawn',1)}}
                    @endif
                    @if($_REQUEST['status']=='written_off')
                        {{trans_choice('general.loan',2)}} {{trans_choice('general.written_off',1)}}
                    @endif
                    @if($_REQUEST['status']=='closed')
                        {{trans_choice('general.loan',2)}} {{trans_choice('general.closed',1)}}
                    @endif
                    @if($_REQUEST['status']=='rescheduled')
                        {{trans_choice('general.loan',2)}} {{trans_choice('general.rescheduled',1)}}
                    @endif
                @else
                    {{trans_choice('general.all',2)}} {{trans_choice('general.loan',2)}}
                @endif
            </h6>

            <div class="heading-elements">
                @if(Sentinel::hasAccess('loans.create'))
                    <a href="{{ url('loan/create') }}"
                       class="btn btn-info btn-sm">{{trans_choice('general.add',1)}} {{trans_choice('general.loan',1)}}</a>
                @endif
            </div>
        </div>
        <div class="panel-body table-responsive">
            <table id="data-table" class="table table-striped table-condensed table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{trans_choice('general.borrower',1)}}</th>
                    <th>{{trans_choice('general.principal',1)}}</th>
                    <th>{{trans_choice('general.balance',1)}}</th>
                    <th>{{trans_choice('general.disbursed',1)}}</th>
                    <th>{{trans_choice('general.product',1)}}</th>
                    <th>{{trans_choice('general.status',1)}}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>{{$key->id}}</td>
                        <td>
                            @if(!empty($key->borrower))
                                <a href="{{url('borrower/'.$key->borrower_id.'/show')}}">{{$key->borrower->first_name}} {{$key->borrower->last_name}}</a>
                            @else
                                <span class="label label-danger">{{trans_choice('general.broken',1)}} <i
                                            class="fa fa-exclamation-triangle"></i> </span>
                            @endif
                        </td>
                        <td>
                            @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }} {{number_format($key->principal,2)}}
                            @else
                                {{number_format($key->principal,2)}} {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}
                            @endif

                        </td>
                        <td>
                            @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }} {{number_format(\App\Helpers\GeneralHelper::loan_total_balance($key->id),2)}}
                            @else
                                {{number_format(\App\Helpers\GeneralHelper::loan_total_balance($key->id),2)}} {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}
                            @endif


                        </td>
                        <td>{{$key->release_date}}</td>
                        <td>
                            @if(!empty($key->loan_product))
                                {{$key->loan_product->name}}
                            @else
                                <span class="label label-danger">{{trans_choice('general.broken',1)}} <i
                                            class="fa fa-exclamation-triangle"></i> </span>
                            @endif
                        </td>
                        <td>
                            @if($key->maturity_date<date("Y-m-d") && \App\Helpers\GeneralHelper::loan_total_balance($key->id)>0)
                                <span class="label label-danger">{{trans_choice('general.past_maturity',1)}}</span>
                            @else
                                @if($key->status=='pending')
                                    <span class="label label-warning">{{trans_choice('general.pending',1)}} {{trans_choice('general.approval',1)}}</span>
                                @endif
                                @if($key->status=='approved')
                                    <span class="label label-warning">{{trans_choice('general.awaiting',1)}} {{trans_choice('general.disbursement',1)}}</span>
                                @endif
                                @if($key->status=='disbursed')
                                    <span class="label label-info">{{trans_choice('general.active',1)}}</span>
                                @endif
                                @if($key->status=='declined')
                                    <span class="label label-danger">{{trans_choice('general.declined',1)}}</span>
                                @endif
                                @if($key->status=='withdrawn')
                                    <span class="label label-danger">{{trans_choice('general.withdrawn',1)}}</span>
                                @endif
                                @if($key->status=='written_off')
                                    <span class="label label-danger">{{trans_choice('general.written_off',1)}}</span>
                                @endif
                                @if($key->status=='closed')
                                    <span class="label label-success">{{trans_choice('general.closed',1)}}</span>
                                @endif
                                @if($key->status=='pending_reschedule')
                                    <span class="label label-warning">{{trans_choice('general.pending',1)}} {{trans_choice('general.reschedule',1)}}</span>
                                @endif
                                @if($key->status=='rescheduled')
                                    <span class="label label-info">{{trans_choice('general.rescheduled',1)}}</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            <ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        @if(Sentinel::hasAccess('loans.view'))
                                            <li><a href="{{ url('loan/'.$key->id.'/show') }}"><i
                                                            class="fa fa-search"></i> {{ trans_choice('general.detail',2) }}
                                                </a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('loans.create'))
                                            <li><a href="{{ url('loan/'.$key->id.'/edit') }}"><i
                                                            class="fa fa-edit"></i> {{ trans('general.edit') }} </a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('loans.delete'))
                                            <li><a href="{{ url('loan/'.$key->id.'/delete') }}"
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

    <script>
        $('#data-table').DataTable({
            "order": [[4, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": [7]}
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
            }
        });
    </script>
@endsection
