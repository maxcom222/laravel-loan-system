@extends('layouts.master')
@section('title')
    {{ trans_choice('general.change',1) }} {{ trans_choice('general.branch',1) }}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{ trans_choice('general.change',1) }} {{ trans_choice('general.branch',1) }}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('branch/change'), 'method' => 'post', 'class' => 'form-horizontal')) !!}
        <div class="panel-body">
            <div class="form-group">
                {!! Form::label('branch_id',trans_choice('general.branch',1),array('class'=>'col-sm-2 control-label')) !!}
                <div class="col-sm-10">
                    {!! Form::select('branch_id',$branches,null, array('class' => 'form-control select2', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
            </div>
        </div>
        <!-- /.panel-body -->
        <div class="panel-footer">
            <div class="heading-elements">
                <button type="submit" class="btn btn-primary pull-right">{{ trans_choice('general.save',1) }}</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <!-- /.box -->
@endsection

