@extends('layouts.master')
@section('title'){{trans_choice('general.borrower',1)}} {{trans_choice('general.report',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.borrower',1)}} {{trans_choice('general.report',2)}}</h6>

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
                        <a href="{{url('report/borrower_report/borrowers_overview')}}">{{trans_choice('general.borrower',2)}} {{trans_choice('general.overview',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.borrowers_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/borrower_report/borrowers_overview')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="{{url('report/borrower_report/borrower_numbers')}}">{{trans_choice('general.borrower',1)}} {{trans_choice('general.number',2)}} {{trans_choice('general.report',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.borrower_numbers_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/borrower_report/borrower_numbers')}}"><i class="icon-search4"></i> </a>
                    </td>
                </tr>

                <tr class="hidden">
                    <td>
                        <a href="{{url('report/borrower_report/top_borrowers')}}">{{trans_choice('general.top',1)}} {{trans_choice('general.borrower',2)}} {{trans_choice('general.report',1)}}</a>
                    </td>
                    <td>
                        {{trans_choice('general.top_borrowers_report_description',1)}}
                    </td>
                    <td><a href="{{url('report/borrower_report/top_borrowers')}}"><i class="icon-search4"></i> </a>
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
