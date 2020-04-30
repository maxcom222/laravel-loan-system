<style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #4CAF50;
        color: white;
    }
</style>
<div>

    <table class="">
        <caption>
            {{trans_choice('general.collection',1)}} {{trans_choice('general.sheet',1)}}
            @if(!empty($start_date))
                for period: {{$start_date}} to {{$end_date}}
            @endif
        </caption>
        <thead>
        <tr class="">
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
                if ($due < 0) {
                    $actual = $expected;
                } else {
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
                            {{$key->loan_officer->first_name}} {{$key->loan_officer->last_name}}
                        @endif
                    </td>
                    <td>
                        @if(!empty($key->borrower))
                            {{$key->borrower->first_name}} {{$key->borrower->last_name}}
                        @endif
                    </td>
                    <td>
                        @if(!empty($key->borrower))
                            {{$key->borrower->mobile}}
                        @endif
                    </td>
                    <td>{{$key->id}}</td>
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