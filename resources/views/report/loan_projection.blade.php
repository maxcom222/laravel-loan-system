@extends('layouts.master')
@section('title'){{trans_choice('general.loan',1)}} {{trans_choice('general.projection',1)}}
@endsection
@section('content')


    <h4>{{trans_choice('general.loan',1)}} {{trans_choice('general.projection',1)}} </h4>
    <div class="row">
        <div class="col-md-12">
            <!-- AREA CHART -->
            <!-- LINE CHART -->
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">{{trans_choice('general.loan',1)}} {{trans_choice('general.projection',1)}}</h6>

                    <div class="heading-elements">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="panel-body" style="display: block;">
                    <div id="operatingProfit" style="height: 450px;">

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

            "dataProvider": {!! $monthly_collections !!},
            "valueAxes": [{
                "axisAlpha": 0,

            }],
            "startDuration": 1,
            "graphs": [{
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b> [[value]]</b> [[additional]]</span>",
                "lineAlpha": 0,
                "fillColors": "#ff0000",
                "fillAlphas": 1,
                "title": "{{trans_choice('general.expected',1)}} {{trans_choice('general.amount',1)}}",
                "type": "column",
                "valueField": "due"
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
