@extends('layouts.master')
@section('title'){{trans_choice('general.add',1)}} {{trans_choice('general.supplier',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.add',1)}} {{trans_choice('general.supplier',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('supplier/store'), 'method' => 'post', 'class' => 'form-horizontal')) !!}
        <div class="panel-body">
            <div class="form-group">
                {!! Form::label('name',trans_choice('general.name',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::text('name',null, array('class' => 'form-control', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('email',trans_choice('general.email',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::email('email',null, array('class' => 'form-control', 'placeholder'=>"")) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('mobile_phone',trans_choice('general.mobile_phone',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::text('mobile_phone',null, array('class' => 'form-control', 'placeholder'=>"")) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('work_phone',trans_choice('general.work_phone',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::text('work_phone',null, array('class' => 'form-control', 'placeholder'=>"")) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('address',trans_choice('general.address',2),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-9">
                    {!! Form::textarea('address',null, array('class' => 'form-control', 'rows'=>"3")) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('notes',trans_choice('general.note',2),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-9">
                    {!! Form::textarea('notes',null, array('class' => 'form-control', 'rows'=>"4")) !!}
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

