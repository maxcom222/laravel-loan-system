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

    <h3 class="text-center"><b>{{trans_choice('general.loan',1)}} {{trans_choice('general.calculator',1)}}</b></h3>

    <div style="margin-top:30px;margin-left: auto;margin-right: auto;text-transform: capitalize;font-size: 8px;">
        <table>
            <thead>
            <tr style="background-color: #D1F9FF">
                <th>{{trans_choice('general.released',1)}}</th>
                <th>{{trans_choice('general.maturity',1)}}</th>
                <th>{{trans_choice('general.repayment',1)}}</th>
                <th>{{trans_choice('general.principal',1)}}</th>
                <th>{{trans_choice('general.interest',1)}}%</th>
                <th>{{trans_choice('general.interest',1)}}</th>
                <th>{{trans_choice('general.fee',2)}}</th>
                <th>{{trans_choice('general.due',1)}}</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{$request->release_date}}</td>
                <td>{{$request->maturity_date}}</td>
                <td>{{trim($request->repayment_cycle)}}</td>
                <td>{{$request->principal_amount}}</td>
                <td>{{$request->interest_rate}}%/{{$request->interest_period}}</td>
                <td>{{$request->total_interest}}</td>
                <td id="fees">0</td>
                <td>{{$request->total_due}}</td>
            </tr>
            </tbody>
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
                    <b>{{trans_choice('general.due',1)}}</b>
                </th>
                <th style="text-align:right;">
                    <b>{{trans_choice('general.principal',1)}} {{trans_choice('general.balance',1)}} {{trans_choice('general.owed',1)}}</b>
                </th>
            </tr>
            </thead>
            <tbody>

            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align:right;">
                    {{$request->principal_amount}}
                </td>
            </tr>
            <?php
            for ($i = 0;$i < $request->count;$i++) {
            ?>
            <tr>
                <td>
                    {{$i+1}}
                </td>
                <td>
                    {{$request->due_date[$i]}}
                </td>
                <td>
                    {{$request->description[$i]}}
                </td>
                <td style="text-align:right">
                    {{$request->principal[$i]}}
                </td>
                <td style="text-align:right">
                    {{$request->interest[$i]}}
                </td>
                <td style="text-align:right">
                    {{$request->fees[$i]}}
                </td>
                <td style="text-align:right">
                    {{$request->penalty[$i]}}
                </td>
                <td style="text-align:right; font-weight:bold">
                    {{$request->due[$i]}}
                </td>
                <td style="text-align:right;">
                    {{$request->principal_balance[$i]}}
                </td>
            </tr>
            <?php
            }
            ?>
            <tr>
                <td></td>
                <td></td>
                <td style="font-weight:bold">{{trans_choice('general.total',1)}} {{trans_choice('general.due',1)}}</td>
                <td style="text-align:right;">
                    {{$request->principalTotal}}
                </td>
                <td style="text-align:right;">
                    {{$request->interestTotal}}
                </td>
                <td style="text-align:right;">
                    {{$request->feesTotal}}
                </td>
                <td style="text-align:right;">
                    {{$request->penaltyTotal}}
                </td>
                <td style="text-align:right; font-weight:bold">
                    {{$request->inputTotalDueAmountTotal}}
                </td>

                <td></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
