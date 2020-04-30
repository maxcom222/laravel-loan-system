@extends('layouts.master')
@section('title'){{trans_choice('general.send',1)}} {{trans_choice('general.email',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.send',1)}} {{trans_choice('general.email',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('communication/email/store'), 'method' => 'post', 'class' => 'form-horizontal')) !!}
        <div class="panel-body">
            <p>In your email you can use any of the following tags:
                {borrowerTitle}, {borrowerFirstName}, {borrowerLastName}, {borrowerAddress}, {borrowerMobile},
                {borrowerEmail}, {borrowerTotalLoansDue}, {borrowerTotalLoansBalance},
                {borrowerTotalLoansPaid}</p>

            <div class="form-group">
                {!! Form::label('send_to',trans_choice('general.to',1),array('class'=>'col-sm-2 control-label')) !!}
                <div class="col-sm-10">
                    {!! Form::select('send_to',$borrowers, $selected,array('class' => 'form-control select2', 'required'=>"")) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('subject',trans_choice('general.subject',1),array('class'=>'col-sm-2 control-label')) !!}
                <div class="col-sm-10">
                    {!! Form::text('subject',null, array('class' => 'form-control', 'required'=>"")) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('message',trans_choice('general.message',1),array('class'=>'col-sm-2 control-label')) !!}
                <div class="col-sm-10">
                    {!! Form::textarea('message',null, array('class' => 'form-control tinymce', 'placeholder'=>"")) !!}
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

