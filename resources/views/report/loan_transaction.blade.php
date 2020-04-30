@extends('layouts.master')
@section('title')
    {{trans_choice('general.loan',1)}} {{trans_choice('general.transaction',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">
                {{trans_choice('general.loan',1)}} {{trans_choice('general.transaction',2)}}
                @if(!empty($start_date))
                    for period: <b>{{$start_date}} to {{$end_date}}</b>
                @endif
            </h6>

            <div class="heading-elements">
                <button class="btn btn-sm btn-info hidden-print" onclick="window.print()">Print</button>
            </div>
        </div>
        <div class="panel-body hidden-print">
            <h4 class="">{{trans_choice('general.date',1)}} {{trans_choice('general.range',1)}}</h4>
            {!! Form::open(array('url' => Request::url(), 'method' => 'post','class'=>'form-horizontal', 'name' => 'form')) !!}
            <div class="row">
                <div class="col-xs-5">
                    {!! Form::text('start_date',null, array('class' => 'form-control date-picker', 'placeholder'=>"From Date",'required'=>'required')) !!}
                </div>
                <div class="col-xs-1  text-center" style="padding-top: 5px;">
                    to
                </div>
                <div class="col-xs-5">
                    {!! Form::text('end_date',null, array('class' => 'form-control date-picker', 'placeholder'=>"To Date",'required'=>'required')) !!}
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-2">
                        <span class="input-group-btn">
                          <button type="submit" class="btn bg-olive btn-flat">{{trans_choice('general.search',1)}}!
                          </button>
                        </span>
                        <span class="input-group-btn">
                          <a href="{{Request::url()}}"
                             class="btn bg-purple  btn-flat pull-right">{{trans_choice('general.reset',1)}}!</a>
                        </span>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}

        </div>
        <!-- /.panel-body -->

    </div>
    <!-- /.box -->
    <div class="box box-info">
        <div class="panel-body table-responsive no-padding">

            <table id="view-repayments"
                   class="table table-bordered table-condensed table-hover dataTable no-footer">
                <thead>
                <tr style="background-color: #D1F9FF" role="row">
                    <th>
                        {{trans_choice('general.collection',1)}} {{trans_choice('general.date',1)}}
                    </th>
                    <th>{{trans_choice('general.loan',1)}}#</th>
                    <th>
                        {{trans_choice('general.borrower',1)}}
                    </th>
                    <th>
                        {{trans_choice('general.collected_by',1)}}
                    </th>
                    <th>
                        {{trans_choice('general.method',1)}}
                    </th>
                    <th>
                        {{trans_choice('general.amount',1)}}
                    </th>
                    <th>
                        {{trans_choice('general.action',1)}}
                    </th>
                    <th>
                        {{trans_choice('general.receipt',1)}}
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php $amount=0; ?>
                @foreach($data as $key)
                    <?php $amount=$amount+$key->amount; ?>
                    <tr>
                        <td>{{$key->collection_date}}</td>
                        <td>{{$key->loan_id}}</td>
                        <td>
                            @if(!empty($key->borrower))
                                <a href="{{url('borrower/'.$key->borrower->id.'/show')}}"> {{$key->borrower->first_name}} {{$key->borrower->last_name}}</a>
                            @endif
                        </td>
                        <td>
                            @if(!empty($key->user))
                                {{$key->user->first_name}} {{$key->user->last_name}}
                            @endif
                        </td>
                        <td>
                            @if(!empty($key->loan_repayment_method))
                                {{$key->loan_repayment_method->name}}
                            @endif
                        </td>
                        <td>{{number_format($key->amount,2)}}</td>
                        <td>
                            <div class="btn-group-horizontal">
                                @if(Sentinel::hasAccess('repayments.update'))
                                    <a type="button" class="btn bg-white btn-xs text-bold"
                                       href="{{url('loan/'.$key->loan_id.'/repayment/'.$key->id.'/edit')}}">{{trans_choice('general.edit',1)}}</a>
                                @endif
                                @if(Sentinel::hasAccess('repayments.delete'))
                                    <a type="button"
                                       class="btn bg-white btn-xs text-bold deletePayment"
                                       href="{{url('loan/'.$key->loan_id.'/repayment/'.$key->id.'/delete')}}"
                                            >{{trans_choice('general.delete',1)}}</a>
                                @endif
                            </div>
                        </td>
                        <td>
                            <a type="button" class="btn btn-default btn-xs"
                               href="{{url('loan/'.$key->loan_id.'/repayment/'.$key->id.'/print')}}"
                               target="_blank">
                                                                <span class="glyphicon glyphicon-print"
                                                                      aria-hidden="true"></span>
                            </a>
                            <a type="button" class="btn btn-default btn-xs"
                               href="{{url('loan/'.$key->loan_id.'/repayment/'.$key->id.'/pdf')}}"
                               target="_blank">
                                                                <span class="glyphicon glyphicon-file"
                                                                      aria-hidden="true"></span>
                            </a></td>
                    </tr>
                @endforeach
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                            <b>  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }} {{number_format($amount,2)}}</b>
                        @else
                            <b>{{number_format($amount,2)}} {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</b>
                        @endif
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('footer-scripts')

@endsection
