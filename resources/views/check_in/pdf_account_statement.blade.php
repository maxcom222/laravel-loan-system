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

    <h3 class="text-center"><b>{{trans_choice('general.loan',1)}} {{trans_choice('general.statement',1)}}</b></h3>

    <div style="width: 100%;margin-left: auto;font-size:10px;margin-right: auto;border-top: solid thin #2cc3dd;border-bottom: solid thin #2cc3dd;padding-top: 40px;text-transform: capitalize">
        <table style="margin-top: 20px">
            <tr>
                <td style="width: 30%;margin-right: 20px;float: left">
                    <b>{{trans_choice('general.date',1)}}:</b>{{date("Y-m-d")}}<br><br>
                    <b>{{$loan->borrower->title}}. {{$loan->borrower->first_name}} {{$loan->borrower->last_name}}</b>
                </td>
                <td style="width: 30%;margin-right: 20px;float: left">
                    <table width="100%">
                        <tr>
                            <td><b>{{trans_choice('general.loan',1)}} #</b></td>
                            <td>{{$loan->id}}</td>
                        </tr>
                        <tr>
                            <td><b>{{trans_choice('general.released',1)}}</b></td>
                            <td>{{$loan->release_date}}</td>
                        </tr>
                        <tr>
                            <td><b>{{trans_choice('general.maturity_date',1)}}</b></td>
                            <td>{{$loan->maturity_date}}</td>
                        </tr>
                        <tr>
                            <td><b>{{trans_choice('general.repayment',1)}}</b></td>
                            <td>{{$loan->repayment_cycle}}</td>
                        </tr>
                        <tr>
                            <td><b>{{trans_choice('general.principal',1)}}</b></td>
                            <td>{{round($loan->principal,2)}}</td>
                        </tr>
                        <tr>
                            <td><b>{{trans_choice('general.interest',1)}}%</b></td>
                            <td>{{round($loan->interest_rate,2)}}
                                %/{{$loan->interest_period}}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 30%;margin-right: 20px;float: left">
                    <table>
                        <tr>
                            <td><b>{{trans_choice('general.interest',1)}} </b></td>
                            <td>{{round(\App\Helpers\GeneralHelper::loan_total_interest($loan->id),2)}}</td>
                        </tr>
                        <tr>
                            <td><b>{{trans_choice('general.fee',2)}}</b></td>
                            <td>{{round(\App\Helpers\GeneralHelper::loan_total_fees($loan->id),2)}}</td>
                        </tr>
                        <tr>
                            <td><b>{{trans_choice('general.penalty',1)}}</b></td>
                            <td>{{round(\App\Helpers\GeneralHelper::loan_total_penalty($loan->id),2)}}</td>
                        </tr>
                        <tr>
                            <td><b>{{trans_choice('general.due',1)}}</b></td>
                            <td>{{round(\App\Helpers\GeneralHelper::loan_total_due_amount($loan->id),2)}}</td>
                        </tr>
                        <tr>
                            <td><b>{{trans_choice('general.paid',1)}}</b></td>
                            <td>{{round(\App\Helpers\GeneralHelper::loan_total_paid($loan->id),2)}}</td>
                        </tr>
                        <tr>
                            <td><b>{{trans_choice('general.balance',1)}}</b></td>
                            <td>{{round(\App\Helpers\GeneralHelper::loan_total_balance($loan->id),2)}}</td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>
    </div>
    <div style="margin-top:30px;margin-left: auto;margin-right: auto;text-transform: capitalize;font-size: 8px;">
        <h3 class="text-center"><b>{{trans_choice('general.repayment',2)}}</b></h3>
        @if(count($payments)>0)
            <table border="1" class="table ">
                <thead>
                <tr>
                    <th>
                        {{trans_choice('general.collection',1)}} {{trans_choice('general.date',1)}}
                    </th>
                    <th>
                        {{trans_choice('general.collected_by',1)}}
                    </th>
                    <th>
                        {{trans_choice('general.method',1)}}
                    </th>
                    <th>
                        {{trans_choice('general.amount',1)}}
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($payments as $key)
                    <tr>
                        <td>{{$key->collection_date}}</td>
                        <td>
                            @if(!empty($key->user))
                                {{$key->user->first_name}} {{$key->user->last_name}}
                            @endif
                        </td>
                        <td>
                            @if(!empty($key->loan_repayment_method))
                                {{$key->loan_repayment_method->name}}
                            @endif
                        </td>
                        <td>{{round($key->amount,2)}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <h5>No {{trans_choice('general.repayment',2)}} made</h5>
        @endif
    </div>
</div>
