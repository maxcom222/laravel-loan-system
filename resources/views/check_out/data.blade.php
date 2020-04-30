@extends('layouts.master')
@section('title')
    {{trans_choice('general.check_out',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.check_out',2)}} </h6>

            <div class="heading-elements">
                @if(Sentinel::hasAccess('stock.create'))
                    <a href="{{ url('check_out/create') }}"
                       class="btn btn-info btn-sm">{{trans_choice('general.check_out',1)}} {{trans_choice('general.item',1)}} </a>
                @endif
            </div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table id="data-table" class="table table-bordered table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>{{trans_choice('general.id',1)}}</th>
                        <th>{{trans_choice('general.date',1)}}</th>
                        <th>{{trans_choice('general.borrower',1)}}</th>
                        <th>{{trans_choice('general.type',1)}}</th>
                        <th>{{trans_choice('general.total',1)}}</th>
                        <th>{{trans_choice('general.paid',1)}}</th>
                        <th>{{trans_choice('general.balance',1)}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key)
                        <tr>
                            <td>{{ $key->id }}</td>
                            <td>{{ $key->date }}</td>
                            <td>
                                @if(!empty($key->borrower))
                                    <a href="{{url('borrower/'.$key->borrower_id.'/show')}}">{{$key->borrower->first_name}} {{$key->borrower->last_name}}</a>
                                @endif
                            </td>
                            <td>
                                @if($key->type=='cash')
                                    {{trans_choice('general.cash',1)}}
                                @endif
                                @if($key->type=='loan')
                                    {{trans_choice('general.loan',1)}}
                                @endif
                            </td>
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
                                @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                    {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}{{number_format($key->selling_price,2)}}
                                @else
                                    {{number_format($key->selling_price,2)}}{{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }}
                                @endif
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
    <script src="{{ asset('assets/plugins/datatable/media/js/jquery.dataTables.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatable/media/js/dataTables.bootstrap.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatable/extensions/Buttons/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatable/extensions/Buttons/js/buttons.html5.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatable/extensions/Buttons/js/buttons.print.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatable/extensions/Responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatable/extensions/Buttons/js/buttons.colVis.min.js')}}"></script>
    <script>
        $('#data-table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {extend: 'copy', 'text': '{{ trans('general.copy') }}'},
                {extend: 'excel', 'text': '{{ trans('general.excel') }}'},
                {extend: 'pdf', 'text': '{{ trans('general.pdf') }}'},
                {extend: 'print', 'text': '{{ trans('general.print') }}'},
                {extend: 'csv', 'text': '{{ trans('general.csv') }}'},
                {extend: 'colvis', 'text': '{{ trans('general.colvis') }}'}
            ],
            "paging": true,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[1, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": [7]}
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
