<style>

    .table {
        width: 100%;
        max-width: 100%;
        margin-bottom: 20px;
        display: table;
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

    <h3 class="text-center"><b>{{trans_choice('general.account',1)}} {{trans_choice('general.payment',1)}} {{trans_choice('general.schedule',1)}}</b></h3>

    <div style="width: 100%;margin-left: auto;font-size:10px;margin-right: auto;border-top: solid thin #2cc3dd;border-bottom: solid thin #2cc3dd;padding-top: 40px;text-transform: capitalize">
        <table style="margin-top: 20px">
            <tr>
                <td style="width: 30%;margin-right: 20px;float: left">
                    <b>{{trans_choice('general.date',1)}}:</b>{{date("Y-m-d")}}<br><br>
                    <b>{{$account->debtor->name}}</b>
                </td>
                <td style="width: 30%;margin-right: 20px;float: left">
                    <table width="100%">
                        <tr>
                            <td><b>{{trans_choice('general.account',1)}} #</b></td>
                            <td>{{$account->id}}</td>
                        </tr>
                        <tr>
                            <td><b>Start Date</b></td>
                            <td>{{$account->start_date}}</td>
                        </tr>
                        <tr>
                            <td><b>End {{trans_choice('general.date',1)}}</b></td>
                            <td>{{$account->end_date}}</td>
                        </tr>

                        <tr>
                            <td><b>{{trans_choice('general.amount',1)}}</b></td>
                            <td>{{number_format($account->amount,2)}}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 30%;margin-right: 20px;float: left">
                    <table>
                        <tr>
                            <td><b>{{trans_choice('general.due',1)}}</b></td>
                            <td>{{number_format(\App\Helpers\GeneralHelper::account_amount_due($account->id),2)}}</td>
                        </tr>
                        <tr>
                            <td><b>{{trans_choice('general.paid',1)}}</b></td>
                            <td>{{number_format(\App\Models\AccountPayment::where('account_id',$account->id)->sum('amount'),2)}}</td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>
    </div>
    <div style="margin-top:30px;margin-left: auto;margin-right: auto;text-transform: capitalize;font-size: 8px;">
        <table border="1" class="table ">
            <thead>
            <tr>
                <th>
                    <b>#</b>
                </th>
                <th>
                    <b>{{trans_choice('general.date',1)}}</b>
                </th>
                <th>
                    <b>{{trans_choice('general.description',1)}}</b>
                </th>
                <th style="text-align:right;">
                    <b>{{trans_choice('general.amount',1)}}</b>
                </th>
                <th style="text-align:right;">
                    {{trans_choice('general.paid',1)}}
                </th>
                <th style="text-align:right;">
                    {{trans_choice('general.balance',1)}} {{trans_choice('general.owed',1)}}
                </th>
            </tr>
            </thead>
            <tbody>

            <?php
            $count = 1;
            $total_due = 0;
            $principal_balance = $account->amount;
            foreach ($account->schedules as $schedule) {
            $principal_balance = $principal_balance - $schedule->amount;
            if ($count == 1) {
                $total_due = ($schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty);

            } else {
                $total_due = $total_due + ($schedule->principal + $schedule->interest + $schedule->fees + $schedule->penalty);
            }
            ?>
            <tr class="@if((($schedule->amount)-\App\Models\AccountPayment::where('account_id',$account->id)->where('due_date',$schedule->due_date)->sum('amount'))<=0) success @endif">
                <td>
                    {{$count}}
                </td>
                <td>
                    {{$schedule->due_date}}
                </td>
                <td>
                    {{$schedule->description}}
                </td>
                <td style="text-align:right">
                    {{number_format($schedule->amount,2)}}
                </td>
                <td style="text-align:right;">
                    {{number_format(\App\Models\AccountPayment::where('account_id',$account->id)->where('due_date',$schedule->due_date)->sum('amount'),2)}}
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
                <td style="font-weight:bold">{{trans_choice('general.total',1)}} </td>
                <td style="text-align:right; font-weight:bold">
                    {{number_format($account->amount,2)}}
                </td>

                <td style="text-align:right; font-weight:bold">
                    {{number_format(\App\Models\AccountPayment::where('account_id',$account->id)->sum('amount'),2)}}

                </td>
                <td></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
