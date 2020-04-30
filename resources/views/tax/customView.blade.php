@extends('layouts.master')
@section('title')
{{ trans('invoice.custom_report') }}
@endsection
@section('current-page')
{{ trans('invoice.custom_report') }}
@endsection
@section('content')
        <!-- Default box -->
<div class="box box-default">
    <div class="panel-heading">
        <h3 class="panel-title">{{ trans('invoice.custom_report') }}</h3>
        <div class="heading-elements">

        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            {!! Form::open(array('url' =>'invoice/report/custom','class'=>'',"enctype" => "multipart/form-data")) !!}
            <div class="col-md-12">
                <div class="row form-group">
                    <div class="col-md-6">
                        {!!  Form::label('Start Date:',null,array('class'=>' control-label')) !!}
                        <div class="input-icon">
                            <i class="fa fa-calendar"></i>
                            {!! Form::text('subject','',array('class'=>'form-control date-picker','required'=>'required', 'data-date-viewmode'=>"years",'data-date-end-date'=>"+0d",
                                   'name'=>"start" )) !!}

                        </div>
                    </div>
                    <div class="col-md-6">
                        {!!  Form::label('End Date:',null,array('class'=>' control-label')) !!}
                        <div class="input-icon">
                            <i class="fa fa-calendar"></i>
                            {!! Form::text('subject',date("Y-m-d"),array('class'=>'form-control date-picker','required'=>'required', 'data-date-viewmode'=>"years",'data-date-end-date'=>"+0d",
                                   'name'=>"end" )) !!}

                        </div>
                    </div>


                </div>
                <button class="btn btn-info btn-block margin-top-20" type="submit">SEARCH <i
                            class="m-icon-swapright m-icon-white"></i></button>
                <p></p>
            </div>
            {!! Form::close() !!}
        </div>
        <div class="row">
            @if(isset($invoices))
                <table id="data-table" class="table table-bordered table-stripped table-hover">
                    <thead>
                    <tr>
                        <th>{{ trans('general.id') }}</th>
                        <th>{{ trans('general.client') }}</th>
                        <th>{{ trans('invoice.repair') }}</th>
                        <th>{{ trans('general.title') }}</th>
                        <th>{{ trans('general.amount') }}</th>
                        <th>{{ trans('general.status') }}</th>
                        <th>{{ trans('invoice.emailed') }}</th>
                        <th>{{ trans('invoice.due_on') }}</th>
                        <th>{{ trans('general.date') }}</th>
                        <th>{{ trans('general.action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($invoices as $key)
                        <tr>
                            <td>{{ $key->id }}</td>
                            <td><a href="{{ url('client/'.$key->user_id.'/show') }}"
                                   title="{{ trans('general.click_to_view') }}"> {{ Sentinel::findUserById($key->user_id)->first_name }} {{ Sentinel::findUserById($key->user_id)->last_name }}</a>
                            </td>
                            <td>
                                <a href="{{ url('repair/'.$key->repair_id.'/show') }}"
                                   title="{{ trans('general.click_to_view') }}"> {{ \App\Models\Repair::where('id',$key->repair_id)->first()->name }} </a>
                            </td>
                            <td>{{ $key->title}}</td>
                            <td>
                                @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                    {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value.' '.$key->invoice_amount }}
                                @else
                                    {{ $key->invoice_amount .' '.\App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}
                                @endif
                            </td>
                            <td>
                                @if($key->status=='unpaid')
                                    <span class="label label-danger">{{ trans('invoice.unpaid') }}</span>
                                @endif
                                @if($key->status=='paid')
                                    <span class="label label-success">{{ trans('invoice.paid') }}</span>
                                @endif
                                @if($key->status=='cancelled')
                                    <span class="label label-info">{{ trans('invoice.cancelled') }}</span>
                                @endif
                                @if($key->status=='partially_paid')
                                    <span class="label label-warning">{{ trans('invoice.partially_paid') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($key->emailed==1)
                                    <span class="label label-success">{{ trans('general.yes') }}</span>
                                @else
                                    <span class="label label-danger">{{ trans('general.no') }}</span>
                                @endif
                            </td>
                            <td>{{ $key->due_on }}</td>
                            <td>{{ $key->created_at }}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info btn-flat">{{ trans('general.choose') }}...
                                    </button>
                                    <button type="button" class="btn btn-info btn-flat dropdown-toggle"
                                            data-toggle="dropdown" aria-expanded="false">
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="{{ url('invoice/'.$key->id.'/show') }}"><i class="fa fa-search"></i>
                                                {{ trans('general.view') }}</a></li>
                                        <li><a href="{{ url('invoice/'.$key->id.'/edit') }}"><i class="fa fa-edit"></i>
                                                {{ trans('general.edit') }} </a></li>
                                        @if(\App\Helpers\GeneralHelper::invoice_due($key->id)>0)
                                            <li>
                                                <a href="{{ url('invoice/'.$key->id.'/payment/create') }}"><i
                                                            class="fa fa-plus"></i>
                                                    {{ trans('invoice.add_payment') }}t</a>
                                            </li>
                                        @endif
                                        <li>
                                            <a href="{{ url('invoice/'.$key->id.'/payment/data') }}"><i
                                                        class="fa fa-money"></i>
                                                {{ trans('invoice.payment_history') }}</a>
                                        </li>
                                        <li><a href="{{ url('invoice/'.$key->id.'/email') }}"><i class="fa fa-envelope"></i>
                                                {{ trans('general.email') }} </a></li>
                                        <li><a href="{{ url('invoice/'.$key->id.'/pdf') }}"><i class="fa fa-file-pdf-o"></i>
                                                {{ trans('general.pdf') }} </a></li>
                                        <li><a href="{{ url('invoice/'.$key->id.'/delete') }}"
                                               data-toggle="confirmation"><i
                                                        class="fa fa-trash"></i> {{ trans('general.delete') }} </a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif

        </div>
    </div>
</div>
@endsection
@section('page-footer-scripts')
    <script src="{{ asset('assets/global/plugins/datatables/media/js/jquery.dataTables.min.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}"
            type="text/javascript"></script>
    <script>
        $(document).ready(function () {
            $('#data-table').DataTable({
                dom: 'T<"clear">lfrtip',
                "lengthMenu": [
                    [20, 50, 70, -1],
                    [20, 50, 70, "All"] // change per page values here
                ],
                "pageLength": 20,
                "pagingType": "bootstrap_full_number",
                "language": {
                    "search": "Search: ",
                    "lengthMenu": "  _MENU_ records",
                    "paginate": {
                        "previous": "Prev",
                        "next": "Next",
                        "last": "Last",
                        "first": "First"
                    }
                },
                "order": [
                    [4, "asc"]
                ] // set first column as a default sort by asc
            });
        });
    </script>
@endsection