@extends('layouts.master')
@section('title'){{trans_choice('general.edit',1)}} {{trans_choice('general.repayment',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.edit',1)}} {{trans_choice('general.repayment',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('loan/repayment/'.$loan_transaction->id.'/update'), 'method' => 'post', 'class' => 'form-horizontal')) !!}
        <div class="panel-body">
            <div class="form-group">
                {!! Form::label('amount',trans_choice('general.repayment',1).' '.trans_choice('general.amount',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::text('amount',$loan_transaction->credit, array('class' => 'form-control touchspin', 'placeholder'=>"Number or decimal only",'required'=>'required')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('repayment_method_id',trans_choice('general.repayment',1).' '.trans_choice('general.method',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::select('repayment_method_id',$repayment_methods,$loan_transaction->repayment_method_id, array('class' => ' form-control','required'=>'required','id'=>'loanProduct')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('receipt',trans_choice('general.receipt',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::text('receipt',$loan_transaction->receipt, array('class' => 'form-control', 'placeholder'=>"",''=>'required')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('collection_date',trans_choice('general.collection',1).' '.trans_choice('general.date',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-5">
                    {!! Form::text('collection_date',$loan_transaction->date, array('class' => 'form-control date-picker', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('notes',trans_choice('general.description',1),array('class'=>'col-sm-3 control-label')) !!}
                <div class="col-sm-9">
                    {!! Form::textarea('notes',$loan_transaction->notes, array('class' => 'form-control', 'rows'=>"4")) !!}
                </div>
            </div>
            <p class="bg-navy disabled color-palette">{{trans_choice('general.custom_field',2)}}</p>
            @foreach($custom_fields as $key)

                <div class="form-group">
                    {!! Form::label($key->id,$key->name,array('class'=>'')) !!}
                    @if($key->field_type=="number")
                        <input type="number" class="form-control" name="{{$key->id}}"
                               @if($key->required==1) required
                               @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first()->name}} @endif">
                    @endif
                    @if($key->field_type=="textfield")
                        <input type="text" class="form-control" name="{{$key->id}}"
                               @if($key->required==1) required
                               @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first()->name}} @endif">
                    @endif
                    @if($key->field_type=="date")
                        <input type="text" class="form-control date-picker" name="{{$key->id}}"
                               @if($key->required==1) required
                               @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first()->name}} @endif">
                    @endif
                    @if($key->field_type=="textarea")
                        <textarea class="form-control" name="{{$key->id}}"
                                  @if($key->required==1) required @endif>@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first()->name}} @endif</textarea>
                    @endif
                    @if($key->field_type=="decimal")
                        <input type="text" class="form-control touchspin" name="{{$key->id}}"
                               @if($key->required==1) required
                               @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$loan_transaction->id)->where('category','repayments')->first()->name}} @endif">
                    @endif
                </div>
            @endforeach

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

