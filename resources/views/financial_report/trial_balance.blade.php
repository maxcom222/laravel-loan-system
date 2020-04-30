@extends('layouts.master')
@section('title')
    {{trans_choice('general.trial_balance',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">
                {{trans_choice('general.trial_balance',1)}}
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
                                    <a href="{{url('report/financial_report/trial_balance/pdf?start_date='.$start_date.'&end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/financial_report/trial_balance/excel?start_date='.$start_date.'&end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/financial_report/trial_balance/csv?start_date='.$start_date.'&end_date='.$end_date)}}"
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
                    <tr  class="bg-green">
                        <th>{{trans_choice('general.gl_code',1)}}</th>
                        <th>{{trans_choice('general.account',1)}}</th>
                        <th>{{trans_choice('general.debit',1)}}</th>
                        <th>{{trans_choice('general.credit',1)}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $credit_total = 0;
                    $debit_total = 0;
                    ?>
                    @foreach(\App\Models\ChartOfAccount::orderBy('gl_code','asc')->get() as $key)
                        <?php
                        $cr = 0;
                        $dr = 0;
                        $cr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                            [$start_date, $end_date])->where('branch_id',
                            session('branch_id'))->sum('credit');
                        $dr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                            [$start_date, $end_date])->where('branch_id',
                            session('branch_id'))->sum('debit');
                        $credit_total = $credit_total + $cr;
                        $debit_total = $debit_total + $dr;
                        ?>
                        <tr>
                            <td>{{ $key->gl_code }}</td>
                            <td>
                                {{$key->name}}
                            </td>
                            <td>{{ number_format($dr,2) }}</td>
                            <td>{{ number_format($cr,2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="2"><b>{{trans_choice('general.total',1)}}</b></td>
                        <th>{{number_format($debit_total,2)}}</th>
                        <th>{{number_format($credit_total,2)}}</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
@endsection
@section('footer-scripts')

@endsection
