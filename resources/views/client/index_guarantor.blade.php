@extends('client.layout')
@section('title')
    {{ trans_choice('general.my',1) }} {{ trans_choice('general.guarantee',1) }} {{ trans_choice('general.request',2) }}
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h3 class="panel-title"> {{ trans_choice('general.my',1) }} {{ trans_choice('general.guarantee',1) }} {{ trans_choice('general.request',2) }}</h3>

                    <div class="heading-elements">
                    </div>
                </div>
                <div class="panel-body table-responsive ">
                    <table id="data-table" class="table  table-condensed">
                        <thead>
                        <tr style="background-color: #D1F9FF">
                            <th>{{trans_choice('general.name',1)}}</th>
                            <th>{{trans_choice('general.status',1)}}</th>
                            <th>{{trans_choice('general.amount',1)}}</th>
                            <th>{{trans_choice('general.accepted',1)}} {{trans_choice('general.amount',1)}}</th>
                            <th>{{trans_choice('general.date',1)}}</th>
                            <th>{{trans_choice('general.action',1)}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $key)
                            <tr>

                                <td>
                                    @if(!empty($key->borrower))
                                        {{$key->borrower->first_name}} {{$key->borrower->middle_name}} {{$key->borrower->last_name}}
                                    @endif
                                </td>
                                <td>
                                    @if($key->status=='accepted')
                                        <span class="label label-success">{{trans_choice('general.accepted',1)}}</span>
                                    @endif
                                    @if($key->status=='pending')
                                        <span class="label label-warning">{{trans_choice('general.pending',1)}}</span>
                                    @endif
                                    @if($key->status=='declined')
                                        <span class="label label-danger">{{trans_choice('general.declined',1)}}</span>
                                    @endif
                                </td>
                                <td>{{ $key->amount }}</td>
                                <td>{{ $key->accepted_amount }}</td>
                                <td>{{ $key->date }}</td>
                                <td>
                                    @if($key->status=='pending' || $key->status=='declined')
                                        <button class="btn btn-info btn-sm" data-id="{{$key->id}}"
                                                data-amount="{{$key->amount}}" data-toggle="modal"
                                                data-target="#acceptRequest">{{trans_choice('general.accept',1)}}</button>
                                        <a class="btn btn-danger btn-sm delete"
                                           href="#">{{trans_choice('general.decline',1)}}</a>
                                    @endif

                                </td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="acceptRequest">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">*</span></button>
                    <h4 class="modal-title">{{trans_choice('general.accept',1)}} {{trans_choice('general.request',1)}}</h4>
                </div>
                {!! Form::open(array('url' =>url('client/guarantor/'.$key->id.'/accept'),'method'=>'post','id'=>'')) !!}
                <div class="modal-body">
                    <div class="form-group">
                        {!! Form::label('amount',trans_choice('general.amount',1),array('class'=>'')) !!}
                        {!! Form::text('amount',null, array('class' => 'form-control touchspin', 'id'=>'accepted_amount','required'=>'')) !!}
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info">{{trans_choice('general.save',1)}}</button>
                    <button type="button" class="btn default"
                            data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
                </div>
                {!! Form::close() !!}
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <script>
        $('#acceptRequest').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            var amount = $(e.relatedTarget).data('amount');
            $(e.currentTarget).find("#accepted_amount").val(amount);
        });
        $(document).ready(function () {

        });
    </script>
@endsection
