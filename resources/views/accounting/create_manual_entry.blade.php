@extends('layouts.master')
@section('title'){{trans_choice('general.journal',1)}} {{trans_choice('general.manual_entry',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.add',1)}} {{trans_choice('general.journal',1)}} {{trans_choice('general.manual_entry',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('accounting/manual_entry/store'), 'method' => 'post', 'class' => 'form-horizontal')) !!}
        <div class="panel-body">

            <div class="form-group">
                {!! Form::label('amount',trans_choice('general.amount',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::text('amount',null, array('class' => 'form-control touchspin', 'placeholder'=>"Number or decimal only",'required'=>'required')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('date',trans_choice('general.date',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::text('date',date("Y-m-d"), array('class' => 'form-control date-picker', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('credit_account_id',trans_choice('general.credit',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::select('credit_account_id',$chart_of_accounts,null, array('class' => 'form-control select2', 'placeholder'=>"",'required'=>'')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('debit_account_id',trans_choice('general.debit',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::select('debit_account_id',$chart_of_accounts,null, array('class' => 'form-control select2', 'placeholder'=>"",'required'=>'')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('reference',trans_choice('general.reference',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-9">
                    {!! Form::text('reference',null, array('class' => 'form-control', 'rows'=>"2")) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('name',trans_choice('general.description',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-9">
                    {!! Form::textarea('name',null, array('class' => 'form-control', 'rows'=>"2")) !!}
                </div>
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

