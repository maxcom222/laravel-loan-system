@extends('layouts.master')
@section('title'){{trans_choice('general.tax_rate',2)}}
@endsection
@section('current-page'){{trans_choice('general.tax_rate',2)}}
@endsection
@section('content')
        <!-- Default box -->
<div class="box box-default">
    <div class="panel-heading">
        <h3 class="panel-title">{{trans_choice('general.tax_rate',2)}}</h3>

        <div class="heading-elements">
            <a data-toggle="modal" data-target="#addTax" class="btn btn-info btn-sm">
                <i class="fa fa-plus"></i> {{trans_choice('general.tax_rate',1)}}
            </a>
        </div>
    </div>
    <div class="panel-body">
        <table class="table table-bordered responsive table-stripped table-hover data-table">
            <thead>
            <tr>
                <th>{{trans_choice('general.name',1)}}</th>
                <th>{{trans_choice('general.tax_rate',1)}}</th>
                <th>{{trans_choice('general.note',1)}}</th>
                <th>{{trans_choice('general.action',1)}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data as $key)
                <tr>
                    <td>{{ $key->title }}</td>
                    <td>{{ $key->percentage}}%</td>
                    <td>{{ $key->notes}}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info btn-flat dropdown-toggle"
                                    data-toggle="dropdown" aria-expanded="false">
                                {{trans_choice('general.choose',1)}} <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a data-id="{{$key->id}}" data-toggle="modal" data-target="#editTax"><i
                                                class="fa fa-edit"></i>
                                        {{trans_choice('general.edit',1)}}</a></li>
                                <li><a href="{{ url('tax/'.$key->id.'/delete') }}"
                                       data-toggle="confirmation"><i
                                                class="fa fa-trash"></i> {{trans_choice('general.delete',1)}}</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.panel-body -->
    <div class="modal" id="addTax">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Add Tax</h4>
                </div>
                {!! Form::open(array('url' => url('tax/store'),'method'=>'post')) !!}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" style="">

                            <div class="form-group">
                                <div class="form-line">
                                    {!!  Form::label( 'Name',null,array('class'=>' control-label')) !!}
                                    {!! Form::text('name','',array('class'=>'form-control ','required'=>'required')) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-line">
                                    {!!  Form::label( 'Percentage',null,array('class'=>' control-label')) !!}
                                    {!! Form::text('percentage','',array('class'=>'form-control touchspin','required'=>'required')) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-line">
                                    {!!  Form::label( 'Notes',null,array('class'=>' control-label')) !!}
                                    {!! Form::textarea('notes','',array('class'=>'form-control')) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info">Save</button>
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                </div>
                {!! Form::close() !!}
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal" id="editTax">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <script>
        $('#editTax').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $.ajax({
                type: 'GET',
                url: "{!!  url('/') !!}/tax/" + id + "/edit",
                success: function (data) {
                    $(e.currentTarget).find(".modal-content").html(data);
                }
            });
        })
    </script>
</div>
<!-- /.box -->
@endsection
