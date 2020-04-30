@extends('layouts.master')
@section('title')
    {{trans_choice('general.expected',1)}} {{trans_choice('general.repayment',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">
                {{trans_choice('general.expected',1)}} {{trans_choice('general.repayment',2)}}
                @if(!empty($start_date))
                    for period: <b>{{$start_date}} to {{$end_date}}</b>
                @endif
            </h6>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body hidden-print">
            <h4 class="">{{trans_choice('general.date',1)}} {{trans_choice('general.range',1)}}</h4>
            {!! Form::open(array('url' => Request::url(), 'method' => 'post','class'=>'form-horizontal', 'name' => 'form')) !!}
            <div class="row">
                <div class="col-xs-5">
                    {!! Form::text('start_date',$start_date, array('class' => 'form-control date-picker', 'placeholder'=>"From Date",'required'=>'required')) !!}
                </div>
                <div class="col-xs-1  text-center" style="padding-top: 5px;">
                    to
                </div>
                <div class="col-xs-5">
                    {!! Form::text('end_date',$end_date, array('class' => 'form-control date-picker', 'placeholder'=>"To Date",'required'=>'required')) !!}
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-success">{{trans_choice('general.search',1)}}!
                        </button>


                        <a href="{{Request::url()}}"
                           class="btn btn-danger">{{trans_choice('general.reset',1)}}!</a>

                        <div class="btn-group">
                            <button type="button" class="btn bg-blue dropdown-toggle legitRipple"
                                    data-toggle="dropdown">{{trans_choice('general.download',1)}} {{trans_choice('general.report',1)}}
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="{{url('report/loan_report/expected_repayments/pdf?start_date='.$start_date.'&end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/loan_report/expected_repayments/excel?start_date='.$start_date.'&end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/loan_report/expected_repayments/csv?start_date='.$start_date.'&end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-download"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.csv',1)}}
                                    </a></li>
                            </ul>
                        </div>

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

            <table class="table table-bordered table-condensed table-hover">
                <thead>
                <tr class="bg-green">
                    <th></th>
                    <th>{{trans_choice('general.principal',1)}}</th>
                    <th>{{trans_choice('general.interest',1)}}</th>
                    <th>{{trans_choice('general.fee',2)}}</th>
                    <th>{{trans_choice('general.penalty',2)}}</th>
                    <th>{{trans_choice('general.total',1)}}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><b>{{trans_choice('general.expected',1)}}</b></td>
                    <td>{{number_format($due_items["principal"],2)}}</td>
                    <td>{{number_format($due_items["interest"],2)}}</td>
                    <td>{{number_format($due_items["fees"],2)}}</td>
                    <td>{{number_format($due_items["penalty"],2)}}</td>
                    <td>{{number_format($due_items["principal"]+$due_items["interest"]+$due_items["fees"]+$due_items["penalty"],2)}}</td>
                </tr>
                <tr>
                    <td><b>{{trans_choice('general.actual',1)}}</b></td>
                    <td>{{number_format($paid_items["principal"],2)}}</td>
                    <td>{{number_format($paid_items["interest"],2)}}</td>
                    <td>{{number_format($paid_items["fees"],2)}}</td>
                    <td>{{number_format($paid_items["penalty"],2)}}</td>
                    <td>{{number_format($paid_items["principal"]+$paid_items["interest"]+$paid_items["fees"]+$paid_items["penalty"],2)}}</td>
                </tr>
                <tr>
                    <td><b>{{trans_choice('general.balance',1)}}</b></td>
                    <td>{{number_format($due_items["principal"]-$paid_items["principal"],2)}}</td>
                    <td>{{number_format($due_items["interest"]-$paid_items["interest"],2)}}</td>
                    <td>{{number_format($due_items["fees"]-$paid_items["fees"],2)}}</td>
                    <td>{{number_format($due_items["penalty"]-$paid_items["penalty"],2)}}</td>
                    <td>{{number_format(($due_items["principal"]+$due_items["interest"]+$due_items["fees"]+$due_items["penalty"])-($paid_items["principal"]+$paid_items["interest"]+$paid_items["fees"]+$paid_items["penalty"]),2)}}</td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('footer-scripts')

@endsection
