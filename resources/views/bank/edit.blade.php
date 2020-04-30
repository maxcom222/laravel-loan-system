@extends('layouts.master')
@section('title'){{trans_choice('general.edit',1)}} {{trans_choice('general.bank',1)}} {{trans_choice('general.account',1)}}
@endsection
@section('content')
<div class="box">
    <div class="panel-heading">
        <h6 class="panel-title">{{trans_choice('general.edit',1)}} {{trans_choice('general.bank',1)}} {{trans_choice('general.account',1)}}</h6>

        <div class="heading-elements">

        </div>
    </div>
    {!! Form::open(array('url' => url('capital/bank/'.$bank->id.'/update'), 'method' => 'post', 'class' => 'form-horizontal')) !!}
    <div class="panel-body">
        <div class="form-group">
            {!! Form::label('name',trans_choice('general.name',1),array('class'=>'col-sm-3 control-label')) !!}
            <div class="col-sm-5">
                {!! Form::text('name',$bank->name, array('class' => 'form-control', 'placeholder'=>"",'required'=>'required')) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('notes',trans_choice('general.description',1),array('class'=>'col-sm-3 control-label')) !!}
            <div class="col-sm-9">
                {!! Form::textarea('notes',$bank->notes, array('class' => 'form-control', 'rows'=>"4")) !!}
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

