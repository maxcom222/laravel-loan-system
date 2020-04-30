@extends('layouts.master')
@section('title')
    Loan Details
@endsection
@section('content')
    <div class="box box-widget">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-4">
                    <div class="user-block">
                        <img class="img-circle"
                             src="https://x.loandisk.com/uploads/borrower_images/thumbnails/placeholder.png"
                             alt="user image"/>
                            <span class="username">
                                {{$borrower->title}}
                                . {{$borrower->first_name}} {{$borrower->last_name}}
                            </span>
                            <span class="description" style="font-size:13px; color:#000000">{{$borrower->unique_number}}
                                <br>
                                <a href="{{url('borrower/'.$borrower->id.'/show')}}">Edit</a><br>
                                {{$borrower->business_name}}, {{$borrower->working_status}}
                                <br>{{$borrower->gender}}, {{date("Y-m-d")-$borrower->dob}} years
                            </span>
                    </div>
                    <!-- /.user-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-4">
                    <ul class="list-unstyled">
                        <li><b>Address:</b> {{$borrower->address}}</li>
                        <li><b>City:</b> {{$borrower->city}}</li>
                        <li><b>Province:</b> {{$borrower->state}}</li>
                        <li><b>Zipcode:</b> {{$borrower->zip}}</li>
                    </ul>
                </div>
                <div class="col-sm-4">
                    <ul class="list-unstyled">
                        <li><b>Landline:</b> {{$borrower->phone}}</li>
                        <li><b>Email:</b> <a
                                    onclick="javascript:window.open('mailto:{{$borrower->email}}', 'mail');event.preventDefault()"
                                    href="mailto:{{$borrower->email}}">{{$borrower->email}}</a>

                            <div class="btn-group-horizontal"><a type="button" class="btn-xs bg-red"
                                                                 href="https://x.loandisk.com/borrowers/send_email_to_borrower.php?borrower_id=13315">Send
                                    Email</a></div>
                        </li>
                        <li><b>Mobile:</b> {{$borrower->mobile}}
                            <div class="btn-group-horizontal"><a type="button" class="btn-xs bg-red"
                                                                 href="https://x.loandisk.com/borrowers/send_sms_to_borrower.php?borrower_id=13315">Send
                                    SMS</a></div>
                        </li>

                    </ul>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-sm-8">
                    <div class="btn-group-horizontal"><a type="button" class="btn bg-olive margin"
                                                         href="{{url('loan/create?borrower_id='.$borrower->id)}}">Add
                            Loan</a></div>
                </div>
                <div class="col-sm-4">
                    <div class="pull-left">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-info dropdown-toggle margin" data-toggle="dropdown">
                                Borrower Statement
                                <span class="fa fa-caret-down"></span></button>
                            <ul class="dropdown-menu" role="menu">

                                <li>
                                    <a href="{{url('loan/'.$borrower->id.'/print')}}"
                                       target="_blank">Print Statement</a></li>

                                <li>
                                    <a href="{{url('loan/'.$borrower->id.'/pdf')}}"
                                       target="_blank">Download in PDF</a></li>

                                <li>
                                    <a href="{{url('loan/'.$borrower->id.'/excel')}}"
                                       target="_blank">Download in Excel</a></li>

                                <li>
                                    <a href="{{url('loan/'.$borrower->id.'/csv')}}"
                                       target="_blank">Download in CSV</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box box-info">
        <div class="panel-heading">
            <h6 class="panel-title">Loans</h6>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body table-responsive ">
            <table id="" class="table table-bordered table-condensed table-hover">
                <thead>
                <tr style="background-color: #D1F9FF">
                    <th>#</th>
                    <th>Principal</th>
                    <th>Released</th>
                    <th>Interest%</th>
                    <th>Due</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>{{ trans('general.action') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($borrower->loans as $key)
                    <tr>

                        <td>{{$key->id}}</td>
                        <td>${{round($key->principal,2)}}</td>
                        <td>{{$key->release_date}}</td>
                        <td>
                            {{round($key->interest_rate,2)}}%/{{$key->interest_period}}
                        </td>
                        <td>{{round(\App\Helpers\GeneralHelper::loan_total_due_amount($key->id),2)}}</td>
                        <td>{{round(\App\Helpers\GeneralHelper::loan_total_paid($key->id),2)}}</td>
                        <td>{{round(\App\Helpers\GeneralHelper::loan_total_balance($key->id),2)}}</td>
                        <td>
                            @if($key->maturity_date<date("Y-m-d") && \App\Helpers\GeneralHelper::loan_total_balance($key->id)>0)
                                <span class="label label-danger">Past Maturity</span>
                            @else
                                @if($key->loan_status=='open')
                                    <span class="label label-info">Open</span>
                                @endif
                                @if($key->loan_status=='defaulted')
                                    <span class="label label-danger">Defaulted</span>
                                @endif
                                @if($key->loan_status=='fully_paid')
                                    <span class="label label-success">Fully Paid</span>
                                @endif
                                @if($key->loan_status=='processing')
                                    <span class="label label-warning">Processing</span>
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
                                    <li><a href="{{ url('loan/'.$key->id.'/show') }}"><i
                                                    class="fa fa-search"></i> {{ trans('general.detail') }} </a></li>
                                    <li><a href="{{ url('loan/'.$key->id.'/edit') }}"><i
                                                    class="fa fa-edit"></i> {{ trans('general.edit') }} </a></li>
                                    <li><a href="{{ url('loan/'.$key->id.'/delete') }}"
                                           data-toggle="confirmation"><i
                                                    class="fa fa-trash"></i> {{ trans('general.delete') }} </a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="box box-info">
        <div class="panel-heading">
            <h3 class="panel-title">Repayments</h3>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body table-responsive">
            <table id="view-repayments"
                   class="table table-bordered table-condensed table-hover dataTable no-footer">
                <thead>
                <tr style="background-color: #D1F9FF" role="row">
                    <th>
                        Collection Date
                    </th>
                    <th>
                        Collected By
                    </th>
                    <th>
                        Method
                    </th>
                    <th>
                        Amount
                    </th>
                    <th>
                        Action
                    </th>
                    <th>
                        Receipt
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($borrower->payments as $key)


                    <tr>
                        <td>{{$key->collection_date}}</td>
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
                        <td>${{round($key->amount,2)}}</td>
                        <td>
                            <div class="btn-group-horizontal">
                                <a type="button" class="btn bg-white btn-xs text-bold"
                                   href="{{url('loan/'.$key->loan_id.'/repayment/'.$key->id.'/edit')}}">Edit</a>
                                <a type="button"
                                   class="btn bg-white btn-xs text-bold deletePayment"
                                   href="{{url('loan/'.$key->loan_id.'/repayment/'.$key->id.'/delete')}}"
                                        >Delete</a>
                            </div>
                        </td>
                        <td>
                            <a type="button" class="btn btn-default btn-xs"
                               href="{{url('loan/'.$key->loan_id.'/repayment/'.$key->id.'/print')}}"
                               target="_blank">
                                                                <span class="glyphicon glyphicon-print"
                                                                      aria-hidden="true"></span>
                            </a>
                            <a type="button" class="btn btn-default btn-xs"
                               href="{{url('loan/'.$key->loan_id.'/repayment/'.$key->id.'/pdf')}}"
                               target="_blank">
                                                                <span class="glyphicon glyphicon-file"
                                                                      aria-hidden="true"></span>
                            </a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('footer-scripts')
    <script>
        $(document).ready(function () {
            $('.deletePayment').on('click', function (e) {
                e.preventDefault();
                var href = $(this).attr('href');
                swal({
                    title: 'Are you sure?',
                    text: 'If you delete a payment, a fully paid loan may change status to open.',
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
        });
    </script>
@endsection