@extends('layouts.master')
@section('title')
    {{trans_choice('general.edit',1)}} {{trans_choice('general.category',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.edit',1)}} {{trans_choice('general.category',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('product/category/'.$product_category->id.'/update'), 'method' => 'post', 'class' => 'form-horizontal')) !!}
        <div class="panel-body">
            <div class="form-group">
                {!! Form::label('name',trans_choice('general.name',1),array('class'=>'col-sm-2 control-label')) !!}
                <div class="col-sm-10">
                    {!! Form::text('name',$product_category->name, array('class' => 'form-control', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('parent_id',trans_choice('general.parent',1),array('class'=>'col-sm-2 control-label')) !!}
                <div class="col-sm-10">
                    <select class="form-control select2" name="parent_id" id="parent_id" required>
                        <option value="0">{{trans_choice('general.none',1)}}</option>
                        {{\App\Helpers\GeneralHelper::printTree($tree)}}
                    </select>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('active',trans_choice('general.active',1),array('class'=>'col-sm-2 control-label')) !!}
                <div class="col-sm-10">
                    {!! Form::select('active',['1'=>trans_choice('general.yes',1),'0'=>trans_choice('general.no',1)],$product_category->active, array('class' => 'form-control', 'required'=>"",''=>'')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('notes',trans_choice('general.note',2),array('class'=>'col-sm-2 control-label')) !!}
                <div class="col-sm-10">
                    {!! Form::textarea('notes',$product_category->notes, array('class' => 'form-control', 'placeholder'=>"",'rows'=>'3')) !!}
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
    <script>
        $(document).ready(function (e) {
            $('#parent_id option[value="{{$product_category->parent_id}}"]').attr('selected', true)
        })
    </script>
@endsection