@extends('layouts.master')
@section('title')
    {{trans_choice('general.repayment',2)}} {{trans_choice('general.report',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">
                {{trans_choice('general.repayment',2)}} {{trans_choice('general.report',1)}}
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
                                    <a href="{{url('report/loan_report/repayments_report/pdf?start_date='.$start_date.'&end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/loan_report/repayments_report/excel?start_date='.$start_date.'&end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/loan_report/repayments_report/csv?start_date='.$start_date.'&end_date='.$end_date)}}"
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
    @if(!empty($start_date))
        <div class="panel panel-white">
            <div class="panel-body table-responsive no-padding">

                <table class="table table-bordered table-condensed table-hover">
                    <thead>
                    <tr class="bg-green">
                        <th>{{trans_choice('general.id',1)}}</th>
                        <th>{{trans_choice('general.borrower',1)}}</th>
                        <th>{{trans_choice('general.principal',1)}}</th>
                        <th>{{trans_choice('general.interest',1)}}</th>
                        <th>{{trans_choice('general.fee',2)}}</th>
                        <th>{{trans_choice('general.penalty',2)}}</th>
                        <th>{{trans_choice('general.total',1)}}</th>
                        <th>{{trans_choice('general.date',1)}}</th>
                        <th>{{trans_choice('general.receipt',1)}}</th>
                        <th>{{trans_choice('general.payment',1)}} {{trans_choice('general.method',1)}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total_principal = 0;
                    $total_fees = 0;
                    $total_interest = 0;
                    $total_penalty = 0;
                    ?>
                    @foreach($data as $key)
                        <?php
                        $principal = \App\Models\JournalEntry::where('loan_transaction_id', $key->id)->where('reversed',
                            0)->where('transaction_sub_type', 'repayment_principal')->sum('credit');
                        $interest = \App\Models\JournalEntry::where('loan_transaction_id', $key->id)->where('reversed',
                            0)->where('transaction_sub_type', 'repayment_interest')->sum('credit');
                        $fees = \App\Models\JournalEntry::where('loan_transaction_id', $key->id)->where('reversed',
                            0)->where('transaction_sub_type', 'repayment_fees')->sum('credit');
                        $penalty = \App\Models\JournalEntry::where('loan_transaction_id', $key->id)->where('reversed',
                            0)->where('transaction_sub_type', 'repayment_penalty')->sum('credit');
                        $total_principal = $total_principal + $principal;
                        $total_interest = $total_interest + $interest;
                        $total_fees = $total_fees + $fees;
                        $total_penalty = $total_penalty + $penalty;
                        ?>
                        <tr>
                            <td>{{$key->id}}</td>
                            <td>
                                @if(!empty($key->borrower))
                                    <a href="{{url('borrower/'.$key->borrower_id.'/show')}}">{{$key->borrower->first_name}} {{$key->borrower->last_name}}</a>
                                @endif
                            </td>
                            <td>{{number_format($principal,2)}}</td>
                            <td>{{number_format($interest,2)}}</td>
                            <td>{{number_format($fees,2)}}</td>
                            <td>{{number_format($penalty,2)}}</td>
                            <td>{{number_format($principal+$interest+$fees+$penalty,2)}}</td>
                            <td>{{$key->date}}</td>
                            <td>{{$key->receipt}}</td>
                            <td>
                                @if(!empty($key->loan_repayment_method))
                                    {{$key->loan_repayment_method->name}}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td><b>{{number_format($total_principal,2)}}</b></td>
                        <td><b>{{number_format($total_interest,2)}}</b></td>
                        <td><b>{{number_format($total_fees,2)}}</b></td>
                        <td><b>{{number_format($total_penalty,2)}}</b></td>
                        <td><b>{{number_format($total_principal+$total_interest+$total_fees+$total_penalty,2)}}</b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
@endsection
@section('footer-scripts')

@endsection
