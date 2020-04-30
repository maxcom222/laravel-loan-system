@extends('layouts.master')
@section('title')
    {{$product->name}}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">{{$product->name}} </h6>

                    <div class="heading-elements">

                    </div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        @if(!empty($product->featured_image))
                            <a class="fancybox" rel="group"
                               href="{{ url(asset('uploads/'.$product->featured_image)) }}"> <img
                                        src="{{ url(asset('uploads/'.$product->featured_image)) }}"
                                        class="img-responsive img-thumbnail"/></a>
                        @endif
                        <table class="table table-striped">
                            <tr>
                                <td><b>{{trans_choice('general.id',1)}}</b></td>
                                <td>{{ $product->id }}</td>
                            </tr>
                            <tr>
                                <td><b>{{trans_choice('general.code',1)}}</b></td>
                                <td>{{ $product->code }}</td>
                            </tr>
                            <tr>
                                <td><b>{{trans_choice('general.stock',1)}}</b></td>
                                <td>
                                    @if($product->stock_status==1)
                                        <span class="label label-success">{{ trans_choice('general.in_stock',1) }}</span>
                                    @else
                                        <span class="label label-danger">{{ trans_choice('general.out_of_stock',1) }}</span>
                                    @endif
                                    @if($product->enable_stock_management==1)
                                        ({{$product->qty}})
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><b>{{trans_choice('general.price',1)}}</b></td>
                                <td>
                                    @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')

                                        @if(!empty($product->sale_price))
                                            <s>{{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}{{number_format($product->regular_price,2)}}</s>
                                            <br>
                                            {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}{{number_format($product->sale_price,2)}}
                                        @else
                                            {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}{{number_format($product->regular_price,2)}}
                                        @endif
                                    @else
                                        @if(!empty($product->sale_price))
                                            <s>{{number_format($product->regular_price,2)}}{{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}</s>
                                            <br>
                                            {{number_format($product->sale_price,2)}}{{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}
                                        @else
                                            {{number_format($product->regular_price,2)}}{{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><b>{{trans_choice('general.category',2)}}</b></td>
                                <td>
                                    <?php
                                    $c = 1;
                                    ?>
                                    @foreach($product->categories as $category)
                                        @if(!empty($category->category))
                                            <a href="#">{{$category->category->name}}</a>
                                            @if(count($product->categories)!=$c)
                                                ,
                                            @endif
                                        @endif
                                        <?php
                                        $c++;
                                        ?>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <td><b>{{trans_choice('general.date',1)}}</b></td>
                                <td>{{ date_format(date_create($product->created_at),"Y-m-d") }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">{{trans_choice('general.detail',2)}} </h6>

                    <div class="heading-elements">

                    </div>
                </div>
                <div class="panel-body">
                    {!! $product->notes !!}
                    <div class="row">
                        @foreach(unserialize($product->gallery) as $key=>$value)
                            <div class="col-md-3" style="margin-bottom: 20px">
                                <a class="fancybox" rel="group"
                                   href="{{ url(asset('uploads/'.$value)) }}"> <img
                                            src="{{ url(asset('uploads/'.$value)) }}"
                                            class="img-responsive img-thumbnail"/></a>
                            </div>
                        @endforeach
                    </div>

                    <h3>{{trans_choice('general.review',2)}} </h3>
                    <div class="table-responsive">
                        <table id="data-table" class="table table-bordered table-condensed table-hover">
                            <thead>
                            <tr>
                                <th>{{trans_choice('general.name',1)}}</th>
                                <th>{{trans_choice('general.rating',1)}}</th>
                                <th>{{trans_choice('general.comment',1)}}</th>
                                <th>{{trans_choice('general.date',1)}}</th>
                                <th>{{ trans_choice('general.action',1) }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($product->reviews as $review)
                                <tr>
                                    <td>
                                        {{ $review->name }}<br>
                                        <a href="mailto:{{ $review->email }}">{{ $review->email }}</a>
                                    </td>
                                    <td>{{ $review->rating }}/5</td>
                                    <td>{!!  $review->notes !!}</td>

                                    <td>{{$review->created_at }}</td>

                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-info btn-flat dropdown-toggle"
                                                    data-toggle="dropdown" aria-expanded="false">
                                                {{ trans('general.choose') }} <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                @if(Sentinel::hasAccess('products.update'))
                                                    <li><a href="{{ url('product/review/'.$review->id.'/edit') }}"><i
                                                                    class="fa fa-edit"></i> {{ trans('general.edit') }}
                                                        </a>
                                                    </li>
                                                @endif
                                                @if(Sentinel::hasAccess('products.delete'))
                                                    <li><a href="{{ url('product/review/'.$review->id.'/delete') }}"
                                                           class="delete"><i
                                                                    class="fa fa-trash"></i> {{ trans('general.delete') }}
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
        </div>
    </div>

@endsection
@section('footer-scripts')
    <script src="{{ asset('assets/plugins/datatable/media/js/jquery.dataTables.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatable/media/js/dataTables.bootstrap.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatable/extensions/Buttons/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatable/extensions/Buttons/js/buttons.html5.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatable/extensions/Buttons/js/buttons.print.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatable/extensions/Responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatable/extensions/Buttons/js/buttons.colVis.min.js')}}"></script>
    <script>
        $('#data-table').DataTable();
    </script>
@endsection
