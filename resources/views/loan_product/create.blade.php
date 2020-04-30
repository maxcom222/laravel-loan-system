@extends('layouts.master')
@section('title')
    {{trans_choice('general.add',1)}} {{trans_choice('general.loan',1)}} {{trans_choice('general.product',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.add',1)}} {{trans_choice('general.loan',1)}} {{trans_choice('general.product',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('loan/loan_product/store'), 'method' => 'post', 'class' => 'form-horizontal')) !!}
        <div class="panel-body">
            <div class="form-group">
                {!! Form::label('name',trans_choice('general.name',1)." *",array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::text('name',null, array('class' => 'form-control', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip" title="The name of your loan product"></i>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('minimum_principal',trans_choice('general.principal',1)." *",array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-9">
                    <div class="row">
                        <div class="col-md-3">
                            {!! Form::text('default_principal',null, array('class' => 'form-control touchspin', 'placeholder'=>trans_choice('general.default',1) ,'required'=>'required')) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::text('minimum_principal',null, array('class' => 'form-control touchspin', 'placeholder'=>trans_choice('general.min',1),'required'=>'required')) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::text('maximum_principal',null, array('class' => 'form-control touchspin', 'placeholder'=>trans_choice('general.max',1) ,'required'=>'required')) !!}
                        </div>
                        <div class="col-sm-3">
                            <i class="icon-info3" data-toggle="tooltip"
                               title="The default principal is the amount automatically shown to users when creating a new loan. By setting a minimum and maximum amount, you give users flexibility in determining the loan size. No user will be able to disburse loans with a principal outside of the minimum and maximum amounts."></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('default_loan_duration',trans_choice('general.loan',1).' '.trans_choice('general.term',1).' *',array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-3">
                    {!! Form::number('default_loan_duration',null, array('class' => 'form-control','required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    {!! Form::select('default_loan_duration_type',array('day'=>trans_choice('general.day',1).'(s)','week'=>trans_choice('general.week',1).'(s)','month'=>trans_choice('general.month',1).'(s)','year'=>trans_choice('general.year',1).'(s)'),null, array('class' => 'form-control','required'=>'required',"id"=>"inputMaxInterestPeriod")) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="The default loan term is the amount automatically shown to users when creating a new loan."></i>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('repayment_cycle',trans_choice('general.repayment_cycle',1).' *',array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('repayment_cycle',array('daily'=>trans_choice('general.daily',1),'weekly'=>trans_choice('general.weekly',1),'monthly'=>trans_choice('general.monthly',1),'bi_monthly'=>trans_choice('general.bi_monthly',1),'quarterly'=>trans_choice('general.quarterly',1),'semi_annual'=>trans_choice('general.semi_annually',1),'annual'=>trans_choice('general.annual',1)),null, array('class' => 'form-control','required'=>'required',"id"=>"")) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="The frequency of loan instalments due on the loan"></i>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('default_interest_rate',trans_choice('general.interest',1).' '.trans_choice('general.rate',1)." *",array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-9">
                    <div class="row">
                        <div class="col-md-3">
                            {!! Form::text('default_interest_rate',null, array('class' => 'form-control touchspin', 'placeholder'=>trans_choice('general.default',1) ,'required'=>'required')) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::text('minimum_interest_rate',null, array('class' => 'form-control touchspin', 'placeholder'=>trans_choice('general.min',1),'required'=>'required')) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::text('maximum_interest_rate',null, array('class' => 'form-control touchspin', 'placeholder'=>trans_choice('general.max',1) ,'required'=>'required')) !!}
                        </div>
                        <div class="col-sm-3">
                            <i class="icon-info3" data-toggle="tooltip"
                               title="The default interest rate is the amount automatically shown to users when creating a new loan. By setting a minimum and maximum rate, you give users flexibility in determining the interest charged. No user will be able to disburse loans with an interest outside of the minimum and maximum rates."></i>
                            %
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('default_interest_rate',trans_choice('general.interest',1).' '.trans_choice('general.rate',1).' '.trans_choice('general.period',1).' *',array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('interest_period',array('day'=>trans_choice('general.per_day',1),'week'=>trans_choice('general.per_week',1),'month'=>trans_choice('general.per_month',1),'year'=>trans_choice('general.per_year',1)),null, array('class' => 'form-control','required'=>'required',"id"=>"inputDefaultInterestPeriod")) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="Use this to specify  the interest rate set up"></i>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('interest_method',trans_choice('general.interest',1).' '.trans_choice('general.method',1).' *',array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('interest_method',array('flat_rate'=>trans_choice('general.flat_rate',1),'declining_balance_equal_installments'=>trans_choice('general.declining_balance_equal_installments',1),'declining_balance_equal_principal'=>trans_choice('general.declining_balance_equal_principal',1),'interest_only'=>trans_choice('general.interest_only',1)),null, array('class' => 'form-control','required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="Flat Interest loans charge equal interest amounts on each loan instalment, based on the original loan principal. Declining Balance Loans calculate the interest amount on each instalment based upon the outstanding balance of the loan (therefore the interest amount charged each instalment reduces as the loan is repaid)."></i>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('override_interest',trans_choice('general.override',1).' '.trans_choice('general.interest',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('override_interest',array('0'=>trans_choice('general.no',1),'1'=>trans_choice('general.yes',1)),null, array('class' => 'form-control','id'=>'override_interest')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="{{trans_choice('general.override_msg',1)}}"></i>
                </div>
            </div>

            <div class="form-group" id="overrideDiv">
                {!! Form::label('override_interest_amount',trans_choice('general.override',1).' '.trans_choice('general.interest',1).' '.trans_choice('general.amount',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::text('override_interest_amount',0, array('class' => 'form-control touchspin','id'=>'override_interest_amount')) !!}
                </div>

            </div>
            <div class="form-group">
                {!! Form::label('decimal_places',trans_choice('general.decimal_place',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('decimal_places',array('round_off_to_two_decimal'=>'round off to 2 decimal places','round_off_to_integer'=>'Round off to integer'),"round_off_to_two_decimal", array('class' => 'form-control', 'placeholder'=>"","id"=>"")) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="Enter the number of instalments you do not wish to charge or calculate any interest for"></i>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('grace_on_interest_charged',trans_choice('general.grace_on_interest',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::number('grace_on_interest_charged',0, array('class' => 'form-control')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="Number of periods before interest is charged."></i>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('late_repayment_penalty_grace_period',trans_choice('general.late_repayment_penalty_grace_period',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::number('late_repayment_penalty_grace_period',0, array('class' => 'form-control')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="Number of days before penalty is applied"></i>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('after_maturity_date_penalty_grace_period',trans_choice('general.after_maturity_date_penalty_grace_period',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::number('after_maturity_date_penalty_grace_period',0, array('class' => 'form-control')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="Number of days before penalty is applied"></i>
                </div>
            </div>
            <script>
                function up() {
                    var selectedOpts = $('#to option:selected');
                    if (selectedOpts.length == 0) {

                        alert("Select a column");

                        e.preventDefault();

                    }
                    var selected = $("#to").find(":selected");
                    var before = selected.prev();
                    if (before.length > 0)
                        selected.detach().insertBefore(before);
                }

                function down() {
                    var selectedOpts = $('#to option:selected');
                    if (selectedOpts.length == 0) {

                        alert("Select a column");

                        e.preventDefault();

                    }
                    var selected = $("#to").find(":selected");
                    var next = selected.next();
                    if (next.length > 0)
                        selected.detach().insertAfter(next);
                }
                function selectAll() {
                    var listbox = document.getElementById('to');
                    for (var count = 0; count < listbox.options.length; count++) {
                        listbox.options[count].selected = true;
                    }
                }
            </script>
            <div class="form-group">
                <label for="inputLoanRepaymentOrder"
                       class="col-md-3 control-label">{{trans_choice('general.repayment',1)}} {{trans_choice('general.order',1)}}
                    *</label>

                <div class="col-md-6">
                    {!! Form::select('repayment_order[]',array('penalty'=>trans_choice('general.penalty',1),'fees'=>trans_choice('general.fee',2),'interest'=>trans_choice('general.interest',1),'principal'=>trans_choice('general.principal',1)),['penalty','fees','interest','principal'], array('class' => 'form-control', 'required'=>"required","multiple"=>"" ,"id"=>"to" ,"size"=>"7")) !!}
                    <input type="button" value="Up" onclick="up()">
                    <input type="button" value="Down" onclick="down()">
                </div>
                <div class="col-md-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="The Transaction Processing Strategy determines the order incoming payments are allocated between Interest, Principal, Penalties and Fees. the default setting is ‘Interest, Principal, Penalties, Fees’, which ensures that clients with an outstanding Penalty are still able to repay their full loan instalment. N.B. Make sure you select all options after you have done rearranging them. You can use Ctrl+Left Click"></i>
                </div>
            </div>
            <hr>
            <p class="bg-navy color-palette">{{trans_choice('general.accounting',1)}}</p>
            <div class="form-group">
                {!! Form::label('accounting_rule',trans_choice('general.accounting',1).' '.trans_choice('general.rule',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('accounting_rule',['cash_based'=>trans_choice('general.cash_based',1),'accrual_periodic'=>trans_choice('general.accrual_periodic',1),'accrual_upfront'=>trans_choice('general.accrual_upfront',1)],'cash_based', array('class' => 'form-control select2','required'=>'required','id'=>'accounting_rule')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="The accounting rule to be used."></i>
                </div>
            </div>
            <p class="text-red"><b>{{trans_choice('general.asset',2)}}:</b></p>
            <div class="form-group">
                {!! Form::label('chart_fund_source_id',trans_choice('general.fund_source',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('chart_fund_source_id',$chart_assets,null, array('class' => 'form-control select2','placeholder'=>'','required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="The Fund Source is the pool of funds used to disburse loans from (such as your bank account). This account is credited when the loan is disbursed and debited when a repayment is made."></i>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('chart_loan_portfolio_id',trans_choice('general.loan',1).' '.trans_choice('general.portfolio',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('chart_loan_portfolio_id',$chart_assets,null, array('class' => 'form-control select2','placeholder'=>'','required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="The account where you monitor all of your outstanding loans (such as Group Loans). This account is debited when the loan is disbursed and credited when the loan is repaid."></i>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('chart_receivable_interest_id',trans_choice('general.interest',1).' '.trans_choice('general.receivable',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('chart_receivable_interest_id',$chart_assets,null, array('class' => 'form-control select2','placeholder'=>'','required'=>'required')) !!}
                </div>
                <div class="col-sm-3">

                </div>
            </div>
            <div class="form-group">
                {!! Form::label('chart_receivable_fee_id',trans_choice('general.fee',2).' '.trans_choice('general.receivable',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('chart_receivable_fee_id',$chart_assets,null, array('class' => 'form-control select2','placeholder'=>'','required'=>'required')) !!}
                </div>
                <div class="col-sm-3">

                </div>
            </div>
            <div class="form-group">
                {!! Form::label('chart_receivable_penalty_id',trans_choice('general.penalty',2).' '.trans_choice('general.receivable',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('chart_receivable_penalty_id',$chart_assets,null, array('class' => 'form-control select2','placeholder'=>'','required'=>'required')) !!}
                </div>
                <div class="col-sm-3">

                </div>
            </div>
            <p class="text-red"><b>{{trans_choice('general.liability',2)}}:</b></p>
            <div class="form-group">
                {!! Form::label('chart_loan_over_payments_id',trans_choice('general.over_payment',2),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('chart_loan_over_payments_id',$chart_liability,null, array('class' => 'form-control select2','placeholder'=>'','required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="When a loan is overpaid, the amount overpaid is a Liability and is shown on your balance sheet in this account."></i>
                </div>
            </div>
            <p class="text-red"><b>{{trans_choice('general.income',1)}}:</b></p>
            <div class="form-group">
                {!! Form::label('chart_income_interest_id',trans_choice('general.income',1).' '.trans_choice('general.for',1).' '.trans_choice('general.interest',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('chart_income_interest_id',$chart_income,null, array('class' => 'form-control select2','placeholder'=>'','required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="Any interest income (from loan repayments) is booked into this account."></i>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('chart_income_fee_id',trans_choice('general.income',1).' '.trans_choice('general.from',1).' '.trans_choice('general.fee',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('chart_income_fee_id',$chart_income,null, array('class' => 'form-control select2','placeholder'=>'','required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="Any income related to fees (related to a loan disbursement for example) is booked into this account."></i>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('chart_income_penalty_id',trans_choice('general.income',1).' '.trans_choice('general.from',1).' '.trans_choice('general.penalty',2),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('chart_income_penalty_id',$chart_income,null, array('class' => 'form-control select2','placeholder'=>'','required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="Any income from penalties is booked into this account."></i>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('chart_income_recovery_id',trans_choice('general.income',1).' '.trans_choice('general.from',1).' '.trans_choice('general.recovery',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('chart_income_recovery_id',$chart_income,null, array('class' => 'form-control select2','placeholder'=>'','required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="Any income from recovery is booked into this account."></i>
                </div>
            </div>

            <p class="text-red"><b>{{trans_choice('general.expense',2)}}:</b></p>
            <div class="form-group">
                {!! Form::label('chart_loans_written_off_id',trans_choice('general.loan',2).' '.trans_choice('general.written_off',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('chart_loans_written_off_id',$chart_expenses,null, array('class' => 'form-control select2','placeholder'=>'','required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="When you write off a loan, the outstanding balance is booked into this account."></i>
                </div>
            </div>

            <hr>
            <p class="bg-navy color-palette">{{trans_choice('general.charge',2)}}</p>
            <div class="form-group">
                {!! Form::label('cha',trans_choice('general.charge',1).' '.trans_choice('general.string',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('cha',$charges,null, array('class' => 'form-control select2','placeholder'=>trans_choice('general.select',1).' '.trans_choice('general.charge',1),'id'=>'charges_dropdown')) !!}
                </div>
                <div class="col-sm-3">
                    <button type="button" id="chargesAdd"
                            class="btn btn-primary pull-right">{{trans_choice('general.add',1)}}</button>
                </div>
            </div>
            <div class="form-group" id="chargesDiv">
                <div style="display: none;" id="saved_charges">
                </div>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>{{trans_choice('general.name',1)}}</th>
                        <th>{{trans_choice('general.amount',1)}}</th>
                        <th>{{trans_choice('general.collected',1)}} {{trans_choice('general.on',1)}}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="charges_table">

                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.panel-body -->
        <div class="panel-footer">
            <div class="heading-elements">
                <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <!-- /.box -->

@endsection
@section('footer-scripts')
    <script>
        $(document).ready(function () {
            if ($('#override_interest').val() == 0) {
                $('#overrideDiv').hide();
                $('#override_interest_amount').removeAttr('required');
            }
            if ($('#override_interest').val() == 1) {
                $('#overrideDiv').show();
                $('#override_interest_amount').attr('required', 'required');
            }
            $('#override_interest').change(function (e) {
                if ($('#override_interest').val() == 0) {
                    $('#overrideDiv').hide();
                    $('#override_interest_amount').removeAttr('required');
                }
                if ($('#override_interest').val() == 1) {
                    $('#overrideDiv').show();
                    $('#override_interest_amount').attr('required', 'required');
                }
            });
            $('#chargesAdd').click(function (e) {
                if ($('#charges_dropdown').val() == "") {
                    alert("Please select an item")
                } else {
                    //try to build table
                    var id = $('#charges_dropdown').val();
                    $.ajax({
                        type: 'GET',
                        url: "{{url('loan/loan_product/get_charge_detail')}}" + "/" + id,
                        dataType: "json",
                        success: function (data) {
                            $('#charges_table').append('<tr id="row' + id + '"><td>' + data.name + '</td><td>' + data.amount + '</td><td>' + data.collected_on + '</td><td><button type="button" class="btn btn-danger btn-xs" data-id="' + id + '" onclick="deleteCharge(this)"><i class="fa fa-trash"></i></button></td></tr>');
                            $('#saved_charges').append('<input name="charges[]" id="charge' + id + '" value="' + id + '">');

                        },
                        error: function (data) {
                            swal({
                                title: 'Error',
                                text: 'An Error occurred, please try again',
                                type: 'warning',
                                showCancelButton: false,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Ok',
                                timer: 2000
                            })
                        }
                    });
                }

            });
        });
        function deleteCharge(e) {
            swal({
                title: 'Are you sure?',
                text: '',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ok',
                cancelButtonText: 'Cancel'
            }).then(function () {
                $('#charge' + $(e).attr("data-id")).remove();
                $('#row' + $(e).attr("data-id")).remove();

            })


        }
    </script>
@endsection
