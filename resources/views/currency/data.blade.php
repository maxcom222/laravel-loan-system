@extends('layouts.master')
@section('title'){{trans_choice('general.currency',2)}}
@endsection
@section('content')
    <div class="box">
        <div class="panel-heading">
            <h6 class="panel-title"> {{trans_choice('general.currency',2)}}</h6>

            <div class="heading-elements">
                <a href="{{ url('currency/create') }}"
                   class="btn btn-info btn-sm">{{trans_choice('general.add',1)}} {{trans_choice('general.currency',1)}}</a>
            </div>
        </div>
        <div class="panel-body">
            <table id="" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>{{ trans_choice('general.name',1) }}</th>
                    <th>{{ trans_choice('general.code',1) }}</th>
                    <th>{{ trans_choice('general.symbol',1) }}</th>
                    <th>{{ trans_choice('general.rate',1) }}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>{{ $key->name }}</td>
                        <td>{{ $key->code }}</td>
                        <td>{{ $key->symbol }}</td>
                        <td>
                            @if(\App\Models\Setting::where('setting_key','default_company_currency')->first()->setting_value==$key->id)
                                <span class="label label-success">{{ trans_choice('general.default',1) }}</span>
                            @else
                                {{ $key->rate }}
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-info btn-xs dropdown-toggle"
                                        data-toggle="dropdown" aria-expanded="false">
                                    {{ trans('general.choose') }} <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="{{ url('currency/'.$key->id.'/edit') }}"><i
                                                    class="fa fa-edit"></i> {{ trans('general.edit') }} </a></li>
                                    <li><a href="{{ url('currency/'.$key->id.'/delete') }}"
                                           class="delete"><i
                                                    class="fa fa-trash"></i> {{ trans('general.delete') }} </a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')

@endsection
