@extends('layouts.master')
@section('title')
    {{trans_choice('general.edit',1)}} {{trans_choice('general.loan',1)}} {{trans_choice('general.comment',1)}}
@endsection
@section('content')
    <!-- Default box -->
    <div class="panel panel-white">
        <div class="panel-header">
            <h6 class="panel-title"> {{trans_choice('general.edit',1)}} {{trans_choice('general.loan',1)}} {{trans_choice('general.comment',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('loan/'.$id.'/loan_comment/'.$loan_comment->id.'/update'), 'method' => 'post', 'class' => 'form-horizontal')) !!}
        <div class="panel-body">
            <div class="form-group">
                {!! Form::label('notes',trans_choice('general.comment',1),array('class'=>'col-sm-2 control-label')) !!}
                <div class="col-sm-10">
                    {!! Form::textarea('notes',$loan_comment->notes, array('class' => 'form-control', 'placeholder'=>"",'required'=>'required')) !!}
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

