@extends('layouts.master')
@section('title'){{trans_choice('general.loan',2)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">

                @if(isset($_REQUEST['status']))
                    @if($_REQUEST['status']=='pending')
                        {{trans_choice('general.loan',2)}}  {{trans_choice('general.pending',1)}} {{trans_choice('general.approval',1)}}
                    @endif
                    @if($_REQUEST['status']=='approved')
                        {{trans_choice('general.loan',2)}}  {{trans_choice('general.awaiting',1)}} {{trans_choice('general.disbursement',1)}}
                    @endif
                    @if($_REQUEST['status']=='disbursed')
                        {{trans_choice('general.loan',2)}}  {{trans_choice('general.disbursed',1)}}
                    @endif
                    @if($_REQUEST['status']=='declined')
                        {{trans_choice('general.loan',2)}} {{trans_choice('general.declined',1)}}
                    @endif
                    @if($_REQUEST['status']=='withdrawn')
                        {{trans_choice('general.loan',2)}} {{trans_choice('general.withdrawn',1)}}
                    @endif
                    @if($_REQUEST['status']=='written_off')
                        {{trans_choice('general.loan',2)}} {{trans_choice('general.written_off',1)}}
                    @endif
                    @if($_REQUEST['status']=='closed')
                        {{trans_choice('general.loan',2)}} {{trans_choice('general.closed',1)}}
                    @endif
                    @if($_REQUEST['status']=='pending_reschedule')
                        {{trans_choice('general.loan',2)}} {{trans_choice('general.pending',1)}} {{trans_choice('general.reschedule',1)}}
                    @endif
                @else
                    {{trans_choice('general.all',2)}} {{trans_choice('general.loan',2)}}
                @endif
            </h6>

            <div class="heading-elements">
                @if(Sentinel::hasAccess('loans.create'))
                    <a href="{{ url('loan/create') }}"
                       class="btn btn-info btn-sm">{{trans_choice('general.add',1)}} {{trans_choice('general.loan',1)}}</a>
                @endif
            </div>
        </div>
        <div class="panel-body table-responsive">
            <table id="data-table" class="table table-bordered table-striped table-condensed table-hover">
                <thead>
                <tr style="background-color: #D1F9FF">
                    <th>{{trans_choice('general.borrower',1)}}</th>
                    <th>#</th>
                    <th>{{trans_choice('general.principal',1)}}</th>
                    <th>{{trans_choice('general.expected',1)}} {{trans_choice('general.disbursement',1)}} {{trans_choice('general.date',1)}}</th>
                    <th>{{trans_choice('general.interest',1)}}%</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>
                            @if(!empty($key->borrower))
                                <a href="{{url('borrower/'.$key->borrower_id.'/show')}}">{{$key->borrower->first_name}} {{$key->borrower->last_name}}</a>
                            @else
                                <span class="label label-danger">{{trans_choice('general.broken',1)}} <i
                                            class="fa fa-exclamation-triangle"></i> </span>
                            @endif
                            {{ $key->name }}
                        </td>
                        <td>{{$key->id}}</td>
                        <td>
                            @if(\App\Models\Setting::where('setting_key', 'currency_position')->first()->setting_value=='left')
                                {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value }} {{round($key->applied_amount,2)}}
                            @else
                                {{round($key->applied_amount,2)}} {{ \App\Models\Setting::where('setting_key', 'currency_symbol')->first()->setting_value}}
                            @endif

                        </td>
                        <td>{{$key->release_date}}</td>
                        <td>
                            {{round($key->interest_rate,2)}}%/{{$key->interest_period}}
                        </td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-info btn-xs dropdown-toggle"
                                        data-toggle="dropdown" aria-expanded="false">
                                    {{ trans('general.choose') }} <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    @if(Sentinel::hasAccess('loans.view'))
                                        <li><a href="{{ url('loan/'.$key->id.'/show') }}"><i
                                                        class="fa fa-search"></i> {{ trans_choice('general.detail',2) }}
                                            </a>
                                        </li>
                                    @endif
                                    @if(Sentinel::hasAccess('loans.create'))
                                        <li><a href="{{ url('loan/'.$key->id.'/edit') }}"><i
                                                        class="fa fa-edit"></i> {{ trans('general.edit') }} </a></li>
                                    @endif
                                    @if(Sentinel::hasAccess('loans.delete'))
                                        <li><a href="{{ url('loan/'.$key->id.'/delete') }}"
                                               class="delete"><i
                                                        class="fa fa-trash"></i> {{ trans('general.delete') }} </a></li>
                                    @endif
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
            "displayLength": 25,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[3, "desc"]],
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
