@extends('client.layout')
@section('title')
    {{ trans_choice('general.profile',1) }}
@endsection

@section('content')
    <div class="acount-sec">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="panel panel-flat">

                            <ul class="list-group no-border no-padding-top">
                                <li class="list-group-item active text-center">
                                    Basic Details
                                </li>
                                <li class="list-group-item">
                                    {{trans_choice('general.name',1)}}<span class="pull-right">{{$borrower->title}}
                                        . {{$borrower->first_name}} {{$borrower->last_name}}</span>
                                </li>
                                <li class="list-group-item">
                                    {{trans_choice('general.phone',1)}}<span
                                            class="pull-right">{{$borrower->mobile}}</span>
                                </li>
                                <li class="list-group-item">
                                    {{trans_choice('general.email',1)}}<span
                                            class="pull-right">{{$borrower->email}}</span>
                                </li>
                                <li class="list-group-item">
                                    {{trans_choice('general.address',1)}}<span
                                            class="pull-right">{{$borrower->address}}</span>
                                </li>
                                <li class="list-group-item active text-center">
                                    Financial Position
                                </li>
                                <li class="list-group-item">
                                    Total Loan Value<span
                                            class="label label-success pull-right">{{round(\App\Helpers\GeneralHelper::borrower_loans_total_due($borrower->id),2)}}</span>
                                </li>
                                <li class="list-group-item">
                                    Total Paid<span
                                            class="label label-info pull-right">{{round(\App\Helpers\GeneralHelper::borrower_loans_total_paid($borrower->id),2)}}</span>
                                </li>
                                <li class="list-group-item">
                                    Balance<span
                                            class="label label-danger pull-right">{{round(\App\Helpers\GeneralHelper::borrower_loans_total_due($borrower->id),2)-round(\App\Helpers\GeneralHelper::borrower_loans_total_paid($borrower->id),2)}}</span>
                                </li>
                            </ul>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="panel panel-white">
                        <div class="panel-heading">
                            <h6 class="panel-title">Change Password</h6>

                            <div class="heading-elements">

                            </div>
                        </div>

                        <div class="panel-body">
                            {!! Form::open(array('url' => url('client_profile'), 'method' => 'post', 'name' => 'form','class'=>'')) !!}
                            <div class="row">

                                <div class="col-md-6 feild">
                                    <div class="form-group">
                                        {!! Form::password('password', array('class' => 'form-control', 'placeholder'=>'Password','required'=>'required')) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6 feild">
                                    <div class="form-group">
                                        {!! Form::password('repeatpassword', array('class' => 'form-control', 'placeholder'=>'Repeat Password','required'=>'required')) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6 feild">
                                    <div class="form-group">
                                        <button type="submit"
                                                class="btn btn-info">{{trans_choice('general.save',1)}}</button>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {

        });
    </script>
@endsection
