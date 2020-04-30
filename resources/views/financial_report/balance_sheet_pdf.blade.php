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
                {{trans_choice('general.balance',1)}} {{trans_choice('general.sheet',1)}}
                @if(!empty($start_date))
                    as at: {{$start_date}}
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
                <td colspan="3" style="text-align: left"><b>{{trans_choice('general.asset',2)}}</b></td>
            </tr>
            <?php
            $total_liabilities = 0;
            $total_assets = 0;
            $total_equity = 0;
            $retained_earnings = 0;
            ?>
            @foreach(\App\Models\ChartOfAccount::where('account_type','asset')->orderBy('gl_code','asc')->get() as $key)
                <?php
                $balance = 0;
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $dr - $cr;
                $total_assets = $total_assets + $balance;
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
                    <b>{{trans_choice('general.total',1)}} {{trans_choice('general.asset',2)}}</b></td>
                <td><b>{{ number_format($total_assets,2) }}</b></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: left"><b>{{trans_choice('general.liability',2)}}</b></td>
            </tr>
            @foreach(\App\Models\ChartOfAccount::where('account_type','liability')->orderBy('gl_code','asc')->get() as $key)
                <?php
                $balance = 0;
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $cr - $dr;
                $total_liabilities = $total_liabilities + $balance;
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
                    <b>{{trans_choice('general.total',1)}} {{trans_choice('general.liability',2)}}</b></td>
                <td><b>{{ number_format($total_liabilities,2) }}</b></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: left"><b>{{trans_choice('general.equity',2)}}</b></td>
            </tr>
            @foreach(\App\Models\ChartOfAccount::where('account_type','equity')->orderBy('gl_code','asc')->get() as $key)
                <?php
                $balance = 0;
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $cr - $dr;
                $total_equity = $total_equity + $balance;
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
                    <b>{{trans_choice('general.total',1)}} {{trans_choice('general.equity',2)}}</b></td>
                <td><b>{{ number_format($total_equity,2) }}</b></td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2" style="text-align: right">
                    <b>{{trans_choice('general.total',1)}} {{trans_choice('general.liability',2)}} {{trans_choice('general.and',1)}} {{trans_choice('general.equity',2)}}</b>
                </td>
                <td><b>{{ number_format($total_liabilities+$total_equity,2) }}</b></td>
            </tr>
            </tfoot>
        </table>
    @endif
</div>