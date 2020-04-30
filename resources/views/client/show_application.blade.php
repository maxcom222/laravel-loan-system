@extends('client.layout')
@section('title')
    {{ trans_choice('general.application',1) }} {{ trans_choice('general.detail',2) }}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-flat">

                <ul class="list-group no-border no-padding-top">
                    <li class="list-group-item">
                        {{trans_choice('general.branch',1)}}
                        <span class="pull-right">
                         @if(!empty($loan_application->branch))
                                {{$loan_application->branch->name}}
                            @endif
                    </span>
                    </li>
                    <li class="list-group-item">
                        {{trans_choice('general.product',1)}}
                        <span class="pull-right">
                         @if(!empty($loan_application->loan_product))
                                {{$loan_application->loan_product->name}}
                            @endif
                    </span>
                    </li>
                    <li class="list-group-item">
                        {{trans_choice('general.amount',1)}}
                        <span class="pull-right">
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
                        <span class="pull-right">
                         {!! $loan_application->created_at !!}
                    </span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-8">
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">{{trans_choice('general.note',2)}}</h6>

                    <div class="heading-elements">

                    </div>
                </div>
                <div class="panel-body ">
                    {{$loan_application->notes}}
                </div>
            </div>
        </div>

    </div>

    <script>
        $(document).ready(function () {

        });
    </script>
@endsection
