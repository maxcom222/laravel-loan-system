@extends('layouts.master')
@section('title')
    {{trans_choice('general.loan',1)}}  {{trans_choice('general.classification',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">
                {{trans_choice('general.loan',1)}}  {{trans_choice('general.classification',1)}}
                @if(!empty($start_date))
                    for period: <b>{{$start_date}} to {{$end_date}}</b>
                @endif
            </h6>
            <h4>
                @if(!empty($status))
                    Status: <b>{{$status}}</b>
                @endif
            </h4>

            <div class="heading-elements">
                <button class="btn btn-sm btn-info hidden-print" onclick="window.print()">Print</button>
            </div>
        </div>
        <div class="panel-body hidden-print hidden">
            <h4 class="">{{trans_choice('general.date',1)}} {{trans_choice('general.range',1)}}</h4>
            {!! Form::open(array('url' => Request::url(), 'method' => 'post','class'=>'form-horizontal', 'name' => 'form')) !!}
            <div class="row">
                <div class="col-xs-4">
                    {!! Form::text('start_date',null, array('class' => 'form-control date-picker', 'placeholder'=>"From Date",'required'=>'required')) !!}
                </div>
                <div class="col-xs-4">
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
            <table id="data-table" class="table table-bordered table-striped table-condensed table-hover">
                <thead>
                <tr style="background-color: #D1F9FF">
                    <th>{{trans_choice('general.borrower',1)}}</th>
                    <th>#</th>
                    <th>{{trans_choice('general.principal',1)}}</th>
                    <th>{{trans_choice('general.classification',1)}}</th>
                    <th>{{trans_choice('general.arrears',1)}}</th>
                    <th>{{trans_choice('general.provision',1)}}%</th>
                    <th>{{trans_choice('general.provided',1)}} {{trans_choice('general.amount',1)}}</th>
                    <th>{{trans_choice('general.balance',1)}}</th>
                    <th>{{trans_choice('general.day',2)}}</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $principal = 0;
                $balance = 0;
                $due = 0;
                $paid = 0;
                $provided_amount = 0;
                $arrears = 0;
                ?>
                @foreach($data as $key)
                    <?php
                    $principal = $principal + $key->principal;
                    $arrears = $arrears + $key->principal + \App\Helpers\GeneralHelper::loan_total_interest($key->id);
                    $balance = $balance + \App\Helpers\GeneralHelper::loan_total_balance($key->id);
                    $paid = $paid + \App\Helpers\GeneralHelper::loan_total_paid($key->id);
                    if ($key->maturity_date > date("Y-m-d")) {
                        $classification = "Current";
                        $provision_rate = \App\Models\ProvisionRate::find(1)->rate;
                        $provision = $provision_rate * \App\Helpers\GeneralHelper::loan_total_balance($key->id) / 100;
                        $provided_amount = $provided_amount + $provision;
                        $days = 0;
                    } else {
                        $days = date_diff(date_create($key->maturity_date), date_create(date("Y-m-d")))->days;
                        if ($days > 30 && $days < 61) {
                            $classification = "Especially Mentioned";
                            $provision_rate = \App\Models\ProvisionRate::find(2)->rate;
                            $provision = $provision_rate * \App\Helpers\GeneralHelper::loan_total_balance($key->id) / 100;
                            $provided_amount = $provided_amount + $provision;
                        } elseif ($days > 60 && $days < 91) {
                            $classification = "Substandard";
                            $provision_rate = \App\Models\ProvisionRate::find(3)->rate;
                            $provision = $provision_rate * \App\Helpers\GeneralHelper::loan_total_balance($key->id) / 100;
                            $provided_amount = $provided_amount + $provision;
                        } elseif ($days > 90 && $days < 181) {
                            $classification = "Doubtful";
                            $provision_rate = \App\Models\ProvisionRate::find(4)->rate;
                            $provision = $provision_rate * \App\Helpers\GeneralHelper::loan_total_balance($key->id) / 100;
                            $provided_amount = $provided_amount + $provision;
                        } elseif ($days > 180) {
                            $classification = "Loss";
                            $provision_rate = \App\Models\ProvisionRate::find(5)->rate;
                            $provision = $provision_rate * \App\Helpers\GeneralHelper::loan_total_balance($key->id) / 100;
                            $provided_amount = $provided_amount + $provision;
                        }
                    }
                    ?>
                    <tr>
                        <td>
                            @if(!empty($key->borrower))
                                <a href="{{url('borrower/'.$key->borrower_id.'/show')}}">{{$key->borrower->first_name}} {{$key->borrower->last_name}}</a>
                            @else
                                <span class="label label-danger">{{trans_choice('general.broken',1)}} <i
                                            class="fa fa-exclamation-triangle"></i> </span>
                            @endif
                            {{ $key->name }}
                        </td>
                        <td>{{$key->id}}</td>
                        <td>
                            @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }} {{number_format($key->principal,2)}}
                            @else
                                {{number_format($key->principal,2)}} {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}
                            @endif

                        </td>
                        <td>{{$classification}}</td>
                        <td>
                            @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }} {{$key->principal + \App\Helpers\GeneralHelper::loan_total_interest($key->id)}}
                            @else
                                {{$key->principal + \App\Helpers\GeneralHelper::loan_total_interest($key->id)}} {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}
                            @endif

                        </td>
                        <td>{{$provision_rate}}</td>
                        <td>
                            @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }} {{number_format($provision,2)}}
                            @else
                                {{number_format($provision,2)}} {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}
                            @endif

                        </td>
                        <td>
                            @if($key->override==1)
                                <?php   $due = $due + $key->balance; ?>
                                <s>{{number_format(\App\Helpers\GeneralHelper::loan_total_due_amount($key->id),2)}}</s><br>
                                {{number_format($key->balance,2)}}
                            @else
                                <?php   $due = $due + \App\Helpers\GeneralHelper::loan_total_due_amount($key->id); ?>
                                {{number_format(\App\Helpers\GeneralHelper::loan_total_due_amount($key->id),2)}}
                            @endif

                        </td>
                        <td>{{$days}}</td>
                    </tr>
                @endforeach
                <tr>
                    <td></td>
                    <td></td>
                    <td>
                        @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                            <b>{{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }} {{number_format($principal,2)}}</b>
                        @else
                            <b>{{number_format($principal,2)}} {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</b>
                        @endif
                    </td>
                    <td></td>
                    <td>
                        @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                            <b>  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }} {{number_format($arrears,2)}}</b>
                        @else
                            <b>{{number_format($arrears,2)}} {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</b>
                        @endif
                    </td>
                    <td></td>
                    <td>
                        @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                            <b>  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }} {{number_format($provided_amount,2)}}</b>
                        @else
                            <b>{{number_format($provided_amount,2)}} {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</b>
                        @endif
                    </td>

                    <td>
                        @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                            <b>{{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }} {{number_format($balance,2)}}</b>
                        @else
                            <b> {{number_format($balance,2)}} {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</b>
                        @endif
                    </td>
                    <td></td>
                </tr>
                </tbody>
            </table>

        </div>
    </div>
@endsection
@section('footer-scripts')

@endsection
