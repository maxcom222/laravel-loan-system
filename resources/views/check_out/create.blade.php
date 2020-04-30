@extends('layouts.master')
@section('title')
    {{ trans_choice('general.add',1) }} {{ trans_choice('general.check_out',1) }}
@endsection

@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{ trans_choice('general.add',1) }} {{ trans_choice('general.check_out',1) }}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('check_out/store'), 'method' => 'post', 'class' => 'form-horizontal', 'name' => 'form',"enctype"=>"multipart/form-data")) !!}
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('date',trans_choice('general.date',1),array('class'=>'')) !!}
                        {!! Form::text('date',date("Y-m-d"), array('class' => 'form-control date-picker', 'placeholder'=>"",'required'=>'required')) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('borrower_id',trans_choice('general.borrower',1),array('class'=>'')) !!}
                        {!! Form::select('borrower_id',$borrowers,null, array('class' => 'form-control select2', 'placeholder'=>"",'required'=>'required')) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('products',trans_choice('general.product',1),array('class'=>'')) !!}
                        {!! Form::select('products',$products,null, array('class' => 'form-control select2', 'placeholder'=>trans_choice('general.select',1).' '.trans_choice('general.product',1),'id'=>'products')) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 ">
                    <h4>{{trans_choice('general.order',1)}} {{trans_choice('general.item',2)}}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered " id="items_table">
                            <thead>
                            <tr>
                                <th><strong>{{trans_choice('general.item',1)}}</strong></th>
                                <th><strong>{{trans_choice('general.qty',1)}}</strong></th>
                                <th><strong>{{trans_choice('general.unit_cost',1)}}</strong></th>
                                <th><strong>{{trans_choice('general.total',1)}}</strong></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody id="items_area">

                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="3" class="text-right"><strong>{{trans_choice('general.total',1)}}</strong>
                                </td>
                                <td>
                                    <div class="total_amount"></div>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right"><strong>{{trans_choice('general.paid',1)}}</strong>
                                </td>
                                <td>
                                    <input type="text" class="form-control touchspin" value="" name="paid"
                                           id=""/>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right">
                                    <strong>{{trans_choice('general.payment',1)}} {{trans_choice('general.method',1)}}</strong>
                                </td>
                                <td>
                                    {!! Form::select('payment_method_id',$payment_methods,null, array('class' => 'form-control', 'placeholder'=>"")) !!}
                                </td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('type',trans_choice('general.type',1),array('class'=>'')) !!}

                        {!! Form::select('type',['cash'=>trans_choice('general.cash',1),'loan'=>trans_choice('general.loan',1)],'cash', array('class' => 'form-control', 'id'=>"type",'required'=>'required')) !!}

                    </div>
                    <div id="loanDetails">
                        <div class="form-group">
                            {!! Form::label('loan_officer_id',trans_choice('general.loan_officer',1)." *",array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-5">
                                {!! Form::select('loan_officer_id',$users,null, array('class' => ' select2 form-control', 'placeholder'=>"Select",'id'=>'loan_officer_id')) !!}
                            </div>
                            <div class="col-sm-4">
                                <i class="icon-info3" data-toggle="tooltip" title="Select the officer in charge of the loan"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('loan_product_id',trans_choice('general.loan',1).' '.trans_choice('general.product',1)." *",array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-5">
                                {!! Form::select('loan_product_id',$loan_products,$loan_product->id, array('class' => 'form-control select2','required'=>'required','id'=>'loanProduct')) !!}
                            </div>
                            <div class="col-sm-4">
                                <i class="icon-info3" data-toggle="tooltip" title="Select the loan product"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('principal',trans_choice('general.principal',1).' '.trans_choice('general.amount',1)." *",array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-5">
                                {!! Form::text('principal',$loan_product->default_principal, array('class' => 'form-control touchspin', 'id'=>"principal",'required'=>'required')) !!}
                            </div>
                            <div class="col-sm-4">
                                <i class="icon-info3" data-toggle="tooltip"
                                   title="Principal amount is the loan size. Its required."></i>
                            </div>
                        </div>



                        <div class="form-group">
                            {!! Form::label('loan_duration',trans_choice('general.loan',1).' '.trans_choice('general.duration',1)." *",array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-2">
                                {!! Form::number('loan_duration',$loan_product->default_loan_duration, array('class' => 'form-control', 'placeholder'=>"5",'required'=>'required','id'=>'loan_duration')) !!}
                            </div>
                            <div class="col-sm-3">
                                {!! Form::select('loan_duration_type',array('day'=>trans_choice('general.day',1).'(s)','week'=>trans_choice('general.week',1).'(s)','month'=>trans_choice('general.month',1).'(s)','year'=>trans_choice('general.year',1).'(s)'),$loan_product->default_loan_duration_type, array('class' => 'form-control',"id"=>"loan_duration_type",'required'=>'required')) !!}
                            </div>
                            <div class="col-sm-3">
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('repayment_cycle',trans_choice('general.repayment_cycle',1)." *",array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-5">
                                {!! Form::select('repayment_cycle',array('daily'=>trans_choice('general.daily',1),'weekly'=>trans_choice('general.weekly',1),'monthly'=>trans_choice('general.monthly',1),'bi_monthly'=>trans_choice('general.bi_monthly',1),'quarterly'=>trans_choice('general.quarterly',1),'semi_annual'=>trans_choice('general.semi_annually',1),'annual'=>trans_choice('general.annual',1)),$loan_product->repayment_cycle, array('class' => 'form-control',"id"=>"repayment_cycle",'required'=>'required')) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('release_date',trans_choice('general.expected',1).' '.trans_choice('general.disbursement',1).' '.trans_choice('general.date',1)." *",array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-5">
                                {!! Form::text('release_date',null, array('class' => 'form-control date-picker', 'placeholder'=>"yyyy-mm-dd",'required'=>'required','id'=>'release_date')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('first_payment_date',trans_choice('general.first',1).' '.trans_choice('general.payment',1).' '.trans_choice('general.date',1)." ",array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-5">
                                {!! Form::text('first_payment_date',null, array('class' => 'form-control date-picker', 'placeholder'=>"yyyy-mm-dd",''=>'','id'=>'first_payment_date')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('interest_method',trans_choice('general.interest',1).' '.trans_choice('general.method',1)." *",array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-5">
                                {!! Form::select('interest_method',array('flat_rate'=>trans_choice('general.flat_rate',1),'declining_balance_equal_installments'=>trans_choice('general.declining_balance_equal_installments',1),'declining_balance_equal_principal'=>trans_choice('general.declining_balance_equal_principal',1),'interest_only'=>trans_choice('general.interest_only',1)),$loan_product->interest_method, array('class' => 'form-control','required'=>'required','id'=>'interest_method')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('interest_rate',trans_choice('general.loan',1).' '.trans_choice('general.interest',1).' (%) *',array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-2">
                                {!! Form::text('interest_rate',$loan_product->default_interest_rate, array('class' => 'form-control touchspin', 'id'=>"interest_rate",'required'=>'required')) !!}
                            </div>
                            <div class="col-sm-3">
                                {!! Form::select('interest_period',array('day'=>trans_choice('general.per_day',1),'week'=>trans_choice('general.per_week',1),'month'=>trans_choice('general.per_month',1),'year'=>trans_choice('general.per_year',1)),$loan_product->interest_period, array('class' => 'form-control', "id"=>"interest_period",'required'=>'required')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('override_interest',trans_choice('general.override',1).' '.trans_choice('general.interest',1),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-5">
                                {!! Form::select('override_interest',array('0'=>trans_choice('general.no',1),'1'=>trans_choice('general.yes',1)),0, array('class' => 'form-control','id'=>'override_interest')) !!}
                            </div>
                            <div class="col-sm-4">
                                <i class="icon-info3" data-toggle="tooltip"
                                   title="{{trans_choice('general.override_msg',1)}}"></i>
                            </div>
                        </div>
                        <div class="form-group" id="overrideDiv">
                            {!! Form::label('override_interest_amount',trans_choice('general.override',1).' '.trans_choice('general.interest',1).' %',array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-5">
                                {!! Form::text('override_interest_amount',0, array('class' => 'form-control touchspin','id'=>'override_interest_amount')) !!}
                            </div>

                        </div>

                        <div class="form-group">
                            {!! Form::label('grace_on_interest_charged',trans_choice('general.grace_on_interest',1),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-5">
                                {!! Form::number('grace_on_interest_charged',$loan_product->grace_on_interest_charged, array('class' => 'form-control', 'placeholder'=>"2")) !!}
                            </div>
                            <div class="col-sm-4">
                                <i class="icon-info3" data-toggle="tooltip"
                                   title="{{trans_choice('general.grace_on_interest_msg',1)}}"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('decimal_places',trans_choice('general.decimal_place',1),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-5">
                                {!! Form::select('decimal_places',array('round_off_to_two_decimal'=>trans_choice('general.round_off_to_two_decimal',1),'round_off_to_integer'=>trans_choice('general.round_off_to_integer',1)),$loan_product->decimal_places, array('class' => 'form-control',"id"=>"",'required'=>'required')) !!}
                            </div>
                            <div class="col-sm-4">
                                <i class="icon-info3" data-toggle="tooltip"
                                   title="{{trans_choice('general.loan_decimal_msg',1)}}"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('description',trans_choice('general.description',2),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-5">
                                {!! Form::textarea('description',null, array('class' => 'form-control', 'rows'=>"3")) !!}
                            </div>

                        </div>
                        <div class="form-group">
                            {!! Form::label('files',trans_choice('general.loan',1).' '.trans_choice('general.file',2).'('.trans_choice('general.borrower_file_types',2).')',array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-5">
                                {!! Form::file('files[]', array('class' => 'form-control file-styled', 'multiple'=>"multiple")) !!}
                            </div>
                            <div class="col-sm-9">
                            </div>
                        </div>
                        <div class="form-group" id="chargesDiv">
                            <div style="display: none;" id="saved_charges">
                                @foreach($loan_product->charges as $key)
                                    <input name="charges[]" id="charge{{$key->charge_id}}" value="{{$key->charge_id}}">
                                @endforeach
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>{{trans_choice('general.name',1)}}</th>
                                    <th>{{trans_choice('general.type',1)}}</th>
                                    <th>{{trans_choice('general.amount',1)}}</th>
                                    <th>{{trans_choice('general.collected',1)}} {{trans_choice('general.on',1)}}</th>
                                    <th>{{trans_choice('general.date',1)}}</th>
                                </tr>
                                </thead>
                                <tbody id="charges_table">
                                @foreach($loan_product->charges as $key)
                                    @if(!empty($key->charge))
                                        <tr id="row{{$key->charge->id}}">
                                            <td>{{ $key->charge->name }}</td>
                                            <td>
                                                @if($key->charge->charge_option=="fixed")
                                                    {{trans_choice('general.fixed',1)}}
                                                @endif
                                                @if($key->charge->charge_option=="principal_due")
                                                    % {{trans_choice('general.principal',1)}} {{trans_choice('general.due',1)}}
                                                @endif
                                                @if($key->charge->charge_option=="principal_interest")
                                                    % {{trans_choice('general.principal',1)}}
                                                    + {{trans_choice('general.interest',1)}} {{trans_choice('general.due',1)}}
                                                @endif
                                                @if($key->charge->charge_option=="interest_due")
                                                    % {{trans_choice('general.interest',1)}} {{trans_choice('general.due',1)}}
                                                @endif
                                                @if($key->charge->charge_option=="total_due")
                                                    % {{trans_choice('general.total',1)}} {{trans_choice('general.due',1)}}
                                                @endif
                                                @if($key->charge->charge_option=="original_principal")
                                                    % {{trans_choice('general.original',1)}} {{trans_choice('general.principal',1)}}
                                                @endif
                                            </td>
                                            <td>
                                                @if($key->charge->override==1)
                                                    <input type="text" class="form-control"
                                                           name="charge_amount_{{$key->charge->id}}"
                                                           value="{{$key->charge->amount}}" required>
                                                @else
                                                    <input  type="hidden" class="form-control"
                                                            name="charge_amount_{{$key->charge->id}}"
                                                            value="{{$key->charge->amount}}">
                                                    {{$key->charge->amount}}
                                                @endif
                                            </td>
                                            <td>
                                                @if($key->charge->charge_type=='disbursement')
                                                    {{trans_choice('general.disbursement',1)}}
                                                @endif
                                                @if($key->charge->charge_type=='specified_due_date')
                                                    {{trans_choice('general.specified_due_date',2)}}
                                                @endif
                                                @if($key->charge->charge_type=='installment_fee')
                                                    {{trans_choice('general.installment_fee',2)}}
                                                @endif
                                                @if($key->charge->charge_type=='overdue_installment_fee')
                                                    {{trans_choice('general.overdue_installment_fee',2)}}
                                                @endif
                                                @if($key->charge->charge_type=='loan_rescheduling_fee')
                                                    {{trans_choice('general.loan_rescheduling_fee',2)}}
                                                @endif
                                                @if($key->charge->charge_type=='overdue_maturity')
                                                    {{trans_choice('general.overdue_maturity',2)}}
                                                @endif
                                                @if($key->charge->charge_type=='savings_activation')
                                                    {{trans_choice('general.savings_activation',2)}}
                                                @endif
                                                @if($key->charge->charge_type=='withdrawal_fee')
                                                    {{trans_choice('general.withdrawal_fee',2)}}
                                                @endif
                                                @if($key->charge->charge_type=='monthly_fee')
                                                    {{trans_choice('general.monthly_fee',2)}}
                                                @endif
                                                @if($key->charge->charge_type=='annual_fee')
                                                    {{trans_choice('general.annual_fee',2)}}
                                                @endif
                                            </td>
                                            <td>
                                                @if($key->charge->charge_type=='specified_due_date')
                                                    <input type="text" class="form-control date-picker"
                                                           name="charge_date_{{$key->charge->id}}"
                                                           value="" >
                                                @else
                                                    <input  type="hidden" class="form-control"
                                                            name="charge_date_{{$key->charge->id}}"
                                                            value="">
                                                @endif
                                            </td>

                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="bg-navy disabled color-palette">{{trans_choice('general.custom_field',2)}}</p>
                        @foreach($custom_fields as $key)
                            <div class="form-group">
                                {!! Form::label($key->id,$key->name,array('class'=>'')) !!}
                                <div class="">
                                    @if($key->field_type=="number")
                                        <input type="number" class="form-control" name="{{$key->id}}"
                                        >
                                    @endif
                                    @if($key->field_type=="textfield")
                                        <input type="text" class="form-control" name="{{$key->id}}"
                                        >
                                    @endif
                                    @if($key->field_type=="date")
                                        <input type="text" class="form-control date-picker" name="{{$key->id}}"
                                        >
                                    @endif
                                    @if($key->field_type=="textarea")
                                        <textarea class="form-control" name="{{$key->id}}"
                                        ></textarea>
                                    @endif
                                    @if($key->field_type=="decimal")
                                        <input type="text" class="form-control touchspin" name="{{$key->id}}">
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary pull-right" name="save_return"
                    value="save_return">{{ trans_choice('general.save',1) }}
            </button>
        </div>
    {!! Form::close() !!}
    <!-- /.panel-body -->
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')
    <script>
        $(document).ready(function (e) {

            $('#products').change(function (e) {
                if ($('#products').val() != "") {
                    $.ajax({
                        type: 'GET',
                        url: "{!!  url('check_in') !!}/" + $('#products').val() + "/get_product_data",
                        success: function (data) {
                            $("#items_area").append(data);
                            doCalc();
                        }
                    });
                } else {

                }
            })
        });
        function doCalc() {
            var total = 0;
            $('tr').each(function () {
                $(this).find('span.amount').html($('input:eq(2)', this).val() * $('input:eq(3)', this).val());
            });
            $('.amount').each(function () {
                total += parseInt($(this).html(), 10);
            });
            $('div.total_amount').html(total);
            $('#principal').val(total);
        }
        function deleteRow(d) {
            var id = $(d).data("id");
            $("#" + id).remove();
            doCalc();
        }
        if ($('#type').val() == 'cash') {
            $('#loanDetails').hide();
            $('#cashDetails').show();
            $('#amount').attr('required', 'required');
            $('#loanProduct').removeAttr('required');
            $('#loan_officer_id').removeAttr('required');
            $('#principal').removeAttr('required');
            $('#loan_duration').removeAttr('required');
            $('#loan_duration_type').removeAttr('required');
            $('#repayment_cycle').removeAttr('required');
            $('#bank_account_id').removeAttr('required');
            $('#release_date').removeAttr('required');
            $('#interest_method').removeAttr('required');
            $('#interest_rate').removeAttr('required');
            $('#interest_period').removeAttr('required');
        } else {
            $('#loanDetails').show();
            $('#cashDetails').hide();
            $('#amount').removeAttr('required');
            $('#loanProduct').attr('required', 'required');
            $('#loan_officer_id').attr('required', 'required');
            $('#bank_account_id').attr('required', 'required');
            $('#principal').attr('required', 'required');
            $('#loan_duration').attr('required', 'required');
            $('#loan_duration_type').attr('required', 'required');
            $('#repayment_cycle').attr('required', 'required');
            $('#release_date').attr('required', 'required');
            $('#interest_method').attr('required', 'required');
            $('#interest_rate').attr('required', 'required');
            $('#interest_period').attr('required', 'required');
        }
        $('#type').change(function (e) {
            if ($('#type').val() == 'cash') {
                $('#loanDetails').hide();
                $('#cashDetails').show();
                $('#amount').attr('required', 'required');
                $('#loan_officer_id').removeAttr('required');
                $('#bank_account_id').removeAttr('required');
                $('#loanProduct').removeAttr('required');
                $('#principal').removeAttr('required');
                $('#loan_duration').removeAttr('required');
                $('#loan_duration_type').removeAttr('required');
                $('#repayment_cycle').removeAttr('required');
                $('#release_date').removeAttr('required');
                $('#interest_method').removeAttr('required');
                $('#interest_rate').removeAttr('required');
                $('#interest_period').removeAttr('required');
            } else {
                $('#loanDetails').show();
                $('#cashDetails').hide();
                $('#amount').removeAttr('required');
                $('#loan_officer_id').attr('required', 'required');
                $('#bank_account_id').attr('required', 'required');
                $('#loanProduct').attr('required', 'required');
                $('#principal').attr('required', 'required');
                $('#loan_duration').attr('required', 'required');
                $('#loan_duration_type').attr('required', 'required');
                $('#repayment_cycle').attr('required', 'required');
                $('#release_date').attr('required', 'required');
                $('#interest_method').attr('required', 'required');
                $('#interest_rate').attr('required', 'required');
                $('#interest_period').attr('required', 'required');
            }
        });

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
        })
        $('#loanProduct').change(function (e) {
            window.location = "{!! url('check_out/create?product_id=') !!}" + $("#loanProduct").val();
        })
    </script>
@endsection

