@extends('layouts.master')
@section('title'){{trans_choice('general.overview',1)}} {{trans_choice('general.report',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">
                {{trans_choice('general.overview',1)}} {{trans_choice('general.report',1)}}
                @if(!empty($start_date))
                    for period: <b>{{$start_date}} to {{$end_date}}</b>
                @endif
            </h6>

            <div class="heading-elements">
                <button class="btn btn-sm btn-info hidden-print"
                        onclick="window.print()">{{trans_choice('general.print',1)}}</button>
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
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title">{{trans_choice('general.total',1)}} {{trans_choice('general.check_in',1)}}</h3>
                    <!-- /.panel-tools -->
                </div>
                <!-- /.panel-header -->
                <div class="panel-body">
                    {{number_format(\App\Helpers\GeneralHelper::check_ins_total_amount($start_date,$end_date),2)}}
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <div class="col-md-4">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">{{trans_choice('general.total',1)}} {{trans_choice('general.check_out',1)}}</h3>
                    <!-- /.panel-tools -->
                </div>
                <!-- /.panel-header -->
                <div class="panel-body">
                    {{number_format(\App\Helpers\GeneralHelper::check_outs_total_amount($start_date,$end_date),2)}}
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>

        <div class="col-md-4">
            <div class="panel panel-success panel-solid">
                <div class="panel-heading">
                    <h3 class="panel-title">{{trans_choice('general.profit',1)}}</h3>
                    <!-- /.panel-tools -->
                </div>
                <!-- /.panel-header -->
                <div class="panel-body">
                    {{number_format(\App\Helpers\GeneralHelper::check_outs_total_amount($start_date,$end_date)-\App\Helpers\GeneralHelper::check_ins_total_amount($start_date,$end_date),2)}}
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>

    <h4>Monthly </h4>
    <div class="row">
        <div class="col-md-12">
            <!-- AREA CHART -->
            <!-- LINE CHART -->
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h3 class="panel-title"><span
                                style="color: #370fc6">{{trans_choice('general.check_in',1)}}</span>
                        /
                        <span style="color: #00a65a">{{trans_choice('general.check_out',1)}} </span>
                    </h3>

                    <div class="heading-elements">

                    </div>
                </div>
                <div class="panel-body" style="display: block;">
                    <div id="operatingProfit" style="height: 350px;">

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

            "dataProvider": {!! $monthly_stats !!},
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
                "title": "{{trans_choice('general.check_in',1)}} {{trans_choice('general.item',2)}}",
                "type": "smoothedLine",
                "valueField": "check_in"
            }, {
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b> [[value]]</b> [[additional]]</span>",
                "bullet": "round",
                "bulletSize": 8,
                "lineColor": "#1bd126",
                "lineThickness": 4,
                "title": "{{trans_choice('general.check_out',1)}} {{trans_choice('general.item',2)}}",
                "type": "smoothedLine",
                "valueField": "check_out"
            }],
            "categoryField": "month",
            "categoryAxis": {
                "gridPosition": "start",
                "axisAlpha": 0,
                "tickLength": 0,
                "labelRotation": 30,

            }


        }).addLegend(new AmCharts.AmLegend());
    </script>
@endsection
