@extends('layouts.master')
@section('title')
    {{trans_choice('general.add',1)}} {{trans_choice('general.saving',2)}} {{trans_choice('general.transaction',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h3 class="panel-title">{{trans_choice('general.add',1)}} {{trans_choice('general.saving',2)}} {{trans_choice('general.transaction',1)}}</h3>
            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('saving/'.$saving->id.'/transfer/store'), 'method' => 'post', 'id' => 'transfer_form',"enctype"=>"multipart/form-data")) !!}
        <div class="panel-body">
            <div class="alert alert-warning alert-styled-left">
                <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>
                {{trans_choice('general.transfer_warning',1)}}
            </div>
            <div class="form-group">
                {!! Form::label('loan_id',trans_choice('general.loan',1),array('class'=>'')) !!}
                {!! Form::select('loan_id',$loans,null, array('class' => 'form-control select2', 'placeholder'=>'','required'=>'','id'=>'loan_id')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('amount',trans_choice('general.amount',1),array('class'=>'')) !!}
                {!! Form::number('amount',null, array('class' => 'form-control', 'placeholder'=>'','required'=>'','id'=>'amount')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('date',trans_choice('general.date',2),array('class'=>'')) !!}
                {!! Form::text('date',date("Y-m-d"), array('class' => 'form-control date-picker', 'placeholder'=>'','required'=>'','id'=>'date')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('time',trans_choice('general.time',2),array('class'=>'')) !!}
                {!! Form::text('time',date("H:i"), array('class' => 'form-control time-picker', 'placeholder'=>'','required'=>'','id'=>'time')) !!}
            </div>

            <div class="form-group">
                {!! Form::label('notes',trans_choice('general.note',2),array('class'=>'')) !!}
                {!! Form::textarea('notes',null, array('class' => 'form-control', 'rows'=>'3',)) !!}
            </div>
        </div>
        <!-- /.panel-body -->
        <div class="panel-footer">
            <div class="heading-elements">
                <button type="submit" class="btn btn-primary pull-right"
                        id="submit_transfer">{{trans_choice('general.save',1)}}</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')
    <script>
        $("#transfer_form").validate({
            rules: {
                field: {
                    required: true,
                    number: true
                }
            }
        });
    </script>
@endsection
