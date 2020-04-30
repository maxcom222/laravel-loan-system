<style>


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

    .row {
        clear: both;
        margin-right: -15px;
        margin-left: -15px;
    }

    .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
        position: relative;
        min-height: 1px;
        padding-right: 15px;
        padding-left: 15px;
    }

    .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12 {
        float: left;
    }

    .col-md-12 {
        width: 100%;
    }

    .col-md-11 {
        width: 91.66666667%;
    }

    .col-md-10 {
        width: 83.33333333%;
    }

    .col-md-9 {
        width: 75%;
    }

    .col-md-8 {
        width: 66.66666667%;
    }

    .col-md-7 {
        width: 58.33333333%;
    }

    .col-md-6 {
        width: 50%;
    }

    .col-md-5 {
        width: 41.66666667%;
    }

    .col-md-4 {
        width: 33.33333333%;
    }

    .col-md-3 {
        width: 25%;
    }

    .col-md-2 {
        width: 16.66666667%;
    }

    .col-md-1 {
        width: 8.33333333%;
    }

    .col-md-pull-12 {
        right: 100%;
    }

    .col-md-pull-11 {
        right: 91.66666667%;
    }

    .col-md-pull-10 {
        right: 83.33333333%;
    }

    .col-md-pull-9 {
        right: 75%;
    }

    .col-md-pull-8 {
        right: 66.66666667%;
    }

    .col-md-pull-7 {
        right: 58.33333333%;
    }

    .col-md-pull-6 {
        right: 50%;
    }

    .col-md-pull-5 {
        right: 41.66666667%;
    }

    .col-md-pull-4 {
        right: 33.33333333%;
    }

    .col-md-pull-3 {
        right: 25%;
    }

    .col-md-pull-2 {
        right: 16.66666667%;
    }

    .col-md-pull-1 {
        right: 8.33333333%;
    }

    .col-md-pull-0 {
        right: auto;
    }

    .col-md-push-12 {
        left: 100%;
    }

    .col-md-push-11 {
        left: 91.66666667%;
    }

    .col-md-push-10 {
        left: 83.33333333%;
    }

    .col-md-push-9 {
        left: 75%;
    }

    .col-md-push-8 {
        left: 66.66666667%;
    }

    .col-md-push-7 {
        left: 58.33333333%;
    }

    .col-md-push-6 {
        left: 50%;
    }

    .col-md-push-5 {
        left: 41.66666667%;
    }

    .col-md-push-4 {
        left: 33.33333333%;
    }

    .col-md-push-3 {
        left: 25%;
    }

    .col-md-push-2 {
        left: 16.66666667%;
    }

    .col-md-push-1 {
        left: 8.33333333%;
    }

    .col-md-push-0 {
        left: auto;
    }

    .col-md-offset-12 {
        margin-left: 100%;
    }

    .col-md-offset-11 {
        margin-left: 91.66666667%;
    }

    .col-md-offset-10 {
        margin-left: 83.33333333%;
    }

    .col-md-offset-9 {
        margin-left: 75%;
    }

    .col-md-offset-8 {
        margin-left: 66.66666667%;
    }

    .col-md-offset-7 {
        margin-left: 58.33333333%;
    }

    .col-md-offset-6 {
        margin-left: 50%;
    }

    .col-md-offset-5 {
        margin-left: 41.66666667%;
    }

    .col-md-offset-4 {
        margin-left: 33.33333333%;
    }

    .col-md-offset-3 {
        margin-left: 25%;
    }

    .col-md-offset-2 {
        margin-left: 16.66666667%;
    }

    .col-md-offset-1 {
        margin-left: 8.33333333%;
    }

    .col-md-offset-0 {
        margin-left: 0;
    }

    .table {
        width: 100%;
        max-width: 100%;
        margin-bottom: 20px;
    }

    .table > thead > tr > th,
    .table > tbody > tr > th,
    .table > tfoot > tr > th,
    .table > thead > tr > td,
    .table > tbody > tr > td,
    .table > tfoot > tr > td {
        padding: 8px;
        line-height: 1.42857143;
        vertical-align: top;

    }

    .table > thead > tr > th {
        vertical-align: bottom;

    }

    .lead .table > tfoot > tr > td {
        border-top: none;
    }

    .lead .table > thead > tr > th {
        border-bottom: none;
    }

    .table > caption + thead > tr:first-child > th,
    .table > colgroup + thead > tr:first-child > th,
    .table > thead:first-child > tr:first-child > th,
    .table > caption + thead > tr:first-child > td,
    .table > colgroup + thead > tr:first-child > td,
    .table > thead:first-child > tr:first-child > td {
        border-top: 0;
    }

    .table > tbody + tbody {

    }

    .table .table {
        background-color: #fff;
    }

    .table-condensed > thead > tr > th,
    .table-condensed > tbody > tr > th,
    .table-condensed > tfoot > tr > th,
    .table-condensed > thead > tr > td,
    .table-condensed > tbody > tr > td,
    .table-condensed > tfoot > tr > td {
        padding: 5px;
    }

    .table-bordered {
        border: 1px solid #ddd;
        border-collapse: collapse;
    }

    .table-bordered > thead > tr > th,
    .table-bordered > tbody > tr > th,
    .table-bordered > tfoot > tr > th,
    .table-bordered > thead > tr > td,
    .table-bordered > tbody > tr > td,
    .table-bordered > tfoot > tr > td {
        border: 1px solid #ddd;
    }

    .table-bordered > thead > tr > th,
    .table-bordered > thead > tr > td {
        border-bottom-width: 2px;
    }

    .table-striped > tbody > tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }

    .table > thead > tr > td.active,
    .table > tbody > tr > td.active,
    .table > tfoot > tr > td.active,
    .table > thead > tr > th.active,
    .table > tbody > tr > th.active,
    .table > tfoot > tr > th.active,
    .table > thead > tr.active > td,
    .table > tbody > tr.active > td,
    .table > tfoot > tr.active > td,
    .table > thead > tr.active > th,
    .table > tbody > tr.active > th,
    .table > tfoot > tr.active > th {
        background-color: #f5f5f5;
    }

    .table-hover > tbody > tr > td.active:hover,
    .table-hover > tbody > tr > th.active:hover,
    .table-hover > tbody > tr.active:hover > td,
    .table-hover > tbody > tr:hover > .active,
    .table-hover > tbody > tr.active:hover > th {
        background-color: #e8e8e8;
    }

    .table > thead > tr > td.success,
    .table > tbody > tr > td.success,
    .table > tfoot > tr > td.success,
    .table > thead > tr > th.success,
    .table > tbody > tr > th.success,
    .table > tfoot > tr > th.success,
    .table > thead > tr.success > td,
    .table > tbody > tr.success > td,
    .table > tfoot > tr.success > td,
    .table > thead > tr.success > th,
    .table > tbody > tr.success > th,
    .table > tfoot > tr.success > th {
        background-color: #dff0d8;
    }

    .table-hover > tbody > tr > td.success:hover,
    .table-hover > tbody > tr > th.success:hover,
    .table-hover > tbody > tr.success:hover > td,
    .table-hover > tbody > tr:hover > .success,
    .table-hover > tbody > tr.success:hover > th {
        background-color: #d0e9c6;
    }

    .table > thead > tr > td.info,
    .table > tbody > tr > td.info,
    .table > tfoot > tr > td.info,
    .table > thead > tr > th.info,
    .table > tbody > tr > th.info,
    .table > tfoot > tr > th.info,
    .table > thead > tr.info > td,
    .table > tbody > tr.info > td,
    .table > tfoot > tr.info > td,
    .table > thead > tr.info > th,
    .table > tbody > tr.info > th,
    .table > tfoot > tr.info > th {
        background-color: #d9edf7;
    }

    .table-hover > tbody > tr > td.info:hover,
    .table-hover > tbody > tr > th.info:hover,
    .table-hover > tbody > tr.info:hover > td,
    .table-hover > tbody > tr:hover > .info,
    .table-hover > tbody > tr.info:hover > th {
        background-color: #c4e3f3;
    }

    .table > thead > tr > td.warning,
    .table > tbody > tr > td.warning,
    .table > tfoot > tr > td.warning,
    .table > thead > tr > th.warning,
    .table > tbody > tr > th.warning,
    .table > tfoot > tr > th.warning,
    .table > thead > tr.warning > td,
    .table > tbody > tr.warning > td,
    .table > tfoot > tr.warning > td,
    .table > thead > tr.warning > th,
    .table > tbody > tr.warning > th,
    .table > tfoot > tr.warning > th {
        background-color: #fcf8e3;
    }

    .table-hover > tbody > tr > td.warning:hover,
    .table-hover > tbody > tr > th.warning:hover,
    .table-hover > tbody > tr.warning:hover > td,
    .table-hover > tbody > tr:hover > .warning,
    .table-hover > tbody > tr.warning:hover > th {
        background-color: #faf2cc;
    }

    .table > thead > tr > td.danger,
    .table > tbody > tr > td.danger,
    .table > tfoot > tr > td.danger,
    .table > thead > tr > th.danger,
    .table > tbody > tr > th.danger,
    .table > tfoot > tr > th.danger,
    .table > thead > tr.danger > td,
    .table > tbody > tr.danger > td,
    .table > tfoot > tr.danger > td,
    .table > thead > tr.danger > th,
    .table > tbody > tr.danger > th,
    .table > tfoot > tr.danger > th {
        background-color: #f2dede;
    }

    .table-hover > tbody > tr > td.danger:hover,
    .table-hover > tbody > tr > th.danger:hover,
    .table-hover > tbody > tr.danger:hover > td,
    .table-hover > tbody > tr:hover > .danger,
    .table-hover > tbody > tr.danger:hover > th {
        background-color: #ebcccc;
    }
