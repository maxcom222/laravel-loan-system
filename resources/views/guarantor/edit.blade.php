@extends('layouts.master')
@section('title')
    {{trans_choice('general.edit',1)}} {{trans_choice('general.guarantor',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.edit',1)}} {{trans_choice('general.guarantor',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('guarantor/'.$guarantor->id.'/update'), 'method' => 'post', 'name' => 'form',"enctype"=>"multipart/form-data")) !!}
        <div class="panel-body">
            <div class="col-md-12">
                <p class="bg-navy disabled color-palette">{{trans_choice('general.required',1)}} {{trans_choice('general.field',2)}}</p>

                <div class="form-group">
                    {!! Form::label('first_name',trans_choice('general.first_name',1),array('class'=>'')) !!}
                    {!! Form::text('first_name',$guarantor->first_name, array('class' => 'form-control', 'placeholder'=>trans_choice('general.first_name',1),'required'=>'required')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('last_name',trans_choice('general.last_name',1),array('class'=>'')) !!}
                    {!! Form::text('last_name',$guarantor->last_name, array('class' => 'form-control', 'placeholder'=>trans_choice('general.last_name',1),'required'=>'required')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('gender',trans_choice('general.gender',1),array('class'=>'')) !!}
                    {!! Form::select('gender',array('Male'=>trans_choice('general.Male',1),'Female'=>trans_choice('general.Female',1)),$guarantor->gender, array('class' => 'form-control','required'=>'required')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('country',trans_choice('general.country',1),array('class'=>'')) !!}
                    {!! Form::select('country_id',$countries,$guarantor->country_id,array('class'=>'form-control select2','placeholder'=>'','required'=>'required')) !!}
                </div>
                <p class="bg-navy disabled color-palette">{{trans_choice('general.optional',1)}} {{trans_choice('general.field',2)}}</p>

                <div class="form-group">
                    {!! Form::label('title',trans_choice('general.title',1),array('class'=>'')) !!}
                    {!! Form::select('title',array('Mr'=>trans_choice('general.Mr',1),'Mrs'=>trans_choice('general.Mrs',1), 'Miss'=>trans_choice('general.Miss',1),'Ms'=>trans_choice('general.Ms',1),'Dr'=>trans_choice('general.Dr',1),'Prof'=>trans_choice('general.Prof',1),'Rev'=>trans_choice('general.Rev',1)),$guarantor->title, array('class' => 'form-control',)) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('mobile',trans_choice('general.mobile',1),array('class'=>'')) !!}
                    {!! Form::text('mobile',$guarantor->mobile, array('class' => 'form-control', 'placeholder'=>trans_choice('general.numbers_only',1))) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('email',trans_choice('general.email',1),array('class'=>'')) !!}
                    {!! Form::text('email',$guarantor->email, array('class' => 'form-control', 'placeholder'=>trans_choice('general.email',1))) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('unique_number',trans_choice('general.unique_number',1),array('class'=>'')) !!}
                    {!! Form::text('unique_number',$guarantor->unique_number, array('class' => 'form-control', 'placeholder'=>trans_choice('general.unique_number',1))) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('dob',trans_choice('general.dob',1),array('class'=>'')) !!}
                    {!! Form::text('dob',$guarantor->dob, array('class' => 'form-control date-picker', 'placeholder'=>trans_choice('general.dob',1))) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('address',trans_choice('general.address',1),array('class'=>'')) !!}
                    {!! Form::text('address',$guarantor->address, array('class' => 'form-control', 'placeholder'=>"")) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('city',trans_choice('general.city',1),array('class'=>'')) !!}
                    {!! Form::text('city',$guarantor->city, array('class' => 'form-control', 'placeholder'=>"")) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('state',trans_choice('general.state',1),array('class'=>'')) !!}
                    {!! Form::text('state',$guarantor->state, array('class' => 'form-control', 'placeholder'=>"")) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('zip',trans_choice('general.zip',1),array('class'=>'')) !!}
                    {!! Form::text('zip',$guarantor->zip, array('class' => 'form-control', 'placeholder'=>"")) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('phone',trans_choice('general.phone',1),array('class'=>'')) !!}
                    {!! Form::text('phone',$guarantor->phone, array('class' => 'form-control', 'placeholder'=>"")) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('business_name',trans_choice('general.business',1),array('class'=>'')) !!}
                    {!! Form::text('business_name',$guarantor->business_name, array('class' => 'form-control', 'placeholder'=>"")) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('working_status',trans_choice('general.working_status',1),array('class'=>'')) !!}
                    {!! Form::select('working_status',array('Employee'=>trans_choice('general.Employee',1),'Owner'=>trans_choice('general.Owner',1),'Student'=>trans_choice('general.Student',1),'Overseas Worker'=>trans_choice('general.Overseas Worker',1),'Pensioner'=>trans_choice('general.Pensioner',1),'Unemployed'=>trans_choice('general.Unemployed',1)),$guarantor->working_status, array('class' => 'form-control')) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('photo',trans_choice('general.photo',1),array('class'=>'')) !!}
                    {!! Form::file('photo', array('class' => 'form-control', 'placeholder'=>"")) !!}
                    @if(!empty($guarantor->photo))
                        <a class="fancybox" rel="group" href="{{ url(asset('uploads/'.$guarantor->photo)) }}"> <img
                                    src="{{ url(asset('uploads/'.$guarantor->photo)) }}" width="120"/></a>
                    @endif
                </div>
                <div class="form-group">
                    {!! Form::label('notes',trans_choice('general.description',1),array('class'=>'')) !!}
                    {!! Form::textarea('notes',$guarantor->notes, array('class' => 'form-control', 'placeholder'=>"")) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('files',trans_choice('general.file',2). ' '.trans_choice('general.borrower_file_types',2),array('class'=>'')) !!}
                    {!! Form::file('files[]', array('class' => 'form-control', 'multiple'=>"")) !!}
                    <div class="col-sm-12">
                        {{trans_choice('general.select_thirty_files',2)}}<br>
                        @foreach(unserialize($guarantor->files) as $key=>$value)
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
                <div class="form-group">
                    <hr>
                </div>

                <p class="bg-navy disabled color-palette">{{trans_choice('general.custom_field',2)}}</p>
                @foreach($custom_fields as $key)

                    <div class="form-group">
                        {!! Form::label($key->id,$key->name,array('class'=>'')) !!}
                        @if($key->field_type=="number")
                            <input type="number" class="form-control" name="{{$key->id}}"
                                   @if($key->required==1) required
                                   @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$guarantor->id)->where('category','borrowers')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$guarantor->id)->where('category','borrowers')->first()->name}} @endif">
                        @endif
                        @if($key->field_type=="textfield")
                            <input type="text" class="form-control" name="{{$key->id}}"
                                   @if($key->required==1) required
                                   @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$guarantor->id)->where('category','borrowers')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$guarantor->id)->where('category','borrowers')->first()->name}} @endif">
                        @endif
                        @if($key->field_type=="date")
                            <input type="text" class="form-control date-picker" name="{{$key->id}}"
                                   @if($key->required==1) required
                                   @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$guarantor->id)->where('category','borrowers')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$guarantor->id)->where('category','borrowers')->first()->name}} @endif">
                        @endif
                        @if($key->field_type=="textarea")
                            <textarea class="form-control" name="{{$key->id}}"
                                      @if($key->required==1) required @endif>@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$guarantor->id)->where('category','borrowers')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$guarantor->id)->where('category','borrowers')->first()->name}} @endif</textarea>
                        @endif
                        @if($key->field_type=="decimal")
                            <input type="text" class="form-control touchspin" name="{{$key->id}}"
                                   @if($key->required==1) required
                                   @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$guarantor->id)->where('category','borrowers')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$guarantor->id)->where('category','borrowers')->first()->name}} @endif">
                        @endif

                    </div>
                @endforeach
                <p style="text-align:center; font-weight:bold;">
                    <small><a href="{{url('custom_field/create')}}" target="_blank">Click here to add custom fields
                            on this page</a></small>
                </p>

            </div>
        </div>
        <!-- /.panel-body -->
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',2)}}</button>
        </div>
        {!! Form::close() !!}
    </div>
    <!-- /.box -->
@endsection

@section('footer-scripts')
    <script>
        function delete_file(e) {
            var id = e.id;
            swal({
                title: 'Are you sure?',
                text: '',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ok',
                cancelButtonText: 'Cancel'
            }).then(function () {
                $.ajax({
                    type: 'GET',
                    url: "{!!  url('borrower/'.$guarantor->id) !!}/delete_file?id=" + id,
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