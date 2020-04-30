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
                {{trans_choice('general.income',1)}} {{trans_choice('general.statement',1)}}
                @if(!empty($start_date))
                    for period: {{$start_date}} to {{$end_date}}
                @endif
            </caption>
            <thead>
            <tr class="">
                <th>{{trans_choice('general.gl_code',1)}}</th>
                <th>{{trans_choice('general.account',1)}}</th>
                <th>{{trans_choice('general.balance',1)}}</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="3" style="text-align: center"><b>{{trans_choice('general.income',1)}}</b></td>
            </tr>
            <?php
            $total_income = 0;
            $total_expenses = 0;
            ?>
            @foreach(\App\Models\ChartOfAccount::where('account_type','income')->orderBy('gl_code','asc')->get() as $key)
                <?php
                $balance = 0;
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance =  $cr-$dr;
                $total_income = $total_income + $balance;
                ?>
                <tr>
                    <td>{{ $key->gl_code }}</td>
                    <td>
                        {{$key->name}}
                    </td>
                    <td>{{ number_format($balance,2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" style="text-align: right">
                    <b>{{trans_choice('general.total',1)}} {{trans_choice('general.income',1)}}</b></td>
                <td><b>{{ number_format($total_income,2) }}</b></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center"><b>{{trans_choice('general.expense',2)}}</b></td>
            </tr>
            @foreach(\App\Models\ChartOfAccount::where('account_type','expense')->orderBy('gl_code','asc')->get() as $key)
                <?php
                $balance = 0;
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance =  $dr-$cr;
                $total_expenses = $total_expenses + $balance;
                ?>
                <tr>
                    <td>{{ $key->gl_code }}</td>
                    <td>
                        {{$key->name}}
                    </td>
                    <td>{{ number_format($balance,2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" style="text-align: right">
                    <b>{{trans_choice('general.total',1)}} {{trans_choice('general.expense',2)}}</b></td>
                <td><b>{{ number_format($total_expenses,2) }}</b></td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2" style="text-align: right">
                    <b>{{trans_choice('general.net',1)}} {{trans_choice('general.income',1)}}</b></td>
                <td><b>{{ number_format($total_income-$total_expenses,2) }}</b></td>
            </tr>
            </tfoot>
        </table>
    @endif
</div>