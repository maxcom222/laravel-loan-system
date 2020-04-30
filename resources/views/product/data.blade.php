@extends('layouts.master')
@section('title')
    {{trans_choice('general.product',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.product',2)}} </h6>

            <div class="heading-elements">
                @if(Sentinel::hasAccess('stock.create'))
                    <a href="{{ url('product/create') }}"
                       class="btn btn-info btn-sm">{{trans_choice('general.add',1)}} {{trans_choice('general.product',1)}} </a>
                @endif
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="panel panel-body bg-blue-400 has-bg-image">
                        <div class="media no-margin">
                            <div class="media-body">
                                <h3 class="no-margin">{{ \App\Models\Product::sum('qty') }}</h3>
                                <span class="text-uppercase text-size-mini">{{ trans_choice('general.total',1) }} {{ trans_choice('general.item',2) }}</span>
                            </div>

                            <div class="media-right media-middle">
                                <i class="icon-cube3 icon-3x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix visible-sm-block"></div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="panel panel-body bg-green-400 has-bg-image">
                        <div class="media no-margin">
                            <div class="media-body">
                                @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                    <h3 class="no-margin"> {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}{{ number_format(\App\Helpers\GeneralHelper::stock_total_cost_amount(),2) }} </h3>
                                @else
                                    <h3 class="no-margin"> {{ number_format(\App\Helpers\GeneralHelper::stock_total_cost_amount(),2) }}  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</h3>
                                @endif
                                <span class="text-uppercase text-size-mini">{{ trans_choice('general.total',1) }} {{ trans_choice('general.cost',1) }} {{ trans_choice('general.value',1) }}</span>
                            </div>
                            <div class="media-right media-middle">
                                <i class="icon-files-empty icon-3x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="panel panel-body bg-green-400 has-bg-image">
                        <div class="media no-margin">
                            <div class="media-body">
                                @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                    <h3 class="no-margin"> {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}{{ number_format(\App\Helpers\GeneralHelper::stock_total_selling_amount(),2) }} </h3>
                                @else
                                    <h3 class="no-margin"> {{ number_format(\App\Helpers\GeneralHelper::stock_total_selling_amount(),2) }}  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</h3>
                                @endif
                                <span class="text-uppercase text-size-mini">{{ trans_choice('general.total',1) }} {{ trans_choice('general.selling',1) }} {{ trans_choice('general.value',1) }}</span>
                            </div>
                            <div class="media-right media-middle">
                                <i class="icon-coin-dollar icon-3x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="panel panel-body bg-blue-400 has-bg-image">
                        <div class="media no-margin">
                            <div class="media-body">
                                @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                    <h3 class="no-margin"> {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}{{ number_format(\App\Helpers\GeneralHelper::stock_total_selling_amount()-\App\Helpers\GeneralHelper::stock_total_cost_amount(),2) }} </h3>
                                @else
                                    <h3 class="no-margin"> {{ number_format(\App\Helpers\GeneralHelper::stock_total_selling_amount()-\App\Helpers\GeneralHelper::stock_total_cost_amount(),2) }}  {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}</h3>
                                @endif
                                <span class="text-uppercase text-size-mini">{{ trans_choice('general.expected',1) }} {{ trans_choice('general.profit',2) }}</span>
                            </div>
                            <div class="media-right media-middle">
                                <i class="icon-wallet icon-3x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div class="table-responsive">
                <table id="data-table" class="table table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>{{trans_choice('general.id',1)}}</th>
                        <th><i class="fa fa-picture-o"></i></th>
                        <th>{{trans_choice('general.name',1)}}</th>
                        <th>{{trans_choice('general.code',1)}}</th>
                        <th>{{trans_choice('general.qty',1)}}</th>
                        <th>{{trans_choice('general.alert',1)}} {{trans_choice('general.on',1)}}</th>
                        <th>{{trans_choice('general.cost_price',1)}}</th>
                        <th>{{trans_choice('general.selling_price',1)}}</th>
                        <th>{{trans_choice('general.category',2)}}</th>
                        <th>{{ trans_choice('general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>{{ $key->id }}</td>
                            <td>
                                @if(!empty($key->picture))
                                    <a class="fancybox" rel="group"
                                       href="{{ url(asset('uploads/'.$key->picture)) }}"> <img
                                                src="{{ url(asset('uploads/'.$key->picture)) }}"
                                                style="max-width: 40px!important;"/></a>
                                @endif
                            </td>
                            <td>{{ $key->name }}</td>
                            <td>{{ $key->code }}</td>
                            <td>{{ $key->qty }}</td>
                            <td>{{ $key->alert_qty }}</td>
                            <td>
                                @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                    {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}{{number_format($key->cost_price,2)}}
                                @else
                                    {{number_format($key->cost_price,2)}}{{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}
                                @endif
                            </td>
                            <td>
                                @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                    {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}{{number_format($key->selling_price,2)}}
                                @else
                                    {{number_format($key->selling_price,2)}}{{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}
                                @endif
                            </td>
                            <td>
                                <?php
                                $c = 1;
                                ?>
                                @foreach($key->categories as $category)
                                    @if(!empty($category->category))
                                        <a href="#">{{$category->category->name}}</a>
                                        @if(count($key->categories)!=$c)
                                            ,
                                        @endif
                                    @endif
                                    <?php
                                    $c++;
                                    ?>
                                @endforeach
                            </td>
                            <td>
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu9"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            @if(Sentinel::hasAccess('stock.view'))
                                                <li><a href="{{ url('product/'.$key->id.'/show') }}"><i
                                                                class="fa fa-search"></i> {{ trans_choice('general.detail',1) }}
                                                    </a>
                                                </li>
                                            @endif
                                            @if(Sentinel::hasAccess('stock.update'))
                                                <li><a href="{{ url('product/'.$key->id.'/edit') }}"><i
                                                                class="fa fa-edit"></i> {{ trans('general.edit') }} </a>
                                                </li>
                                            @endif
                                            @if(Sentinel::hasAccess('stock.delete'))
                                                <li><a href="{{ url('product/'.$key->id.'/delete') }}"
                                                       class="delete"><i
                                                                class="fa fa-trash"></i> {{ trans('general.delete') }}
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')
    <script>
        $('#data-table').DataTable({
            "order": [[0, "asc"]],
            "columnDefs": [
                {"orderable": false, "targets": [9]}
            ],
            "language": {
                "lengthMenu": "{{ trans('general.lengthMenu') }}",
                "zeroRecords": "{{ trans('general.zeroRecords') }}",
                "info": "{{ trans('general.info') }}",
                "infoEmpty": "{{ trans('general.infoEmpty') }}",
                "search": "{{ trans('general.search') }}",
                "infoFiltered": "{{ trans('general.infoFiltered') }}",
                "paginate": {
                    "first": "{{ trans('general.first') }}",
                    "last": "{{ trans('general.last') }}",
                    "next": "{{ trans('general.next') }}",
                    "previous": "{{ trans('general.previous') }}"
                }
            },
            responsive: false
        });
    </script>
@endsection
