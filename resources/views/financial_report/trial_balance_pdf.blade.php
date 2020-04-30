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

    @if(!empty($start_date))
        <table class="">
            <caption>
                {{trans_choice('general.trial_balance',1)}}
                @if(!empty($start_date))
                    for period: {{$start_date}} to {{$end_date}}
                @endif
            </caption>
            <thead>
            <tr class="">
                <th>{{trans_choice('general.gl_code',1)}}</th>
                <th>{{trans_choice('general.account',1)}}</th>
                <th>{{trans_choice('general.debit',1)}}</th>
                <th>{{trans_choice('general.credit',1)}}</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $credit_total = 0;
            $debit_total = 0;
            ?>
            @foreach(\App\Models\ChartOfAccount::orderBy('gl_code','asc')->get() as $key)
                <?php
                $cr = 0;
                $dr = 0;
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('debit');
                $credit_total = $credit_total + $cr;
                $debit_total = $debit_total + $dr;
                ?>
                <tr>
                    <td>{{ $key->gl_code }}</td>
                    <td>
                        {{$key->name}}
                    </td>
                    <td>{{ number_format($dr,2) }}</td>
                    <td>{{ number_format($cr,2) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th colspan="2"><b>{{trans_choice('general.total',1)}}</b></th>
                <th>{{number_format($debit_total,2)}}</th>
                <th>{{number_format($credit_total,2)}}</th>
            </tr>
            </tfoot>
        </table>
    @endif
</div>