@extends('layouts.master')
@section('title')
    {{trans_choice('general.borrower',1)}}  {{trans_choice('general.report',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">
                {{trans_choice('general.borrower',1)}}  {{trans_choice('general.report',1)}}
                @if(!empty($start_date))
                    for period: <b>{{$start_date}} to {{$end_date}}</b>
                @endif
            </h6>

            <div class="heading-elements">
                <button class="btn btn-sm btn-info hidden-print" onclick="window.print()">Print</button>
            </div>
        </div>
        <div class="panel-body hidden-print">
            <h4 class="">{{trans_choice('general.date',1)}} {{trans_choice('general.range',1)}}</h4>
            {!! Form::open(array('url' => Request::url(), 'method' => 'post','class'=>'form-horizontal', 'name' => 'form')) !!}
            <div class="row">
                <div class="col-xs-4">
                    {!! Form::text('start_date',null, array('class' => 'form-control date-picker', 'placeholder'=>"From Date",'required'=>'required')) !!}
                </div>
                <div class="col-xs-4">
                    {!! Form::text('end_date',null, array('class' => 'form-control date-picker', 'placeholder'=>"To Date",'required'=>'required')) !!}
                </div>

            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-2">
                        <span class="input-group-btn">
                          <button type="submit" class="btn bg-olive btn-flat">{{trans_choice('general.search',1)}}!
                          </button>
                        </span>
                        <span class="input-group-btn">
                          <a href="{{Request::url()}}"
                             class="btn bg-purple  btn-flat pull-right">{{trans_choice('general.reset',1)}}!</a>
                        </span>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}

        </div>
        <!-- /.panel-body -->

    </div>
    <!-- /.box -->
    <div class="panel panel-white">
        <div class="panel-body table-responsive">
            <table id="reports_table" class="table table-bordered table-condensed  table-hover ">
                <thead>
                <tr  >
                    <th>
                        <b>{{trans_choice('general.borrower',1)}}  {{trans_choice('general.report',1)}}</b>
                    </th>
                    <th>
                        @if(!empty($start_date))
                            <b>{{$start_date}} to {{$end_date}}</b>
                        @endif
                    </th>
                    <th>
                        <b>{{trans_choice('general.number',1)}}</b>
                    </th>
                    <th>{{trans_choice('general.principal',1)}}</th>
                    <th>{{trans_choice('general.interest',1)}}</th>
                    <th>{{trans_choice('general.fee',2)}}</th>
                    <th>{{trans_choice('general.penalty',1)}}</th>
                    <th>{{trans_choice('general.total',1)}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <?php
                    $principal = \App\Helpers\GeneralHelper::loans_borrower_total_due_item($key->id, 'principal',
                        $start_date, $end_date);
                    $interest = \App\Helpers\GeneralHelper::loans_borrower_total_due_item($key->id, 'interest',
                        $start_date, $end_date);
                    $fees = \App\Helpers\GeneralHelper::loans_borrower_total_due_item($key->id, 'fees', $start_date,
                        $end_date);
                    $penalty = \App\Helpers\GeneralHelper::loans_borrower_total_due_item($key->id, 'penalty',
                        $start_date, $end_date);
                    $principal_payments = \App\Helpers\GeneralHelper::loans_borrower_total_paid_item($key->id,
                        'principal', $start_date, $end_date);
                    $interest_payments = \App\Helpers\GeneralHelper::loans_borrower_total_paid_item($key->id, 'interest',
                        $start_date, $end_date);
                    $fees_payments = \App\Helpers\GeneralHelper::loans_borrower_total_paid_item($key->id, 'fees',
                        $start_date, $end_date);
                    $penalty_payments = \App\Helpers\GeneralHelper::loans_borrower_total_paid_item($key->id, 'penalty',
                        $start_date, $end_date);
                    $total_due = $principal + $interest + $fees + $penalty;
                    $total_payments = $principal_payments + $interest_payments + $penalty_payments + $fees_payments;
                    $total_balance = $total_due - $total_payments;
                    ?>
                    <tr role="row" class="odd">
                        <td class="text-bold bg-gray">
                            {{$key->first_name}} {{$key->last_name}}-{{$key->unique_number}}
                        </td>
                        <td class="text-red" style="text-align:right">
                            {{trans_choice('general.due',1)}} {{trans_choice('general.loan',2)}}
                        </td>
                        <td style="text-align:right">
                            {{\App\Helpers\GeneralHelper::loans_borrower_count($key->id,$start_date,$end_date)}}
                        </td>
                        <td style="text-align:right">
                            {{number_format($principal,2)}}
                        </td>
                        <td style="text-align:right">
                            {{number_format($interest,2)}}
                        </td>
                        <td style="text-align:right">
                            {{number_format($fees,2)}}
                        </td>
                        <td style="text-align:right">
                            {{number_format($penalty,2)}}
                        </td>
                        <td style="text-align:right;" class="text-red">
                            {{number_format($total_due,2)}}
                        </td>
                    </tr>
                    <tr role="row" class="even">
                        <td class="text-green">
                        </td>
                        <td class="text-green" style="text-align:right">
                            {{trans_choice('general.payment',2)}}
                        </td>
                        <td style="text-align:right">
                            {{\App\Helpers\GeneralHelper::payments_borrower_count($key->id,$start_date,$end_date)}}
                        </td>
                        <td style="text-align:right">
                            {{number_format($principal_payments,2)}}
                        </td>
                        <td style="text-align:right">
                            {{number_format($interest_payments,2)}}
                        </td>
                        <td style="text-align:right">
                            {{number_format($fees_payments,2)}}
                        </td>
                        <td style="text-align:right">
                            {{number_format($penalty_payments,2)}}
                        </td>
                        <td style="text-align:right;" class="text-green">
                            {{number_format($total_payments,2)}}
                        </td>
                    </tr>
                    <tr role="row" class="odd">
                        <td style="text-align:right; border-bottom: 1px solid #000">
                        </td>
                        <td class="text-bold" style="text-align:right; border-bottom: 1px solid #000">
                            {{trans_choice('general.pending',1)}} {{trans_choice('general.balance',1)}}
                        </td>
                        <td style="text-align:right; border-bottom: 1px solid #000">
                        </td>
                        <td style="text-align:right; border-bottom: 1px solid #000">
                            {{number_format(($principal-$principal_payments),2)}}
                        </td>
                        <td style="text-align:right; border-bottom: 1px solid #000">
                            {{number_format(($interest-$interest_payments),2)}}
                        </td>
                        <td style="text-align:right; border-bottom: 1px solid #000">
                            {{number_format(($fees-$fees_payments),2)}}
                        </td>
                        <td style="text-align:right; border-bottom: 1px solid #000">
                            {{number_format(($penalty-$penalty_payments),2)}}
                        </td>
                        <td style="text-align:right; font-weight:bold; border-bottom: 1px solid #000">
                            {{number_format($total_balance,2)}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('footer-scripts')

    <script>
        $('#reports_table').DataTable({

            "paging": false,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true,
            "ordering": false,
            "info": false,
            "autoWidth": true,
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
