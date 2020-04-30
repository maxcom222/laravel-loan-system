@extends('client.layout')
@section('title')
    {{trans_choice('general.add',1)}} {{trans_choice('general.guarantor',1)}}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4">
            <ul class="list-group">
                <li class="list-group-item">
                    {{trans_choice('general.product',1)}}
                    <span class="badge pull-right">
                         @if(!empty($loan_application->loan_product))
                            {{$loan_application->loan_product->name}}
                        @endif
                    </span>
                </li>
                <li class="list-group-item">
                    {{trans_choice('general.amount',1)}}
                    <span class="badge pull-right">
                        {{round($loan_application->amount,2)}}
                    </span>
                </li>
                <li class="list-group-item">
                    {{trans_choice('general.status',1)}}
                    <span class="">
                         @if($loan_application->status=='declined')
                            <span class="label label-danger pull-right">{{trans_choice('general.declined',1)}}</span>
                        @endif
                        @if($loan_application->status=='approved')
                            <span class="label label-success pull-right">{{trans_choice('general.approved',1)}}</span>
                        @endif
                        @if($loan_application->status=='pending')
                            <span class="label label-warning pull-right">{{trans_choice('general.pending',1)}}</span>
                        @endif

                    </span>
                </li>
                <li class="list-group-item">
                    {{trans_choice('general.date',1)}}
                    <span class="badge pull-right">
                         {!! $loan_application->created_at !!}
                    </span>
                </li>
            </ul>
        </div>
        <div class="col-md-8">
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h3 class="panel-title">{{trans_choice('general.add',1)}} {{trans_choice('general.guarantor',1)}}</h3>

                    <div class="heading-elements">

                    </div>
                </div>
                {!! Form::open(array('url' => url('client/application/'.$loan_application->id.'/guarantor/store'), 'method' => 'post','class'=>'', 'name' => 'form',"enctype"=>"multipart/form-data")) !!}
                <div class="panel-body">
                    @if(isset($_REQUEST['return_url']))
                        <input type="hidden" value="{{$_REQUEST['return_url']}}" name="return_url">
                    @endif
                    <div class="form-group">
                        {!! Form::label('guarantor_id',trans_choice('general.guarantor',1),array('class'=>' control-label')) !!}
                        {!! Form::select('guarantor_id',$borrowers,null, array('class' => 'form-control select2','placeholder'=>'','required'=>'required')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('amount',trans_choice('general.amount',1),array('class'=>'')) !!}
                        {!! Form::text('amount',$loan_application->amount, array('class' => 'form-control touchspin', 'id'=>"amount",'required'=>'required')) !!}
                    </div>


                </div>

                <div class="panel-footer">
                    <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
                </div>
                {!! Form::close() !!}
                        <!-- /.panel-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
@endsection
@section('footer-scripts')
    <script>

        $(document).ready(function (e) {
            if ($('#status').val() == "accepted") {
                $('#accepted_amount').val($('#amount').val());
                $('#accepted_amount').attr('required', 'required');

            }
            $('#status').change(function (e) {
                if ($('#status').val() == "accepted") {
                    $('#accepted_amount').val($('#amount').val());
                    $('#accepted_amount').attr('required', 'required');
                } else {
                    $('#accepted_amount').val("");
                    $('#accepted_amount').removeAttr('required');
                }
            })
        })

    </script>
@endsection

