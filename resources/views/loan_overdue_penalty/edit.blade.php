@extends('layouts.master')
@section('title')
    {{trans_choice('general.edit',1)}} {{trans_choice('general.loan',1)}} {{trans_choice('general.penalty',1)}}
@endsection
@section('current-page'){{trans_choice('general.edit',1)}} {{trans_choice('general.loan',1)}} {{trans_choice('general.penalty',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.edit',1)}} {{trans_choice('general.loan',1)}} {{trans_choice('general.penalty',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('loan/loan_overdue_penalty/'.$loan_overdue_penalty->id.'/update'), 'method' => 'post', 'class' => 'form-horizontal')) !!}
        <div class="panel-body">
            <div class="form-group">
                {!! Form::label('name',trans_choice('general.name',1),array('class'=>'col-sm-2 control-label')) !!}
                <div class="col-sm-10">
                    {!! Form::text('name',$loan_overdue_penalty->name, array('class' => 'form-control', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
            </div>
            <div class="form-group">
                <label for="inputName" class="col-sm-2 control-label">{{trans_choice('general.type',1)}}</label>

                <div class="col-sm-5">
                    <div class="radio">
                        <label>
                            <input class="styled" type="radio" name="type" id="inputFeeAmountFixed" value="fixed"
                                   @if($loan_overdue_penalty->type=='fixed') checked @endif>
                            {{trans_choice('general.fee_fixed',1)}}

                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input class="styled" type="radio" name="type" id="inputFeeAmountPercentage" value="percentage"
                                   @if($loan_overdue_penalty->type=='percentage') checked @endif>
                            {{trans_choice('general.fee_percentage',1)}}
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('amount',trans_choice('general.amount',1),array('class'=>'col-sm-2 control-label')) !!}
                <div class="col-sm-10">
                    {!! Form::text('amount',$loan_overdue_penalty->amount, array('class' => 'form-control touchspin', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('days',trans_choice('general.day',2),array('class'=>'col-sm-2 control-label')) !!}
                <div class="col-sm-10">
                    {!! Form::number('days',$loan_overdue_penalty->days, array('class' => 'form-control', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('notes',trans_choice('general.note',2),array('class'=>'col-sm-2 control-label')) !!}
                <div class="col-sm-10">
                    {!! Form::textarea('notes',$loan_overdue_penalty->notes, array('class' => 'form-control', 'placeholder'=>"",'rows'=>'3')) !!}
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

