@extends('layouts.master')
@section('title')
    {{trans_choice('general.profit_loss',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">
                {{trans_choice('general.profit_loss',1)}}
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
    <div class="row">
        <div class="col-md-4">
            <table id="profitloss" class="table table-bordered table-hover " style="background: #FFF;">
                <tbody>
                <tr style="background: #CCC;">
                    <td style="font-weight:bold">{{trans_choice('general.profit_loss',1)}} {{trans_choice('general.statement',1)}}</td>
                    <td align="right" style="font-weight:bold">{{trans_choice('general.balance',1)}}</td>
                </tr>
                <tr class="bg-green">
                    <td style="font-weight:bold">{{trans_choice('general.operating_profit',1)}} (P)</td>
                    <td style="font-weight:bold"></td>
                </tr>
                <tr>
                    <td>{{trans_choice('general.interest',1)}} {{trans_choice('general.repayment',2)}}</td>
                    <td align="right">{{round($interest_paid,2)}}</td>
                </tr>
                <tr>
                    <td>{{trans_choice('general.fee',2)}} {{trans_choice('general.repayment',2)}}</td>
                    <td align="right">{{round($fees_paid,2)}}</td>
                </tr>
                <tr>
                    <td>{{trans_choice('general.penalty',1)}} {{trans_choice('general.repayment',2)}}</td>
                    <td align="right">{{round($penalty_paid,2)}}</td>
                </tr>
                <tr>
                    <td>{{trans_choice('general.other_income',2)}}</td>
                    <td align="right">{{round($other_income,2)}}</td>
                </tr>
                <tr class="bg-red">
                    <th>{{trans_choice('general.operating_expense',2)}} (E)
                    </th>
                    <th>
                    </th>
                </tr>
                <tr>
                    <td>{{trans_choice('general.payroll',1)}}</td>
                    <td align="right">{{round($payroll,2)}}</td>
                </tr>
                <tr>
                    <td>{{trans_choice('general.expense',2)}}</td>
                    <td align="right">{{round($expenses,2)}}</td>
                </tr>
                <tr>
                    <td style="font-weight:bold">{{trans_choice('general.gross',1)}} {{trans_choice('general.profit',1)}}
                        (G) = P - E
                    </td>
                    <td style="font-weight:bold" align="right">{{round($gross_profit,2)}}</td>
                </tr>
                <tr class="bg-red">
                    <td style="font-weight:bold">{{trans_choice('general.other',1)}} {{trans_choice('general.expense',1)}}
                        (O)
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>{{trans_choice('general.saving',2)}} {{trans_choice('general.interest',1)}} *</td>
                    <td align="right">{{round($savings,2)}}</td>
                </tr>
                <tr>
                    <td>{{trans_choice('general.default',1)}} {{trans_choice('general.loan',2)}} *</td>
                    <td align="right">{{round($loan_default,2)}}</td>
                </tr>
                <tr style="background: #CCC;">
                    <td style="font-weight:bold">{{trans_choice('general.net',1)}} {{trans_choice('general.income',1)}}
                        (N) = G - O
                    </td>
                    <td style="font-weight:bold" align="right">{{round($net_profit,2)}}</td>
                </tr>
                </tbody>
            </table>
            <p><b>Default Loans *</b> is loans(principal amount - repayments made) that have been marked as default.</p>
        </div>
        <div class="col-md-8 hidden-print">
            <!-- AREA CHART -->
            <!-- LINE CHART -->
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h3 class="panel-title">{{trans_choice('general.monthly',1)}} {{trans_choice('general.net',1)}} {{trans_choice('general.income',1)}}</h3>

                    <div class="heading-elements">

                    </div>
                </div>
                <div class="panel-body">
                    <div id="netIncomeChart" style="height: 250px;">
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.box -->
            <div class="panel panel-white hidden-print">
                <div class="panel-heading">
                    <h3 class="panel-title"><span
                                style="color: #00a65a">{{trans_choice('general.operating_profit',1)}}</span>
                        / {{trans_choice('general.operating_expense',1)}}</h3>

                    <div class="heading-elements">

                    </div>
                </div>
                <div class="panel-body">
                    <div class="chart" id="operatingProfit" style="height: 350px;">
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.box -->

            <!-- LINE CHART -->
            <div class="panel panel-white hidden-print">
                <div class="panel-heading">
                    <h3 class="panel-title">{{trans_choice('general.other',1)}} {{trans_choice('general.expense',2)}}</h3>

                    <div class="heading-elements">

                    </div>
                </div>
                <div class="panel-body" style="display: block;">
                    <div class="chart" id="otherExpensesChart" style="height: 250px;">
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
@endsection
@section('footer-scripts')
    <script src="{{ asset('assets/plugins/amcharts/amcharts.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/serial.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/pie.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/amcharts/themes/light.js') }}"
            type="text/javascript"></script>

    <script>
        AmCharts.makeChart("netIncomeChart", {
            "type": "serial",
            "theme": "light",
            "autoMargins": true,
            "marginLeft": 30,
            "marginRight": 8,
            "marginTop": 10,
            "marginBottom": 26,
            "fontFamily": 'Open Sans',
            "color": '#888',

            "dataProvider": {!! $monthly_net_income_data !!},
            "valueAxes": [{
                "axisAlpha": 0,

            }],
            "startDuration": 1,
            "graphs": [{
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b> [[value]]</b> [[additional]]</span>",
                "bullet": "round",
                "bulletSize": 8,
                "lineColor": "#637bb6",
                "lineThickness": 4,
                "negativeLineColor": "#d1655d",
                "title": "{{trans_choice('general.net',1)}} {{trans_choice('general.income',1)}}",
                "type": "smoothedLine",
                "valueField": "amount"
            }],
            "categoryField": "month",
            "categoryAxis": {
                "gridPosition": "start",
                "axisAlpha": 0,
                "tickLength": 0,
                "labelRotation": 30,

            }


        });
        AmCharts.makeChart("operatingProfit", {
            "type": "serial",
            "theme": "light",
            "autoMargins": true,
            "marginLeft": 30,
            "marginRight": 8,
            "marginTop": 10,
            "marginBottom": 26,
            "fontFamily": 'Open Sans',
            "color": '#888',

            "dataProvider": {!! $monthly_operating_profit_expenses_data !!},
            "valueAxes": [{
                "axisAlpha": 0,

            }],
            "startDuration": 1,
            "graphs": [{
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b> [[value]]</b> [[additional]]</span>",
                "lineAlpha": 0,
                "fillColors": "#00a65a",
                "fillAlphas": 1,
                "title": "{{trans_choice('general.profit',1)}}",
                "type": "column",
                "valueField": "profit"
            }, {
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b> [[value]]</b> [[additional]]</span>",
                "lineAlpha": 0,
                "fillColors": "#000",
                "fillAlphas": 1,
                "title": "{{trans_choice('general.expense',2)}}",
                "type": "column",
                "valueField": "expenses"
            }],
            "categoryField": "month",
            "categoryAxis": {
                "gridPosition": "start",
                "axisAlpha": 0,
                "tickLength": 0,
                "labelRotation": 30,

            }


        }).addLegend(new AmCharts.AmLegend());
        AmCharts.makeChart("otherExpensesChart", {
            "type": "serial",
            "theme": "light",
            "autoMargins": true,
            "marginLeft": 30,
            "marginRight": 8,
            "marginTop": 10,
            "marginBottom": 26,
            "fontFamily": 'Open Sans',
            "color": '#888',

            "dataProvider": {!! $monthly_other_expenses_data !!},
            "valueAxes": [{
                "axisAlpha": 0,

            }],
            "startDuration": 1,
            "graphs": [{
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b> [[value]]</b> [[additional]]</span>",
                "bullet": "round",
                "bulletSize": 8,
                "lineColor": "#d1655d",
                "lineThickness": 4,
                "negativeLineColor": "#637bb6",
                "title": "{{trans_choice('general.other',1)}} {{trans_choice('general.expense',2)}}",
                "type": "smoothedLine",
                "valueField": "expenses"
            }],
            "categoryField": "month",
            "categoryAxis": {
                "gridPosition": "start",
                "axisAlpha": 0,
                "tickLength": 0,
                "labelRotation": 30,

            }


        });

    </script>

@endsection
