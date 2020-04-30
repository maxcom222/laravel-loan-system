@extends('layouts.master')
@section('title')
    {{trans_choice('general.edit',1)}} {{trans_choice('general.expense',1)}}
@endsection

@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.edit',1)}} {{trans_choice('general.expense',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('expense/'.$expense->id.'/update'), 'method' => 'post','class'=>'', 'name' => 'form',"enctype"=>"multipart/form-data")) !!}
        <div class="panel-body">
            <div class="form-group">
                {!! Form::label('expense_type_id',trans_choice('general.expense',1).' '.trans_choice('general.type',1),array('class'=>' control-label')) !!}

                {!! Form::select('expense_type_id',$types,$expense->expense_type_id, array('class' => 'form-control','required'=>'required')) !!}

            </div>
            <div class="form-group">
                {!! Form::label('amount',trans_choice('general.expense',1).' '.trans_choice('general.amount',1),array('class'=>'')) !!}
                {!! Form::text('amount',$expense->amount, array('class' => 'form-control touchspin', 'placeholder'=>"",'required'=>'required')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('account_id',trans_choice('general.account',1).' '.trans_choice('general.from',1),array('class'=>' control-label')) !!}
                {!! Form::select('account_id',$chart_assets,$expense->account_id, array('class' => 'form-control select2','placeholder'=>"",'required'=>'required')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('date',trans_choice('general.date',1),array('class'=>'')) !!}
                {!! Form::text('date',$expense->date, array('class' => 'form-control date-picker', 'placeholder'=>"",'required'=>'required')) !!}
            </div>

            <div class="form-group">
                {!! Form::label('Recurring',trans_choice('general.is_expenses_recurring',1),array('class'=>'active')) !!}
                {!! Form::select('recurring', array('1'=>trans_choice('general.yes',1),'0'=>trans_choice('general.no',1)),$expense->recurring, array('class' => 'form-control','id'=>'recurring')) !!}
            </div>
            <div id="recur">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="form-line">
                                {!! Form::label('recur_frequency',trans_choice('general.recur_frequency',1),array('class'=>'')) !!}
                                {!! Form::number('recur_frequency',$expense->recur_frequency, array('class' => 'form-control','id'=>'recurF')) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="form-line">
                                {!! Form::label('recur_type',trans_choice('general.recur_type',1),array('class'=>'active')) !!}
                                {!! Form::select('recur_type', array('day'=>'Day(s)','week'=>'Week(s)','month'=>'Month(s)','year'=>'Year(s)'),$expense->recur_type, array('class' => 'form-control','id'=>'recurT')) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="form-line">
                                {!! Form::label('recur_start_date',trans_choice('general.recur_starts',1),array('class'=>'')) !!}
                                {!! Form::text('recur_start_date',$expense->recur_start_date, array('class' => 'form-control date-picker','id'=>'recur_start_date')) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="form-line">
                                {!! Form::label('recur_end_date',trans_choice('general.recur_ends',1),array('class'=>'')) !!}
                                {!! Form::text('recur_end_date',$expense->recur_end_date, array('class' => 'form-control date-picker','id'=>'recur_end_date')) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('notes',trans_choice('general.description',1),array('class'=>'')) !!}
                {!! Form::textarea('notes',$expense->notes, array('class' => 'form-control','rows'=>'3')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('files',trans_choice('general.file',2).'('.trans_choice('general.borrower_file_types',1).')',array('class'=>'')) !!}
                {!! Form::file('files[]', array('class' => 'form-control', 'multiple'=>"",'rows'=>'3')) !!}
                <div class="col-sm-12">
                    @foreach(unserialize($expense->files) as $key=>$value)
                        <span id="file_{{$key}}_span"><a href="{!!asset('uploads/'.$value)!!}"
                                                         target="_blank">{!!  $value!!}</a> <button value="{{$key}}"
                                                                                                    id="{{$key}}"
                                                                                                    onclick="delete_file(this)"
                                                                                                    type="button"
                                                                                                    class="btn btn-danger btn-xs">
                                <i class="fa fa-trash"></i></button> </span><br>
                    @endforeach
                </div>
            </div>
            <p class="bg-navy disabled color-palette clearfix">{{trans_choice('general.custom_field',2)}}</p>
            @foreach($custom_fields as $key)

                <div class="form-group">
                    {!! Form::label($key->id,$key->name,array('class'=>'')) !!}
                    @if($key->field_type=="number")
                        <input type="number" class="form-control" name="{{$key->id}}"
                               @if($key->required==1) required
                               @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$expense->id)->where('category','expenses')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$expense->id)->where('category','expenses')->first()->name}} @endif">
                    @endif
                    @if($key->field_type=="textfield")
                        <input type="text" class="form-control" name="{{$key->id}}"
                               @if($key->required==1) required
                               @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$expense->id)->where('category','expenses')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$expense->id)->where('category','expenses')->first()->name}} @endif">
                    @endif
                    @if($key->field_type=="date")
                        <input type="text" class="form-control date-picker" name="{{$key->id}}"
                               @if($key->required==1) required
                               @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$expense->id)->where('category','expenses')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$expense->id)->where('category','expenses')->first()->name}} @endif">
                    @endif
                    @if($key->field_type=="textarea")
                        <textarea class="form-control" name="{{$key->id}}"
                                  @if($key->required==1) required @endif>@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$expense->id)->where('category','expenses')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$expense->id)->where('category','expenses')->first()->name}} @endif</textarea>
                    @endif
                    @if($key->field_type=="decimal")
                        <input type="text" class="form-control touchspin" name="{{$key->id}}"
                               @if($key->required==1) required
                               @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$expense->id)->where('category','expenses')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$expense->id)->where('category','expenses')->first()->name}} @endif">
                    @endif
                </div>
            @endforeach

        </div>

        <div class="panel-footer">
            <div class="heading-elements">
                <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
            </div>
        </div>
    {!! Form::close() !!}
    <!-- /.panel-body -->
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')
    <script>

        $(document).ready(function (e) {
            if ($('#recurring').val() == '1') {
                $('#recur').show();
                $('#recurT').attr('required', 'required');
                $('#recur_start_date').attr('required', 'required');
                $('#recurF').attr('required', 'required');
            } else {
                $('#recur').hide();
                $('#recurT').removeAttr('required');
                $('#recur_start_date').removeAttr('required');
                $('#recurF').removeAttr('required');
            }
            $('#recurring').change(function () {
                if ($('#recurring').val() == '1') {
                    $('#recur').show();
                    $('#recurT').attr('required', 'required');
                    $('#recurF').attr('required', 'required');
                    $('#recur_start_date').attr('required', 'required');
                } else {
                    $('#recur').hide();
                    $('#recurT').removeAttr('required');
                    $('#recur_start_date').removeAttr('required');
                    $('#recurF').removeAttr('required');
                }
            })
        })
        function delete_file(e) {
            var id = e.id;
            swal({
                title: '{{trans_choice('general.are_you_sure',1)}}',
                text: '',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{trans_choice('general.ok',1)}}',
                cancelButtonText: '{{trans_choice('general.cancel',1)}}'
            }).then(function () {
                $.ajax({
                    type: 'GET',
                    url: "{!!  url('expense/'.$expense->id) !!}/delete_file?id=" + id,
                    success: function (data) {
                        $("#file_" + id + "_span").remove();
                        swal({
                            title: 'Deleted',
                            text: 'File successfully deleted',
                            type: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ok',
                            timer: 2000
                        })
                    }
                });
            })

        }
    </script>
@endsection

