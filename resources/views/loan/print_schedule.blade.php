<style>
    thead {
        display: table-header-group;
    }

    .table {
        border-collapse: collapse !important;
    }

    .table td,
    .table th {
        background-color: #fff !important;
    }

    .table-bordered th,
    .table-bordered td {
    }

    th {
        text-align: left;
    }

    .table {
        width: 100%;
        max-width: 100%;
        margin-bottom: 20px;
    }

    .table > thead > tr > th,
    .table > tbody > tr > th,
    .table > tfoot > tr > th,
    .table > thead > tr > td,
    .table > tbody > tr > td,
    .table > tfoot > tr > td {
        padding: 8px;
        line-height: 1.42857143;
        vertical-align: top;
    }

    .table > thead > tr > th {
        vertical-align: bottom;
    }

    .table > caption + thead > tr:first-child > th,
    .table > colgroup + thead > tr:first-child > th,
    .table > thead:first-child > tr:first-child > th,
    .table > caption + thead > tr:first-child > td,
    .table > colgroup + thead > tr:first-child > td,
    .table > thead:first-child > tr:first-child > td {
        border-top: 0;
    }

    .table > tbody + tbody {
    }

    .table .table {
        background-color: #fff;
    }

    .table-condensed > thead > tr > th,
    .table-condensed > tbody > tr > th,
    .table-condensed > tfoot > tr > th,
    .table-condensed > thead > tr > td,
    .table-condensed > tbody > tr > td,
    .table-condensed > tfoot > tr > td {
        padding: 5px;
    }

    .table-bordered {
        border: 1px solid #ddd;
    }

    .table-bordered > thead > tr > th,
    .table-bordered > tbody > tr > th,
    .table-bordered > tfoot > tr > th,
    .table-bordered > thead > tr > td,
    .table-bordered > tbody > tr > td,
    .table-bordered > tfoot > tr > td {
        border: 1px solid #ddd;
    }

    .table-bordered > thead > tr > th,
    .table-bordered > thead > tr > td {
        border-bottom-width: 2px;
    }

    .table-striped > tbody > tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }

    .table-hover > tbody > tr:hover {
        background-color: #f5f5f5;
    }

    table col[class*="col-"] {
        position: static;
        display: table-column;
        float: none;
    }

    table td[class*="col-"],
    table th[class*="col-"] {
        position: static;
        display: table-cell;
        float: none;
    }

    .table > thead > tr > td.active,
    .table > tbody > tr > td.active,
    .table > tfoot > tr > td.active,
    .table > thead > tr > th.active,
    .table > tbody > tr > th.active,
    .table > tfoot > tr > th.active,
    .table > thead > tr.active > td,
    .table > tbody > tr.active > td,
    .table > tfoot > tr.active > td,
    .table > thead > tr.active > th,
    .table > tbody > tr.active > th,
    .table > tfoot > tr.active > th {
        background-color: #f5f5f5;
    }

    .table-hover > tbody > tr > td.active:hover,
    .table-hover > tbody > tr > th.active:hover,
    .table-hover > tbody > tr.active:hover > td,
    .table-hover > tbody > tr:hover > .active,
    .table-hover > tbody > tr.active:hover > th {
        background-color: #e8e8e8;
    }

    .table > thead > tr > td.success,
    .table > tbody > tr > td.success,
    .table > tfoot > tr > td.success,
    .table > thead > tr > th.success,
    .table > tbody > tr > th.success,
    .table > tfoot > tr > th.success,
    .table > thead > tr.success > td,
    .table > tbody > tr.success > td,
    .table > tfoot > tr.success > td,
    .table > thead > tr.success > th,
    .table > tbody > tr.success > th,
    .table > tfoot > tr.success > th {
        background-color: #dff0d8;
    }

    .table-hover > tbody > tr > td.success:hover,
    .table-hover > tbody > tr > th.success:hover,
    .table-hover > tbody > tr.success:hover > td,
    .table-hover > tbody > tr:hover > .success,
    .table-hover > tbody > tr.success:hover > th {
        background-color: #d0e9c6;
    }

    .table > thead > tr > td.info,
    .table > tbody > tr > td.info,
    .table > tfoot > tr > td.info,
    .table > thead > tr > th.info,
    .table > tbody > tr > th.info,
    .table > tfoot > tr > th.info,
    .table > thead > tr.info > td,
    .table > tbody > tr.info > td,
    .table > tfoot > tr.info > td,
    .table > thead > tr.info > th,
    .table > tbody > tr.info > th,
    .table > tfoot > tr.info > th {
        background-color: #d9edf7;
    }

    .table-hover > tbody > tr > td.info:hover,
    .table-hover > tbody > tr > th.info:hover,
    .table-hover > tbody > tr.info:hover > td,
    .table-hover > tbody > tr:hover > .info,
    .table-hover > tbody > tr.info:hover > th {
        background-color: #c4e3f3;
    }

    .table > thead > tr > td.warning,
    .table > tbody > tr > td.warning,
    .table > tfoot > tr > td.warning,
    .table > thead > tr > th.warning,
    .table > tbody > tr > th.warning,
    .table > tfoot > tr > th.warning,
    .table > thead > tr.warning > td,
    .table > tbody > tr.warning > td,
    .table > tfoot > tr.warning > td,
    .table > thead > tr.warning > th,
    .table > tbody > tr.warning > th,
    .table > tfoot > tr.warning > th {
        background-color: #fcf8e3;
    }

    .table-hover > tbody > tr > td.warning:hover,
    .table-hover > tbody > tr > th.warning:hover,
    .table-hover > tbody > tr.warning:hover > td,
    .table-hover > tbody > tr:hover > .warning,
    .table-hover > tbody > tr.warning:hover > th {
        background-color: #faf2cc;
    }

    .table > thead > tr > td.danger,
    .table > tbody > tr > td.danger,
    .table > tfoot > tr > td.danger,
    .table > thead > tr > th.danger,
    .table > tbody > tr > th.danger,
    .table > tfoot > tr > th.danger,
    .table > thead > tr.danger > td,
    .table > tbody > tr.danger > td,
    .table > tfoot > tr.danger > td,
    .table > thead > tr.danger > th,
    .table > tbody > tr.danger > th,
    .table > tfoot > tr.danger > th {
        background-color: #f2dede;
    }

    .table-hover > tbody > tr > td.danger:hover,
    .table-hover > tbody > tr > th.danger:hover,
    .table-hover > tbody > tr.danger:hover > td,
    .table-hover > tbody > tr:hover > .danger,
    .table-hover > tbody > tr.danger:hover > th {
        background-color: #ebcccc;
    }

    .table-responsive {
        min-height: .01%;
        overflow-x: auto;
    }

    .row {
        margin-right: -15px;
        margin-left: -15px;
        clear: both;
    }

    .col-md-6 {
        width: 50%;
        position: relative;
        min-height: 1px;
        padding-right: 15px;
        padding-left: 15px;
    }

    .well {
        min-height: 20px;
        padding: 19px;
        margin-bottom: 20px;
        background-color: #f5f5f5;
        border: 1px solid #e3e3e3;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
    }

    tbody:before, tbody:after {
        display: none;
    }

    .text-left {
        text-align: left;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .text-justify {
        text-align: justify;
    }

    .pull-right {
        float: right !important;
    }
</style>


<div>
    <h3 class="text-center"><b>{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</b>
    </h3>

    <h3 class="text-center">
        <b>{{trans_choice('general.loan',1)}} {{trans_choice('general.repayment',1)}} {{trans_choice('general.schedule',1)}}</b>
    </h3>

    <div style="width: 980px;height: 224px;margin-left: auto;margin-right: auto;border-top: solid thin rgba(2, 180, 209, 0.44);border-bottom: solid thin rgba(2, 180, 209, 0.44);padding: 20px;text-transform: capitalize">
        <div style="width: 300px;margin-right: 20px;float: left">
            <b>{{trans_choice('general.date',1)}}:</b>{{date("Y-m-d")}}<br><br>
            <b>{{$loan->borrower->title}}. {{$loan->borrower->first_name}} {{$loan->borrower->last_name}}</b>
        </div>
        <div style="width: 300px;margin-right: 40px;float: left">
            <b>{{trans_choice('general.loan',1)}} #</b><span class="pull-right">{{$loan->id}}</span><br><br>
            <b>{{trans_choice('general.released',1)}}</b><span class="pull-right">{{$loan->release_date}}</span><br><br>
            <b>{{trans_choice('general.maturity_date',1)}}</b><span class="pull-right">{{$loan->maturity_date}}</span><br><br>
            <b>{{trans_choice('general.repayment',1)}}</b><span class="pull-right" style="">{{$loan->repayment_cycle}}</span><br><br>
            <b>{{trans_choice('general.principal',1)}}</b><span class="pull-right" style="">{{number_format($loan->principal,2)}}</span><br><br>
            <b>{{trans_choice('general.interest',1)}}%</b><span class="pull-right" style="">{{number_format($loan->interest_rate,2)}}
                %/{{$loan->interest_period}}</span><br><br>
        </div>
        <div style="width: 300px;float: left">
            <b>{{trans_choice('general.interest',1)}} </b><span
                    class="pull-right">{{number_format(\App\Helpers\GeneralHelper::loan_total_interest($loan->id),2)}}</span><br><br>
            <b>{{trans_choice('general.fee',2)}}</b><span
                    class="pull-right">{{number_format(\App\Helpers\GeneralHelper::loan_total_fees($loan->id),2)}}</span><br><br>
            <b>{{trans_choice('general.penalty',1)}}</b><span
                    class="pull-right">{{number_format(\App\Helpers\GeneralHelper::loan_total_penalty($loan->id),2)}}</span><br><br>
            <b>{{trans_choice('general.due',1)}}</b><span class="pull-right"
                            style="">{{number_format(\App\Helpers\GeneralHelper::loan_total_due_amount($loan->id),2)}}</span><br><br>
            <b>{{trans_choice('general.paid',1)}}</b><span class="pull-right"
                             style="">{{number_format(\App\Helpers\GeneralHelper::loan_total_paid($loan->id),2)}}</span><br><br>
            <b>{{trans_choice('general.balance',1)}}</b><span class="pull-right"
                                style="">{{number_format(\App\Helpers\GeneralHelper::loan_total_balance($loan->id),2)}}</span><br><br>
        </div>
    </div>
    <div style="width: 980px;margin-top:30px;margin-left: auto;margin-right: auto;padding: 20px;text-transform: capitalize">
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
            $total_due = $total_due + ($schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty);
            $paid = 0;
            $paid_by = '';
            $overdue = 0;
            $due = $schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty;
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
                    $next_payment[$schedule->due_date] = (($schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty) - $paid);
                }
            } else {
                if (date("Y-m-d") > $schedule->due_date) {
                    $overdue = 1;
                    $total_overdue = $total_overdue + 1;
                    $overdue_date = $schedule->due_date;
                }
                $next_payment[$schedule->due_date] = (($schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty));
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
                    {{number_format(\App\Helpers\GeneralHelper::loan_total_interest($loan->id),2)}}
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

<script>
    window.onload = function () {
        window.print();
    }
</script>