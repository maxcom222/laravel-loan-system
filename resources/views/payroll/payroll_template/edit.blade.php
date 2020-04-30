@extends('layouts.master')
@section('title'){{trans_choice('general.edit',1)}} {{trans_choice('general.payroll',1)}} {{trans_choice('general.template',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.edit',1)}} {{trans_choice('general.payroll',1)}} {{trans_choice('general.template',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('payroll/template/'.$id.'/update'), 'method' => 'post', 'class' => 'form-horizontal')) !!}
        <div class="panel-body">
            <p>You can edit the template by changing the fields and adding or deleting rows.</p>

            <div class="row">
                <div class="col-md-6">
                    @foreach($top_left as $key)
                        <div class="form-group" id="{{$key->id}}">
                            <div class="col-sm-10">
                                {!! Form::text($key->id,$key->name, array('class' => 'form-control', 'placeholder'=>"",'required'=>'required')) !!}
                            </div>
                            <div class="col-sm-2">
                                @if($key->is_default==0)
                                    <a href="{{url('payroll/template/'.$id.'/delete_meta?action=delete_meta&meta_id='.$key->id)}}"
                                       class="deleteMeta" div-id="{{$key->id}}"><i class="fa fa-trash"></i> </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    <div class="form-group">
                        <button type="button" class="btn btn-info margin" data-toggle="modal" data-target="#addRow"
                                data-position="top_left">{{trans_choice('general.add',1)}} {{trans_choice('general.row',1)}}
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group" id="">
                        <div class="col-sm-10">
                            <label>{{trans_choice('general.payroll',1)}} {{trans_choice('general.date',1)}}</label>
                        </div>
                    </div>
                    @foreach($top_right as $key)
                        <div class="form-group" id="{{$key->id}}">
                            <div class="col-sm-10">
                                {!! Form::text($key->id,$key->name, array('class' => 'form-control', 'placeholder'=>"",'required'=>'required')) !!}
                            </div>
                            <div class="col-sm-2">
                                @if($key->is_default==0)
                                    <a href="{{url('payroll/template/'.$id.'/delete_meta?action=delete_meta&meta_id='.$key->id)}}"
                                       class="deleteMeta" div-id="{{$key->id}}"><i class="fa fa-trash"></i> </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    <div class="form-group">
                        <button type="button" class="btn btn-info margin" data-toggle="modal" data-target="#addRow"
                                data-position="top_right">{{trans_choice('general.add',1)}} {{trans_choice('general.row',1)}}
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-solid box-primary">
                        <div class="box-header">
                            <h3 class="panel-title">{{trans_choice('general.addition',2)}}</h3>
                        </div>
                        <div class="panel-body">
                            @foreach($bottom_left as $key)
                                <div class="form-group" id="{{$key->id}}">
                                    <div class="col-sm-10">
                                        {!! Form::text($key->id,$key->name, array('class' => 'form-control', 'placeholder'=>"",'required'=>'required')) !!}
                                    </div>
                                    <div class="col-sm-2">
                                        @if($key->is_default==0)
                                            <a href="{{url('payroll/template/'.$id.'/delete_meta?action=delete_meta&meta_id='.$key->id)}}"
                                               class="deleteMeta" div-id="{{$key->id}}"><i class="fa fa-trash"></i> </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            <div class="form-group">
                                <button type="button" class="btn btn-info margin" data-toggle="modal"
                                        data-target="#addRow"
                                        data-position="bottom_left">{{trans_choice('general.add',1)}} {{trans_choice('general.row',1)}}
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="box box-solid box-danger">
                        <div class="box-header">
                            <h3 class="panel-title">{{trans_choice('general.deduction',2)}}</h3>
                        </div>
                        <div class="panel-body">
                            @foreach($bottom_right as $key)
                                <div class="form-group" id="{{$key->id}}">
                                    <div class="col-sm-10">
                                        {!! Form::text($key->id,$key->name, array('class' => 'form-control', 'placeholder'=>"",'required'=>'required')) !!}
                                    </div>
                                    <div class="col-sm-2">
                                        @if($key->is_default==0)
                                            <a href="{{url('payroll/template/'.$id.'/delete_meta?action=delete_meta&meta_id='.$key->id)}}"
                                               class="deleteMeta" div-id="{{$key->id}}"><i class="fa fa-trash"></i> </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            <div class="form-group">
                                <button type="button" class="btn btn-info margin" data-toggle="modal"
                                        data-target="#addRow"
                                        data-position="bottom_right">{{trans_choice('general.add',1)}} {{trans_choice('general.row',1)}}
                                </button>
                            </div>
                        </div>
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
    <div class="modal fade" id="addRow">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">*</span></button>
                    <h4 class="modal-title">{{trans_choice('general.add',1)}} {{trans_choice('general.row',1)}}</h4>
                </div>
                {!! Form::open(array('url' => url('payroll/template/'.$id.'/add_row'),'class'=>'','id'=>'paypalForm')) !!}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" style="">
                            <input name="position" value="" type="hidden" id="position">

                            <div class="form-group">
                                {!!  Form::label( 'name',trans_choice('general.name',1),array('class'=>' control-label')) !!}
                                {!! Form::text('name','',array('class'=>'form-control','required'=>'required','id'=>'amount')) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info">{{trans_choice('general.submit',1)}}</button>
                    <button type="button" class="btn default"
                            data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
                </div>
                {!! Form::close() !!}
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

@endsection
@section('footer-scripts')
    <script>
        $(document).ready(function () {
            $('.deleteMeta').on('click', function (e) {
                e.preventDefault();
                var href = $(this).attr('href');
                var div_id = $(this).attr('div-id');
                swal({
                    title: 'Are you sure?',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ok',
                    cancelButtonText: 'Cancel'
                }).then(function () {
                    $.ajax({
                        type: 'GET',
                        url: href,
                        success: function (data) {
                            $('#' + div_id).hide();
                        }
                    });
                    swal({title: "Deleted!", text: "Field has been deleted.", type: "success", timer: 2000});
                })
            });
        });
        $('#addRow').on('shown.bs.modal', function (e) {
            var position = $(e.relatedTarget).data('position');
            $(e.currentTarget).find("#position").val(position);
        })
    </script>
@endsection
