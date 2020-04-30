@extends('layouts.master')
@section('title')
    {{ trans('general.dashboard') }}
@endsection
@section('content')
    <div class="row">
        @if(Sentinel::hasAccess('dashboard.registered_borrowers'))
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="panel panel-body bg-blue-400 has-bg-image">
                    <div class="media no-margin">
                        <div class="media-body">
                            <h3 class="no-margin">{{ \App\Models\Borrower::count() }}</h3>
                            <span class="text-uppercase text-size-mini">{{ trans_choice('general.total',1) }} {{ trans_choice('general.borrower',2) }}</span>
                        </div>

                        <div class="media-right media-middle">
                            <i class="icon-users4 icon-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if(Sentinel::hasAccess('dashboard.total_loans_released'))
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="panel panel-body bg-indigo-400 has-bg-image">
                    <div class="media no-margin">
                        <div class="media-body">
                            @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                <h3 class="no-margin"> {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}{{ number_format(\App\Helpers\GeneralHelper::loans_total_principal(),2) }} </h3>
                            @else
                                <h3 class="no-margin"> {{ number_format(\App\Helpers\GeneralHelper::loans_total_principal(),2) }}  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</h3>
                            @endif
                            <span class="text-uppercase text-size-mini">{{ trans_choice('general.loan',2) }} {{ trans_choice('general.released',1) }}</span>
                        </div>
                        <div class="media-right media-middle">
                            <i class="icon-drawer-out icon-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if(Sentinel::hasAccess('dashboard.total_collections'))
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="panel panel-body bg-success-400 has-bg-image">
                    <div class="media no-margin">
                        <div class="media-body">
                            @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                <h3 class="no-margin"> {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}{{ number_format(\App\Helpers\GeneralHelper::loans_total_paid(),2) }} </h3>
                            @else
                                <h3 class="no-margin"> {{ number_format(\App\Helpers\GeneralHelper::loans_total_paid(),2) }}  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</h3>
                            @endif
                            <span class="text-uppercase text-size-mini">{{ trans_choice('general.payment',2) }}</span>
                        </div>
                        <div class="media-right media-middle">
                            <i class="icon-enter6 icon-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if(Sentinel::hasAccess('dashboard.loans_disbursed'))
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="panel panel-body bg-danger-400 has-bg-image">
                    <div class="media no-margin">
                        <div class="media-body">
                            @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                <h3 class="no-margin"> {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}{{ number_format(\App\Helpers\GeneralHelper::loans_total_due(),2) }} </h3>
                            @else
                                <h3 class="no-margin"> {{ number_format(\App\Helpers\GeneralHelper::loans_total_due(),2) }}  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</h3>
                            @endif
                            <span class="text-uppercase text-size-mini">{{ trans_choice('general.due',1) }} {{ trans_choice('general.amount',1) }}</span>
                        </div>
                        <div class="media-right media-middle">
                            <i class="icon-pen-minus icon-3x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="row">
        @if(Sentinel::hasAccess('dashboard.loans_disbursed'))
            <div class="col-md-4">
                <div class="panel panel-flat">
                    <div class="panel-body">

                        <canvas id="loan_status_pie" height="300"></canvas>
                        <div class="list-group no-border no-padding-top">
                            @foreach(json_decode($loan_statuses) as $key)
                                <a href="{{$key->link}}" class="list-group-item">
                                    <span class="badge bg-{{$key->class}} pull-right">{{$key->value}}</span>
                                    {{$key->label}}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-md-8">
        @if(Sentinel::hasAccess('dashboard.loans_disbursed'))
            <!-- Sales stats -->
                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h6 class="panel-title">{{ trans_choice('general.collection',1) }} {{ trans_choice('general.statistic',2) }}</h6>
                        <div class="heading-elements">
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php
                        $target = 0;
                        foreach (\App\Models\LoanSchedule::where('year', date("Y"))->where('month',
                            date("m"))->get() as $key) {
                            $target = $target + $key->principal + $key->interest + $key->fees + $key->penalty;
                        }
                        $paid_this_month = \App\Models\LoanTransaction::where('transaction_type',
                            'repayment')->where('reversed', 0)->where('year', date("Y"))->where('month',
                            date("m"))->sum('credit');
                        if ($target > 0) {
                            $percent = round(($paid_this_month / $target) * 100);
                        } else {
                            $percent = 0;
                        }

                        ?>
                        <div class="container-fluid">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="content-group">
                                        @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                            <h5 class="text-semibold no-margin">{{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}{{ number_format(\App\Models\LoanTransaction::where('transaction_type',
                    'repayment')->where('reversed', 0)->where('date',date("Y-m-d"))->sum('credit'),2) }}  </h5>
                                        @else
                                            <h5 class="text-semibold no-margin">{{ number_format(\App\Models\LoanTransaction::where('transaction_type',
                    'repayment')->where('reversed', 0)->where('date',date("Y-m-d"))->sum('credit'),2) }}  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</h5>
                                        @endif

                                        <span class="text-muted text-size-small">{{ trans_choice('general.today',1) }}</span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="content-group">
                                        @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                            <h5 class="text-semibold no-margin">{{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}{{ number_format(\App\Models\LoanTransaction::where('transaction_type',
                    'repayment')->where('reversed', 0)->whereBetween('date',array('date_sub(now(),INTERVAL 1 WEEK)','now()'))->sum('credit'),2) }} </h5>
                                        @else
                                            <h5 class="text-semibold no-margin">{{ number_format(\App\Models\LoanTransaction::where('transaction_type',
                    'repayment')->where('reversed', 0)->whereBetween('date',array('date_sub(now(),INTERVAL 1 WEEK)','now()'))->sum('credit'),2) }}  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</h5>
                                        @endif
                                        <span class="text-muted text-size-small">{{ trans_choice('general.last',1) }} {{ trans_choice('general.week',1) }}</span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="content-group">
                                        @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                            <h5 class="text-semibold no-margin">{{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}{{ number_format($paid_this_month,2) }} </h5>
                                        @else
                                            <h5 class="text-semibold no-margin">{{ number_format($paid_this_month,2) }}  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</h5>
                                        @endif
                                        <span class="text-muted text-size-small">{{ trans_choice('general.this',1) }} {{ trans_choice('general.month',1) }}</span>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-center">
                                        <h6 class="no-margin text-semibold">{{ trans_choice('general.monthly',1) }} {{ trans_choice('general.target',1) }}</h6>
                                    </div>
                                    <div class="progress" data-toggle="tooltip"
                                         title="Target:{{number_format($target,2)}}">

                                        <div class="progress-bar bg-teal progress-bar-striped active"
                                             style="width: {{$percent}}%">
                                            <span>{{$percent}}% {{ trans_choice('general.complete',1) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if(Sentinel::hasAccess('dashboard.loans_collected_monthly_graph'))
                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h6 class="panel-title">{{ trans_choice('general.monthly',1) }} {{trans_choice('general.overview',1)}}</h6>
                        <div class="heading-elements">
                            <ul class="icons-list">
                                <li><a data-action="collapse"></a></li>
                                <li><a data-action="close"></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div id="monthly_actual_expected_data" class="chart" style="height: 320px;">
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>

    <script src="{{ asset('assets/plugins/amcharts/amcharts.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/serial.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/pie.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/themes/light.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/plugins/export/export.min.js') }}"
            type="text/javascript"></script>
    <script>
        AmCharts.makeChart("monthly_actual_expected_data", {
            "type": "serial",
            "theme": "light",
            "autoMargins": true,
            "marginLeft": 30,
            "marginRight": 8,
            "marginTop": 10,
            "marginBottom": 26,
            "fontFamily": 'Open Sans',
            "color": '#888',

            "dataProvider": {!! $monthly_actual_expected_data !!},
            "valueAxes": [{
                "axisAlpha": 0,

            }],
            "startDuration": 1,
            "graphs": [{
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b> [[value]]</b> [[additional]]</span>",
                "bullet": "round",
                "bulletSize": 8,
                "lineColor": "#370fc6",
                "lineThickness": 4,
                "negativeLineColor": "#0dd102",
                "title": "{{trans_choice('general.actual',1)}}",
                "type": "smoothedLine",
                "valueField": "actual"
            }, {
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b> [[value]]</b> [[additional]]</span>",
                "bullet": "round",
                "bulletSize": 8,
                "lineColor": "#d1655d",
                "lineThickness": 4,
                "negativeLineColor": "#d1cf0d",
                "title": "{{trans_choice('general.expected',2)}}",
                "type": "smoothedLine",
                "valueField": "expected"
            }],
            "categoryField": "month",
            "categoryAxis": {
                "gridPosition": "start",
                "axisAlpha": 0,
                "tickLength": 0,
                "labelRotation": 30,

            }, "export": {
                "enabled": true,
                "libs": {
                    "path": "{{asset('assets/plugins/amcharts/plugins/export/libs')}}/"
                }
            }, "legend": {
                "position": "bottom",
                "marginRight": 100,
                "autoMargins": false
            },


        });

    </script>
    <script src="{{ asset('assets/plugins/chartjs/Chart.min.js') }}"
            type="text/javascript"></script>
    <script>
        var ctx3 = document.getElementById("loan_status_pie").getContext("2d");
        var data3 ={!! $loan_statuses !!};
        var myPieChart = new Chart(ctx3).Pie(data3, {
            segmentShowStroke: true,
            segmentStrokeColor: "#fff",
            segmentStrokeWidth: 0,
            animationSteps: 100,
            tooltipCornerRadius: 0,
            animationEasing: "easeOutBounce",
            animateRotate: true,
            animateScale: false,
            responsive: true,

            legend: {
                display: true,
                labels: {
                    fontColor: 'rgb(255, 99, 132)'
                }
            }
        });
    </script>
@endsection
