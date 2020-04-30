<style>

    table {
        width: 100%;
        border-collapse: collapse;
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        font-size: 9px;
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
            {{trans_choice('general.product',2)}} {{trans_choice('general.summary',1)}}
            @if(!empty($end_date))
                for period: {{$start_date}} to {{$end_date}}
            @endif
        </caption>
        <thead>
        <tr class="bg-green font-11">
            <th></th>
            <th colspan="5">{{trans_choice('general.total',1)}} {{trans_choice('general.disbursed',1)}}</th>
            <th colspan="5">{{trans_choice('general.outstanding',1)}}</th>
        </tr>
        <tr class="bg-green font-11">
            <th>{{trans_choice('general.name',1)}}</th>
            <th>{{trans_choice('general.loan',2)}}</th>
            <th>{{trans_choice('general.principal',1)}}</th>
            <th>{{trans_choice('general.interest',1)}}</th>
            <th>{{trans_choice('general.fee',2)}}</th>
            <th>{{trans_choice('general.total',1)}}</th>
            <th>{{trans_choice('general.principal',1)}}</th>
            <th>{{trans_choice('general.interest',1)}}</th>
            <th>{{trans_choice('general.fee',2)}}</th>
            <th>{{trans_choice('general.penalty',1)}}</th>
            <th>{{trans_choice('general.total',1)}}</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $total_disbursed = 0;
        $total_disbursed_loans = 0;
        $total_disbursed_principal = 0;
        $total_disbursed_interest = 0;
        $total_disbursed_fees = 0;
        $total_disbursed_penalty = 0;
        $total_outstanding = 0;
        $total_outstanding_principal = 0;
        $total_outstanding_interest = 0;
        $total_outstanding_fees = 0;
        $total_outstanding_penalty = 0;

        ?>
        @foreach(\App\Models\LoanProduct::get() as $key)
            <?php
            $principal_disbursed = 0;
            $interest_disbursed = 0;
            $fees_disbursed = 0;
            $penalty_disbursed = 0;
            $principal_outstanding = 0;
            $interest_outstanding = 0;
            $fees_outstanding = 0;
            $penalty_outstanding = 0;
            $disbursed_loans = 0;
            $disbursed = 0;
            $outstanding = 0;
            //loop through loans, this will need to be improved
            foreach (\App\Models\Loan::where('loan_product_id', $key->id)->where('branch_id',
                session('branch_id'))->whereIn('status',
                ['disbursed', 'closed', 'written_off'])->whereBetween('release_date',
                [$start_date, $end_date])->get() as $loan) {
                $disbursed_loans = $disbursed_loans + 1;
                $loan_due_items = \App\Helpers\GeneralHelper::loan_due_items($key->id);
                $loan_paid_items = \App\Helpers\GeneralHelper::loan_paid_items($key->id);
                $principal_disbursed =$principal_disbursed+ $loan_due_items["principal"];
                $interest_disbursed =$interest_disbursed+ $loan_due_items["interest"];
                $fees_disbursed =$fees_disbursed+ $loan_due_items["fees"];
                $penalty_disbursed =$penalty_disbursed+ $loan_due_items["penalty"];
                $principal_outstanding =$principal_outstanding+ $loan_due_items["principal"] - $loan_paid_items["principal"];
                $interest_outstanding =$interest_outstanding+ $loan_due_items["interest"] - $loan_paid_items["interest"];
                $fees_outstanding =$fees_outstanding+ $loan_due_items["fees"] - $loan_paid_items["fees"];
                $penalty_outstanding =$penalty_outstanding+ $loan_due_items["penalty"] - $loan_paid_items["penalty"];
            }
            $disbursed = $principal_disbursed + $interest_disbursed + $fees_disbursed;
            $outstanding = $principal_outstanding + $interest_outstanding + $fees_outstanding + $penalty_outstanding;
            $total_disbursed = $total_disbursed + $disbursed;
            $total_disbursed_loans = $total_disbursed_loans + $disbursed_loans;
            $total_disbursed_principal = $total_disbursed_principal + $principal_disbursed;
            $total_disbursed_interest = $total_disbursed_interest + $interest_disbursed;
            $total_disbursed_fees = $total_disbursed_fees + $fees_disbursed;
            $total_disbursed_penalty = $total_disbursed_penalty + $penalty_disbursed;
            $total_outstanding_principal = $total_outstanding_principal + $principal_outstanding;
            $total_outstanding_interest = $total_outstanding_interest + $interest_outstanding;
            $total_outstanding_fees = $total_outstanding_fees + $fees_outstanding;
            $total_outstanding_penalty = $total_outstanding_penalty + $penalty_outstanding;
            $total_outstanding = $total_outstanding + $principal_outstanding + $interest_outstanding + $fees_outstanding + $penalty_outstanding;

            ?>
            <tr>
                <td>{{$key->name}}</td>
                <td>{{$disbursed_loans}}</td>
                <td>{{number_format($principal_disbursed,2)}}</td>
                <td>{{number_format($interest_disbursed,2)}}</td>
                <td>{{number_format($fees_disbursed,2)}}</td>
                <td>{{number_format($disbursed,2)}}</td>
                <td>{{number_format($principal_outstanding,2)}}</td>
                <td>{{number_format($interest_outstanding,2)}}</td>
                <td>{{number_format($fees_outstanding,2)}}</td>
                <td>{{number_format($penalty_outstanding,2)}}</td>
                <td>{{number_format($outstanding,2)}}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <th></th>
            <th>{{$total_disbursed_loans}}</th>
            <th>{{number_format($total_disbursed_principal,2)}}</th>
            <th>{{number_format($total_disbursed_interest,2)}}</th>
            <th>{{number_format($total_disbursed_fees,2)}}</th>
            <th>{{number_format($total_disbursed,2)}}</th>
            <th>{{number_format($total_outstanding_principal,2)}}</th>
            <th>{{number_format($total_outstanding_interest,2)}}</th>
            <th>{{number_format($total_outstanding_fees,2)}}</th>
            <th>{{number_format($total_outstanding_penalty,2)}}</th>
            <th>{{number_format($total_outstanding,2)}}</th>
        </tr>
        </tfoot>
    </table>
</div>