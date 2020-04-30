@extends('layouts.master')
@section('title')
    {{trans_choice('general.collateral',1)}}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">{{ $collateral->name }} {{ $collateral->last_name }}</h6>

                    <div class="heading-elements">

                    </div>
                </div>
                <div class="panel-body">
                    <table class="table table-responsive table-hover">
                        <tr>
                            <td>{{trans_choice('general.loan',1)}}</td>
                            <td><a href="{{url('loan/'.$collateral->loan_id.'/show')}}"># {{ $collateral->loan_id }}</a>
                            </td>
                        </tr>
                        <tr>
                            <td>{{trans_choice('general.borrower',1)}}</td>
                            <td>
                                @if(!empty($collateral->borrower))
                                    <a href="{{url('borrower/'.$collateral->borrower_id.'/show')}}"> {{$collateral->borrower->first_name}} {{$collateral->borrower->last_name}}</a>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{trans_choice('general.type',1)}}</td>
                            <td>
                                @if(!empty($collateral->collateral_type))
                                    {{$collateral->collateral_type->name}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{trans_choice('general.value',1)}}</td>
                            <td>{{ $collateral->value }}</td>
                        </tr>
                        <tr>
                            <td>{{trans_choice('general.status',1)}}</td>
                            <td>
                                @if($collateral->status=='deposited_into_branch')
                                    {{trans_choice('general.deposited_into_branch',1)}}
                                @endif
                                @if($collateral->status=='collateral_with_borrower')
                                    {{trans_choice('general.collateral_with_borrower',1)}}
                                @endif
                                @if($collateral->status=='returned_to_borrower')
                                    {{trans_choice('general.returned_to_borrower',1)}}
                                @endif
                                @if($collateral->status=='repossession_initiated')
                                    {{trans_choice('general.repossession_initiated',1)}}
                                @endif
                                @if($collateral->status=='repossessed')
                                    {{trans_choice('general.repossessed',1)}}
                                @endif
                                @if($collateral->status=='sold')
                                    {{trans_choice('general.sold',1)}}
                                @endif
                                @if($collateral->status=='lost')
                                    {{trans_choice('general.lost',1)}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{trans_choice('general.serial_number',1)}}</td>
                            <td>{{ $collateral->serial_number }}</td>
                        </tr>
                        <tr>
                            <td>{{trans_choice('general.model_name',1)}}</td>
                            <td>{{ $collateral->model_name }}</td>
                        </tr>
                        <tr>
                            <td>{{trans_choice('general.model_number',1)}}</td>
                            <td>{{ $collateral->model_number }}</td>
                        </tr>
                        <tr>
                            <td>{{trans_choice('general.manufacture_date',1)}}</td>
                            <td>{{ $collateral->manufacture_date }}</td>
                        </tr>
                        <tr>
                            <td>{{trans_choice('general.date',1)}}</td>
                            <td>{{ $collateral->date }}</td>
                        </tr>
                        <tr>
                            <td>{{trans_choice('general.file',2)}}</td>
                            <td>
                                <ul class="" style="font-size:12px; padding-left:10px">

                                    @foreach(unserialize($collateral->files) as $key=>$value)
                                        <li><a href="{!!asset('uploads/'.$value)!!}"
                                               target="_blank">{!!  $value!!}</a></li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                        @foreach($custom_fields as $key)
                            <tr>
                                <td>
                                    @if(!empty($key->custom_field))
                                        <strong>{{$key->custom_field->name}}:</strong>
                                    @endif
                                </td>
                                <td> {{$key->name}}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td>{{ trans('general.updated_at') }}</td>
                            <td>{{ $collateral->updated_at }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">{{ trans_choice('general.note',2) }}</h6>

                    <div class="heading-elements">

                    </div>
                </div>
                <div class="panel-body">
                    @if(!empty($collateral->photo))
                        <img src="{{asset('uploads/'.$collateral->photo)}}" class="img-responsive"/><br>
                    @endif
                    {!!   $collateral->notes !!}
                </div>
            </div>
        </div>
    </div>

@endsection
@section('footer-scripts')

@endsection
