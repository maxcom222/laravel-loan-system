@extends('layouts.master')
@section('title')
    {{trans_choice('general.add',1)}} {{trans_choice('general.loan',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.add',1)}} {{trans_choice('general.loan',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('loan/loan_calculator/show'), 'method' => 'post', 'class' => 'form-horizontal',"enctype"=>"multipart/form-data")) !!}
        <div class="panel-body">
            <p class="bg-navy disabled color-palette">{{trans_choice('general.loan',1)}} {{trans_choice('general.term',2)}}
                ({{trans_choice('general.required',1)}} {{trans_choice('general.field',2)}}):</p>

            <p class="text-red"><b>{{trans_choice('general.principal',1)}} {{trans_choice('general.amount',1)}}:</b></p>


            <div class="form-group">
                {!! Form::label('principal',trans_choice('general.principal',1).' '.trans_choice('general.amount',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::text('principal',null, array('class' => 'form-control touchspin', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
            </div>
            <hr>
            <p class="text-red"><b>{{trans_choice('general.duration',1)}}:</b></p>

            <div class="form-group">
                {!! Form::label('loan_duration',trans_choice('general.loan',1).' '.trans_choice('general.duration',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-3">
                    {!! Form::number('loan_duration',null, array('class' => 'form-control', 'placeholder'=>"5",'required'=>'required','id'=>'loan_duration')) !!}
                </div>
                <div class="col-sm-3">
                    {!! Form::select('loan_duration_type',array('day'=>trans_choice('general.day',1).'(s)','week'=>trans_choice('general.week',1).'(s)','month'=>trans_choice('general.month',1).'(s)','year'=>trans_choice('general.year',1).'(s)'),null, array('class' => 'form-control',"id"=>"loan_duration_type",'required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                </div>
            </div>
            <hr>
            <p class="text-red"><b>{{trans_choice('general.repayment',2)}}:</b></p>

            <div class="form-group">
                {!! Form::label('repayment_cycle',trans_choice('general.repayment_cycle',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::select('repayment_cycle',array('daily'=>trans_choice('general.daily',1),'weekly'=>trans_choice('general.weekly',1),'monthly'=>trans_choice('general.monthly',1),'bi_monthly'=>trans_choice('general.bi_monthly',1),'quarterly'=>trans_choice('general.quarterly',1),'semi_annual'=>trans_choice('general.semi_annually',1),'annual'=>trans_choice('general.annual',1)),null, array('class' => 'form-control',"id"=>"repayment_cycle",'required'=>'required')) !!}
                </div>
            </div>
            <hr>
            <div class="form-group">
                {!! Form::label('release_date',trans_choice('general.expected',1).' '.trans_choice('general.disbursement',1).' '.trans_choice('general.date',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::text('release_date',date("Y-m-d"), array('class' => 'form-control date-picker', 'placeholder'=>"yyyy-mm-dd",'required'=>'required','id'=>'release_date')) !!}
                </div>
            </div>
            <p>{{trans_choice('general.first_payment_date_optional',1)}}</p>

            <div class="form-group">
                {!! Form::label('first_payment_date',trans_choice('general.first',1).' '.trans_choice('general.repayment',1).' '.trans_choice('general.date',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::text('first_payment_date',null, array('class' => 'form-control date-picker', 'placeholder'=>"yyyy-mm-dd",''=>'','id'=>'first_payment_date','required'=>'required')) !!}
                </div>
            </div>
            <hr>
            <p class="text-red"><b>{{trans_choice('general.interest',1)}}:</b></p>

            <div class="form-group">
                {!! Form::label('interest_method',trans_choice('general.interest',1).' '.trans_choice('general.method',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::select('interest_method',array('flat_rate'=>trans_choice('general.flat_rate',1),'declining_balance_equal_installments'=>trans_choice('general.declining_balance_equal_installments',1),'declining_balance_equal_principal'=>trans_choice('general.declining_balance_equal_principal',1),'interest_only'=>trans_choice('general.interest_only',1)),null, array('class' => 'form-control','required'=>'required')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('interest_rate',trans_choice('general.loan',1).' '.trans_choice('general.interest',1).' %',array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-2">
                    {!! Form::text('interest_rate',null, array('class' => 'form-control touchspin', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
                <div class="col-sm-3">
                    {!! Form::select('interest_period',array('day'=>trans_choice('general.per_day',1),'week'=>trans_choice('general.per_week',1),'month'=>trans_choice('general.per_month',1),'year'=>trans_choice('general.per_year',1)),null, array('class' => 'form-control', "id"=>"inputDefaultInterestPeriod",'required'=>'required')) !!}
                </div>
            </div>


            <div class="form-group">
                {!! Form::label('grace_on_interest_charged',trans_choice('general.grace_on_interest',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::number('grace_on_interest_charged',null, array('class' => 'form-control', 'placeholder'=>"2")) !!}
                </div>

            </div>
            <hr>

            <p class="text-red"><b>{{trans_choice('general.loan_due_heading',1)}}:</b></p>

            <p>{{trans_choice('general.loan_decimal_msg',1)}}</p>

            <div class="form-group">
                {!! Form::label('decimal_places',trans_choice('general.decimal_place',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::select('decimal_places',array('round_off_to_two_decimal'=>trans_choice('general.round_off_to_two_decimal',1),'round_off_to_integer'=>trans_choice('general.round_off_to_integer',1)),null, array('class' => 'form-control',"id"=>"",'required'=>'required')) !!}
                </div>
            </div>
            <hr>

        </div>
        <!-- /.panel-body -->
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
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
            })
            $('#loanProduct').change(function (e) {
                window.location = "{!! url('loan/create?product_id=') !!}" + $("#loanProduct").val();
            })
            /*$('#release_date').focusout(function(e){
             var releaseDate=$('#release_date').val();
             var duration=$('#loan_duration').val();
             var durationType=$('#loan_duration_type').val();
             var repaymentCycle=$('#repayment_cycle').val();
             var repaymentDate=moment(releaseDate, 'YYYY-MM-DD');
             if(repaymentCycle=='daily'){

             repaymentDate.add(1, 'days').format('YYYY-MM-DD');
             alert(repaymentDate);
             $('#first_payment_date').val(repaymentDate);

             }
             })*/
        });
    </script>
@endsection
