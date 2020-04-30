@extends('layouts.master')
@section('title'){{trans_choice('general.saving',2)}} {{trans_choice('general.report',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.saving',2)}} {{trans_choice('general.report',2)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body">
            <table id="" class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>{{trans_choice('general.name',1)}}</th>
                    <th>{{ trans_choice('general.description',1) }}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>

                <tr>
                    <td>
                        <a href="{{url('report/savings_report/savings_transactions')}}">{{trans_choice('general.saving',2)}} {{trans_choice('general.transaction',2)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.savings_transactions_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/savings_report/savings_transactions')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                <tr class="hidden">
                    <td>
                        <a href="{{url('report/savings_report/savings_balance')}}">{{trans_choice('general.saving',2)}} {{trans_choice('general.balance',2)}} {{trans_choice('general.report',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.savings_balance_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/savings_report/savings_balance')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')

@endsection
