@extends('layouts.master')
@section('title')
    {{trans_choice('general.guarantor',1)}} {{trans_choice('general.detail',2)}}
@endsection
@section('content')
    <div class="box box-widget">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-3">
                    <div class="user-block">
                        @if(!empty($guarantor->photo))
                            <a href="{{asset('uploads/'.$guarantor->photo)}}" class="fancybox"> <img class="img-circle"
                                                                                                     src="{{asset('uploads/'.$guarantor->photo)}}"
                                                                                                     alt="user image"/></a>
                        @else
                            <img class="img-circle"
                                 src="{{asset('assets/dist/img/user.png')}}"
                                 alt="user image"/>
                        @endif
                        <span class="username">
                                {{$guarantor->title}}
                            . {{$guarantor->first_name}} {{$guarantor->last_name}}
                            </span>
                        <span class="description" style="font-size:13px; color:#000000">{{$guarantor->unique_number}}
                            <br>
                                <a href="{{url('borrower/'.$guarantor->id.'/edit')}}">{{trans_choice('general.edit',1)}}</a><br>
                            {{$guarantor->business_name}}, {{$guarantor->working_status}}
                            <br>{{$guarantor->gender}}
                            , {{date("Y-m-d")-$guarantor->dob}} {{trans_choice('general.year',2)}}
                            </span>
                        <a data-toggle="collapse" data-parent="#accordion" href="#viewFiles">
                            {{trans_choice('general.view',1)}} {{trans_choice('general.guarantor',1)}} {{trans_choice('general.file',2)}}
                        </a>

                        <div id="viewFiles" class="panel-collapse collapse">
                            <div class="panel-body">
                                <ul class="no-margin" style="font-size:12px; padding-left:10px">

                                    @foreach(unserialize($guarantor->files) as $key=>$value)
                                        <li><a href="{!!asset('uploads/'.$value)!!}"
                                               target="_blank">{!!  $value!!}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- /.user-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-3">
                    <ul class="list-unstyled">
                        <li><b>{{trans_choice('general.address',1)}}:</b> {{$guarantor->address}}</li>
                        <li><b>{{trans_choice('general.city',2)}}:</b> {{$guarantor->city}}</li>
                        <li><b>{{trans_choice('general.state',2)}}:</b> {{$guarantor->state}}</li>
                        <li><b>{{trans_choice('general.zip',2)}}:</b> {{$guarantor->zip}}</li>
                        <li>
                            <b>{{trans_choice('general.country',1)}}:</b>
                            @if($guarantor->country)
                                {{$guarantor->country->name}}
                            @endif
                        </li>
                        <li><b>{{trans_choice('general.blacklisted',1)}}:</b>
                            @if($guarantor->blacklisted==1)
                                <span class="label label-danger">{{trans_choice('general.yes',1)}}</span>
                            @else
                                <span class="label label-success">{{trans_choice('general.no',1)}}</span>
                            @endif
                        </li>
                    </ul>
                </div>
                <div class="col-sm-3">
                    <ul class="list-unstyled">
                        <li><b>{{trans_choice('general.phone',1)}}:</b> {{$guarantor->phone}}</li>
                        <li><b>{{trans_choice('general.email',1)}}:</b> <a
                                    onclick="javascript:window.open('mailto:{{$guarantor->email}}', 'mail');event.preventDefault()"
                                    href="mailto:{{$guarantor->email}}">{{$guarantor->email}}</a>

                            <div class="btn-group-horizontal hidden"><a type="button" class="btn-xs bg-red"
                                                                        href="{{url('communication/email/create?borrower_id='.$guarantor->id)}}">{{trans_choice('general.send',1)}}
                                    {{trans_choice('general.email',1)}}</a></div>
                        </li>
                        <li><b>{{trans_choice('general.mobile',1)}}:</b> {{$guarantor->mobile}}
                            <div class="btn-group-horizontal hidden"><a type="button" class="btn-xs bg-red"
                                                                        href="{{url('communication/sms/create?borrower_id='.$guarantor->id)}}">{{trans_choice('general.send',1)}}
                                    {{trans_choice('general.sms',1)}}</a></div>
                        </li>

                    </ul>
                </div>
                <div class="col-sm-3">
                    <ul class="list-unstyled">
                        <li><b>{{trans_choice('general.custom_field',2)}}</b></li>
                        @foreach($custom_fields as $key)
                            <li>
                                @if(!empty($key->custom_field))
                                    <strong>{{$key->custom_field->name}}:</strong>
                                @endif
                                {{$key->name}}
                            </li>
                        @endforeach

                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="box box-info">
        <div class="panel-heading">
            <h3 class="panel-title">{{trans_choice('general.loan',2)}}</h3>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body table-responsive ">
            <table id="data-table" class="table table-bordered table-condensed table-hover">
                <thead>
                <tr style="background-color: #D1F9FF">
                    <th>#</th>
                    <th>{{trans_choice('general.principal',1)}}</th>
                    <th>{{trans_choice('general.released',1)}}</th>
                    <th>{{trans_choice('general.interest',1)}}%</th>
                    <th>{{trans_choice('general.due',1)}}</th>
                    <th>{{trans_choice('general.paid',1)}}</th>
                    <th>{{trans_choice('general.balance',1)}}</th>
                    <th>{{trans_choice('general.status',1)}}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($guarantor->loans as $key)
                    @if(!empty($key->loan))
                        <tr>

                            <td>{{$key->loan->id}}</td>
                            <td>{{number_format(\App\Helpers\GeneralHelper::loan_total_principal($key->loan->id),2)}}</td>
                            <td>{{$key->loan->release_date}}</td>
                            <td>
                                {{round($key->loan->interest_rate,2)}}%/{{$key->loan->interest_period}}
                            </td>
                            <td>{{round(\App\Helpers\GeneralHelper::loan_total_due_amount($key->loan->id),2)}}</td>
                            <td>{{round(\App\Helpers\GeneralHelper::loan_total_paid($key->loan->id),2)}}</td>
                            <td>{{round(\App\Helpers\GeneralHelper::loan_total_balance($key->loan->id),2)}}</td>
                            <td>
                                @if($key->loan->maturity_date<date("Y-m-d") && \App\Helpers\GeneralHelper::loan_total_balance($key->loan->id)>0)
                                    <span class="label label-danger">{{trans_choice('general.past_maturity',1)}}</span>
                                @else
                                    @if($key->loan->status=='pending')
                                        <span class="label label-warning">{{trans_choice('general.pending',1)}} {{trans_choice('general.approval',1)}}</span>
                                    @endif
                                    @if($key->loan->status=='approved')
                                        <span class="label label-info">{{trans_choice('general.awaiting',1)}} {{trans_choice('general.disbursement',1)}}</span>
                                    @endif
                                    @if($key->loan->status=='disbursed')
                                        <span class="label label-info">{{trans_choice('general.active',1)}}</span>
                                    @endif
                                    @if($key->loan->status=='declined')
                                        <span class="label label-danger">{{trans_choice('general.declined',1)}}</span>
                                    @endif
                                    @if($key->loan->status=='withdrawn')
                                        <span class="label label-danger">{{trans_choice('general.withdrawn',1)}}</span>
                                    @endif
                                    @if($key->loan->status=='written_off')
                                        <span class="label label-danger">{{trans_choice('general.written_off',1)}}</span>
                                    @endif
                                    @if($key->loan->status=='closed')
                                        <span class="label label-success">{{trans_choice('general.closed',1)}}</span>
                                    @endif
                                    @if($key->loan->status=='pending_reschedule')
                                        <span class="label label-warning">{{trans_choice('general.pending',1)}} {{trans_choice('general.reschedule',1)}}</span>
                                    @endif
                                    @if($key->loan->status=='rescheduled')
                                        <span class="label label-info">{{trans_choice('general.rescheduled',1)}}</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info btn-flat dropdown-toggle"
                                            data-toggle="dropdown" aria-expanded="false">
                                        {{ trans('general.choose') }} <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="{{ url('loan/'.$key->loan->id.'/show') }}"><i
                                                        class="fa fa-search"></i> {{ trans_choice('general.detail',2) }}
                                            </a></li>
                                        <li><a href="{{ url('loan/'.$key->loan->id.'/edit') }}"><i
                                                        class="fa fa-edit"></i> {{ trans('general.edit') }} </a></li>
                                        <li><a href="{{ url('loan/'.$key->loan->id.'/delete') }}"
                                               data-toggle="confirmation"><i
                                                        class="fa fa-trash"></i> {{ trans('general.delete') }} </a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
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
        $('#data-table').DataTable({
            dom: 'frtip',
            "paging": true,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[2, "asc"]],
            "columnDefs": [
                {"orderable": false, "targets": [0, 8]}
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
        $('#view-repayments').DataTable({
            dom: 'frtip',
            "paging": true,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[0, "asc"]],
            "columnDefs": [
                {"orderable": false, "targets": [4, 5]}
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
    <script>
        $(document).ready(function () {
            $('.deletePayment').on('click', function (e) {
                e.preventDefault();
                var href = $(this).attr('href');
                swal({
                    title: '{{trans_choice('general.are_you_sure',1)}}',
                    text: 'If you delete a payment, a fully paid loan may change status to open.',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{trans_choice('general.ok',1)}}',
                    cancelButtonText: '{{trans_choice('general.cancel',1)}}'
                }).then(function () {
                    window.location = href;
                })
            });
        });
    </script>
@endsection