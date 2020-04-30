@extends('layouts.master')
@section('title')
    New Invoice
@endsection
@section('current-page')New Invoice
@endsection
@section('content')
        <!-- Default box -->
<div class="card">
    <div class="header with-border">
        <h2 class="panel-title">New Invoice</h2>

        <div class="heading-elements">

        </div>
    </div>
    <div class="body">
        <div class="row">
            <div class="col-md-12">
                {!! Form::open(array('url' => url('invoice/store'), 'method' => 'post', 'name' => 'form',"enctype"=>"multipart/form-data")) !!}
                <div class="form-group">
                    {!! Form::label('Client',null,array('class'=>'')) !!}
                    {!! Form::select('client_id', $client,null, array('class' => 'form-control select2','required'=>'required')) !!}
                </div>
                <div class="form-group">
                    <div class="form-line">
                        {!! Form::label('Date',null,array('class'=>'')) !!}
                        {!! Form::text('date', date("Y-d-m"), array('class' => 'form-control date-picker','required'=>'required')) !!}
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-line">
                        {!! Form::label('Due Date',null,array('class'=>'')) !!}
                        {!! Form::text('due_date', date("Y-m-d", strtotime(date("Y-m-d") . " + ".\App\Models\Setting::where('setting_key','invoice_due_after')->first()->setting_value." day")), array('class' => 'form-control date-picker','required'=>'required')) !!}
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-line">
                        {!! Form::label('Discount',null,array('class'=>'')) !!}
                        {!! Form::text('discount', 0.00, array('class' => 'form-control touchspin')) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label("Notes",null,array('class'=>'')) !!}
                    {!! Form::textarea('notes', \App\Models\Setting::where('setting_key','invoice_terms')->first()->setting_value, array('class' => 'form-control tinymce')) !!}
                </div>
                <div class="panel-footer">
                    <button type="submit" class="btn btn-primary">{{ trans('repair.save') }}</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <!-- /.panel-body -->
</div>
<!-- /.box -->
@endsection