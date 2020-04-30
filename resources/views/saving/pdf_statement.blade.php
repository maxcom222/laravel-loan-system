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

    span {

    }
</style>


<div>
    <h3 class="text-center"><b>{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</b>
    </h3>

    <h3 class="text-center">
        <b>{{trans_choice('general.saving',2)}} {{trans_choice('general.account',1)}} {{trans_choice('general.statement',1)}}</b>
    </h3>

    <div style="margin-left: auto;font-size:10px;margin-right: auto;border-top: solid thin #2cc3dd;border-bottom: solid thin #2cc3dd;padding-top: 40px;text-transform: capitalize ;clear: both">
        <table class="table table-striped">
            <thead>
            <tr>
                <th><span>{{trans_choice('general.account',1)}}#</span></th>
                <th><span>{{trans_choice('general.product',1)}}</span></th>
                <th><span>{{trans_choice('general.minimum',1)}} {{trans_choice('general.balance',1)}}</span></th>
                <th><span>{{trans_choice('general.interest_rate_per_annum',1)}}</span></th>
                <th><span>{{trans_choice('general.interest_posting_frequency',1)}}</span></th>
                <th style="text-align:right">{{trans_choice('general.current',1)}} {{trans_choice('general.balance',1)}}</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{$saving->id}}</td>
                <td>
                    @if(!empty($saving->savings_product))
                        {{ $saving->savings_product->name }}
                    @endif
                </td>
                <td>{{$saving->savings_product->minimum_balance}}</td>
                <td>{{$saving->savings_product->interest_rate}}</td>
                <td>
                    @if($saving->savings_product->interest_posting==1)
                        {{trans_choice('general.every_1_month',1)}}
                    @endif
                    @if($saving->savings_product->interest_posting==2)
                        {{trans_choice('general.every_2_month',1)}}
                    @endif
                    @if($saving->savings_product->interest_posting==3)
                        {{trans_choice('general.every_3_month',1)}}
                    @endif
                    @if($saving->savings_product->interest_posting==4)
                        {{trans_choice('general.every_4_month',1)}}
                    @endif
                    @if($saving->savings_product->interest_posting==5)
                        {{trans_choice('general.every_6_month',1)}}
                    @endif
                    @if($saving->savings_product->interest_posting==6)
                        {{trans_choice('general.every_12_month',1)}}

                    @endif
                </td>
                <td style="text-align:right">
                    <span>{{round(\App\Helpers\GeneralHelper::savings_account_balance($saving->id),2)}}</span></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div style="margin-top:30px;margin-left: auto;margin-right: auto;text-transform: capitalize;font-size: 8px; clear: both">
        <table class="table ">
            <thead>
            <tr>
                <th>
                    {{trans_choice('general.id',1)}}
                </th>
                <th>
                    {{trans_choice('general.date',1)}}
                </th>
                <th>
                    {{trans_choice('general.submitted',1)}} {{trans_choice('general.on',1)}}
                </th>
                <th>
                    {{trans_choice('general.type',1)}}
                </th>

                <th>
                    {{trans_choice('general.debit',1)}}
                </th>
                <th>
                    {{trans_choice('general.credit',1)}}
                </th>
                <th>
                    {{trans_choice('general.balance',1)}}
                </th>
                <th>
                    {{trans_choice('general.detail',2)}}
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            $balance = 0;
            ?>
            @foreach(\App\Models\SavingTransaction::where('savings_id',$saving->id)->whereIn('reversal_type',['user','none'])->orderBy('date', 'asc')->orderBy('time',
'asc')->get() as $key)
                <?php
                $balance = $balance + ($key->credit - $key->debit);
                ?>
                <tr>
                    <td>{{$key->id}}</td>
                    <td>{{$key->date}} {{$key->time}}</td>
                    <td>{{$key->created_at}}</td>
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
                        @if($key->type=='transfer_fund')
                            {{trans_choice('general.transfer',1)}}
                        @endif
                        @if($key->type=='transfer_loan')
                            {{trans_choice('general.transfer',1)}}
                        @endif
                        @if($key->type=='guarantee')
                            {{trans_choice('general.on',1)}} {{trans_choice('general.hold',1)}}
                        @endif
                        @if($key->reversed==1)
                            @if($key->reversal_type=="user")
                                <span class="text-danger">({{trans_choice('general.user',1)}} {{trans_choice('general.reversed',1)}}
                                    )</span>
                            @endif
                            @if($key->reversal_type=="system")
                                <span class="text-danger">({{trans_choice('general.system',1)}} {{trans_choice('general.reversed',1)}}
                                    )</span>
                            @endif
                        @endif
                    </td>
                    <td>{{number_format($key->debit,2)}}</td>
                    <td>{{number_format($key->credit,2)}}</td>
                    <td>{{number_format($balance,2)}}</td>
                    <td>{{$key->receipt}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
