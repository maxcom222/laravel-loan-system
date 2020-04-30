@extends('layouts.master')
@section('title'){{trans_choice('general.edit',1)}} {{trans_choice('general.capital',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.edit',1)}} {{trans_choice('general.capital',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('capital/'.$capital->id.'/update'), 'method' => 'post', 'class' => 'form-horizontal')) !!}
        <div class="panel-body">
            <div class="form-group">
                {!! Form::label('amount',trans_choice('general.amount',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::text('amount',$capital->amount, array('class' => 'form-control touchspin', 'placeholder'=>"Number or decimal only",'required'=>'required')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('date',trans_choice('general.date',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::text('date',$capital->date, array('class' => 'form-control date-picker', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('credit_account_id',trans_choice('general.from',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::select('credit_account_id',$chart,$capital->credit_account_id, array('class' => 'form-control select2', 'placeholder'=>"",'required'=>'')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('debit_account_id',trans_choice('general.to',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::select('debit_account_id',$chart,$capital->debit_account_id, array('class' => 'form-control select2', 'placeholder'=>"",'required'=>'')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('notes',trans_choice('general.description',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-9">
                    {!! Form::textarea('notes',$capital->notes, array('class' => 'form-control', 'rows'=>"4")) !!}
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

