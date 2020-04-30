@extends('layouts.master')
@section('title')
    {{trans_choice('general.balance',1)}} {{trans_choice('general.sheet',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">
                {{trans_choice('general.balance',1)}} {{trans_choice('general.sheet',1)}}
                @if(!empty($start_date))
                    as at: <b>{{$start_date}} </b>
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
                    {!! Form::text('start_date',$start_date, array('class' => 'form-control date-picker', 'placeholder'=>"End Date",'required'=>'required')) !!}
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
                                    <a href="{{url('report/financial_report/balance_sheet/pdf?start_date='.$start_date.'&end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/financial_report/balance_sheet/excel?start_date='.$start_date.'&end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/financial_report/balance_sheet/csv?start_date='.$start_date.'&end_date='.$end_date)}}"
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
                        <th>{{trans_choice('general.gl_code',1)}}</th>
                        <th>{{trans_choice('general.account',1)}}</th>
                        <th>{{trans_choice('general.balance',1)}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="3" style="text-align: left"><b>{{trans_choice('general.asset',2)}}</b></td>
                    </tr>
                    <?php
                    $total_liabilities = 0;
                    $total_assets = 0;
                    $total_equity = 0;
                    $retained_earnings = 0;
                    ?>
                    @foreach(\App\Models\ChartOfAccount::where('account_type','asset')->orderBy('gl_code','asc')->get() as $key)
                        <?php
                        $balance = 0;
                        $cr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                            $start_date)->where('branch_id',
                            session('branch_id'))->sum('credit');
                        $dr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                            $start_date)->where('branch_id',
                            session('branch_id'))->sum('debit');
                        $balance = $dr - $cr;
                        $total_assets = $total_assets + $balance;
                        ?>
                        <tr>
                            <td>{{ $key->gl_code }}</td>
                            <td>
                                {{$key->name}}
                            </td>
                            <td>{{ number_format($balance,2) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" style="text-align: right">
                            <b>{{trans_choice('general.total',1)}} {{trans_choice('general.asset',2)}}</b></td>
                        <td><b>{{ number_format($total_assets,2) }}</b></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align: left"><b>{{trans_choice('general.liability',2)}}</b></td>
                    </tr>
                    @foreach(\App\Models\ChartOfAccount::where('account_type','liability')->orderBy('gl_code','asc')->get() as $key)
                        <?php
                        $balance = 0;
                        $cr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                            $start_date)->where('branch_id',
                            session('branch_id'))->sum('credit');
                        $dr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                            $start_date)->where('branch_id',
                            session('branch_id'))->sum('debit');
                        $balance = $cr - $dr;
                        $total_liabilities = $total_liabilities + $balance;
                        ?>
                        <tr>
                            <td>{{ $key->gl_code }}</td>
                            <td>
                                {{$key->name}}
                            </td>
                            <td>{{ number_format($balance,2) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" style="text-align: right">
                            <b>{{trans_choice('general.total',1)}} {{trans_choice('general.liability',2)}}</b></td>
                        <td><b>{{ number_format($total_liabilities,2) }}</b></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align: left"><b>{{trans_choice('general.equity',2)}}</b></td>
                    </tr>
                    @foreach(\App\Models\ChartOfAccount::where('account_type','equity')->orderBy('gl_code','asc')->get() as $key)
                        <?php
                        $balance = 0;
                        $cr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                            $start_date)->where('branch_id',
                            session('branch_id'))->sum('credit');
                        $dr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                            $start_date)->where('branch_id',
                            session('branch_id'))->sum('debit');
                        $balance = $cr - $dr;
                        $total_equity = $total_equity + $balance;
                        ?>
                        <tr>
                            <td>{{ $key->gl_code }}</td>
                            <td>
                                {{$key->name}}
                            </td>
                            <td>{{ number_format($balance,2) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" style="text-align: right">
                            <b>{{trans_choice('general.total',1)}} {{trans_choice('general.equity',2)}}</b></td>
                        <td><b>{{ number_format($total_equity,2) }}</b></td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="2" style="text-align: right">
                            <b>{{trans_choice('general.total',1)}} {{trans_choice('general.liability',2)}} {{trans_choice('general.and',1)}} {{trans_choice('general.equity',2)}}</b>
                        </td>
                        <td><b>{{ number_format($total_liabilities+$total_equity,2) }}</b></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
@endsection
@section('footer-scripts')

@endsection