</style>


<div>
    <h3 class="text-center">
        @if(!empty(\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value))
            <img src="{{ asset('uploads/'.\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value) }}"
                 class="img-responsive" width="150"/>

        @endif
    </h3>
    <h3 class="text-center"><b>{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</b>
    </h3>

    <h3 class="text-center"><b>{{trans_choice('general.loan',1)}} {{trans_choice('general.statement',1)}}</b></h3>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <table class="table">
                <tr>
                    <td><span>{{trans_choice('general.borrower',1)}} #</span></td>
                    <td>{{$loan->borrower->title}} {{$loan->borrower->first_name}} {{$loan->borrower->last_name}}</td>
                </tr>
                <tr>
                    <td><span>{{trans_choice('general.loan',1)}} #</span></td>
                    <td>{{$loan->id}}</td>
                </tr>
                <tr>
                    <td><span>{{trans_choice('general.released',1)}}</span></td>
                    <td>{{$loan->release_date}}</td>
                </tr>
                <tr>
                    <td><span>{{trans_choice('general.maturity_date',1)}}</span></td>
                    <td>{{$loan->maturity_date}}</td>
                </tr>
                <tr>
                    <td><span>{{trans_choice('general.repayment',1)}}</span></td>
                    <td>
                        @if($loan->repayment_cycle=='daily')
                            {{trans_choice('general.daily',1)}}
                        @endif
                        @if($loan->repayment_cycle=='weekly')
                            {{trans_choice('general.weekly',1)}}
                        @endif
                        @if($loan->repayment_cycle=='monthly')
                            {{trans_choice('general.monthly',1)}}
                        @endif
                        @if($loan->repayment_cycle=='bi_monthly')
                            {{trans_choice('general.bi_monthly',1)}}
                        @endif
                        @if($loan->repayment_cycle=='quarterly')
                            {{trans_choice('general.quarterly',1)}}
                        @endif
                        @if($loan->repayment_cycle=='semi_annual')
                            {{trans_choice('general.semi_annually',1)}}
                        @endif
                        @if($loan->repayment_cycle=='annually')
                            {{trans_choice('general.annual',1)}}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><span>{{trans_choice('general.principal',1)}}</span></td>
                    <td>{{number_format($loan->principal,2)}}</td>
                </tr>
                <tr>
                    <td><span>{{trans_choice('general.interest',1)}}%</span></td>
                    <td>{{number_format($loan->interest_rate,2)}}
                        %/{{$loan->interest_period}}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table">
                <tr>
                    <td><span>{{trans_choice('general.interest',1)}} </span></td>
                    <td>{{number_format(\App\Helpers\GeneralHelper::loan_total_interest($loan->id),2)}}</td>
                </tr>
                <tr>
                    <td><span>{{trans_choice('general.fee',2)}}</span></td>
                    <td>{{number_format(\App\Helpers\GeneralHelper::loan_total_fees($loan->id),2)}}</td>
                </tr>
                <tr>
                    <td><span>{{trans_choice('general.penalty',1)}}</span></td>
                    <td>{{number_format(\App\Helpers\GeneralHelper::loan_total_penalty($loan->id),2)}}</td>
                </tr>
                <tr>
                    <td><span>{{trans_choice('general.due',1)}}</span></td>
                    <td>{{number_format(\App\Helpers\GeneralHelper::loan_total_due_amount($loan->id),2)}}</td>
                </tr>
                <tr>
                    <td><span>{{trans_choice('general.paid',1)}}</span></td>
                    <td>{{number_format(\App\Helpers\GeneralHelper::loan_total_paid($loan->id),2)}}</td>
                </tr>
                <tr>
                    <td><span>{{trans_choice('general.balance',1)}}</span></td>
                    <td>{{number_format(\App\Helpers\GeneralHelper::loan_total_balance($loan->id),2)}}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h3 class="text-center"><b>{{trans_choice('general.repayment',2)}}</b></h3>
            <table class="table table-bordered ">
                <tbody>
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
                <tbody>
                @foreach(\App\Models\LoanTransaction::where('loan_id',$loan->id)->where('transaction_type','repayment')->where('reversed',0)->get() as $key)
                    <tr>
                        <td>{{$key->date}}</td>
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
                        <td>{{number_format($key->credit,2)}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
