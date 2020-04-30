@extends('client.layout')
@section('title')
    {{ trans_choice('general.apply',1) }}
@endsection

@section('content')

    <div class="row">

        <div class="col-md-12">

            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">{{ trans_choice('general.apply',1) }}</h6>

                    <div class="heading-elements">
                    </div>
                </div>
                {!! Form::open(array('url' => url('client/application/store'), 'method' => 'post', 'name' => 'form',"enctype"=>"multipart/form-data")) !!}
                <div class="panel-body">
                    <div class="form-group">
                        {!! Form::label('branch_id',trans_choice('general.branch',1),array('class'=>'')) !!}
                        {!! Form::select('branch_id',$branches,null, array('class' => 'form-control','placeholder'=>'','required'=>'required')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('loan_product_id',trans_choice('general.product',1),array('class'=>'')) !!}
                        {!! Form::select('loan_product_id',$products,null, array('class' => 'form-control','placeholder'=>'','required'=>'required')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('amount',trans_choice('general.amount',1),array('class'=>'')) !!}
                        {!! Form::text('amount',null, array('class' => 'form-control touchspin', 'placeholder'=>'','required'=>'required')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('notes',trans_choice('general.note',2),array('class'=>'')) !!}
                        {!! Form::textarea('notes',null, array('class' => 'form-control', 'rows'=>"3")) !!}
                    </div>
                </div>
                <div class="panel-footer">
                    <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {

        });
    </script>
@endsection
