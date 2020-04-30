@extends('layouts.master')
@section('title')
    Edit Permission
@endsection
@section('current-page')
    Edit Permission
@endsection
@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title">Edit Permission</h6>

        </div>
        {!! Form::open(array('url' => 'user/permission/'.$permission->id.'/update','class'=>'',"enctype" => "multipart/form-data")) !!}
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="form-line">
                            {!!  Form::label('Type',null,array('class'=>' control-label')) !!}
                            {!! Form::select('type', array('0' => 'Parent Permission', '1' => 'Sub Permission'),$selected,array('class'=>'form-control','id'=>'type')) !!}
                        </div>
                    </div>
                    <div class="form-group" id="parent">
                        <div class="form-line">
                            {!!  Form::label('Parent',null,array('class'=>' control-label')) !!}
                            {!! Form::select('parent_id', $parent,$permission->parent_id,array('class'=>' select2')) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-line">
                            {!!  Form::label('Name',null,array('class'=>'control-label')) !!}
                            {!! Form::text('name',$permission->name,array('class'=>'form-control')) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-line">
                            {!!  Form::label('Slug',null,array('class'=>'control-label')) !!}
                            {!! Form::text('slug',$permission->slug,array('class'=>'form-control')) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-line">
                            {!!  Form::label('Description',null,array('class'=>'control-label')) !!}
                            {!! Form::textarea('description',$permission->description,array('class'=>'form-control')) !!}
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- /.panel-body -->
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary pull-right">Save</button>
        </div>
        {!! Form::close() !!}
    </div>
    <script>
        $(document).ready(function () {
            if ($('#type').val() == 0) {
                $('#parent').hide();
            } else {
                $('#parent').show();
            }
            $('#type').change(function () {
                if ($('#type').val() == 0) {
                    $('#parent').hide();
                    $('#type').val('0')
                } else {
                    $('#parent').show();
                }
            })
        })
    </script>
@endsection