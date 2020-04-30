@extends('layouts.master')
@section('title'){{trans_choice('general.charge',2)}}
@endsection

@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.charge',2)}}</h6>

            <div class="heading-elements">
                <a href="{{ url('charge/create') }}"
                   class="btn btn-info btn-sm">{{trans_choice('general.add',1)}} {{trans_choice('general.charge',1)}}</a>
            </div>
        </div>
        <div class="panel-body">
            <table id="" class="table table-striped table-condensed table-hover basic-datatable">
                <thead>
                <tr>
                    <th>{{trans_choice('general.name',1)}}</th>
                    <th>{{trans_choice('general.product',1)}}</th>
                    <th>{{trans_choice('general.type',1)}}</th>
                    <th>{{trans_choice('general.active',1)}}</th>
                    <th>{{trans_choice('general.amount',1)}}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>{{ $key->name }}</td>
                        <td>
                            @if($key->product=='loan')
                                {{trans_choice('general.loan',1)}}
                            @endif
                            @if($key->product=='savings')
                                {{trans_choice('general.saving',2)}}
                            @endif
                        </td>
                        <td>
                            @if($key->charge_type=='disbursement')
                                {{trans_choice('general.disbursement',1)}}
                            @endif
                            @if($key->charge_type=='specified_due_date')
                                {{trans_choice('general.specified_due_date',2)}}
                            @endif
                            @if($key->charge_type=='installment_fee')
                                {{trans_choice('general.installment_fee',2)}}
                            @endif
                            @if($key->charge_type=='overdue_installment_fee')
                                {{trans_choice('general.overdue_installment_fee',2)}}
                            @endif
                            @if($key->charge_type=='loan_rescheduling_fee')
                                {{trans_choice('general.loan_rescheduling_fee',2)}}
                            @endif
                            @if($key->charge_type=='overdue_maturity')
                                {{trans_choice('general.overdue_maturity',2)}}
                            @endif
                            @if($key->charge_type=='savings_activation')
                                {{trans_choice('general.savings_activation',2)}}
                            @endif
                            @if($key->charge_type=='withdrawal_fee')
                                {{trans_choice('general.withdrawal_fee',2)}}
                            @endif
                            @if($key->charge_type=='monthly_fee')
                                {{trans_choice('general.monthly_fee',2)}}
                            @endif
                            @if($key->charge_type=='annual_fee')
                                {{trans_choice('general.annual_fee',2)}}
                            @endif
                        </td>
                        <td>
                            @if($key->active==1)
                                {{trans_choice('general.active',1)}}
                            @else
                                {{trans_choice('general.inactive',1)}}
                            @endif
                        </td>
                        <td>
                            {{$key->amount}}
                            @if($key->charge_option=="fixed")
                                {{trans_choice('general.fixed',1)}}
                            @endif
                            @if($key->charge_option=="principal_due")
                                % {{trans_choice('general.principal',1)}} {{trans_choice('general.due',1)}}
                            @endif
                            @if($key->charge_option=="principal_interest")
                                % {{trans_choice('general.principal',1)}} + {{trans_choice('general.interest',1)}} {{trans_choice('general.due',1)}}
                            @endif
                            @if($key->charge_option=="interest_due")
                                % {{trans_choice('general.interest',1)}} {{trans_choice('general.due',1)}}
                            @endif
                            @if($key->charge_option=="total_due")
                                % {{trans_choice('general.total',1)}} {{trans_choice('general.due',1)}}
                            @endif
                            @if($key->charge_option=="original_principal")
                                % {{trans_choice('general.original',1)}} {{trans_choice('general.principal',1)}}
                            @endif

                        </td>
                        <td>
                            <ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <li><a href="{{ url('charge/'.$key->id.'/edit') }}"><i
                                                        class="fa fa-edit"></i> {{ trans('general.edit') }} </a></li>
                                        <li><a href="{{ url('charge/'.$key->id.'/delete') }}"
                                               class="delete"><i
                                                        class="fa fa-trash"></i> {{ trans('general.delete') }} </a></li>
                                    </ul>
                                </li>
                            </ul>
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
