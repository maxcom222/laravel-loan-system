@extends('layouts.master')
@section('title')
    {{trans_choice('general.borrower',1)}} {{trans_choice('general.detail',2)}}
@endsection
@section('content')
    @if($borrower->blacklisted==1)
        <div class="row">
            <div class="col-sm-12">
                <div class="alert bg-danger">
                    <button type="button" class="close" data-dismiss="alert"><span>×</span><span
                                class="sr-only">Close</span></button>
                    {{trans_choice('general.blacklist_notification',1)}}
                </div>
            </div>

        </div>
    @endif
    <!-- Detached sidebar -->

    <div class="sidebar-detached">
        <div class="sidebar sidebar-default sidebar-separate">
            <div class="sidebar-content">
                <!-- User details -->
                <div class="content-group">
                    <div class="panel-body bg-indigo-400 border-radius-top text-center"
                         style="background-image: url(http://demo.interface.club/limitless/assets/images/bg.png); background-size: contain;">
                        <div class="content-group-sm">
                            <h6 class="text-semibold no-margin-bottom">
                                {{$borrower->title}} {{$borrower->first_name}} {{$borrower->last_name}}
                            </h6>
                            <span class="display-block">{{$borrower->unique_number}}</span>
                            @if($borrower->gender=="Male")
                                <span class="display-block">{{trans_choice('general.male',1)}}
                                    , {{date("Y-m-d")-$borrower->dob}} {{trans_choice('general.year',2)}}</span>
                            @endif
                            @if($borrower->gender=="Female")
                                <span class="display-block">{{trans_choice('general.female',1)}}
                                    , {{date("Y-m-d")-$borrower->dob}} {{trans_choice('general.year',2)}}</span>
                            @endif
                        </div>

                        <a href="#" class="display-inline-block content-group-sm">
                            @if(!empty($borrower->photo))
                                <a href="{{asset('uploads/'.$borrower->photo)}}"
                                   class="display-inline-block content-group-sm fancybox"> <img
                                            class="img-circle img-responsive"
                                            src="{{asset('uploads/'.$borrower->photo)}}"
                                            alt="user image" style="max-height: 120px!important;"/></a>
                            @else
                                <a href="#" class="display-inline-block content-group-sm "> <
                                    <img class="img-circle img-responsive"
                                         src="{{asset('assets/dist/img/user.png')}}"
                                         alt="user image" style="height: 120px!important;"/>
                                </a>
                            @endif
                        </a>

                        <ul class="list-inline list-inline-condensed no-margin-bottom">
                            <li><a href="{{url('communication/email/create?borrower_id='.$borrower->id)}}"
                                   class="btn bg-indigo btn-rounded btn-icon" data-toggle="tooltip"
                                   title="{{trans_choice('general.email',1)}}"><i class="icon-envelop3"></i></a>
                            </li>
                            <li><a href="{{url('communication/sms/create?borrower_id='.$borrower->id)}}"
                                   class="btn bg-indigo btn-rounded btn-icon" data-toggle="tooltip"
                                   title="{{trans_choice('general.sms',1)}}"><i class="icon-mobile"></i></a>
                            </li>
                            <li><a href="{{url('borrower/'.$borrower->id.'/edit')}}"
                                   class="btn bg-indigo btn-rounded btn-icon" data-toggle="tooltip"
                                   title="{{trans_choice('general.edit',1)}}"><i class=" icon-pen6"></i></a>
                            </li>
                        </ul>
                    </div>
                    <div class="panel no-border-top no-border-radius-top">
                        <ul class="navigation">
                            <li class="navigation-header">Navigation</li>
                            <li class="active"><a href="#profile" data-toggle="tab"><i class="icon-profile"></i>
                                    {{trans_choice('general.profile',1)}}</a></li>
                            <li><a href="#loans" data-toggle="tab"><i
                                            class="icon-balance"></i> {{trans_choice('general.loan',2)}}</a></li>
                            <li><a href="#payments" data-toggle="tab"><i
                                            class="icon-coin-dollar"></i> {{trans_choice('general.payment',2)}} </a>
                            </li>
                        <!--<li><a href="#savings" data-toggle="tab"><i
                                            class="icon-database2"></i> {{trans_choice('general.saving',2)}}</a>
                            </li>-->
                        </ul>
                    </div>
                </div>
                <!-- /user details -->
            </div>
        </div>
    </div>
    <!-- /detached sidebar -->
    <div class="container-detached">
        <div class="content-detached">
            <!-- Tab content -->
            <div class="tab-content">
                <div class="tab-pane fade in active" id="profile">
                    <div class="panel panel-white">
                        <div class="panel-heading">
                            <h6 class="panel-title">{{trans_choice('general.profile',1)}}</h6>
                            <div class="heading-elements">
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-info dropdown-toggle margin"
                                            data-toggle="dropdown">
                                        {{trans_choice('general.borrower',1)}} {{trans_choice('general.statement',1)}}
                                        <span class="fa fa-caret-down"></span></button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li>
                                            <a href="{{url('loan/'.$borrower->id.'/borrower_statement/print')}}"
                                               target="_blank"><i
                                                        class="icon-printer"></i> {{trans_choice('general.print',1)}} {{trans_choice('general.statement',1)}}
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{url('loan/'.$borrower->id.'/borrower_statement/pdf')}}"
                                               target="_blank"><i
                                                        class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.in',1)}} {{trans_choice('general.pdf',1)}}
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{url('loan/'.$borrower->id.'/borrower_statement/email')}}"><i
                                                        class="icon-envelop"></i> {{trans_choice('general.email',1)}}
                                                {{trans_choice('general.statement',1)}}</a></li>
                                    <!--<li>
                                    <a href="{{url('loan/'.$borrower->id.'/borrower_statement/excel')}}"
                                       target="_blank">{{trans_choice('general.download',1)}} {{trans_choice('general.in',1)}} {{trans_choice('general.excel',1)}}</a></li>

                                <li>
                                    <a href="{{url('loan/'.$borrower->id.'/borrower_statement/csv')}}"
                                       target="_blank">{{trans_choice('general.download',1)}} {{trans_choice('general.in',1)}} {{trans_choice('general.csv',1)}}</a></li>-->
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><b>{{trans_choice('general.basic',1)}} {{trans_choice('general.detail',2)}}</b>
                                    </h6>
                                    <table class="table table-striped table-hover">
                                        <tr>
                                            <td><b>{{trans_choice('general.business',1)}}</b></td>
                                            <td>{{$borrower->business_name}}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{trans_choice('general.working_status',1)}}</b></td>
                                            <td>{{$borrower->working_status}}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{trans_choice('general.dob',1)}}</b></td>
                                            <td>{{$borrower->dob}}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{trans_choice('general.address',1)}}</b></td>
                                            <td>{{$borrower->address}}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{trans_choice('general.city',1)}}</b></td>
                                            <td>{{$borrower->city}}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{trans_choice('general.state',1)}}</b></td>
                                            <td>{{$borrower->state}}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{trans_choice('general.zip',1)}}</b></td>
                                            <td>{{$borrower->zip}}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{trans_choice('general.country',1)}}</b></td>
                                            <td>
                                                @if($borrower->country)
                                                    {{$borrower->country->name}}
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6>
                                        <b>{{trans_choice('general.contact',1)}} {{trans_choice('general.detail',2)}}</b>
                                    </h6>
                                    <table class="table table-striped table-hover">
                                        <tr>
                                            <td><b>{{trans_choice('general.phone',1)}}</b></td>
                                            <td>{{$borrower->phone}}</td>
                                        </tr>
                                        <tr>
                                            <td><b>{{trans_choice('general.email',1)}}</b></td>
                                            <td>
                                                <a
                                                        href="{{url('communication/email/create?borrower_id='.$borrower->id)}}">
                                                    {{$borrower->email}}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>{{trans_choice('general.mobile',1)}}</b></td>
                                            <td>
                                                <a
                                                        href="{{url('communication/sms/create?borrower_id='.$borrower->id)}}">
                                                    {{$borrower->mobile}}
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                    <h6>
                                        <b>{{trans_choice('general.file',2)}}</b>
                                    </h6>
                                    <a data-toggle="collapse" data-parent="#accordion" href="#viewFiles">
                                        {{trans_choice('general.view',1)}} {{trans_choice('general.borrower',1)}} {{trans_choice('general.file',2)}}
                                    </a>

                                    <div id="viewFiles" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <ul class="list-unstyled">
                                                @foreach(unserialize($borrower->files) as $key=>$value)
                                                    <li>
                                                        <a href="{!!asset('uploads/'.$value)!!}"
                                                           target="_blank">{!!  $value!!}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><b>{{trans_choice('general.custom_field',2)}}</b></h6>
                                    <table class="table table-striped table-hover">
                                        @foreach($custom_fields as $key)
                                            <tr>
                                                <td>
                                                    @if(!empty($key->custom_field))
                                                        <strong>{{$key->custom_field->name}}:</strong>
                                                    @endif
                                                </td>
                                                <td>{{$key->name}}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade in " id="loans">
                    <div class="panel panel-white">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{trans_choice('general.loan',2)}}</h3>

                            <div class="heading-elements">

                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table id="loan-data-table" class="table table-condensed">
                                    <thead>
                                    <tr style="">
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
                                    @foreach($borrower->loans as $key)
                                        <tr>
                                            <td>{{$key->id}}</td>
                                            <td>{{number_format($key->principal,2)}}</td>
                                            <td>{{$key->release_date}}</td>
                                            <td>
                                                {{round($key->interest_rate,2)}}%/{{$key->interest_period}}
                                            </td>
                                            <td>{{round(\App\Helpers\GeneralHelper::loan_total_due_amount($key->id),2)}}</td>
                                            <td>{{round(\App\Helpers\GeneralHelper::loan_total_paid($key->id),2)}}</td>
                                            <td>{{round(\App\Helpers\GeneralHelper::loan_total_balance($key->id),2)}}</td>
                                            <td>
                                                @if($key->maturity_date<date("Y-m-d") && \App\Helpers\GeneralHelper::loan_total_balance($key->id)>0)
                                                    <span class="label label-danger">{{trans_choice('general.past_maturity',1)}}</span>
                                                @else
                                                    @if($key->status=='pending')
                                                        <span class="label label-warning">{{trans_choice('general.pending',1)}} {{trans_choice('general.approval',1)}}</span>
                                                    @endif
                                                    @if($key->status=='approved')
                                                        <span class="label label-info">{{trans_choice('general.awaiting',1)}} {{trans_choice('general.disbursement',1)}}</span>
                                                    @endif
                                                    @if($key->status=='disbursed')
                                                        <span class="label label-info">{{trans_choice('general.active',1)}}</span>
                                                    @endif
                                                    @if($key->status=='declined')
                                                        <span class="label label-danger">{{trans_choice('general.declined',1)}}</span>
                                                    @endif
                                                    @if($key->status=='withdrawn')
                                                        <span class="label label-danger">{{trans_choice('general.withdrawn',1)}}</span>
                                                    @endif
                                                    @if($key->status=='written_off')
                                                        <span class="label label-danger">{{trans_choice('general.written_off',1)}}</span>
                                                    @endif
                                                    @if($key->status=='closed')
                                                        <span class="label label-success">{{trans_choice('general.closed',1)}}</span>
                                                    @endif
                                                    @if($key->status=='pending_reschedule')
                                                        <span class="label label-warning">{{trans_choice('general.pending',1)}} {{trans_choice('general.reschedule',1)}}</span>
                                                    @endif
                                                    @if($key->status=='rescheduled')
                                                        <span class="label label-info">{{trans_choice('general.rescheduled',1)}}</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <ul class="icons-list">
                                                    <li class="dropdown">
                                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                            <i class="icon-menu9"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            <li><a href="{{ url('loan/'.$key->id.'/show') }}"><i
                                                                            class="fa fa-search"></i> {{ trans_choice('general.detail',2) }}
                                                                </a></li>
                                                            <li><a href="{{ url('loan/'.$key->id.'/edit') }}"><i
                                                                            class="fa fa-edit"></i> {{ trans('general.edit') }}
                                                                </a></li>
                                                            <li><a href="{{ url('loan/'.$key->id.'/delete') }}"
                                                                   class="delete"><i
                                                                            class="fa fa-trash"></i> {{ trans('general.delete') }}
                                                                </a></li>
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
                    </div>
                </div>
                <div class="tab-pane fade in " id="payments">
                    <div class="panel panel-white">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{trans_choice('general.repayment',2)}}</h3>
                            <div class="heading-elements">

                            </div>
                        </div>
                        <div class="panel-body table-responsive">
                            <table id="repayments-data-table"
                                   class="table  table-condensed table-hover">
                                <thead>
                                <tr>
                                    <th>
                                        {{trans_choice('general.collection',1)}} {{trans_choice('general.date',1)}}
                                    </th>
                                    <th>
                                        {{trans_choice('general.collected_by',1)}}
                                    </th>
                                    <th>
                                        {{trans_choice('general.method',1)}}
                                    </th>
                                    <th>
                                        {{trans_choice('general.amount',1)}}
                                    </th>
                                    <th>
                                        {{trans_choice('general.action',1)}}
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach(\App\Models\LoanTransaction::where('borrower_id',$borrower->id)->where('transaction_type','repayment')->where('reversed',0)->get() as $key)
                                    <tr>
                                        <td>{{$key->date}}</td>
                                        <td>
                                            @if(!empty($key->user))
                                                {{$key->user->first_name}} {{$key->user->last_name}}
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($key->loan_repayment_method))
                                                {{$key->loan_repayment_method->name}}
                                            @endif
                                        </td>
                                        <td>{{number_format($key->credit,2)}}</td>
                                        <td class="text-center">
                                            <ul class="icons-list">
                                                <li class="dropdown">
                                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                        <i class="icon-menu9"></i>
                                                    </a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        <li>
                                                            <a href="{{url('loan/transaction/'.$key->id.'/show')}}"><i
                                                                        class="fa fa-search"></i> {{ trans_choice('general.view',1) }}
                                                            </a></li>
                                                        <li>
                                                        @if($key->transaction_type=='repayment' && $key->reversible==1)
                                                            <li>
                                                                <a href="{{url('loan/transaction/'.$key->id.'/print')}}"
                                                                   target="_blank"><i
                                                                            class="icon-printer"></i> {{ trans_choice('general.print',1) }} {{trans_choice('general.receipt',1)}}
                                                                </a></li>
                                                            <li>
                                                                <a href="{{url('loan/transaction/'.$key->id.'/pdf')}}"
                                                                   target="_blank"><i
                                                                            class="icon-file-pdf"></i> {{ trans_choice('general.pdf',1) }} {{trans_choice('general.receipt',1)}}
                                                                </a></li>
                                                            <li>
                                                                <a href="{{url('loan/repayment/'.$key->id.'/edit')}}"><i
                                                                            class="fa fa-edit"></i> {{ trans('general.edit') }}
                                                                </a></li>
                                                            <li>
                                                                <a href="{{url('loan/repayment/'.$key->id.'/reverse')}}"
                                                                   class="delete"><i
                                                                            class="fa fa-minus-circle"></i> {{ trans('general.reverse') }}
                                                                </a></li>
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
                </div>
                <div class="tab-pane fade in " id="savings"></div>
            </div>
        </div>
    </div>



