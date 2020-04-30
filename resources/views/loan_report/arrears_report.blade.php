@extends('layouts.master')
@section('title')
    {{trans_choice('general.arrears',1)}} {{trans_choice('general.report',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">
                {{trans_choice('general.arrears',1)}} {{trans_choice('general.report',1)}}
                @if(!empty($end_date))
                    as at: <b> {{$end_date}}</b>
                @endif
            </h6>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body hidden-print">
            {!! Form::open(array('url' => Request::url(), 'method' => 'post','class'=>'form-horizontal', 'name' => 'form')) !!}
            <div class="row">
                <div class="col-md-4">
                    {!! Form::label('end_date',trans_choice('general.end',1).' '.trans_choice('general.date',1),array('class'=>'')) !!}
                    {!! Form::text('end_date',$end_date, array('class' => 'form-control date-picker', 'placeholder'=>"",'required'=>'required')) !!}
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
                                    <a href="{{url('report/loan_report/arrears_report/pdf?end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/loan_report/arrears_report/excel?end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/loan_report/arrears_report/csv?end_date='.$end_date)}}"
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
    @if(!empty($end_date))
        <div class="panel panel-white">
            <div class="panel-body table-responsive no-padding">

                <table class="table table-bordered table-condensed table-hover ">
                    <thead>
                    <tr class="bg-green font-11">
                        <th>{{trans_choice('general.loan_officer',1)}}</th>
                        <th>{{trans_choice('general.borrower',1)}}</th>
                        <th>{{trans_choice('general.phone',1)}}</th>
                        <th>{{trans_choice('general.loan',1)}} {{trans_choice('general.id',1)}}</th>
                        <th>{{trans_choice('general.product',1)}}</th>
                        <th>{{trans_choice('general.amount',1)}}</th>
                        <th>{{trans_choice('general.disbursed',1)}}</th>
                        <th>{{trans_choice('general.maturity',1)}} {{trans_choice('general.date',1)}}</th>
                        <th>{{trans_choice('general.principal',1)}}</th>
                        <th>{{trans_choice('general.interest',1)}}</th>
                        <th>{{trans_choice('general.fee',2)}}</th>
                        <th>{{trans_choice('general.penalty',1)}}</th>
                        <th>{{trans_choice('general.outstanding',1)}}</th>
                        <th>{{trans_choice('general.due',1)}}</th>
                        <th>{{trans_choice('general.day',2)}} {{trans_choice('general.in',2)}} {{trans_choice('general.arrears',2)}}</th>
                        <th>{{trans_choice('general.day',2)}} {{trans_choice('general.since',1)}} {{trans_choice('general.payment',1)}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total_outstanding = 0;
                    $total_due = 0;
                    $total_principal = 0;
                    $total_interest = 0;
                    $total_fees = 0;
                    $total_penalty = 0;
                    $total_amount = 0;
                    ?>
                    @foreach(\App\Models\Loan::where('first_payment_date','<=',$end_date)->where('branch_id',
                    session('branch_id'))->where('status', 'disbursed')->orderBy('release_date','asc')->get() as $key)
                        <?php
                        $loan_due_items = \App\Helpers\GeneralHelper::loan_due_items($key->id,
                            $key->release_date, $end_date);
                        $loan_paid_items = \App\Helpers\GeneralHelper::loan_paid_items($key->id,
                            $key->release_date, $end_date);
                        $balance = \App\Helpers\GeneralHelper::loan_total_balance($key->id);
                        $due = ($loan_due_items["principal"] + $loan_due_items["interest"] + $loan_due_items["fees"] + $loan_due_items["penalty"]) - ($loan_paid_items["principal"] + $loan_paid_items["interest"] + $loan_paid_items["fees"] + $loan_paid_items["penalty"]);
                        $principal = $loan_due_items["principal"];
                        $interest = $loan_due_items["interest"];
                        $fees = $loan_due_items["fees"];
                        $penalty = $loan_due_items["penalty"];
                        if ($due > 0) {
                            $total_outstanding = $total_outstanding + $balance;
                            $total_due = $total_due + $due;
                            $total_principal = $total_principal + $principal;
                            $total_interest = $total_interest + $interest;
                            $total_fees = $total_fees + $fees;
                            $total_penalty = $total_penalty + $penalty;
                            $total_amount = $total_amount + $key->principal;
                            //lets find arrears information
                            $schedules = \App\Models\LoanSchedule::where('loan_id', $key->id)->where('due_date', '<=',
                                $end_date)->orderBy('due_date', 'asc')->get();
                            $payments = $loan_paid_items["principal"] + $loan_paid_items["interest"] + $loan_paid_items["fees"] + $loan_paid_items["penalty"];
                            if ($payments > 0) {
                                foreach ($schedules as $schedule) {
                                    if ($payments > $schedule->principal + $schedule->interest + $schedule->penalty + $schedule->fees) {
                                        $payments = $payments - ($schedule->principal + $schedule->interest + $schedule->penalty + $schedule->fees);
                                    } else {
                                        $payments = 0;
                                        $overdue_date = $schedule->due_date;
                                        break;
                                    }
                                }
                            } else {
                                $overdue_date = $schedules->first()->due_date;
                            }
                            $date1 = new DateTime($overdue_date);
                            $date2 = new DateTime($end_date);
                            $days_arrears = $date2->diff($date1)->format("%a");
                            $transaction = \App\Models\LoanTransaction::where('loan_id',
                                $key->id)->where('transaction_type',
                                'repayment')->where('reversed', 0)->orderBy('date', 'desc')->first();
                            if (!empty($transaction)) {
                                $date2 = new DateTime($transaction->date);
                                $date1 = new DateTime($end_date);
                                $days_last_payment = $date2->diff($date1)->format("%r%a");
                            } else {
                                $days_last_payment = 0;
                            }
                        }


                        //select appropriate schedules


                        ?>
                        @if($due >0)
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
                                <td>{{number_format($key->principal,2)}}</td>
                                <td>{{$key->release_date}}</td>
                                <td>{{$key->maturity_date}}</td>
                                <td>{{number_format($principal,2)}}</td>
                                <td>{{number_format($interest,2)}}</td>
                                <td>{{number_format($fees,2)}}</td>
                                <td>{{number_format($penalty,2)}}</td>
                                <td>{{number_format($due,2)}}</td>
                                <td>{{number_format($balance,2)}}</td>
                                <td>{{$days_arrears}}</td>
                                <td>{{$days_last_payment}}</td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>{{number_format($total_amount,2)}}</th>
                        <th></th>
                        <th></th>
                        <th>{{number_format($total_principal,2)}}</th>
                        <th>{{number_format($total_interest,2)}}</th>
                        <th>{{number_format($total_fees,2)}}</th>
                        <th>{{number_format($total_penalty,2)}}</th>
                        <th>{{number_format($total_outstanding,2)}}</th>
                        <th>{{number_format($total_due,2)}}</th>
                        <th></th>
                        <th></th>
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
