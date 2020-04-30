@extends('layouts.master')
@section('title')
    {{trans_choice('general.balance',1)}}  {{trans_choice('general.sheet',1)}}
@endsection
@section('content')

    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">
                {{trans_choice('general.balance',1)}}  {{trans_choice('general.sheet',1)}}
                @if(!empty($start_date))
                    as at  <b>{{$start_date}}</b>
                @else
                    as at {{date("Y-m-d")}}
                @endif
            </h6>

            <div class="heading-elements">
                <button class="btn btn-sm btn-info hidden-print" onclick="window.print()">Print</button>
            </div>
        </div>
        <div class="panel-body hidden-print">
            <h4 class="">{{trans_choice('general.date',1)}} {{trans_choice('general.range',1)}}</h4>
            {!! Form::open(array('url' => Request::url(), 'method' => 'post','class'=>'form-horizontal', 'name' => 'form')) !!}
            <div class="row">
                <div class="col-xs-5">
                    {!! Form::text('start_date',null, array('class' => 'form-control date-picker', 'placeholder'=>"Date",'required'=>'required')) !!}
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-2">
                        <span class="input-group-btn">
                          <button type="submit" class="btn bg-olive btn-flat">{{trans_choice('general.search',1)}}!
                          </button>
                        </span>
                        <span class="input-group-btn">
                          <a href="{{Request::url()}}"
                             class="btn bg-purple  btn-flat pull-right">{{trans_choice('general.reset',1)}}!</a>
                        </span>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}

        </div>
        <!-- /.panel-body -->

    </div>
    <!-- /.box -->
    <script>
        function update_balance_sheet() {
            var current_assets = 0;
            var investments = 0;
            var fixed_assets = 0;
            var intangible_assets = 0;
            var other_assets = 0;

            for (var i = 1; i <= 2; i++) {
                var fieldid = 'inputCurrentAssets' + i;
                if (document.getElementById(fieldid)) {
                    if (document.getElementById(fieldid).value != "")
                        current_assets = parseFloat(current_assets) + parseFloat(document.getElementById(fieldid).value) * 100;
                }

                var fieldid = 'inputInvestments' + i;
                if (document.getElementById(fieldid)) {
                    if (document.getElementById(fieldid).value != "")
                        investments = parseFloat(investments) + parseFloat(document.getElementById(fieldid).value) * 100;
                }

                var fieldid = 'inputFixedAssets' + i;
                if (document.getElementById(fieldid)) {
                    if (document.getElementById(fieldid).value != "")
                        fixed_assets = parseFloat(fixed_assets) + parseFloat(document.getElementById(fieldid).value) * 100;
                }

                var fieldid = 'inputIntangibleAssets' + i;
                if (document.getElementById(fieldid)) {
                    if (document.getElementById(fieldid).value != "")
                        intangible_assets = parseFloat(intangible_assets) + parseFloat(document.getElementById(fieldid).value) * 100;
                }

                var fieldid = 'inputOtherAssets' + i;
                if (document.getElementById(fieldid)) {
                    if (document.getElementById(fieldid).value != "")
                        other_assets = parseFloat(other_assets) + parseFloat(document.getElementById(fieldid).value) * 100;
                }
            }

            var inputCurrentLoansOutstanding = document.getElementById("inputCurrentLoansOutstanding").value;
            var inputCurrentLoansPastDue = document.getElementById("inputCurrentLoansPastDue").value;
            var inputCurrentLoansRestructured = document.getElementById("inputCurrentLoansRestructured").value;
            var inputLoanLossReserve = document.getElementById("inputLoanLossReserve").value;

            if (inputCurrentLoansOutstanding == "")
                inputCurrentLoansOutstanding = 0;
            if (inputCurrentLoansPastDue == "")
                inputCurrentLoansPastDue = 0;
            if (inputCurrentLoansRestructured == "")
                inputCurrentLoansRestructured = 0;
            if (inputLoanLossReserve == "")
                inputLoanLossReserve = 0;

            var loans_outstanding = parseFloat(inputCurrentLoansOutstanding) * 100 + parseFloat(inputCurrentLoansPastDue) * 100 + parseFloat(inputCurrentLoansRestructured) * 100;
            var net_loans_outstanding = parseFloat(loans_outstanding) - parseFloat(inputLoanLossReserve) * 100;
            var total_current_assets = parseFloat(net_loans_outstanding) + parseFloat(current_assets);
            var total_assets = parseFloat(total_current_assets) + parseFloat(investments) + parseFloat(fixed_assets) + parseFloat(intangible_assets) + parseFloat(other_assets);

            var inputClientSavings = document.getElementById("inputClientSavings").value;
            var inputAccountsPayable = document.getElementById("inputAccountsPayable").value;
            var inputWagesPayable = document.getElementById("inputWagesPayable").value;
            var inputShortTermBorrowings = document.getElementById("inputShortTermBorrowings").value;
            var inputLongTermDebtCommercial = document.getElementById("inputLongTermDebtCommercial").value;
            var inputLongTermDebtConcessional = document.getElementById("inputLongTermDebtConcessional").value;
            var inputOtherAccruedExpensesPayable = document.getElementById("inputOtherAccruedExpensesPayable").value;
            var inputIncomeTaxesPayable = document.getElementById("inputIncomeTaxesPayable").value;
            var inputRestrictedRevenue = document.getElementById("inputRestrictedRevenue").value;

            if (inputClientSavings == "")
                inputClientSavings = 0;
            if (inputAccountsPayable == "")
                inputAccountsPayable = 0;
            if (inputWagesPayable == "")
                inputWagesPayable = 0;
            if (inputShortTermBorrowings == "")
                inputShortTermBorrowings = 0;
            if (inputLongTermDebtCommercial == "")
                inputLongTermDebtCommercial = 0;
            if (inputLongTermDebtConcessional == "")
                inputLongTermDebtConcessional = 0;
            if (inputOtherAccruedExpensesPayable == "")
                inputOtherAccruedExpensesPayable = 0;
            if (inputIncomeTaxesPayable == "")
                inputIncomeTaxesPayable = 0;
            if (inputRestrictedRevenue == "")
                inputRestrictedRevenue = 0;

            var total_liabilities = parseFloat(inputClientSavings) * 100 + parseFloat(inputAccountsPayable) * 100 + parseFloat(inputWagesPayable) * 100 + parseFloat(inputShortTermBorrowings) * 100 + parseFloat(inputLongTermDebtCommercial) * 100 + parseFloat(inputLongTermDebtConcessional) * 100 + parseFloat(inputOtherAccruedExpensesPayable) * 100 + parseFloat(inputIncomeTaxesPayable) * 100 + parseFloat(inputRestrictedRevenue) * 100;

            var inputRetainedNetSurplus = document.getElementById("inputRetainedNetSurplus").value;
            var inputNetSurplus = document.getElementById("inputNetSurplus").value;

            if (inputRetainedNetSurplus == "")
                inputRetainedNetSurplus = 0;
            if (inputNetSurplus == "")
                inputNetSurplus = 0;

            var equities = parseFloat(inputRetainedNetSurplus) * 100 + parseFloat(inputNetSurplus) * 100;

            var loan_fund = parseFloat(total_assets) - parseFloat(equities) - parseFloat(total_liabilities);
            var total_equities = parseFloat(loan_fund) + parseFloat(equities);
            var total_liabilities_equities = parseFloat(total_liabilities) + parseFloat(total_equities);

            //document.getElementById("CurrentAssets").innerHTML = numberWithCommas((current_assets / 100).toFixed(2));
            document.getElementById("NetLoansOutstanding").innerHTML = numberWithCommas((net_loans_outstanding / 100).toFixed(2));
            document.getElementById("TotalLiabilities").innerHTML = numberWithCommas((total_liabilities / 100).toFixed(2));
            document.getElementById("TotalCurrentAssets").innerHTML = numberWithCommas((total_current_assets / 100).toFixed(2));
            document.getElementById("TotalInvestments").innerHTML = numberWithCommas((investments / 100).toFixed(2));
            document.getElementById("TotalFixedAssets").innerHTML = numberWithCommas((fixed_assets / 100).toFixed(2));
            document.getElementById("TotalIntangibleAssets").innerHTML = numberWithCommas((intangible_assets / 100).toFixed(2));
            document.getElementById("TotalOtherAssets").innerHTML = numberWithCommas((other_assets / 100).toFixed(2));
            document.getElementById("TotalAssets").innerHTML = numberWithCommas((total_assets / 100).toFixed(2));
            document.getElementById("LoanFundCapital").innerHTML = numberWithCommas((loan_fund / 100).toFixed(2));
            document.getElementById("TotalEquity").innerHTML = numberWithCommas((total_equities / 100).toFixed(2));
            document.getElementById("TotalLiabilitiesEquity").innerHTML = numberWithCommas((total_liabilities_equities / 100).toFixed(2));
        }
        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
    <div class="box box-info">
        <div class="row">
            <div class="col-sm-12 text-center">
                <h3>{{trans_choice('general.balance',1)}}  {{trans_choice('general.sheet',1)}}<br>
                    @if(!empty($start_date))
                        as at  <b>{{$start_date}}</b>
                    @else
                        as at {{date("Y-m-d")}}
                    @endif</h3>
            </div>
        </div>
        <div class="panel-body">

            <form class="form-horizontal" method="post" enctype="multipart/form-data" name="form" id="form"
                  target="_blank">
                <input type="hidden" name="generate_excel" value="1">

                <input type="hidden" name="to_date_heading" value="27th July, 2017">

                <div class="row">
                    <div class="col-sm-6">
                        <table class="table table-bordered table-condensed table-hover">
                            <tbody>
                            <tr style="background-color: #F2F8FF">
                                <td colspan="3" class="text-center"><b>{{trans_choice('general.asset',2)}}</b></td>
                            </tr>
                            <tr>
                                <td class="text-bold text-blue">
                                    <b>{{trans_choice('general.current',1)}} {{trans_choice('general.asset',2)}}:</b>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><b>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{trans_choice('general.loan',2)}} {{trans_choice('general.outstanding',2)}} </b>
                                </td>
                                <td style="text-align:right"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><b>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{trans_choice('general.current',1)}}</b>
                                </td>
                                <td style="text-align:right"><input type="text" name="current_loans_outstanding"
                                                                    id="inputCurrentLoansOutstanding" placeholder=""
                                                                    value="{{round(\App\Helpers\GeneralHelper::loans_total_due($start_date,$end_date),2)}}"
                                                                    class="balance_sheet_input decimal-2-places"
                                                                    onkeyup="update_balance_sheet()"></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td><b>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{trans_choice('general.past_due',1)}}</b>
                                </td>
                                <td style="text-align:right"><input type="text" name="current_loans_past_due"
                                                                    id="inputCurrentLoansPastDue" placeholder=""
                                                                    value="0"
                                                                    class="balance_sheet_input decimal-2-places"
                                                                    onkeyup="update_balance_sheet()"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><b>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{trans_choice('general.restructured',1)}}</b>
                                </td>
                                <td style="text-align:right; border-bottom: 1px solid #000"><input type="text"
                                                                                                   name="current_loans_restructured"
                                                                                                   id="inputCurrentLoansRestructured"
                                                                                                   placeholder=""
                                                                                                   value="0"
                                                                                                   class="balance_sheet_input decimal-2-places"
                                                                                                   onkeyup="update_balance_sheet()">
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><b>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{trans_choice('general.loan',2)}} {{trans_choice('general.outstanding',1)}}
                                        ({{trans_choice('general.gross',1)}})</b></td>
                                <td style="text-align:right;"
                                    class="text-bold">{{round(\App\Helpers\GeneralHelper::loans_total_due($start_date,$end_date),2)}}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><b>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;({{trans_choice('general.loan',1)}} {{trans_choice('general.loss',1)}} {{trans_choice('general.reserve',1)}}
                                        )</b></td>
                                <td style="text-align:right;  border-bottom: 1px solid #000"><input type="text"
                                                                                                    name="current_loan_loss_reserve"
                                                                                                    id="inputLoanLossReserve"
                                                                                                    placeholder=""
                                                                                                    value=""
                                                                                                    class="balance_sheet_input decimal-2-places"
                                                                                                    onkeyup="update_balance_sheet()">
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><b>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{trans_choice('general.net',1)}} {{trans_choice('general.loan',2)}}
                                        {{trans_choice('general.outstanding',1)}}</b></td>
                                <td></td>
                                <td style="text-align:right" class="text-bold">
                                    <div id="NetLoansOutstanding">{{round(\App\Helpers\GeneralHelper::loans_total_due($start_date,$end_date),2)}}</div>
                                </td>
                            </tr>
                            <tr class="active">
                                <td style="">
                                    <b>{{trans_choice('general.total',1)}} {{trans_choice('general.current',1)}} {{trans_choice('general.asset',2)}}</b>
                                </td>
                                <td style=""></td>
                                <td style="text-align:right;" class="text-bold">
                                    <div id="TotalCurrentAssets">{{round(\App\Helpers\GeneralHelper::loans_total_due($start_date,$end_date),2)}}</div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-blue"><b>{{trans_choice('general.investment',2)}}:</b></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?php $investments = 0 ?>
                            @foreach(\App\Models\AssetType::where('type','investment')->get() as $key)
                                <tr>
                                    <td class="text-bold"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$key->name}}</b></td>
                                    <td style="text-align:right"><input type="text"
                                                                        name="investment_assets[{{$key->name}}]"
                                                                        id="inputFixedAssets1" placeholder=""
                                                                        value="{{\App\Helpers\GeneralHelper::asset_type_valuation($key->id,$start_date)}}"
                                                                        class="balance_sheet_input decimal-2-places"
                                                                        onkeyup="update_balance_sheet()"></td>
                                    <td></td>
                                </tr>
                                <?php $investments = $investments + \App\Helpers\GeneralHelper::asset_type_valuation($key->id,
                                                $start_date) ?>
                            @endforeach
                            <tr class="active">
                                <td style=""><b>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{trans_choice('general.total',1)}} {{trans_choice('general.investment',2)}}</b>
                                </td>
                                <td style=""></td>
                                <td style="text-align:right;" class="text-bold">
                                    <div id="TotalInvestments">{{$investments}}</div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-blue">
                                    <b>{{trans_choice('general.fixed',2)}} {{trans_choice('general.asset',2)}}:</b></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?php $fixed = 0 ?>
                            @foreach(\App\Models\AssetType::where('type','fixed')->get() as $key)
                                <tr>
                                    <td class="text-bold"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$key->name}}</b></td>
                                    <td style="text-align:right"><input type="text" name="fixed_assets[{{$key->name}}]"
                                                                        id="inputFixedAssets1" placeholder=""
                                                                        value="{{\App\Helpers\GeneralHelper::asset_type_valuation($key->id,$start_date)}}"
                                                                        class="balance_sheet_input decimal-2-places"
                                                                        onkeyup="update_balance_sheet()"></td>
                                    <td></td>
                                </tr>
                                <?php $fixed = $fixed + \App\Helpers\GeneralHelper::asset_type_valuation($key->id,
                                                $start_date) ?>
                            @endforeach
                            <tr class="active">
                                <td style=""><b>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{trans_choice('general.total',2)}} {{trans_choice('general.fixed',2)}} {{trans_choice('general.asset',2)}}</b>
                                </td>
                                <td style=""></td>
                                <td style="text-align:right;" class="text-bold">
                                    <div id="TotalFixedAssets">{{$fixed}}</div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-blue">
                                    <b>{{trans_choice('general.intangible',2)}} {{trans_choice('general.asset',2)}}:</b>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?php $intangible = 0; ?>
                            @foreach(\App\Models\AssetType::where('type','intangible')->get() as $key)
                                <tr>
                                    <td class="text-bold"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$key->name}}</b></td>
                                    <td style="text-align:right"><input type="text"
                                                                        name="intangible_assets[{{$key->name}}]"
                                                                        id="inputFixedAssets1" placeholder=""
                                                                        value="{{\App\Helpers\GeneralHelper::asset_type_valuation($key->id,$start_date)}}"
                                                                        class="balance_sheet_input decimal-2-places"
                                                                        onkeyup="update_balance_sheet()"></td>
                                    <td></td>
                                </tr>
                                <?php $intangible = $intangible + \App\Helpers\GeneralHelper::asset_type_valuation($key->id,
                                                $start_date) ?>
                            @endforeach
                            <tr class="active">
                                <td style=""><b>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{trans_choice('general.total',2)}} {{trans_choice('general.intangible',2)}} {{trans_choice('general.asset',2)}}</b>
                                </td>
                                <td style=""></td>
                                <td style="text-align:right;" class="text-bold">
                                    <div id="TotalIntangibleAssets">{{$intangible}}</div>
                                </td>
                            </tr>

                            <tr>
                                <td class="text-blue">
                                    <b>{{trans_choice('general.other',2)}} {{trans_choice('general.asset',2)}}:</b></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?php $other = 0; ?>
                            @foreach(\App\Models\AssetType::where('type','other')->get() as $key)
                                <tr>
                                    <td class="text-bold"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$key->name}}</b></td>
                                    <td style="text-align:right"><input type="text" name="other_assets[{{$key->name}}]"
                                                                        id="inputFixedAssets1" placeholder=""
                                                                        value="{{\App\Helpers\GeneralHelper::asset_type_valuation($key->id,$start_date)}}"
                                                                        class="balance_sheet_input decimal-2-places"
                                                                        onkeyup="update_balance_sheet()"></td>
                                    <td></td>
                                </tr>
                                <?php $other = $other + \App\Helpers\GeneralHelper::asset_type_valuation($key->id,
                                                $start_date) ?>
                            @endforeach
                            <tr class="active">
                                <td style=""><b>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{trans_choice('general.total',2)}} {{trans_choice('general.other',2)}} {{trans_choice('general.asset',2)}}</b>
                                </td>
                                <td style=""></td>
                                <td style="text-align:right;" class="text-bold">
                                    <div id="TotalOtherAssets">{{$other}}</div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-sm-6">
                        <table class="table table-bordered table-condensed table-hover">
                            <tbody>
                            <tr style="background-color: #F2F8FF">
                                <td colspan="3" class="text-center"><b>{{trans_choice('general.liability',1)}} {{trans_choice('general.and',2)}} {{trans_choice('general.equity',1)}}</b></td>
                            </tr>

                            <tr>
                                <td class="text-blue" colspan="2"><b>{{trans_choice('general.liability',2)}} </b></td>
                            </tr>
                            <?php
                            $savings=\App\Helpers\GeneralHelper::total_savings_deposits();
                            ?>
                            <tr>
                                <td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{trans_choice('general.client',1)}}  {{trans_choice('general.saving',2)}} </b></td>
                                <td style="text-align:right"><input type="text" name="client_savings"
                                                                    id="inputClientSavings" placeholder="" value="{{$savings}}"
                                                                    class="balance_sheet_input decimal-2-places"
                                                                    onkeyup="update_balance_sheet()"></td>
                            </tr>
                            <tr>
                                <td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{trans_choice('general.account',2)}}  {{trans_choice('general.payable',1)}} </b></td>
                                <td style="text-align:right"><input type="text" name="accounts_payable"
                                                                    id="inputAccountsPayable" placeholder="" value=""
                                                                    class="balance_sheet_input decimal-2-places"
                                                                    onkeyup="update_balance_sheet()"></td>
                            </tr>
                            <tr>
                                <td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{trans_choice('general.wage',2)}}  {{trans_choice('general.payable',1)}}</b></td>
                                <td style="text-align:right"><input type="text" name="wages_payable"
                                                                    id="inputWagesPayable" placeholder="" value=""
                                                                    class="balance_sheet_input decimal-2-places"
                                                                    onkeyup="update_balance_sheet()"></td>
                            </tr>
                            <tr>
                                <td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{trans_choice('general.short_term',1)}}  {{trans_choice('general.borrowing',2)}}</b></td>
                                <td style="text-align:right"><input type="text" name="short_term_borrowings"
                                                                    id="inputShortTermBorrowings" placeholder=""
                                                                    value=""
                                                                    class="balance_sheet_input decimal-2-places"
                                                                    onkeyup="update_balance_sheet()"></td>
                            </tr>
                            <tr>
                                <td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{trans_choice('general.long_term',1)}}  {{trans_choice('general.borrowing',2)}} ({{trans_choice('general.commercial_rate',2)}})</b></td>
                                <td style="text-align:right"><input type="text" name="long_term_debt_commercial"
                                                                    id="inputLongTermDebtCommercial" placeholder=""
                                                                    value=""
                                                                    class="balance_sheet_input decimal-2-places"
                                                                    onkeyup="update_balance_sheet()"></td>
                            </tr>
                            <tr>
                                <td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Long-term Debt (concessional rate)</b></td>
                                <td style="text-align:right"><input type="text" name="long_term_debt_concessional"
                                                                    id="inputLongTermDebtConcessional" placeholder=""
                                                                    value=""
                                                                    class="balance_sheet_input decimal-2-places"
                                                                    onkeyup="update_balance_sheet()"></td>
                            </tr>

                            <tr>
                                <td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other Accrued Expenses Payable</b></td>
                                <td style="text-align:right"><input type="text" name="other_accrued_expenses_payable"
                                                                    id="inputOtherAccruedExpensesPayable" placeholder=""
                                                                    value=""
                                                                    class="balance_sheet_input decimal-2-places"
                                                                    onkeyup="update_balance_sheet()"></td>
                            </tr>
                            <tr>
                                <td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Income Taxes Payable</b></td>
                                <td style="text-align:right"><input type="text" name="income_taxes_payable"
                                                                    id="inputIncomeTaxesPayable" placeholder="" value=""
                                                                    class="balance_sheet_input decimal-2-places"
                                                                    onkeyup="update_balance_sheet()"></td>
                            </tr>
                            <tr>
                                <td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Restricted Revenue</b></td>
                                <td style="text-align:right"><input type="text" name="restricted_revenue"
                                                                    id="inputRestrictedRevenue" placeholder="" value=""
                                                                    class="balance_sheet_input decimal-2-places"
                                                                    onkeyup="update_balance_sheet()"></td>
                            </tr>
                            <tr class="info">
                                <td><b>TOTAL LIABILITIES</b></td>
                                <td style="text-align:right;" class="text-bold">
                                    <div id="TotalLiabilities">{{$savings}}</div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-blue" colspan="2"><br><b>EQUITY</b></td>
                            </tr>
                            <tr>
                                <td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Loan Fund Capital</b></td>
                                <td style="text-align:right;" class="text-bold">
                                    <div id="LoanFundCapital">{{$other+$fixed+$intangible+$investments+round(\App\Helpers\GeneralHelper::loans_total_due($start_date,$end_date),2)-$savings}}</div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retained Net Surplus/(Deficit) prior
                                        years</b></td>
                                <td style="text-align:right"><input type="text" name="retained_net_surplus"
                                                                    id="inputRetainedNetSurplus" placeholder="" value=""
                                                                    class="balance_sheet_input decimal-2-places"
                                                                    onkeyup="update_balance_sheet()"></td>
                            </tr>
                            <tr>
                                <td><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Net Surplus/(Deficit) current year</b></td>
                                <td style="text-align:right"><input type="text" name="net_surplus" id="inputNetSurplus"
                                                                    placeholder="" value=""
                                                                    class="balance_sheet_input decimal-2-places"
                                                                    onkeyup="update_balance_sheet()"></td>
                            </tr>
                            <tr class="info">
                                <td><b>TOTAL EQUITY</b></td>
                                <td style="text-align:right;" class="text-bold">
                                    <div id="TotalEquity">{{$other+$fixed+$intangible+$investments+round(\App\Helpers\GeneralHelper::loans_total_due($start_date,$end_date),2)-$savings}}</div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <table class="table table-bordered table-condensed table-hover">
                            <tbody>
                            <tr class="info">
                                <td><b>TOTAL ASSETS</b></td>
                                <td style="text-align:right;" class="text-bold">
                                    <div id="TotalAssets">{{$other+$fixed+$intangible+$investments+round(\App\Helpers\GeneralHelper::loans_total_due($start_date,$end_date),2)}}</div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <table class="table table-bordered table-condensed table-hover">
                            <tbody>
                            <tr class="info">
                                <td><b>TOTAL LIABILITIES AND EQUITY</b></td>
                                <td style="text-align:right;" class="text-bold">
                                    <div id="TotalLiabilitiesEquity">{{$other+$fixed+$intangible+$investments+round(\App\Helpers\GeneralHelper::loans_total_due($start_date,$end_date),2)}}</div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <button type="submit" class="btn btn-info pull-right"
                        data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please Wait. This can take a few minutes.">
                    Export to Excel
                </button>
            </form>
        </div>
    </div>
@endsection
@section('footer-scripts')


@endsection
