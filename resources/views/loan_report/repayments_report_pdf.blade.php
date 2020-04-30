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
            {{trans_choice('general.repayment',2)}}  {{trans_choice('general.report',1)}}
            @if(!empty($start_date))
                for period: {{$start_date}} to {{$end_date}}
            @endif
        </caption>
        <thead>
        <tr class="">
            <th>{{trans_choice('general.id',1)}}</th>
            <th>{{trans_choice('general.borrower',1)}}</th>
            <th>{{trans_choice('general.principal',1)}}</th>
            <th>{{trans_choice('general.interest',1)}}</th>
            <th>{{trans_choice('general.fee',2)}}</th>
            <th>{{trans_choice('general.penalty',2)}}</th>
            <th>{{trans_choice('general.total',1)}}</th>
            <th>{{trans_choice('general.date',1)}}</th>
            <th>{{trans_choice('general.receipt',1)}}</th>
            <th>{{trans_choice('general.payment',1)}} {{trans_choice('general.method',1)}}</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $total_principal = 0;
        $total_fees = 0;
        $total_interest = 0;
        $total_penalty = 0;
        ?>
        @foreach($data as $key)
            <?php
            $principal = \App\Models\JournalEntry::where('loan_transaction_id', $key->id)->where('reversed',
                0)->where('transaction_sub_type', 'repayment_principal')->sum('credit');
            $interest = \App\Models\JournalEntry::where('loan_transaction_id', $key->id)->where('reversed',
                0)->where('transaction_sub_type', 'repayment_interest')->sum('credit');
            $fees = \App\Models\JournalEntry::where('loan_transaction_id', $key->id)->where('reversed',
                0)->where('transaction_sub_type', 'repayment_fees')->sum('credit');
            $penalty = \App\Models\JournalEntry::where('loan_transaction_id', $key->id)->where('reversed',
                0)->where('transaction_sub_type', 'repayment_penalty')->sum('credit');
            $total_principal = $total_principal + $principal;
            $total_interest = $total_interest + $interest;
            $total_fees = $total_fees + $fees;
            $total_penalty = $total_penalty + $penalty;
            ?>
            <tr>
                <td>{{$key->id}}</td>
                <td>
                    @if(!empty($key->borrower))
                        {{$key->borrower->first_name}} {{$key->borrower->last_name}}
                    @endif
                </td>
                <td>{{number_format($principal,2)}}</td>
                <td>{{number_format($interest,2)}}</td>
                <td>{{number_format($fees,2)}}</td>
                <td>{{number_format($penalty,2)}}</td>
                <td>{{number_format($principal+$interest+$fees+$penalty,2)}}</td>
                <td>{{$key->date}}</td>
                <td>{{$key->receipt}}</td>
                <td>
                    @if(!empty($key->loan_repayment_method))
                        {{$key->loan_repayment_method->name}}
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td><b>{{number_format($total_principal,2)}}</b></td>
            <td><b>{{number_format($total_interest,2)}}</b></td>
            <td><b>{{number_format($total_fees,2)}}</b></td>
            <td><b>{{number_format($total_penalty,2)}}</b></td>
            <td><b>{{number_format($total_principal+$total_interest+$total_fees+$total_penalty,2)}}</b></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        </tfoot>
    </table>
</div>