@extends('layouts.master')
@section('title')
    {{trans_choice('general.saving',2)}} {{trans_choice('general.detail',2)}}
@endsection
@section('content')
    @if($saving->borrower->blacklisted==1)
        <div class="row">
            <div class="col-sm-12">
                <div class="alert bg-danger">{{trans_choice('general.blacklist_notification',1)}}</div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="panel border-left-lg border-left-primary">
                <div class="panel-heading">
                    <h6 class="panel-title">{{trans_choice('general.borrower',1)}} {{trans_choice('general.detail',2)}}</h6>

                    <div class="heading-elements">

                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-2">
                            @if(!empty($saving->borrower->photo))
                                <a href="{{asset('uploads/'.$saving->borrower->photo)}}" class="fancybox"> <img
                                            class="img-thumbnail"
                                            src="{{asset('uploads/'.$saving->borrower->photo)}}"
                                            alt="user image" style="max-height: 150px"/></a>
                            @else
                                <img class="img-thumbnail"
                                     src="{{asset('assets/dist/img/user.png')}}"
                                     alt="user image" style="max-height: 150px"/>
                            @endif
                        </div>

                        <div class="col-md-3 form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-md-4"><strong>{{trans_choice('general.name',1)}}
                                        :</strong></label>
                                <div class="col-md-8" style="padding-top: 9px;">
                                    <span>{{$saving->borrower->title}} {{$saving->borrower->first_name}} {{$saving->borrower->last_name}}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4"><strong>{{trans_choice('general.id',1)}}
                                        :</strong></label>
                                <div class="col-md-8" style="padding-top: 9px;">
                                    <span>#{{$saving->borrower->id}}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4"><strong>{{trans_choice('general.gender',1)}}
                                        :</strong></label>
                                <div class="col-md-8" style="padding-top: 9px;">
                                    @if($saving->borrower->gender=="Male")
                                        <span class="">{{trans_choice('general.male',1)}}</span>
                                    @endif
                                    @if($saving->borrower->gender=="Female")
                                        <span class="">{{trans_choice('general.female',1)}}</span>
                                    @endif
                                </div>
                            </div>

                        </div>
                        <div class="col-md-3 form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-md-4"><strong>{{trans_choice('general.age',1)}}
                                        :</strong></label>
                                <div class="col-md-8" style="padding-top: 9px;">
                                    <span>{{date("Y-m-d")-$saving->borrower->dob}} {{trans_choice('general.year',2)}}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4"><strong>{{trans_choice('general.phone',1)}}
                                        :</strong></label>
                                <div class="col-md-8" style="padding-top: 9px;">
                                    <span> <a href="{{url('communication/sms/create?borrower_id='.$saving->borrower->id)}}"> {{$saving->borrower->mobile}}</a></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4"><strong>{{trans_choice('general.email',1)}}
                                        :</strong></label>
                                <div class="col-md-8" style="padding-top: 9px;">
                                    <span>   <a href="{{url('communication/email/create?borrower_id='.$saving->borrower->id)}}">{{$saving->borrower->email}}</a></span>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-3 form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-md-4"><strong>{{trans_choice('general.business',1)}}
                                        :</strong></label>
                                <div class="col-md-8" style="padding-top: 9px;">
                                    <span>{{$saving->borrower->business_name}}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4"><strong>{{trans_choice('general.address',1)}}
                                        :</strong></label>
                                <div class="col-md-8" style="padding-top: 9px;">
                                    <span>{{$saving->borrower->address}}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4"><strong>{{trans_choice('general.country',1)}}
                                        :</strong></label>
                                <div class="col-sm-8" style="padding-top: 9px;">
                                    @if($saving->borrower->country)
                                        <span>{{$saving->borrower->country->name}}</span>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="panel-footer panel-footer-condensed"><a class="heading-elements-toggle"><i
                                class="icon-more"></i></a>
                    <div class="heading-elements">
                        <span class="heading-text">{{trans_choice('general.created_at',1)}}: <span
                                    class="text-semibold">{{$saving->borrower->created_at}}</span></span>

                        <ul class="list-inline list-inline-condensed heading-text pull-right">
                            <li class="dropdown">
                                <a href="#" class="text-default dropdown-toggle" data-toggle="dropdown"
                                   aria-expanded="false"><i class="icon-menu7"></i> <span class="caret"></span></a>
                                <ul class="dropdown-menu dropdown-menu-right active">
                                    @if(Sentinel::hasAccess('loans.create'))
                                        <li class="">
                                            <a href="{{url('loan/create?borrower_id='.$saving->borrower_id)}}">{{trans_choice('general.add',1)}}
                                                {{trans_choice('general.loan',1)}}</a>
                                        </li>
                                    @endif
                                    @if(Sentinel::hasAccess('borrowers.update'))
                                        <li class="">
                                            <a href="{{url('borrower/'.$saving->borrower_id.'/edit')}}">{{trans_choice('general.edit',1)}}
                                                {{trans_choice('general.borrower',1)}}</a>
                                        </li>
                                    @endif

                                </ul>
                            </li>

                            <li class="dropdown">
                                <a href="#" class="text-default dropdown-toggle" data-toggle="dropdown"
                                   aria-expanded="false">{{trans_choice('general.borrower',1)}} {{trans_choice('general.statement',1)}}
                                    <span class="caret"></span></a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a href="{{url('loan/'.$saving->borrower_id.'/borrower_statement/print')}}"
                                           target="_blank"><i
                                                    class="icon-printer"></i> {{trans_choice('general.print',1)}} {{trans_choice('general.statement',1)}}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{url('loan/'.$saving->borrower_id.'/borrower_statement/pdf')}}"
                                           target="_blank"><i
                                                    class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.in',1)}} {{trans_choice('general.pdf',1)}}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{url('loan/'.$saving->borrower_id.'/borrower_statement/email')}}"
                                        ><i class="icon-envelop"></i> {{trans_choice('general.email',1)}} {{trans_choice('general.statement',1)}}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="panel panel-white">
                <div class="panel-body">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#summary" data-toggle="tab"
                                              aria-expanded="false"> {{trans_choice('general.summary',1)}}</a>
                        </li>

                        <li class=""><a href="#transactions" data-toggle="tab"
                                        aria-expanded="false">{{trans_choice('general.transaction',2)}} </a>
                        </li>
                        <li class="hidden"><a href="#on_hold" data-toggle="tab"
                                              aria-expanded="false">{{trans_choice('general.on',1)}} {{trans_choice('general.hold',2)}}</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="summary">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-sm btn-info m-10 dropdown-toggle"
                                                data-toggle="dropdown"
                                                aria-expanded="false">{{trans_choice('general.saving',2)}} {{trans_choice('general.statement',1)}}
                                            <span class="fa fa-caret-down"></span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">


                                            <li><a href="{{url('saving/'.$saving->id.'/statement/print')}}"
                                                   target="_blank">{{trans_choice('general.print',1)}} {{trans_choice('general.statement',1)}}</a>
                                            </li>

                                            <li><a href="{{url('saving/'.$saving->id.'/statement/pdf')}}"
                                                   target="_blank">{{trans_choice('general.download',1)}} {{trans_choice('general.in',1)}} {{trans_choice('general.pdf',1)}}</a>
                                            </li>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table" style="border: none">
                                        <tr>
                                            <td>{{trans_choice('general.account',1)}}#</td>
                                            <td>{{$saving->id}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{trans_choice('general.product',1)}}</td>
                                            <td>
                                                @if(!empty($saving->savings_product))
                                                    {{ $saving->savings_product->name }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{trans_choice('general.minimum',1)}} {{trans_choice('general.balance',1)}}</td>
                                            <td>
                                                @if(!empty($saving->savings_product))
                                                    {{$saving->savings_product->minimum_balance}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{trans_choice('general.interest_rate_per_annum',1)}}</td>
                                            <td>
                                                @if(!empty($saving->savings_product))
                                                    {{$saving->savings_product->interest_rate}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{trans_choice('general.interest_posting_frequency',1)}}</td>
                                            <td>
                                                @if(!empty($saving->savings_product))
                                                    @if($saving->savings_product->interest_posting==1)
                                                        {{trans_choice('general.every_1_month',1)}}
                                                    @endif
                                                    @if($saving->savings_product->interest_posting==2)
                                                        {{trans_choice('general.every_2_month',1)}}
                                                    @endif
                                                    @if($saving->savings_product->interest_posting==3)
                                                        {{trans_choice('general.every_3_month',1)}}
                                                    @endif
                                                    @if($saving->savings_product->interest_posting==4)
                                                        {{trans_choice('general.every_4_month',1)}}
                                                    @endif
                                                    @if($saving->savings_product->interest_posting==5)
                                                        {{trans_choice('general.every_6_month',1)}}
                                                    @endif
                                                    @if($saving->savings_product->interest_posting==6)
                                                        {{trans_choice('general.every_12_month',1)}}

                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{trans_choice('general.allow_overdraft',1)}}</td>
                                            <td>
                                                @if($saving->savings_product->allow_overdraw==0)
                                                    {{trans_choice('general.no',1)}}
                                                @endif
                                                @if($saving->savings_product->allow_overdraw==1)
                                                    {{trans_choice('general.yes',1)}}
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table" style="border: none">
                                        <?php $allocation = \App\Helpers\GeneralHelper::total_savings_transactions($saving->id) ?>
                                        <tr>
                                            <td>{{trans_choice('general.current',1)}} {{trans_choice('general.balance',1)}}</td>
                                            <td>{{number_format(\App\Helpers\GeneralHelper::savings_account_balance($saving->id),2)}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{trans_choice('general.total',1)}} {{trans_choice('general.deposit',2)}}</td>
                                            <td>{{number_format($allocation["deposits"],2)}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{trans_choice('general.total',1)}} {{trans_choice('general.withdrawal',2)}}</td>
                                            <td>{{number_format($allocation["withdrawals"],2)}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{trans_choice('general.total',1)}} {{trans_choice('general.charge',2)}}</td>
                                            <td>{{number_format($allocation["fees"],2)}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{trans_choice('general.total',1)}} {{trans_choice('general.interest',1)}}</td>
                                            <td>{{number_format($allocation["interest"],2)}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{trans_choice('general.total',1)}} {{trans_choice('general.on',2)}} {{trans_choice('general.hold',2)}}</td>
                                            <td>{{number_format($allocation["guarantee"],2)}}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane " id="transactions">
                            <div class="btn-group-horizontal">
                                @if(Sentinel::hasAccess('savings.transactions.create'))
                                    <a type="button" class="btn btn-info m-10"
                                       href="{{url('saving/'.$saving->id.'/savings_transaction/create')}}">{{trans_choice('general.add',1)}}
                                        {{trans_choice('general.transaction',1)}}</a>
                                    <a type="button" class="btn btn-success m-10"
                                       href="{{url('saving/'.$saving->id.'/savings_transaction/create')}}">{{trans_choice('general.transfer',1)}}
                                    </a>
                                @endif
                            </div>
                            <div class="box box-info">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table id="repayments-data-table"
                                                       class="table  table-condensed table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th>
                                                            {{trans_choice('general.id',1)}}
                                                        </th>
                                                        <th>
                                                            {{trans_choice('general.date',1)}}
                                                        </th>
                                                        <th>
                                                            {{trans_choice('general.submitted',1)}} {{trans_choice('general.on',1)}}
                                                        </th>
                                                        <th>
                                                            {{trans_choice('general.type',1)}}
                                                        </th>

                                                        <th>
                                                            {{trans_choice('general.debit',1)}}
                                                        </th>
                                                        <th>
                                                            {{trans_choice('general.credit',1)}}
                                                        </th>
                                                        <th>
                                                            {{trans_choice('general.balance',1)}}
                                                        </th>
                                                        <th>
                                                            {{trans_choice('general.detail',2)}}
                                                        </th>
                                                        <th class="text-center">
                                                            {{trans_choice('general.action',1)}}
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $balance = 0;
                                                    ?>
                                                    @foreach(\App\Models\SavingTransaction::where('savings_id',$saving->id)->whereIn('reversal_type',['user','none'])->orderBy('date', 'asc')->orderBy('time',
            'asc')->get() as $key)
                                                        <?php
                                                        $balance = $balance + ($key->credit - $key->debit);
                                                        ?>
                                                        <tr>
                                                            <td>{{$key->id}}</td>
                                                            <td>{{$key->date}} {{$key->time}}</td>
                                                            <td>{{$key->created_at}}</td>
                                                            <td>
                                                                @if($key->type=='deposit')
                                                                    {{trans_choice('general.deposit',1)}}
                                                                @endif
                                                                @if($key->type=='withdrawal')
                                                                    {{trans_choice('general.withdrawal',1)}}
                                                                @endif
                                                                @if($key->type=='bank_fees')
                                                                    {{trans_choice('general.charge',1)}}
                                                                @endif
                                                                @if($key->type=='interest')
                                                                    {{trans_choice('general.interest',1)}}
                                                                @endif
                                                                @if($key->type=='dividend')
                                                                    {{trans_choice('general.dividend',1)}}
                                                                @endif
                                                                @if($key->type=='transfer')
                                                                    {{trans_choice('general.transfer',1)}}
                                                                @endif
                                                                @if($key->type=='transfer_fund')
                                                                    {{trans_choice('general.transfer',1)}}
                                                                @endif
                                                                @if($key->type=='transfer_loan')
                                                                    {{trans_choice('general.transfer',1)}}
                                                                @endif
                                                                @if($key->type=='guarantee')
                                                                    {{trans_choice('general.on',1)}} {{trans_choice('general.hold',1)}}
                                                                @endif
                                                                @if($key->reversed==1)
                                                                    @if($key->reversal_type=="user")
                                                                        <span class="text-danger"><b>({{trans_choice('general.user',1)}} {{trans_choice('general.reversed',1)}}
                                                                                )</b></span>
                                                                    @endif
                                                                    @if($key->reversal_type=="system")
                                                                        <span class="text-danger"><b>({{trans_choice('general.system',1)}} {{trans_choice('general.reversed',1)}}
                                                                                )</b></span>
                                                                    @endif
                                                                @endif
                                                            </td>
                                                            <td>{{number_format($key->debit,2)}}</td>
                                                            <td>{{number_format($key->credit,2)}}</td>
                                                            <td>{{number_format($balance,2)}}</td>
                                                            <td>{{$key->receipt}}</td>
                                                            <td class="text-center">
                                                                <ul class="icons-list">
                                                                    <li class="dropdown">
                                                                        <a href="#" class="dropdown-toggle"
                                                                           data-toggle="dropdown">
                                                                            <i class="icon-menu9"></i>
                                                                        </a>
                                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                                            @if($key->reversed==0)
                                                                                <li>
                                                                                    <a href="{{url('saving/savings_transaction/'.$key->id.'/show')}}"><i
                                                                                                class="fa fa-search"></i> {{ trans_choice('general.view',1) }}
                                                                                    </a></li>
                                                                                <li>
                                                                                    <a href="{{url('saving/savings_transaction/'.$key->id.'/print')}}"
                                                                                       target="_blank"><i
                                                                                                class="icon-printer"></i> {{ trans_choice('general.print',1) }} {{trans_choice('general.receipt',1)}}
                                                                                    </a></li>
                                                                                <li>
                                                                                    <a href="{{url('saving/savings_transaction/'.$key->id.'/pdf')}}"
                                                                                       target="_blank"><i
                                                                                                class="icon-file-pdf"></i> {{ trans_choice('general.pdf',1) }} {{trans_choice('general.receipt',1)}}
                                                                                    </a></li>
                                                                            @endif
                                                                            @if($key->reversed==0 && $key->reversible==1)

                                                                                <li>
                                                                                    <a href="{{url('saving/savings_transaction/'.$key->id.'/edit')}}"><i
                                                                                                class="fa fa-edit"></i> {{ trans('general.edit') }}
                                                                                    </a></li>
                                                                                <li>
                                                                                    <a href="{{url('saving/savings_transaction/'.$key->id.'/reverse')}}"
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer-scripts')
    <script>

        $('#repayments-data-table').DataTable({
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            autoWidth: false,
            columnDefs: [{
                orderable: false,
                width: '100px',
                targets: [8]
            }],
            "order": [[1, "asc"]],
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
            drawCallback: function () {
                $('.delete').on('click', function (e) {
                    e.preventDefault();
                    var href = $(this).attr('href');
                    swal({
                        title: 'Are you sure?',
                        text: '',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ok',
                        cancelButtonText: 'Cancel'
                    }).then(function () {
                        window.location = href;
                    })
                });
            }
        });
    </script>
    <script>
        $(document).ready(function () {
            $('.deleteLoan').on('click', function (e) {
                e.preventDefault();
                var href = $(this).attr('href');
                swal({
                    title: '{{trans_choice('general.are_you_sure',1)}}',
                    text: '{{trans_choice('general.delete_loan_msg',1)}}',
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
            $('.deletePayment').on('click', function (e) {
                e.preventDefault();
                var href = $(this).attr('href');
                swal({
                    title: '{{trans_choice('general.are_you_sure',1)}}',
                    text: '{{trans_choice('general.delete_payment_msg',1)}}',
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
            $('.deleteComment').on('click', function (e) {
                e.preventDefault();
                var href = $(this).attr('href');
                swal({
                    title: '{{trans_choice('general.are_you_sure',1)}}',
                    text: '',
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