@extends('layouts.master')
@section('title'){{trans_choice('general.product',1)}} {{trans_choice('general.category',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.product',1)}} {{trans_choice('general.category',2)}}</h6>

            <div class="heading-elements">
                @if(Sentinel::hasAccess('stock.create'))
                    <a href="{{ url('product/category/create') }}"
                       class="btn btn-info btn-sm">{{trans_choice('general.add',1)}} {{trans_choice('general.product',1)}} {{trans_choice('general.category',1)}}</a>
                @endif
            </div>
        </div>
        <div class="panel-body">
            <table id="" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>{{ trans_choice('general.name',1) }}</th>
                    <th>{{ trans_choice('general.slug',1) }}</th>
                    <th>{{ trans_choice('general.active',1) }}</th>
                    <th>{{ trans_choice('general.note',2) }}</th>
                    <th>{{ trans_choice('general.count',2) }}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
               {!! \App\Helpers\GeneralHelper::printTableTree($tree)!!}

                </tbody>
            </table>
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')

@endsection
