@extends('client.layout')
@section('title')
    {{ trans_choice('general.my',1) }} {{ trans_choice('general.application',2) }}
@endsection

@section('content')

    <div class="row">

        <div class="col-md-12">

            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">{{ trans_choice('general.my',1) }} {{ trans_choice('general.application',2) }}</h6>

                    <div class="heading-elements">
                        <a href="{{ url('client/application/create') }}"
                           class="btn btn-info btn-sm">{{trans_choice('general.apply',1)}} {{trans_choice('general.loan',1)}}</a>
                    </div>
                </div>
                <div class="panel-body table-responsive ">
                    <table id="data-table" class="table  table-condensed">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{trans_choice('general.branch',1)}}</th>
                            <th>{{trans_choice('general.product',1)}}</th>
                            <th>{{trans_choice('general.amount',1)}}</th>
                            <th>{{trans_choice('general.status',1)}}</th>
                            <th>{{trans_choice('general.date',1)}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $key)
                            <tr>

                                <td>{{$key->id}}</td>
                                <td>
                                    @if(!empty($key->branch))
                                        {{$key->branch->name}}
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($key->loan_product))
                                        {{$key->loan_product->name}}
                                    @else
                                        <span class="label label-danger">{{trans_choice('general.broken',1)}} <i
                                                    class="fa fa-exclamation-triangle"></i> </span>
                                    @endif
                                </td>
                                <td>{{round($key->amount,2)}}</td>
                                <td>
                                    @if($key->status=='declined')
                                        <span class="label label-danger">{{trans_choice('general.declined',1)}}</span>
                                    @endif
                                    @if($key->status=='approved')
                                        <span class="label label-success">{{trans_choice('general.approved',1)}}</span>
                                    @endif
                                    @if($key->status=='pending')
                                        <span class="label label-warning">{{trans_choice('general.pending',1)}}</span>
                                    @endif
                                </td>
                                <td>{!! $key->created_at !!}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {

        });
    </script>
@endsection
