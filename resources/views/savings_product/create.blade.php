@extends('layouts.master')
@section('title')
    {{trans_choice('general.add',1)}} {{trans_choice('general.saving',2)}} {{trans_choice('general.product',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.add',1)}} {{trans_choice('general.saving',2)}} {{trans_choice('general.product',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('saving/savings_product/store'), 'method' => 'post', 'name' => 'form', 'class' => 'form-horizontal')) !!}
        <div class="panel-body">
            <div class="form-group">
                {!! Form::label('name',trans_choice('general.name',1)." *",array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::text('name',null, array('class' => 'form-control', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip" title="The name of your savings product"></i>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('notes',trans_choice('general.description',1)." *",array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::text('notes',null, array('class' => 'form-control', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="Enter a description of the product to make it easier to identify in the future"></i>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('interest_rate',trans_choice('general.interest_rate_per_annum',1)." *",array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::text('interest_rate',null, array('class' => 'form-control touchspin', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="The amount of interest the client earns on their savings each year"></i>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('allow_overdraw',trans_choice('general.allow_saving_overdraw',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('allow_overdraw',array('0'=>trans_choice('general.no',1),'1'=>trans_choice('general.yes',1)),'0', array('class' => 'form-control','required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="{{trans_choice('general.allow_saving_overdraw_yes',1)}}"></i>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('interest_posting',trans_choice('general.interest_posting_frequency_on_savings_accounts',1)." *",array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('interest_posting',$interest_posting,null, array('class' => 'form-control','required'=>'')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="This determines how frequently interest earned by the client is posted to the client’s account."></i>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('interest_adding',trans_choice('general.interest_adding',1)." *",array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('interest_adding',$interest_adding,null, array('class' => 'form-control','required'=>'','placeholder'=>'')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip" title=""></i>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('minimum_balance',trans_choice('general.minimum',1).' '.trans_choice('general.balance',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::number('minimum_balance',0, array('class' => 'form-control', 'placeholder'=>trans_choice('general.numbers_only',1),'required'=>'')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="{{trans_choice('general.minimum_balance_msg',1)}}"></i>
                </div>
            </div>
            <hr>
            <p class="bg-navy color-palette">{{trans_choice('general.accounting',1)}}</p>
            <div class="form-group">
                {!! Form::label('accounting_rule',trans_choice('general.accounting',1).' '.trans_choice('general.rule',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('accounting_rule',['cash_based'=>trans_choice('general.cash_based',1)],'cash_based', array('class' => 'form-control ','required'=>'required','id'=>'accounting_rule')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="The accounting rule to be used."></i>
                </div>
            </div>
            <p class="text-red"><b>{{trans_choice('general.asset',2)}}:</b></p>
            <div class="form-group">
                {!! Form::label('chart_reference_id',trans_choice('general.saving',2).' '.trans_choice('general.reference',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('chart_reference_id',$chart_assets,null, array('class' => 'form-control select2','placeholder'=>'','required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="The Savings Reference refers to the pool where all client savings are actually stored (typically a bank account). This account is debited when a client deposits savings, and credited when a client withdraws."></i>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('chart_overdraft_portfolio_id',trans_choice('general.overdraft',1).' '.trans_choice('general.portfolio',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('chart_overdraft_portfolio_id',$chart_assets,null, array('class' => 'form-control select2','placeholder'=>'','required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="Any interest income from overdrawn savings accounts is booked into this account"></i>
                </div>
            </div>
            <p class="text-red"><b>{{trans_choice('general.liability',2)}}:</b></p>
            <div class="form-group">
                {!! Form::label('chart_savings_control_id',trans_choice('general.saving',2).' '.trans_choice('general.control',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('chart_savings_control_id',$chart_liability,null, array('class' => 'form-control select2','placeholder'=>'','required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="The account where you monitor all of the client savings. This account is credited when a client deposits savings, and debited when a client withdraws."></i>
                </div>
            </div>
            <p class="text-red"><b>{{trans_choice('general.income',1)}}:</b></p>
            <div class="form-group">
                {!! Form::label('chart_income_fee_id',trans_choice('general.income',1).' '.trans_choice('general.from',1).' '.trans_choice('general.fee',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('chart_income_fee_id',$chart_income,null, array('class' => 'form-control select2','placeholder'=>'','required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="Any income related to fees (related to a withdrawal fee for example) is booked into this account."></i>
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
                {!! Form::label('chart_income_interest_id',trans_choice('general.income',1).' '.trans_choice('general.for',1).' '.trans_choice('general.interest',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('chart_income_interest_id',$chart_income,null, array('class' => 'form-control select2','placeholder'=>'','required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="Any interest income from overdrawn savings accounts is booked into this account"></i>
                </div>
            </div>
            <p class="text-red"><b>{{trans_choice('general.expense',2)}}:</b></p>
            <div class="form-group">
                {!! Form::label('chart_expense_interest_id',trans_choice('general.interest',1).' '.trans_choice('general.on',1).' '.trans_choice('general.saving',2),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('chart_expense_interest_id',$chart_expenses,null, array('class' => 'form-control select2','placeholder'=>'','required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="This account is where any interest earned on a client’s savings account is posted."></i>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('chart_expense_written_off_id',trans_choice('general.write_off',1).' '.trans_choice('general.account',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-6">
                    {!! Form::select('chart_expense_written_off_id',$chart_expenses,null, array('class' => 'form-control select2','placeholder'=>'','required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    <i class="icon-info3" data-toggle="tooltip"
                       title="When you write off a savings account overdraft, the written off funds are booked to this account"></i>
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
