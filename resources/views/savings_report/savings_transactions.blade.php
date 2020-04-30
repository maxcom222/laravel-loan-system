@extends('layouts.master')
@section('title')
    {{trans_choice('general.saving',2)}} {{trans_choice('general.transaction',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">
                {{trans_choice('general.saving',2)}} {{trans_choice('general.transaction',2)}}
                @if(!empty($start_date))
                    for period: <b>{{$start_date}} to {{$end_date}}</b>
                @endif
            </h6>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body hidden-print">
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
                                    <a href="{{url('report/savings_report/savings_transactions/pdf?start_date='.$start_date.'&end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/savings_report/savings_transactions/excel?start_date='.$start_date.'&end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/savings_report/savings_transactions/csv?start_date='.$start_date.'&end_date='.$end_date)}}"
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
                        <th>{{trans_choice('general.borrower',1)}}</th>
                        <th>{{trans_choice('general.account',1)}}</th>
                        <th>{{trans_choice('general.product',1)}}</th>
                        <th>{{trans_choice('general.type',1)}}</th>
                        <th>{{trans_choice('general.debit',1)}}</th>
                        <th>{{trans_choice('general.credit',1)}}</th>
                        <th>{{trans_choice('general.date',2)}}</th>
                        <th>{{trans_choice('general.receipt',1)}}</th>
                        <th>{{trans_choice('general.payment',1)}} {{trans_choice('general.method',1)}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total_deposited = 0;
                    $total_withdrawn = 0;
                    $cr = 0;
                    $dr = 0;
                    ?>
                    @foreach($data as $key)
                        <?php
                        $dr = $dr + $key->debit;
                        $cr = $cr + $key->credit;
                        ?>
                        <tr>

                            <td>
                                @if(!empty($key->borrower))
                                    <a href="{{url('borrower/'.$key->borrower_id.'/show')}}">{{$key->borrower->first_name}} {{$key->borrower->last_name}}</a>
                                @endif
                            </td>
                            <td>{{$key->savings_id}}</td>
                            <td>
                                @if(!empty($key->savings))
                                    @if(!empty($key->savings->savings_product))
                                        {{$key->savings->savings_product->name}}
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($key->type=='deposit')
                                    {{trans_choice('general.deposit',1)}}
                                @endif
                                @if($key->type=='withdrawal')
                                    {{trans_choice('general.withdrawal',1)}}
                                @endif
                                @if($key->type=='bank_fees')
                                    {{trans_choice('general.charge',1)}}
                                @endif
                                @if($key->type=='interest')
                                    {{trans_choice('general.interest',1)}}
                                @endif
                                @if($key->type=='dividend')
                                    {{trans_choice('general.dividend',1)}}
                                @endif
                                @if($key->type=='transfer')
                                    {{trans_choice('general.transfer',1)}}
                                @endif
                                @if($key->type=='guarantee')
                                    {{trans_choice('general.on',1)}} {{trans_choice('general.hold',1)}}
                                @endif
                                @if($key->reversed==1)
                                    @if($key->reversal_type=="user")
                                        <span class="text-danger"><b>({{trans_choice('general.user',1)}} {{trans_choice('general.reversed',1)}}
                                                )</b></span>
                                    @endif
                                    @if($key->reversal_type=="system")
                                        <span class="text-danger"><b>({{trans_choice('general.system',1)}} {{trans_choice('general.reversed',1)}}
                                                )</b></span>
                                    @endif
                                @endif
                            </td>
                            <td>{{number_format($key->debit,2)}}</td>
                            <td>{{number_format($key->credit,2)}}</td>
                            <td>{{$key->date}}</td>
                            <td>{{$key->receipt}}</td>
                            <td>
                                @if(!empty($key->repayment_method))
                                    {{$key->repayment_method->name}}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>{{number_format($dr,2)}}</b></td>
                        <td><b>{{number_format($cr,2)}}</b></td>
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
