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
        font-weight: bold;
    }
</style>


<div>
    <h3 class="text-center">
        @if(!empty(\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value))
            <img src="{{ url(asset('uploads/'.\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value)) }}"
                 class="img-responsive" width="150"/>

        @endif
    </h3>
    <h3 class="text-center">
        <span>{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</span></h3>

    <h3 class="text-center"><span> {{trans_choice('general.receipt',1)}}</span>
    </h3>
    <div style="margin-top:30px;margin-left: auto;margin-right: auto;text-transform: capitalize;font-size: 8px; clear: both; border-top:solid thin #ccc">
        <table class="table">
            <tr>
                <td><h2><span>{{trans_choice('general.borrower',1)}} {{trans_choice('general.name',1)}}</span></h2></td>
                <td class="text-right"><h2>{{$savings_transaction->borrower->title}}
                        {{$savings_transaction->borrower->first_name}} {{$savings_transaction->borrower->last_name}}</h2>
                </td>
            </tr>
            <tr>
                <td><h2><span>{{trans_choice('general.account',1)}} #</span></h2></td>
                <td class="text-right"><h2>{{$savings_transaction->savings->id}}</h2></td>
            </tr>
            <tr>
                <td><h2><span>{{trans_choice('general.transaction',1)}} {{trans_choice('general.type',1)}}</span></h2>
                </td>
                <td class="text-right">
                    <h2>
                        @if($savings_transaction->type=='deposit')
                            {{trans_choice('general.deposit',1)}}
                        @endif
                        @if($savings_transaction->type=='withdrawal')
                            {{trans_choice('general.withdrawal',1)}}
                        @endif
                        @if($savings_transaction->type=='bank_fees')
                            {{trans_choice('general.charge',1)}}
                        @endif
                        @if($savings_transaction->type=='interest')
                            {{trans_choice('general.interest',1)}}
                        @endif
                        @if($savings_transaction->type=='dividend')
                            {{trans_choice('general.dividend',1)}}
                        @endif
                        @if($savings_transaction->type=='transfer')
                            {{trans_choice('general.transfer',1)}}
                        @endif
                        @if($savings_transaction->type=='transfer_fund')
                            {{trans_choice('general.transfer',1)}}
                        @endif
                        @if($savings_transaction->type=='transfer_loan')
                            {{trans_choice('general.transfer',1)}}
                        @endif
                        @if($savings_transaction->type=='guarantee')
                            {{trans_choice('general.on',1)}} {{trans_choice('general.hold',1)}}
                        @endif
                        @if($savings_transaction->reversed==1)
                            @if($savings_transaction->reversal_type=="user")
                                <span class="text-danger"><b>({{trans_choice('general.user',1)}} {{trans_choice('general.reversed',1)}}
                                        )</b></span>
                            @endif
                            @if($savings_transaction->reversal_type=="system")
                                <span class="text-danger"><b>({{trans_choice('general.system',1)}} {{trans_choice('general.reversed',1)}}
                                        )</b></span>
                            @endif
                        @endif

                    </h2>
                </td>
            </tr>
            <tr>
                <td><h2><span> {{trans_choice('general.date',1)}}:</span></h2></td>
                <td class="text-right"><h2>{{$savings_transaction->date}} {{$savings_transaction->time}}</h2></td>
            </tr>
            <tr>
                <td><h2><span>{{trans_choice('general.amount',1)}}</span></h2></td>
                <td class="text-right">
                    <h2>
                        @if($savings_transaction->credit>$savings_transaction->debit)
                            {{number_format($savings_transaction->credit,2)}}
                        @else
                            {{number_format($savings_transaction->debit,2)}}
                        @endif
                    </h2>
                </td>
            </tr>
            <tr>
                <td><h2><span>{{trans_choice('general.receipt',1)}}</span></h2></td>
                <td class="text-right"><h2>{{$savings_transaction->receipt}}</h2></td>
            </tr>
            <tr>
                <td><h2><span>{{trans_choice('general.balance',1)}}</span></h2></td>
                <td class="text-right">
                    <h2>{{number_format(\App\Helpers\GeneralHelper::savings_account_balance($savings_transaction->savings_id),2)}}</h2>
                </td>
            </tr>

        </table>
        <p></p>
        <hr>
    </div>
</div>

