@extends('layouts.master')
@section('title')
    {{trans_choice('general.add',1)}} {{trans_choice('general.saving',2)}} {{trans_choice('general.transaction',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.add',1)}} {{trans_choice('general.saving',2)}} {{trans_choice('general.transaction',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('saving/'.$saving->id.'/savings_transaction/store'), 'method' => 'post', 'id' => 'transaction_form', 'class' => 'form-horizontal')) !!}
        <div class="panel-body">
            <div class="form-group">
                {!! Form::label('type',trans_choice('general.type',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::select('type',array('deposit'=>trans_choice('general.deposit',1),'withdrawal'=>trans_choice('general.withdrawal',1),'interest'=>trans_choice('general.interest',1),'bank_fees'=>trans_choice('general.charge',1)),null, array('class' => 'form-control','required'=>'','id'=>'type')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('amount',trans_choice('general.amount',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::number('amount',null, array('class' => 'form-control', 'placeholder'=>"Number or decimal only",'required'=>'required','id'=>'amount')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('payment_method_id',trans_choice('general.payment',1).' '.trans_choice('general.method',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::select('payment_method_id',$repayment_methods,null, array('class' => ' form-control','required'=>'required','id'=>'payment_method_id')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('receipt',trans_choice('general.receipt',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::text('receipt',null, array('class' => 'form-control', 'placeholder'=>"",''=>'required','id'=>'receipt')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('date',trans_choice('general.date',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::text('date',date("Y-m-d"), array('class' => 'form-control date-picker', 'placeholder'=>"",'required'=>'required','id'=>'date')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('time',trans_choice('general.time',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::text('time',date("H:i"), array('class' => 'form-control time-picker', 'placeholder'=>'','required'=>'','id'=>'time')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('notes',trans_choice('general.description',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-9">
                    {!! Form::textarea('notes',null, array('class' => 'form-control', 'rows'=>"4",'id'=>'notes')) !!}
                </div>
            </div>
        </div>
        <!-- /.panel-body -->
        <div class="panel-footer">
            <div class="heading-elements">
                <button type="submit" class="btn btn-primary pull-right" id="submit_transaction">{{trans_choice('general.save',1)}}</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')
    <script>
        $("#transaction_form").validate({
            rules: {
                field: {
                    required: true,
                    number: true
                }
            }
        });
    </script>
@endsection

