@extends('layouts.master')
@section('title')
    {{ trans_choice('general.add',1) }} {{ trans_choice('general.other_income',1) }}
@endsection

@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{ trans_choice('general.add',1) }} {{ trans_choice('general.other_income',1) }}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('other_income/store'), 'method' => 'post','class'=>'', 'name' => 'form',"enctype"=>"multipart/form-data")) !!}
        <div class="panel-body">

            <div class="form-group">
                {!! Form::label('other_income_type_id',trans_choice('general.income',1).' '.trans_choice('general.type',1),array('class'=>' control-label')) !!}

                {!! Form::select('other_income_type_id',$types,null, array('class' => 'form-control','required'=>'required')) !!}

            </div>
            <div class="form-group">
                {!! Form::label('amount',trans_choice('general.income',1).' '.trans_choice('general.amount',1),array('class'=>'')) !!}
                {!! Form::text('amount',null, array('class' => 'form-control touchspin', 'placeholder'=>"",'required'=>'required')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('account_id',trans_choice('general.account',1).' '.trans_choice('general.to',1),array('class'=>' control-label')) !!}
                {!! Form::select('account_id',$chart_assets,null, array('class' => 'form-control select2','placeholder'=>"",'required'=>'required')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('date',trans_choice('general.date',1),array('class'=>'')) !!}
                {!! Form::text('date',null, array('class' => 'form-control date-picker', 'placeholder'=>"",'required'=>'required')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('notes',trans_choice('general.description',1),array('class'=>'')) !!}
                {!! Form::textarea('notes',null, array('class' => 'form-control','rows'=>'3')) !!}
            </div>
            <div class="form-group">
                {!! Form::label('files',trans_choice('general.file',2).'('.trans_choice('general.borrower_file_types',1).')',array('class'=>'')) !!}
                {!! Form::file('files[]', array('class' => 'form-control', 'multiple'=>"",'rows'=>'3')) !!}

            </div>
            <p class="bg-navy disabled color-palette">{{trans_choice('general.custom_field',2)}}</p>
            @foreach($custom_fields as $key)

                <div class="form-group">
                    {!! Form::label($key->id,$key->name,array('class'=>'')) !!}
                    @if($key->field_type=="number")
                        <input type="number" class="form-control" name="{{$key->id}}"
                               @if($key->required==1) required @endif>
                    @endif
                    @if($key->field_type=="textfield")
                        <input type="text" class="form-control" name="{{$key->id}}"
                               @if($key->required==1) required @endif>
                    @endif
                    @if($key->field_type=="date")
                        <input type="text" class="form-control date-picker" name="{{$key->id}}"
                               @if($key->required==1) required @endif>
                    @endif
                    @if($key->field_type=="textarea")
                        <textarea class="form-control" name="{{$key->id}}"
                                  @if($key->required==1) required @endif></textarea>
                    @endif
                    @if($key->field_type=="decimal")
                        <input type="text" class="form-control touchspin" name="{{$key->id}}"
                               @if($key->required==1) required @endif>
                    @endif
                </div>
            @endforeach


        </div>

        <div class="panel-footer">
            <div class="heading-elements">
                <button type="submit" class="btn btn-primary pull-right">{{ trans_choice('general.save',1) }}</button>
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

        })

    </script>
@endsection