@endsection
@section('footer-scripts')

    <script>
        $('#loan-data-table').DataTable({
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            autoWidth: false,
            columnDefs: [{
                orderable: false,
                width: '100px',
                targets: [8]
            }]
            , "order": [[0, "desc"]],
            language: {
                "lengthMenu": "{{ trans('general.lengthMenu') }}",
                "zeroRecords": "{{ trans('general.zeroRecords') }}",
                "info": "{{ trans('general.info') }}",
                "infoEmpty": "{{ trans('general.infoEmpty') }}",
                "search": "{{ trans('general.search') }}:",
                "infoFiltered": "{{ trans('general.infoFiltered') }}",
                "paginate": {
                    "first": "{{ trans('general.first') }}",
                    "last": "{{ trans('general.last') }}",
                    "next": "{{ trans('general.next') }}",
                    "previous": "{{ trans('general.previous') }}"
                }
            }
        });
        $('#repayments-data-table').DataTable({
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            autoWidth: false,
            columnDefs: [{
                orderable: false,
                width: '100px',
                targets: [4]
            }],
            "order": [[0, "desc"]],
            language: {
                "lengthMenu": "{{ trans('general.lengthMenu') }}",
                "zeroRecords": "{{ trans('general.zeroRecords') }}",
                "info": "{{ trans('general.info') }}",
                "infoEmpty": "{{ trans('general.infoEmpty') }}",
                "search": "{{ trans('general.search') }}:",
                "infoFiltered": "{{ trans('general.infoFiltered') }}",
                "paginate": {
                    "first": "{{ trans('general.first') }}",
                    "last": "{{ trans('general.last') }}",
                    "next": "{{ trans('general.next') }}",
                    "previous": "{{ trans('general.previous') }}"
                }
            },
        });

    </script>
    <script>
        $(document).ready(function () {
            $('body').addClass('has-detached-left');
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