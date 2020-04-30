@extends('client.layout')
@section('title')
    {{ trans('general.dashboard') }}
@endsection

@section('content')

    <div class="panel panel-white">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-3 col-xs-6">
                    <div class="description-block border-right">
                        @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                            <h5 class="description-header"> {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }} {{ number_format(\App\Helpers\GeneralHelper::borrower_loans_total_due($borrower->id)) }} </h5>
                        @else
                            <h5 class="description-header"> {{ number_format(\App\Helpers\GeneralHelper::borrower_loans_total_due($borrower->id)) }}  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</h5>
                        @endif
                        <span class="description-text">{{trans_choice('general.total',1)}} {{trans_choice('general.value',1)}}</span>
                    </div>
                    <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-3 col-xs-6">
                    <div class="description-block border-right">
                        @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                            <h5 class="description-header"> {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }} {{number_format(\App\Helpers\GeneralHelper::borrower_loans_total_paid($borrower->id))}} </h5>
                        @else
                            <h5 class="description-header"> {{number_format(\App\Helpers\GeneralHelper::borrower_loans_total_paid($borrower->id))}} {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</h5>
                        @endif
                        <span class="description-text">{{trans_choice('general.total',1)}} {{trans_choice('general.paid',1)}}</span>
                    </div>
                    <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-3 col-xs-6">
                    <div class="description-block border-right">
                        @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                            <h5 class="description-header"> {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }} {{number_format(\App\Helpers\GeneralHelper::borrower_loans_total_due($borrower->id)-\App\Helpers\GeneralHelper::borrower_loans_total_paid($borrower->id))}} </h5>
                        @else
                            <h5 class="description-header"> {{number_format(\App\Helpers\GeneralHelper::borrower_loans_total_due($borrower->id)-\App\Helpers\GeneralHelper::borrower_loans_total_paid($borrower->id))}}  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</h5>
                        @endif
                        <span class="description-text">{{trans_choice('general.balance',1)}}</span>

                    </div>
                    <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-3 col-xs-6">
                    <div class="description-block">
                        @if(!empty(\App\Models\LoanSchedule::where('due_date','>',date('Y-m-d'))->first()))
                            <h5 class="description-header"
                                style="font-weight: 300;color: #ff5f5f;">{{ \App\Models\LoanSchedule::where('due_date','>',date('Y-m-d'))->orderBy('due_date','asc')->first()->due_date }}</h5>
                        @endif
                        <span class="description-text">{{trans_choice('general.next_payment_due',1)}}</span>
                    </div>
                    <!-- /.description-block -->
                </div>
            </div>
            <!-- /.row -->
        </div>
    </div>

    <div class="row">

        <div class="col-md-7">

            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">{{trans_choice('general.loan',2)}}</h6>

                    <div class="heading-elements">

                    </div>
                </div>
                <div class="panel-body table-responsive ">
                    <table id="loan-data-table" class="table  table-condensed">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{trans_choice('general.principal',1)}}</th>
                            <th>{{trans_choice('general.balance',1)}}</th>
                            <th>{{trans_choice('general.released',1)}}</th>
                            <th>{{trans_choice('general.status',1)}}</th>
                            <th>{{trans_choice('general.action',1)}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($borrower->loans as $key)
                            <tr>

                                <td>{{$key->id}}</td>
                                <td>{{number_format($key->principal,2)}}</td>
                                <td>{{number_format(\App\Helpers\GeneralHelper::loan_total_balance($key->id),2)}}</td>
                                <td>{{$key->release_date}}</td>


                                <td>
                                    @if($key->maturity_date<date("Y-m-d") && \App\Helpers\GeneralHelper::loan_total_balance($key->id)>0)
                                        <span class="label label-danger">{{trans_choice('general.past_maturity',1)}}</span>
                                    @else
                                        @if($key->status=='pending')
                                            <span class="label label-warning">{{trans_choice('general.pending',1)}} {{trans_choice('general.approval',1)}}</span>
                                        @endif
                                        @if($key->status=='approved')
                                            <span class="label label-info">{{trans_choice('general.awaiting',1)}} {{trans_choice('general.disbursement',1)}}</span>
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
                                    <a href="{{ url('client/loan/'.$key->id.'/show') }}"
                                       class="btn btn-info btn-xs legitRipple" data-toggle="tooltip"
                                       data-title="{{ trans_choice('general.detail',2) }}"><i
                                                class="fa fa-search"></i>
                                    </a>
                                    @if(\App\Models\Setting::where('setting_key','enable_online_payment')->first()->setting_value==1 && \App\Helpers\GeneralHelper::loan_total_balance($key->id)>0)
                                        <a href="{{ url('client/loan/'.$key->id.'/pay') }}"
                                           class="btn btn-success btn-xs legitRipple" data-toggle="tooltip"
                                           data-title="{{ trans('general.pay') }}"><i
                                                    class="fa fa-money"></i>
                                        </a>
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">{{trans_choice('general.repayment',2)}}</h6>

                    <div class="heading-elements">

                    </div>
                </div>
                <div class="panel-body table-responsive">
                    <table id="view-repayments"
                           class="table table-condensed">
                        <thead>
                        <tr role="row">
                            <th>
                                {{trans_choice('general.collection',1)}} {{trans_choice('general.date',1)}}
                            </th>
                            <th>
                                {{trans_choice('general.method',1)}}
                            </th>
                            <th>
                                {{trans_choice('general.amount',1)}}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach(\App\Models\LoanTransaction::where('transaction_type',
            'repayment')->where('reversed', 0)->where('borrower_id', $borrower->id)->get() as $key)


                            <tr>
                                <td>{{$key->date}}</td>
                                <td>
                                    @if(!empty($key->loan_repayment_method))
                                        {{$key->loan_repayment_method->name}}
                                    @endif
                                </td>
                                <td>{{number_format($key->credit,2)}}</td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {

        });
    </script>
@endsection
@section('footer-scripts')
    <script>
        $('#loan-data-table').DataTable({
            "order": [[0, "asc"]],
            "columnDefs": [
                {"orderable": false, "targets": [5]}
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
        $('#view-repayments').DataTable({
            "order": [[0, "asc"]],
            "columnDefs": [
                {"orderable": false, "targets": []}
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