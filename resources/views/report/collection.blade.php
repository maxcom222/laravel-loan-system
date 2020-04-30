@extends('layouts.master')
@section('title'){{trans_choice('general.collection',1)}} {{trans_choice('general.report',1)}}
@endsection
@section('content')

    <p>Please note that <b>Total Loans</b> may not equal <b>Principal Loans</b> + <b>Interest Loans</b> + <b>Penalty
            Loans</b> + <b>Fees Loans</b> if you have overriden the total due amount of any loan.</p>
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">
                {{trans_choice('general.collection',1)}} {{trans_choice('general.report',1)}}
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
        <div class="col-md-2">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h6 class="panel-title">{{trans_choice('general.principal',1)}} {{trans_choice('general.due',1)}}</h6>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="panel-body">
                    {{number_format(\App\Helpers\GeneralHelper::loans_total_due_item('principal',$start_date,$end_date),2)}}
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-2">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h6 class="panel-title">{{trans_choice('general.interest',1)}} {{trans_choice('general.due',1)}}</h6>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="panel-body">
                    {{number_format(\App\Helpers\GeneralHelper::loans_total_due_item('interest',$start_date,$end_date),2)}}
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-2">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h6 class="panel-title">{{trans_choice('general.penalty',1)}} {{trans_choice('general.due',1)}}</h6>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="panel-body">
                    {{number_format(\App\Helpers\GeneralHelper::loans_total_due_item('penalty',$start_date,$end_date),2)}}
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-2">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h6 class="panel-title">{{trans_choice('general.fee',2)}} {{trans_choice('general.due',1)}}</h6>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="panel-body">
                    {{number_format(\App\Helpers\GeneralHelper::loans_total_due_item('fees',$start_date,$end_date),2)}}
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-4">
            <div class="panel panel-danger box-solid">
                <div class="panel-heading">
                    <h6 class="panel-title">{{trans_choice('general.total',1)}} {{trans_choice('general.loan',2)}} {{trans_choice('general.due',1)}}</h6>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="panel-body">
                    {{number_format(\App\Helpers\GeneralHelper::loans_total_due($start_date,$end_date),2)}}
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h6 class="panel-title">{{trans_choice('general.principal',1)}} {{trans_choice('general.paid',1)}}</h6>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="panel-body">
                    {{number_format(\App\Helpers\GeneralHelper::loans_total_paid_item('principal',$start_date,$end_date),2)}}
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-2">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h6 class="panel-title">{{trans_choice('general.interest',1)}} {{trans_choice('general.paid',1)}}</h6>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="panel-body">
                    {{number_format(\App\Helpers\GeneralHelper::loans_total_paid_item('interest',$start_date,$end_date),2)}}
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-2">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h6 class="panel-title">{{trans_choice('general.penalty',1)}} {{trans_choice('general.paid',1)}}</h6>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="panel-body">
                    {{number_format(\App\Helpers\GeneralHelper::loans_total_paid_item('penalty',$start_date,$end_date),2)}}
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-2">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h6 class="panel-title">{{trans_choice('general.fee',2)}} {{trans_choice('general.paid',1)}}</h6>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="panel-body">
                    {{number_format(\App\Helpers\GeneralHelper::loans_total_paid_item('fees',$start_date,$end_date),2)}}
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-4">
            <div class="panel panel-success box-solid">
                <div class="panel-heading">
                    <h6 class="panel-title">{{trans_choice('general.total',1)}} {{trans_choice('general.paid',1)}}</h6>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="panel-body">
                    {{number_format(\App\Helpers\GeneralHelper::loans_total_paid($start_date,$end_date),2)}}
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
                    <h6 class="panel-title"><span
                                style="color: #ff0000">{{trans_choice('general.due',1)}} {{trans_choice('general.amount',1)}}</span>
                        /
                        <span style="color: #00a65a">{{trans_choice('general.paid',1)}} {{trans_choice('general.amount',1)}}</span>
                    </h6>

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
                "title": "{{trans_choice('general.due',1)}} {{trans_choice('general.amount',1)}}",
                "type": "column",
                "valueField": "due"
            }, {
                "balloonText": "<span style='font-size:13px;'>[[title]] in [[category]]:<b> [[value]]</b> [[additional]]</span>",
                "lineAlpha": 0,
                "fillColors": "#00a65a",
                "fillAlphas": 1,
                "title": "{{trans_choice('general.paid',1)}} {{trans_choice('general.amount',1)}}",
                "type": "column",
                "valueField": "payments"
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
