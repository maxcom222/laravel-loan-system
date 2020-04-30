@extends('layouts.master')
@section('title')
    {{trans_choice('general.collection',1)}} {{trans_choice('general.sheet',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">
                {{trans_choice('general.collection',1)}} {{trans_choice('general.sheet',1)}}
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
                <div class="col-md-4">
                    {!! Form::label('start_date',trans_choice('general.start',1).' '.trans_choice('general.date',1),array('class'=>'')) !!}
                    {!! Form::text('start_date',$start_date, array('class' => 'form-control date-picker', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('end_date',trans_choice('general.end',1).' '.trans_choice('general.date',1),array('class'=>'')) !!}
                    {!! Form::text('end_date',$end_date, array('class' => 'form-control date-picker', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
                <div class="col-md-4">
                    {!! Form::label('user_id',trans_choice('general.loan_officer',1),array('class'=>'')) !!}
                    {!! Form::select('user_id',$users,$user_id, array('class' => 'form-control select2','required'=>'required')) !!}
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
                                    <a href="{{url('report/loan_report/collection_sheet/pdf?start_date='.$start_date.'&end_date='.$end_date."&user_id=".$user_id)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/loan_report/collection_sheet/excel?start_date='.$start_date.'&end_date='.$end_date."&user_id=".$user_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/loan_report/collection_sheet/csv?start_date='.$start_date.'&end_date='.$end_date."&user_id=".$user_id)}}"
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
                        <th>{{trans_choice('general.loan_officer',1)}}</th>
                        <th>{{trans_choice('general.borrower',1)}}</th>
                        <th>{{trans_choice('general.phone',1)}}</th>
                        <th>{{trans_choice('general.loan',1)}} {{trans_choice('general.id',1)}}</th>
                        <th>{{trans_choice('general.product',1)}}</th>
                        <th>{{trans_choice('general.expected',1)}} {{trans_choice('general.repayment',1)}} {{trans_choice('general.date',1)}}</th>
                        <th>{{trans_choice('general.maturity',1)}} {{trans_choice('general.date',1)}}</th>
                        <th>{{trans_choice('general.expected',1)}}  {{trans_choice('general.amount',1)}}</th>
                        <th>{{trans_choice('general.due',1)}}</th>
                        <th>{{trans_choice('general.outstanding',1)}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total_outstanding = 0;
                    $total_due = 0;
                    $total_expected = 0;
                    $total_actual = 0;
                    ?>
                    @foreach($data as $key)
                        <?php

                        //select appropriate schedules
                        $schedule = \App\Models\LoanSchedule::where('loan_id', $key->id)->whereBetween('due_date',
                            [$start_date, $end_date])->orderBy('due_date', 'desc')->limit(1)->first();
                        if (!empty($schedule)) {
                            $balance = \App\Helpers\GeneralHelper::loan_total_balance($key->id);
                            $loan_due_items = \App\Helpers\GeneralHelper::loan_due_items($key->id,
                                $key->release_date, $schedule->due_date);
                            $loan_paid_items = \App\Helpers\GeneralHelper::loan_paid_items($key->id,
                                $key->release_date, $schedule->due_date);
                            $expected = $schedule->principal + $schedule->interest + $schedule->fees + $schedule->panalty;
                            $due = ($loan_due_items["principal"] + $loan_due_items["interest"] + $loan_due_items["fees"] + $loan_due_items["penalty"]) - ($loan_paid_items["principal"] + $loan_paid_items["interest"] + $loan_paid_items["fees"] + $loan_paid_items["penalty"]);
                            if($due<0){
                                $actual=$expected;
                            }else{
                                $actual = 0;
                            }


                            $total_outstanding = $total_outstanding + $balance;
                            $total_due = $total_due + $due;
                            $total_expected = $total_expected + $expected;
                            $total_actual = $total_actual + $actual;
                        }




                        ?>
                        @if(!empty($schedule))
                            <tr>
                                <td>
                                    @if(!empty($key->loan_officer))
                                        <a href="{{url('user/'.$key->loan_officer_id.'/show')}}">{{$key->loan_officer->first_name}} {{$key->loan_officer->last_name}}</a>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($key->borrower))
                                        <a href="{{url('borrower/'.$key->borrower_id.'/show')}}">{{$key->borrower->first_name}} {{$key->borrower->last_name}}</a>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($key->borrower))
                                        {{$key->borrower->mobile}}
                                    @endif
                                </td>
                                <td><a href="{{url('loan/'.$key->id.'/show')}}">{{$key->id}}</a></td>
                                <td>
                                    @if(!empty($key->loan_product))
                                        {{$key->loan_product->name}}
                                    @endif
                                </td>
                                <td>{{$schedule->due_date}}</td>
                                <td>{{$key->maturity_date}}</td>
                                <td>{{number_format($expected,2)}}</td>
                                <td>{{number_format($due,2)}}</td>
                                <td>{{number_format($balance,2)}}</td>


                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><b>{{number_format($total_expected,2)}}</b></td>
                        <td><b>{{number_format($total_due,2)}}</b></td>
                        <td><b>{{number_format($total_outstanding,2)}}</b></td>



                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <script>
            $(document).ready(function () {
                $("body").addClass('sidebar-xs');
            });
        </script>
    @endif
@endsection
@section('footer-scripts')

@endsection
