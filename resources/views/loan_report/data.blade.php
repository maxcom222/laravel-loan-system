@extends('layouts.master')
@section('title'){{trans_choice('general.loan',1)}} {{trans_choice('general.report',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.loan',1)}} {{trans_choice('general.report',2)}}</h6>

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
                        <a href="{{url('report/loan_report/collection_sheet')}}">{{trans_choice('general.collection',2)}} {{trans_choice('general.sheet',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.collection_sheet_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan_report/collection_sheet')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{url('report/loan_report/repayments_report')}}">{{trans_choice('general.repayment',2)}} {{trans_choice('general.report',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.repayments_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan_report/repayments_report')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>

                <tr>
                    <td>
                        <a href="{{url('report/loan_report/expected_repayments')}}">{{trans_choice('general.expected',1)}} {{trans_choice('general.repayment',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.expected_repayments_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan_report/expected_repayments')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{url('report/loan_report/arrears_report')}}">{{trans_choice('general.arrears',1)}} {{trans_choice('general.report',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.arrears_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan_report/arrears_report')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{url('report/loan_report/disbursed_loans')}}">{{trans_choice('general.disbursed',1)}} {{trans_choice('general.loan',2)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.disbursed_loans_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan_report/disbursed_loans')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                <tr class="hidden">
                    <td>
                        <a href="{{url('report/loan_report/written_off_loans')}}">{{trans_choice('general.loan',2)}} {{trans_choice('general.written_off',2)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.written_off_loans_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/loan_report/written_off_loans')}}"><i class="icon-search4"></i> </a>
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
