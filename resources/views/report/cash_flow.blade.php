@extends('layouts.master')
@section('title')
    {{trans_choice('general.cash_flow',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">
                {{trans_choice('general.cash_flow',1)}}
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
                <div class="col-xs-5">
                    {!! Form::text('start_date',null, array('class' => 'form-control date-picker', 'placeholder'=>"From Date",'required'=>'required')) !!}
                </div>
                <div class="col-xs-1  text-center" style="padding-top: 5px;">
                    to
                </div>
                <div class="col-xs-5">
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
        <div class="panel-body table-responsive no-padding">

            <div class="col-sm-6">
                <table class="table table-bordered table-condensed table-hover">
                    <tbody>
                    <tr style="background-color: #F2F8FF">
                        <td></td>
                        <td style="text-align:right"><b>{{trans_choice('general.balance',1)}} </b></td>
                    </tr>
                    <tr>
                        <td class="text-blue"><b>{{trans_choice('general.receipt',2)}}</b></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            <b>{{trans_choice('general.capital',1)}}</b>
                        </td>
                        <td style="text-align:right">{{number_format($capital,2)}}</td>
                    </tr>
                    <tr>
                        <td>
                            <b>{{trans_choice('general.loan',1)}} {{trans_choice('general.principal',1)}} {{trans_choice('general.repayment',2)}}</b>
                        </td>
                        <td style="text-align:right">{{number_format($principal_paid,2)}}</td>
                    </tr>
                    <tr>
                        <td>
                            <b>{{trans_choice('general.loan',1)}} {{trans_choice('general.interest',1)}} {{trans_choice('general.repayment',2)}}</b>
                        </td>
                        <td style="text-align:right">{{number_format($interest_paid,2)}}</td>
                    </tr>
                    <tr>
                        <td>
                            <b>{{trans_choice('general.loan',1)}} {{trans_choice('general.penalty',1)}} {{trans_choice('general.repayment',2)}}</b>
                        </td>
                        <td style="text-align:right">{{number_format($penalty_paid,2)}}</td>
                    </tr>
                    <tr>
                        <td>
                            <b>{{trans_choice('general.loan',1)}} {{trans_choice('general.fee',2)}} {{trans_choice('general.repayment',2)}}</b>
                        </td>
                        <td style="text-align:right">{{number_format($fees_paid,2)}}</td>
                    </tr>
                    <tr>
                        <td><b>{{trans_choice('general.saving',2)}} {{trans_choice('general.deposit',2)}}</b></td>
                        <td style="text-align:right">{{number_format($deposits,2)}}</td>
                    </tr>
                    <tr>
                        <td><b>{{trans_choice('general.other_income',1)}}</b></td>
                        <td style="text-align:right">{{number_format($other_income,2)}}</td>
                    </tr>
                    <tr class="active">
                        <td style="border-bottom:1px solid #000000">
                            <b>{{trans_choice('general.total',1)}} {{trans_choice('general.receipt',2)}} (A)</b></td>
                        <td style="text-align:right; border-bottom:1px solid #000000"
                            class="text-bold">{{number_format($total_receipts,2)}}</td>
                    </tr>
                    <tr>
                        <td class="text-blue"><b>{{trans_choice('general.payment',2)}}</b></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><b>{{trans_choice('general.expense',2)}}</b></td>
                        <td style="text-align:right">{{number_format($expenses,2)}}</td>
                    </tr>
                    <tr>
                        <td><b>{{trans_choice('general.payroll',1)}}</b></td>
                        <td style="text-align:right">{{number_format($payroll,2)}}</td>
                    </tr>
                    <tr>
                        <td><b>{{trans_choice('general.loan',2)}} {{trans_choice('general.released',1)}}
                                ({{trans_choice('general.principal',1)}})</b></td>
                        <td style="text-align:right">{{number_format($principal,2)}}</td>
                    </tr>
                    <tr>
                        <td><b>{{trans_choice('general.saving',2)}} {{trans_choice('general.deposit',2)}}</b></td>
                        <td style="text-align:right">{{number_format($withdrawals,2)}}</td>
                    </tr>
                    <tr class="active">
                        <td style="border-bottom:1px solid #000000">
                            <b>{{trans_choice('general.total',1)}} {{trans_choice('general.payment',2)}} (B)</b></td>
                        <td style="text-align:right; border-bottom:1px solid #000000 " class="text-red text-bold">
                            ({{number_format($total_payments,2)}})
                        </td>
                    </tr>
                    <tr class="info">
                        <td style="color:green;">
                            <b>{{trans_choice('general.total',1)}} {{trans_choice('general.cash',1)}} {{trans_choice('general.balance',1)}}
                                (A) - (B)</b></td>
                        <td style="text-align:right"><b>{{number_format($cash_balance,2)}}</b></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('footer-scripts')

@endsection
