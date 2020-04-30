@extends('client.layout')
@section('title')
    {{ trans_choice('general.loan',1) }}  {{ trans_choice('general.detail',2) }}
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="panel panel-white">
                <div class="panel-body">
                    <ul class="nav nav-tabs">
                        @if($loan->status=="disbursed" || $loan->status=="closed" || $loan->status=="withdrawn" || $loan->status=="written_off" || $loan->status=="rescheduled" )
                            <li class=""><a href="#transactions" data-toggle="tab"
                                            aria-expanded="true">{{trans_choice('general.transaction',2)}}</a></li>
                            <li><a href="#loan_schedule" data-toggle="tab"
                                   aria-expanded="false">{{trans_choice('general.loan',1)}}
                                    {{trans_choice('general.schedule',1)}}</a></li>
                            <li class=""><a href="#pending_dues" data-toggle="tab"
                                            aria-expanded="false">{{trans_choice('general.summary',1)}}</a>
                            </li>

                        @endif
                        <li class="active"><a href="#loan_terms" data-toggle="tab"
                                              aria-expanded="false"> {{trans_choice('general.detail',2)}}</a>
                        </li>

                        <li class=""><a href="#loan_collateral" data-toggle="tab"
                                        aria-expanded="false">{{trans_choice('general.loan',1)}} {{trans_choice('general.collateral',1)}}</a>
                        </li>
                        <li class=""><a href="#loan_guarantors" data-toggle="tab"
                                        aria-expanded="false">{{trans_choice('general.loan',1)}} {{trans_choice('general.guarantor',2)}}</a>
                        </li>
                        <li class=""><a href="#loan_files" data-toggle="tab"
                                        aria-expanded="false">{{trans_choice('general.loan',1)}} {{trans_choice('general.file',2)}}</a>
                        </li>


                    </ul>
                    <div class="tab-content">
                    @if($loan->status=="disbursed" || $loan->status=="closed" || $loan->status=="withdrawn" || $loan->status=="written_off" || $loan->status=="rescheduled" )
                        <!-- /.tab-pane -->
                            <div class="tab-pane " id="transactions">
                                <div class="btn-group-horizontal">
                                    @if(\App\Models\Setting::where('setting_key','enable_online_payment')->first()->setting_value==1)
                                        <a type="button" class="btn btn-info m-10"
                                           href="{{url('client/loan/'.$loan->id.'/pay')}}">{{trans_choice('general.add',1)}}
                                            {{trans_choice('general.repayment',1)}}</a>
                                    @endif
                                </div>
                                <div class="box box-info">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="table-responsive">
                                                    <table id="repayments-data-table"
                                                           class="table  table-condensed table-hover">
                                                        <thead>
                                                        <tr>
                                                            <th>
                                                                {{trans_choice('general.id',1)}}
                                                            </th>
                                                            <th>
                                                                {{trans_choice('general.date',1)}}
                                                            </th>
                                                            <th>
                                                                {{trans_choice('general.submitted',1)}} {{trans_choice('general.on',1)}}
                                                            </th>
                                                            <th>
                                                                {{trans_choice('general.type',1)}}
                                                            </th>

                                                            <th>
                                                                {{trans_choice('general.debit',1)}}
                                                            </th>
                                                            <th>
                                                                {{trans_choice('general.credit',1)}}
                                                            </th>
                                                            <th>
                                                                {{trans_choice('general.balance',1)}}
                                                            </th>
                                                            <th>
                                                                {{trans_choice('general.detail',2)}}
                                                            </th>

                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        $balance = 0;
                                                        ?>
                                                        @foreach(\App\Models\LoanTransaction::where('loan_id',$loan->id)->whereIn('reversal_type',['user','none'])->get() as $key)
                                                            <?php
                                                            $balance = $balance + ($key->debit - $key->credit);
                                                            ?>
                                                            <tr>
                                                                <td>{{$key->id}}</td>
                                                                <td>{{$key->date}}</td>
                                                                <td>{{$key->created_at}}</td>
                                                                <td>
                                                                    @if($key->transaction_type=='disbursement')
                                                                        {{trans_choice('general.disbursement',1)}}
                                                                    @endif
                                                                    @if($key->transaction_type=='specified_due_date_fee')
                                                                        {{trans_choice('general.specified_due_date',2)}}   {{trans_choice('general.fee',1)}}
                                                                    @endif
                                                                    @if($key->transaction_type=='installment_fee')
                                                                        {{trans_choice('general.installment_fee',2)}}
                                                                    @endif
                                                                    @if($key->transaction_type=='overdue_installment_fee')
                                                                        {{trans_choice('general.overdue_installment_fee',2)}}
                                                                    @endif
                                                                    @if($key->transaction_type=='loan_rescheduling_fee')
                                                                        {{trans_choice('general.loan_rescheduling_fee',2)}}
                                                                    @endif
                                                                    @if($key->transaction_type=='overdue_maturity')
                                                                        {{trans_choice('general.overdue_maturity',2)}}
                                                                    @endif
                                                                    @if($key->transaction_type=='disbursement_fee')
                                                                        {{trans_choice('general.disbursement',1)}} {{trans_choice('general.charge',2)}}
                                                                    @endif
                                                                    @if($key->transaction_type=='interest')
                                                                        {{trans_choice('general.interest',1)}} {{trans_choice('general.applied',2)}}
                                                                    @endif
                                                                    @if($key->transaction_type=='repayment')
                                                                        {{trans_choice('general.repayment',1)}}
                                                                    @endif
                                                                    @if($key->transaction_type=='penalty')
                                                                        {{trans_choice('general.penalty',1)}}
                                                                    @endif
                                                                    @if($key->transaction_type=='interest_waiver')
                                                                        {{trans_choice('general.interest',1)}} {{trans_choice('general.waiver',2)}}
                                                                    @endif
                                                                    @if($key->transaction_type=='waiver')
                                                                        {{trans_choice('general.waiver',2)}}
                                                                    @endif
                                                                    @if($key->transaction_type=='charge_waiver')
                                                                        {{trans_choice('general.charge',1)}}  {{trans_choice('general.waiver',2)}}
                                                                    @endif
                                                                    @if($key->transaction_type=='write_off')
                                                                        {{trans_choice('general.write_off',1)}}
                                                                    @endif
                                                                    @if($key->transaction_type=='write_off_recovery')
                                                                        {{trans_choice('general.recovery',1)}} {{trans_choice('general.repayment',1)}}
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
                                                                <td>{{number_format($balance,2)}}</td>
                                                                <td>{{$key->receipt}}</td>

                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.tab-pane -->
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="loan_schedule">
                                <div class="row">
                                    <div class="col-sm-3">

                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-info dropdown-toggle m-10"
                                                    data-toggle="dropdown"
                                                    aria-expanded="false">{{trans_choice('general.loan',1)}} {{trans_choice('general.schedule',1)}}
                                                <span class="fa fa-caret-down"></span></button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li>
                                                    <a href="{{url('client/loan/'.$loan->id.'/schedule/print')}}"
                                                       target="_blank">{{trans_choice('general.print',1)}} {{trans_choice('general.statement',1)}}</a>
                                                </li>
                                                <li>
                                                    <a href="{{url('client/loan/'.$loan->id.'/schedule/pdf')}}"
                                                       target="_blank">{{trans_choice('general.download',1)}} {{trans_choice('general.in',1)}} {{trans_choice('general.pdf',1)}}</a>
                                                </li>


                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-sm-9 pull-right">
                                        <div class="btn-group-horizontal">
                                            <a type="button" class="btn btn-info m-10"
                                               href="{{url('client/loan/'.$loan->id.'/schedule/print')}}"
                                               target="_blank">{{trans_choice('general.print',1)}} {{trans_choice('general.schedule',1)}}</a>

                                        </div>
                                    </div>
                                </div>
                                <div class="box box-success">
                                    <div class="panel-body table-responsive no-padding">
                                        <table class="table table-bordered table-condensed table-hover">
                                            <tbody>
                                            <tr>
                                                <th style="width: 10px">
                                                    <b>#</b>
                                                </th>
                                                <th>
                                                    <b>{{trans_choice('general.date',1)}}</b>
                                                </th>
                                                <th style="text-align:right;">
                                                    <b>{{trans_choice('general.paid',1)}} {{trans_choice('general.by',1)}}</b>
                                                </th>
                                                <th>
                                                    <b>{{trans_choice('general.description',1)}}</b>
                                                </th>
                                                <th style="">
                                                    <b>{{trans_choice('general.principal',1)}}</b>
                                                </th>
                                                <th style="text-align:right;">
                                                    <b>{{trans_choice('general.interest',1)}}</b>
                                                </th>
                                                <th style="text-align:right;">
                                                    <b>{{trans_choice('general.fee',2)}}</b>
                                                </th>
                                                <th style="text-align:right;">
                                                    <b>{{trans_choice('general.penalty',1)}}</b>
                                                </th>

                                                <th style="text-align:right;">
                                                    <b> {{trans_choice('general.due',1)}}</b>
                                                </th>
                                                <th style="text-align:right;">
                                                    <b>{{trans_choice('general.paid',1)}}</b>
                                                </th>
                                                <th style="text-align:right;">
                                                    <b> {{trans_choice('general.pending',1)}} {{trans_choice('general.due',1)}}</b>
                                                </th>
                                                <th style="text-align:right;">
                                                    <b> {{trans_choice('general.principal',1)}} {{trans_choice('general.balance',1)}}</b>
                                                </th>
                                            </tr>
                                            <?php
                                            //check for disbursement charges
                                            $disbursement_charges = \App\Models\LoanTransaction::where('loan_id',
                                                $loan->id)->where('transaction_type',
                                                'disbursement_fee')->where('reversed', 0)->sum('debit');
                                            ?>
                                            <tr>
                                                <td></td>
                                                <td>{{$loan->release_date}}</td>
                                                <td></td>
                                                <td>{{trans_choice('general.disbursement',1)}}</td>
                                                <td></td>
                                                <td></td>
                                                <td style="text-align:right;">
                                                    @if(!empty($disbursement_charges))
                                                        <b>{{number_format($disbursement_charges,2)}}</b>
                                                    @endif
                                                </td>
                                                <td></td>
                                                <td style="text-align:right;">
                                                    @if(!empty($disbursement_charges))
                                                        <b>{{number_format($disbursement_charges,2)}}</b>
                                                    @endif
                                                </td>
                                                <td style="text-align:right;">
                                                    @if(!empty($disbursement_charges))
                                                        <b>{{number_format($disbursement_charges,2)}}</b>
                                                    @endif
                                                </td>
                                                <td></td>
                                                <td style="text-align:right;">
                                                    {{number_format(\App\Models\LoanSchedule::where('loan_id',
                                                    $loan->id)->sum('principal'),2)}}
                                                </td>
                                            </tr>
                                            <?php
                                            $timely = 0;
                                            $total_overdue = 0;
                                            $overdue_date = "";
                                            $total_till_now = 0;
                                            $count = 1;
                                            $total_due = 0;
                                            $principal_balance = \App\Models\LoanSchedule::where('loan_id',
                                                $loan->id)->sum('principal');
                                            $payments = \App\Helpers\GeneralHelper::loan_total_paid($loan->id);
                                            $total_paid = $payments;
                                            $next_payment = [];
                                            $next_payment_amount = "";
                                            foreach ($loan->schedules as $schedule) {
                                            $principal_balance = $principal_balance - $schedule->principal;
                                            $total_due = $total_due + ($schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty- $schedule->interest_waived);
                                            $paid = 0;
                                            $paid_by = '';
                                            $overdue = 0;
                                            $due = $schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty- $schedule->interest_waived;
                                            if ($payments > 0) {
                                                if ($payments > $due) {
                                                    $paid = $due;
                                                    $payments = $payments - $due;
                                                    //find the corresponding paid by date
                                                    $p_paid = 0;
                                                    foreach (\App\Models\LoanTransaction::where('loan_id',
                                                        $loan->id)->where('transaction_type',
                                                        'repayment')->where('reversed', 0)->orderBy('date',
                                                        'asc')->get() as $key) {
                                                        $p_paid = $p_paid + $key->credit;
                                                        if ($p_paid >= $total_due) {
                                                            $paid_by = $key->date;
                                                            if ($key->date > $schedule->due_date && date("Y-m-d") > $schedule->due_date) {
                                                                $overdue = 1;
                                                                $total_overdue = $total_overdue + 1;
                                                                $overdue_date = '';
                                                            }
                                                            break;
                                                        }
                                                    }
                                                } else {
                                                    $paid = $payments;
                                                    $payments = 0;
                                                    if (date("Y-m-d") > $schedule->due_date) {
                                                        $overdue = 1;
                                                        $total_overdue = $total_overdue + 1;
                                                        $overdue_date = $schedule->due_date;
                                                    }
                                                    $next_payment[$schedule->due_date] = (($schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty- $schedule->interest_waived) - $paid);
                                                }
                                            } else {
                                                if (date("Y-m-d") > $schedule->due_date) {
                                                    $overdue = 1;
                                                    $total_overdue = $total_overdue + 1;
                                                    $overdue_date = $schedule->due_date;
                                                }
                                                $next_payment[$schedule->due_date] = (($schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty- $schedule->interest_waived));
                                            }
                                            $outstanding = $due - $paid;
                                            //check if the schedule has been paid in time


                                            ?>
                                            <tr class="@if($overdue==1) danger  @endif @if($overdue==0 && $outstanding==0) success  @endif">
                                                <td>
                                                    {{$count}}
                                                </td>
                                                <td>
                                                    {{$schedule->due_date}}
                                                </td>
                                                <td style="">
                                                    @if(empty($paid_by) && $overdue==1)
                                                        {{trans_choice('general.overdue',1)}}
                                                    @endif
                                                    @if(!empty($paid_by) && $overdue==1)
                                                        {{$paid_by}} <i class="fa fa-minus-circle" data-toggle="tooltip"
                                                                        title=" {{trans_choice('general.late',1)}}"></i>
                                                    @endif
                                                    @if(!empty($paid_by) && $overdue==0)
                                                        {{$paid_by}} <i class="fa fa-check-circle" data-toggle="tooltip"
                                                                        title=" {{trans_choice('general.timely',1)}}"></i>
                                                    @endif

                                                </td>
                                                <td>
                                                    {{$schedule->description}}
                                                </td>
                                                <td style="text-align:right">
                                                    {{number_format($schedule->principal,2)}}
                                                </td>
                                                <td style="text-align:right">
                                                    @if($schedule->interest_waived>0)
                                                        <s> {{number_format($schedule->interest_waived,2)}}</s>
                                                    @endif
                                                    {{number_format($schedule->interest,2)}}
                                                </td>
                                                <td style="text-align:right">
                                                    {{number_format($schedule->fees,2)}}
                                                </td>
                                                <td style="text-align:right">
                                                    {{number_format($schedule->penalty,2)}}
                                                </td>
                                                <td style="text-align:right; font-weight:bold">
                                                    {{number_format($due,2)}}
                                                </td>

                                                <td style="text-align:right;">
                                                    {{number_format($paid,2)}}
                                                </td>
                                                <td style="text-align:right;">
                                                    {{number_format($outstanding,2)}}
                                                </td>
                                                <td style="text-align:right;">
                                                    {{number_format($principal_balance,2)}}
                                                </td>

                                            </tr>
                                            <?php
                                            $count++;
                                            }
                                            ?>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td style="font-weight:bold">{{trans_choice('general.total',1)}} {{trans_choice('general.due',1)}}</td>
                                                <td style="text-align:right;">
                                                    {{number_format(\App\Helpers\GeneralHelper::loan_total_principal($loan->id),2)}}
                                                </td>
                                                <td style="text-align:right;">
                                                    {{number_format(\App\Helpers\GeneralHelper::loan_total_interest($loan->id)-\App\Helpers\GeneralHelper::loan_total_interest_waived($loan->id),2)}}
                                                </td>
                                                <td style="text-align:right;">
                                                    {{number_format(\App\Helpers\GeneralHelper::loan_total_fees($loan->id)+$disbursement_charges,2)}}
                                                </td>
                                                <td style="text-align:right;">
                                                    {{number_format(\App\Helpers\GeneralHelper::loan_total_penalty($loan->id),2)}}
                                                </td>
                                                <td style="text-align:right;">
                                                    {{number_format($total_due+$disbursement_charges,2)}}
                                                </td>
                                                <td style="text-align:right;">
                                                    {{number_format($total_paid+$disbursement_charges,2)}}
                                                </td>
                                                <td style="text-align:right;">
                                                    {{number_format(\App\Helpers\GeneralHelper::loan_total_balance($loan->id),2)}}
                                                </td>
                                                <td></td>
                                            </tr>
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="pending_dues">
                                <div class="tab_content">
                                    <?php
                                    $loan_due_items = \App\Helpers\GeneralHelper::loan_due_items($loan->id,
                                        $loan->release_date, date("Y-m-d"));
                                    $loan_paid_items = \App\Helpers\GeneralHelper::loan_paid_items($loan->id,
                                        $loan->release_date, date("Y-m-d"));

                                    ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6"><h6>
                                                        <b>{{trans_choice('general.timely',1)}} {{trans_choice('general.repayment',2)}}
                                                            :</b></h6></div>
                                                <div class="col-md-6">
                                                    <?php
                                                    $count = \App\Models\LoanSchedule::where('due_date', '<=',
                                                        date("Y-m-d"))->where('loan_id', $loan->id)->count();
                                                    ?>
                                                    @if($count>0)
                                                        <h6><b>{{round(($count-$total_overdue)/$count)}}%</b></h6>
                                                    @else
                                                        <h6><b>0 %</b></h6>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6"><h6>
                                                        <b>{{trans_choice('general.amount',1)}} {{trans_choice('general.in',1)}} {{trans_choice('general.arrears',2)}}
                                                            :</b></h6></div>
                                                <div class="col-md-6">
                                                    @if(($loan_due_items["principal"]+$loan_due_items["interest"]+$loan_due_items["fees"]+$loan_due_items["penalty"])>($loan_paid_items["principal"]+$loan_paid_items["interest"]+$loan_paid_items["fees"]+$loan_paid_items["penalty"]))
                                                        <h6><b>
                                                                <span class="text-danger">{{number_format(($loan_due_items["principal"]+$loan_due_items["interest"]+$loan_due_items["fees"]+$loan_due_items["penalty"])-($loan_paid_items["principal"]+$loan_paid_items["interest"]+$loan_paid_items["fees"]+$loan_paid_items["penalty"]),2)}}</span></b>
                                                        </h6>
                                                    @else
                                                        <h6><b> <span class="text-danger">0.00</span></b></h6>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6"><h6>
                                                        <b>{{trans_choice('general.day',2)}} {{trans_choice('general.in',1)}} {{trans_choice('general.arrears',2)}}
                                                            :</b></h6></div>
                                                <div class="col-md-6">
                                                    @if(!empty($overdue_date))
                                                        <?php
                                                        $date1 = new DateTime($overdue_date);
                                                        $date2 = new DateTime(date("Y-m-d"));
                                                        ?>
                                                        <h6>
                                                            <b><span class="text-danger">{{$date2->diff($date1)->format("%a")}}</span></b>
                                                        </h6>
                                                    @else
                                                        <h6><b> <span class="text-danger">0</span></b></h6>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6"><h6>
                                                        <b>{{trans_choice('general.last',1)}} {{trans_choice('general.payment',1)}}
                                                            :</b></h6></div>
                                                <div class="col-md-6">
                                                    <?php
                                                    $last_payment = \App\Models\LoanTransaction::where('loan_id',
                                                        $loan->id)->where('transaction_type',
                                                        'repayment')->where('reversed', 0)->orderBy('date',
                                                        'desc')->first();
                                                    ?>
                                                    @if(!empty($last_payment))
                                                        <h6><b>{{number_format($last_payment->credit)}}
                                                                on {{$last_payment->date}}</b></h6>
                                                    @else
                                                        ----
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6"><h6>
                                                        <b>{{trans_choice('general.next',1)}} {{trans_choice('general.payment',1)}}
                                                            :</b></h6></div>
                                                <div class="col-md-6">
                                                    <?php
                                                    $count = \App\Models\LoanSchedule::where('due_date', '<=',
                                                        date("Y-m-d"))->where('loan_id', $loan->id)->count();
                                                    ?>
                                                    @foreach($next_payment as $key=>$value)
                                                        <?php
                                                        if ($key > date("Y-m-d")) {
                                                            echo ' <h6><b>' . number_format($value) . ' on ' . $key . '</b></h6>';
                                                            break;
                                                        }
                                                        ?>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6"><h6>
                                                        <b>{{trans_choice('general.last',1)}} {{trans_choice('general.payment',1)}} {{trans_choice('general.expected',2)}}
                                                            :</b></h6></div>
                                                <div class="col-md-6">
                                                    <h6>
                                                        <b>{{\App\Models\LoanSchedule::where('loan_id', $loan->id)->orderBy('due_date','asc')->get()->last()->due_date}}</b>
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <table class="table table-bordered table-condensed">
                                        <tbody>
                                        <tr class="bg-success">
                                            <th width="200">
                                                <b>{{trans_choice('general.item',1)}}
                                                    :</b>
                                            </th>
                                            <th style="text-align:right;">
                                                <b>{{trans_choice('general.principal',1)}}</b>
                                            </th>
                                            <th style="text-align:right;">
                                                <b>{{trans_choice('general.interest',1)}}</b>
                                            </th>
                                            <th style="text-align:right;">
                                                <b>{{trans_choice('general.fee',2)}}</b>
                                            </th>
                                            <th style="text-align:right;">
                                                <b>{{trans_choice('general.penalty',1)}}</b>
                                            </th>
                                            <th style="text-align:right;">
                                                <b>{{trans_choice('general.total',1)}}</b>
                                            </th>
                                        </tr>
                                        <tr>
                                            <td class="text-bold bg-danger">
                                                {{trans_choice('general.total',1)}} {{trans_choice('general.due',1)}}
                                            </td>
                                            <td style="text-align:right">
                                                {{number_format(\App\Helpers\GeneralHelper::loan_total_principal($loan->id),2)}}
                                            </td>
                                            <td style="text-align:right">
                                                {{number_format(\App\Helpers\GeneralHelper::loan_total_interest($loan->id),2)}}
                                            </td>
                                            <td style="text-align:right">
                                                {{number_format(\App\Helpers\GeneralHelper::loan_total_fees($loan->id)+$disbursement_charges,2)}}
                                            </td>
                                            <td style="text-align:right">
                                                {{number_format(\App\Helpers\GeneralHelper::loan_total_penalty($loan->id),2)}}
                                            </td>
                                            <td style="text-align:right; font-weight:bold">
                                                {{number_format(\App\Helpers\GeneralHelper::loan_total_due_amount($loan->id)+$disbursement_charges,2)}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-bold bg-green">
                                                {{trans_choice('general.total',1)}} {{trans_choice('general.paid',1)}}
                                            </td>
                                            <td style="text-align:right">
                                                {{number_format($loan_paid_items['principal'],2)}}
                                            </td>
                                            <td style="text-align:right">
                                                {{number_format($loan_paid_items['interest'],2)}}
                                            </td>
                                            <td style="text-align:right">
                                                {{number_format($loan_paid_items['fees']+$disbursement_charges,2)}}
                                            </td>
                                            <td style="text-align:right">
                                                {{number_format($loan_paid_items['penalty'],2)}}
                                            </td>
                                            <td style="text-align:right; font-weight:bold">
                                                {{number_format(($loan_paid_items['principal']+$loan_paid_items['interest']+$loan_paid_items['fees']+$loan_paid_items['penalty'])+$disbursement_charges,2)}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-bold btn-info">
                                                {{trans_choice('general.balance',1)}}
                                            </td>
                                            <td style="text-align:right">
                                                {{number_format((\App\Helpers\GeneralHelper::loan_total_principal($loan->id)-$loan_paid_items['principal']),2)}}
                                            </td>
                                            <td style="text-align:right">
                                                {{number_format((\App\Helpers\GeneralHelper::loan_total_interest($loan->id)-$loan_paid_items['interest']),2)}}

                                            </td>
                                            <td style="text-align:right">
                                                {{number_format((\App\Helpers\GeneralHelper::loan_total_fees($loan->id)-$loan_paid_items['fees']),2)}}

                                            </td>
                                            <td style="text-align:right">
                                                {{number_format((\App\Helpers\GeneralHelper::loan_total_penalty($loan->id)-$loan_paid_items['penalty']),2)}}

                                            </td>
                                            <td style="text-align:right; font-weight:bold">
                                                {{number_format((\App\Helpers\GeneralHelper::loan_total_due_amount($loan->id)-($loan_paid_items['principal']+$loan_paid_items['interest']+$loan_paid_items['fees']+$loan_paid_items['penalty'])),2)}}

                                            </td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>


                        @endif
                        <div class="tab-pane active" id="loan_terms">
                            <div class="row">
                                <div class="col-sm-8">

                                    @if($loan->status=='declined')

                                    @endif
                                    @if($loan->status=='approved')

                                    @endif
                                    @if($loan->status=='written_off')

                                    @endif
                                    @if($loan->status=='withdrawn')
                                        <div class="col-sm-6">

                                        </div>
                                    @endif
                                    @if($loan->status=='disbursed')

                                    @endif

                                    @if($loan->status=="disbursed" || $loan->status=="closed" || $loan->status=="withdrawn" || $loan->status=="written_off" || $loan->status=="rescheduled" )
                                        <div class="col-sm-3">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn btn-info dropdown-toggle m-10"
                                                        data-toggle="dropdown"
                                                        aria-expanded="false">{{trans_choice('general.loan',1)}} {{trans_choice('general.schedule',1)}}
                                                    <span class="fa fa-caret-down"></span></button>
                                                <ul class="dropdown-menu" role="menu">

                                                    <li>
                                                        <a href="{{url('client/loan/'.$loan->id.'/loan_statement/print')}}"
                                                           target="_blank">{{trans_choice('general.print',1)}} {{trans_choice('general.statement',1)}}</a>
                                                    </li>

                                                    <li>
                                                        <a href="{{url('client/loan/'.$loan->id.'/loan_statement/pdf')}}"
                                                           target="_blank">{{trans_choice('general.download',1)}} {{trans_choice('general.in',1)}} {{trans_choice('general.pdf',1)}}</a>
                                                    </li>

                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-sm-4 pull-right">
                                    <div class="btn-group-horizontal">

                                    </div>
                                </div>
                            </div>

                            <div class="panel-body no-padding">
                                <table class="table table-condensed">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <b>{{trans_choice('general.loan',1)}} {{trans_choice('general.status',1)}}</b>
                                        </td>
                                        <td>
                                            @if($loan->maturity_date<date("Y-m-d") && \App\Helpers\GeneralHelper::loan_total_balance($loan->id)>0)
                                                <span class="label label-danger">{{trans_choice('general.past_maturity',1)}}</span>
                                            @else
                                                @if($loan->status=='pending')
                                                    <span class="label label-warning">{{trans_choice('general.pending',1)}} {{trans_choice('general.approval',1)}}</span>
                                                @endif
                                                @if($loan->status=='approved')
                                                    <span class="label label-info">{{trans_choice('general.awaiting',1)}} {{trans_choice('general.disbursement',1)}}</span>
                                                @endif
                                                @if($loan->status=='disbursed')
                                                    <span class="label label-info">{{trans_choice('general.active',1)}}</span>
                                                @endif
                                                @if($loan->status=='declined')
                                                    <span class="label label-danger">{{trans_choice('general.declined',1)}}</span>
                                                @endif
                                                @if($loan->status=='withdrawn')
                                                    <span class="label label-danger">{{trans_choice('general.withdrawn',1)}}</span>
                                                @endif
                                                @if($loan->status=='written_off')
                                                    <span class="label label-danger">{{trans_choice('general.written_off',1)}}</span>
                                                @endif
                                                @if($loan->status=='closed')
                                                    <span class="label label-success">{{trans_choice('general.closed',1)}}</span>
                                                @endif
                                                @if($loan->status=='pending_reschedule')
                                                    <span class="label label-warning">{{trans_choice('general.pending',1)}} {{trans_choice('general.reschedule',1)}}</span>
                                                @endif
                                                @if($loan->status=='rescheduled')
                                                    <span class="label label-info">{{trans_choice('general.rescheduled',1)}}</span>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>

                                        <td width="200">
                                            <b>{{trans_choice('general.loan',1)}} {{trans_choice('general.application',1)}} {{trans_choice('general.id',1)}}</b>
                                        </td>
                                        <td>{{$loan->id}}</td>

                                    </tr>
                                    <tr>
                                        <td>
                                            <b>{{trans_choice('general.loan',1)}} {{trans_choice('general.product',1)}}</b>
                                        </td>
                                        <td>
                                            @if(!empty($loan->loan_product))
                                                {{$loan->loan_product->name}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="bg-navy disabled color-palette">
                                            {{trans_choice('general.loan',1)}} {{trans_choice('general.term',2)}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>{{trans_choice('general.disbursed_by',1)}}</b></td>
                                        <td>
                                            @if(!empty($loan->loan_disbursed_by))
                                                {{$loan->loan_disbursed_by->name}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>

                                        <td>
                                            <b>{{trans_choice('general.principal',1)}} {{trans_choice('general.amount',1)}}</b>
                                        </td>
                                        <td>{{number_format($loan->principal,2)}}</td>

                                    </tr>
                                    <tr>

                                        <td>
                                            <b>{{trans_choice('general.loan',1)}} {{trans_choice('general.release',1)}} {{trans_choice('general.date',1)}}</b>
                                        </td>
                                        <td>{{$loan->release_date}}</td>

                                    </tr>
                                    <tr>
                                        <td>
                                            <b>{{trans_choice('general.loan',1)}} {{trans_choice('general.interest',1)}} {{trans_choice('general.method',1)}}</b>
                                        </td>
                                        <td>
                                            @if($loan->interest_method=='declining_balance_equal_installments')
                                                {{trans_choice('general.declining_balance_equal_installments',1)}}
                                            @endif
                                            @if($loan->interest_method=='declining_balance_equal_principal')
                                                {{trans_choice('general.declining_balance_equal_principal',1)}}
                                            @endif
                                            @if($loan->interest_method=='interest_only')
                                                {{trans_choice('general.interest_only',1)}}
                                            @endif
                                            @if($loan->interest_method=='flat_rate')
                                                {{trans_choice('general.flat_rate',1)}}
                                            @endif
                                            @if($loan->interest_method=='compound_interest')
                                                {{trans_choice('general.compound_interest',1)}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>{{trans_choice('general.loan',1)}} {{trans_choice('general.interest',1)}}</b>
                                        </td>
                                        <td>{{number_format($loan->interest_rate,2)}}%/{{$loan->interest_period}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>{{trans_choice('general.loan',1)}} {{trans_choice('general.duration',1)}}</b>
                                        </td>
                                        <td>{{$loan->loan_duration}} {{$loan->loan_duration_type}}s
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>{{trans_choice('general.repayment_cycle',1)}}</b></td>
                                        <td>
                                            @if($loan->repayment_cycle=='daily')
                                                {{trans_choice('general.daily',1)}}
                                            @endif
                                            @if($loan->repayment_cycle=='weekly')
                                                {{trans_choice('general.weekly',1)}}
                                            @endif
                                            @if($loan->repayment_cycle=='monthly')
                                                {{trans_choice('general.monthly',1)}}
                                            @endif
                                            @if($loan->repayment_cycle=='bi_monthly')
                                                {{trans_choice('general.bi_monthly',1)}}
                                            @endif
                                            @if($loan->repayment_cycle=='quarterly')
                                                {{trans_choice('general.quarterly',1)}}
                                            @endif
                                            @if($loan->repayment_cycle=='semi_annual')
                                                {{trans_choice('general.semi_annually',1)}}
                                            @endif
                                            @if($loan->repayment_cycle=='annually')
                                                {{trans_choice('general.annual',1)}}
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><b>{{trans_choice('general.number',1)}}
                                                of {{trans_choice('general.repayment',2)}}</b></td>
                                        <td>
                                            {{\App\Models\LoanSchedule::where('loan_id',$loan->id)->count()}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>{{trans_choice('general.decimal_place',1)}}</b></td>
                                        <td>
                                            @if($loan->decimal_places=='round_off_to_two_decimal')
                                                {{trans_choice('general.round_off_to_two_decimal',1)}}
                                            @endif
                                            @if($loan->decimal_places=='round_off_to_integer')
                                                {{trans_choice('general.round_off_to_integer',1)}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>{{trans_choice('general.first',1)}} {{trans_choice('general.repayment',1)}} {{trans_choice('general.date',1)}}</b>
                                        </td>
                                        <td>{{$loan->first_payment_date}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                    </tr>

                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <div class="tab-pane" id="loan_collateral">
                            <div class="btn-group-horizontal">

                            </div>
                            <div class="box box-success">
                                <div class="table-responsive">
                                    <table id="data-table" class="table table-striped table-condensed table-hover">
                                        <thead>
                                        <tr>
                                            <th>{{trans_choice('general.type',1)}}</th>
                                            <th>{{trans_choice('general.name',1)}}</th>
                                            <th>{{trans_choice('general.value',1)}}</th>
                                            <th>{{trans_choice('general.status',1)}}</th>
                                            <th>{{trans_choice('general.date',1)}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($loan->collateral as $key)
                                            <tr>
                                                <td>
                                                    @if(!empty($key->collateral_type))
                                                        {{$key->collateral_type->name}}
                                                    @endif
                                                </td>
                                                <td>{{ $key->name }}</td>
                                                <td>{{ $key->value }}</td>
                                                <td>
                                                    @if($key->status=='deposited_into_branch')
                                                        {{trans_choice('general.deposited_into_branch',1)}}
                                                    @endif
                                                    @if($key->status=='collateral_with_borrower')
                                                        {{trans_choice('general.collateral_with_borrower',1)}}
                                                    @endif
                                                    @if($key->status=='returned_to_borrower')
                                                        {{trans_choice('general.returned_to_borrower',1)}}
                                                    @endif
                                                    @if($key->status=='repossession_initiated')
                                                        {{trans_choice('general.repossession_initiated',1)}}
                                                    @endif
                                                    @if($key->status=='repossessed')
                                                        {{trans_choice('general.repossessed',1)}}
                                                    @endif
                                                    @if($key->status=='sold')
                                                        {{trans_choice('general.sold',1)}}
                                                    @endif
                                                    @if($key->status=='lost')
                                                        {{trans_choice('general.lost',1)}}
                                                    @endif
                                                </td>
                                                <td>{{ $key->date }}</td>

                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="loan_guarantors">
                            <div class="btn-group-horizontal">

                            </div>
                            <div class="box box-success">
                                <div class="table-responsive">
                                    <table id="data-table" class="table table-bordered table-condensed table-hover">
                                        <thead>
                                        <tr>
                                            <th>{{trans_choice('general.full_name',1)}}</th>
                                            <th>{{trans_choice('general.business',1)}}</th>
                                            <th>{{trans_choice('general.unique',1)}}#</th>
                                            <th>{{trans_choice('general.mobile',1)}}</th>
                                            <th>{{trans_choice('general.email',1)}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($loan->guarantors as $key)
                                            @if(!empty($key->guarantor))
                                                <tr>
                                                    <td>{{ $key->guarantor->first_name }} {{ $key->guarantor->last_name }}</td>
                                                    <td>{{ $key->guarantor->business_name }}</td>
                                                    <td>{{ $key->guarantor->unique_number }}</td>
                                                    <td>{{ $key->guarantor->mobile }}</td>
                                                    <td>{{ $key->guarantor->email }}</td>

                                                </tr>
                                            @endif
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="loan_files">
                            <ul class="" style="font-size:12px; padding-left:10px">

                                @foreach(unserialize($loan->files) as $key=>$value)
                                    <li><a href="{!!asset('uploads/'.$value)!!}"
                                           target="_blank">{!!  $value!!}</a></li>
                                @endforeach
                            </ul>
                        </div>

                    </div>
                    <!-- /.tab-content -->
                </div>
            </div>
            <!-- nav-tabs-custom -->
        </div>
    </div>
@endsection
