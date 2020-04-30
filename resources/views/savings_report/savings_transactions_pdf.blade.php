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
            {{trans_choice('general.saving',2)}} {{trans_choice('general.transaction',2)}}
            @if(!empty($start_date))
                for period: {{$start_date}} to {{$end_date}}
            @endif
        </caption>
        <thead>
        <tr class="">
            <th>{{trans_choice('general.borrower',1)}}</th>
            <th>{{trans_choice('general.account',1)}}</th>
            <th>{{trans_choice('general.product',1)}}</th>
            <th>{{trans_choice('general.type',1)}}</th>
            <th>{{trans_choice('general.debit',1)}}</th>
            <th>{{trans_choice('general.credit',1)}}</th>
            <th>{{trans_choice('general.date',2)}}</th>
            <th>{{trans_choice('general.receipt',1)}}</th>
            <th>{{trans_choice('general.payment',1)}} {{trans_choice('general.method',1)}}</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $total_deposited = 0;
        $total_withdrawn = 0;
        $cr = 0;
        $dr = 0;
        ?>
        @foreach($data as $key)
            <?php
            $dr = $dr + $key->debit;
            $cr = $cr + $key->credit;
            ?>
            <tr>

                <td>
                    @if(!empty($key->borrower))
                        {{$key->borrower->first_name}} {{$key->borrower->last_name}}
                    @endif
                </td>
                <td>{{$key->savings_id}}</td>
                <td>
                    @if(!empty($key->savings))
                        @if(!empty($key->savings->savings_product))
                            {{$key->savings->savings_product->name}}
                        @endif
                    @endif
                </td>
                <td>
                    @if($key->type=='deposit')
                        {{trans_choice('general.deposit',1)}}
                    @endif
                    @if($key->type=='withdrawal')
                        {{trans_choice('general.withdrawal',1)}}
                    @endif
                    @if($key->type=='bank_fees')
                        {{trans_choice('general.charge',1)}}
                    @endif
                    @if($key->type=='interest')
                        {{trans_choice('general.interest',1)}}
                    @endif
                    @if($key->type=='dividend')
                        {{trans_choice('general.dividend',1)}}
                    @endif
                    @if($key->type=='transfer')
                        {{trans_choice('general.transfer',1)}}
                    @endif
                    @if($key->type=='guarantee')
                        {{trans_choice('general.on',1)}} {{trans_choice('general.hold',1)}}
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
                <td>{{$key->date}}</td>
                <td>{{$key->receipt}}</td>
                <td>
                    @if(!empty($key->payment_method))
                        {{$key->payment_method->name}}
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><b>{{number_format($dr,2)}}</b></td>
            <td><b>{{number_format($cr,2)}}</b></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        </tfoot>
    </table>
</div>