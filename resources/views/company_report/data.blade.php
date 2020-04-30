@extends('layouts.master')
@section('title'){{trans_choice('general.organisation',1)}} {{trans_choice('general.report',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.organisation',1)}} {{trans_choice('general.report',2)}}</h6>

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

                <tr class="hidden">
                    <td>
                        <a href="{{url('report/company_report/indicator_report')}}">{{trans_choice('general.indicator',1)}} {{trans_choice('general.report',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.indicator_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/company_report/indicator_report')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                <tr class="hidden">
                    <td>
                        <a href="{{url('report/company_report/loan_officer_performance')}}"> {{trans_choice('general.loan_officer',2)}} {{trans_choice('general.performance',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.loan_officer_performance_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/company_report/loan_officer_performance')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>

                <tr>
                    <td>
                        <a href="{{url('report/company_report/products_summary')}}">{{trans_choice('general.product',2)}} {{trans_choice('general.summary',1)}} {{trans_choice('general.report',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.products_summary_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/company_report/products_summary')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{url('report/company_report/general_report')}}">{{trans_choice('general.general',2)}} {{trans_choice('general.report',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.general_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/company_report/general_report')}}"><i class="icon-search4"></i> </a>
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
