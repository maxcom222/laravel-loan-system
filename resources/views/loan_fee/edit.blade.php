@extends('layouts.master')
@section('title')
    {{trans_choice('general.edit',1)}} {{trans_choice('general.loan',1)}} {{trans_choice('general.fee',1)}}
@endsection
@section('current-page'){{trans_choice('general.edit',1)}} {{trans_choice('general.loan',1)}} {{trans_choice('general.fee',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.edit',1)}} {{trans_choice('general.loan',1)}} {{trans_choice('general.fee',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('loan/loan_fee/'.$loan_fee->id.'/update'), 'method' => 'post', 'class' => 'form-horizontal')) !!}
        <div class="panel-body">
            <div class="form-group">
                {!! Form::label('name',trans_choice('general.name',1),array('class'=>'col-sm-2 control-label')) !!}
                <div class="col-sm-10">
                    {!! Form::text('name',$loan_fee->name, array('class' => 'form-control', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
            </div>
            <hr>
            <div class="callout callout-info">
                <p>{{trans_choice('general.fee_msg',1)}}</p>
            </div>
            <div class="form-group">
                <label for="inputName"
                       class="col-sm-2 control-label">{{trans_choice('general.fee_calculation',1)}}</label>

                <div class="col-sm-5">
                    <div class="radio">
                        <label>
                            <input type="radio" class="styled" name="loan_fee_type" id="inputFeeAmountFixed" value="fixed"
                                   @if($loan_fee->loan_fee_type=='fixed') checked @endif>
                            {{trans_choice('general.fee_fixed',1)}}

                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" class="styled" name="loan_fee_type" id="inputFeeAmountPercentage" value="percentage"
                                   @if($loan_fee->loan_fee_type=='percentage') checked @endif>
                            {{trans_choice('general.fee_percentage',1)}}
                        </label>
                    </div>
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

