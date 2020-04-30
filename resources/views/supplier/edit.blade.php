@extends('layouts.master')
@section('title'){{trans_choice('general.edit',1)}} {{trans_choice('general.supplier',1)}}
@endsection
@section('content')
<div class="box">
    <div class="panel-heading">
        <h6 class="panel-title">{{trans_choice('general.edit',1)}} {{trans_choice('general.supplier',1)}}</h6>

        <div class="heading-elements">

        </div>
    </div>
    {!! Form::open(array('url' => url('supplier/'.$supplier->id.'/update'), 'method' => 'post', 'class' => 'form-horizontal')) !!}
    <div class="panel-body">
        <div class="form-group">
            {!! Form::label('name',trans_choice('general.name',1),array('class'=>'col-sm-3 control-label')) !!}
            <div class="col-sm-5">
                {!! Form::text('name',$supplier->name, array('class' => 'form-control', 'placeholder'=>"",'required'=>'required')) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('email',trans_choice('general.email',1),array('class'=>'col-sm-3 control-label')) !!}
            <div class="col-sm-5">
                {!! Form::email('email',$supplier->email, array('class' => 'form-control', 'placeholder'=>"")) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('mobile_phone',trans_choice('general.mobile_phone',1),array('class'=>'col-sm-3 control-label')) !!}
            <div class="col-sm-5">
                {!! Form::text('mobile_phone',$supplier->mobile_phone, array('class' => 'form-control', 'placeholder'=>"")) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('work_phone',trans_choice('general.work_phone',1),array('class'=>'col-sm-3 control-label')) !!}
            <div class="col-sm-5">
                {!! Form::text('work_phone',$supplier->work_phone, array('class' => 'form-control', 'placeholder'=>"")) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('address',trans_choice('general.address',2),array('class'=>'col-sm-3 control-label')) !!}
            <div class="col-sm-9">
                {!! Form::textarea('address',$supplier->address, array('class' => 'form-control', 'rows'=>"3")) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('notes',trans_choice('general.description',1),array('class'=>'col-sm-3 control-label')) !!}
            <div class="col-sm-9">
                {!! Form::textarea('notes',$supplier->notes, array('class' => 'form-control', 'rows'=>"4")) !!}
            </div>
        </div>

    </div>
    <!-- /.panel-body -->
    <div class="panel-footer">
        <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
    </div>
    {!! Form::close() !!}
</div>
<!-- /.box -->
@endsection

