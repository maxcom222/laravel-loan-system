@extends('layouts.master')
@section('title'){{trans_choice('general.loan',1)}} {{trans_choice('general.transaction',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.loan',1)}} {{trans_choice('general.transaction',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body">
            <div class="col-md-6">
                <table class="table table-striped table-hover">
                    <tr>
                        <td>{{trans_choice('general.id',1)}}</td>
                        <td>{{$loan_transaction->id}}</td>
                    </tr>
                    <tr>
                        <td>{{trans_choice('general.type',1)}}</td>
                        <td>
                            @if($loan_transaction->transaction_type=='disbursement')
                                {{trans_choice('general.disbursement',1)}}
                            @endif
                            @if($loan_transaction->transaction_type=='specified_due_date')
                                {{trans_choice('general.specified_due_date',2)}}   {{trans_choice('general.fee',1)}}
                            @endif
                            @if($loan_transaction->transaction_type=='installment_fee')
                                {{trans_choice('general.installment_fee',2)}}
                            @endif
                            @if($loan_transaction->transaction_type=='overdue_installment_fee')
                                {{trans_choice('general.overdue_installment_fee',2)}}
                            @endif
                            @if($loan_transaction->transaction_type=='loan_rescheduling_fee')
                                {{trans_choice('general.loan_rescheduling_fee',2)}}
                            @endif
                            @if($loan_transaction->transaction_type=='overdue_maturity')
                                {{trans_choice('general.overdue_maturity',2)}}
                            @endif
                            @if($loan_transaction->transaction_type=='disbursement_fee')
                                {{trans_choice('general.disbursement',1)}} {{trans_choice('general.charge',2)}}
                            @endif
                            @if($loan_transaction->transaction_type=='interest')
                                {{trans_choice('general.interest',1)}} {{trans_choice('general.applied',2)}}
                            @endif
                            @if($loan_transaction->transaction_type=='repayment')
                                {{trans_choice('general.repayment',1)}}
                            @endif
                            @if($loan_transaction->transaction_type=='write_off_recovery')
                                {{trans_choice('general.recovery',1)}} {{trans_choice('general.repayment',1)}}
                            @endif
                            @if($loan_transaction->transaction_type=='penalty')
                                {{trans_choice('general.penalty',1)}}
                            @endif
                            @if($loan_transaction->transaction_type=='interest_waiver')
                                {{trans_choice('general.interest',1)}} {{trans_choice('general.waiver',2)}}
                            @endif
                            @if($loan_transaction->transaction_type=='charge_waiver')
                                {{trans_choice('general.charge',1)}}  {{trans_choice('general.waiver',2)}}
                            @endif
                            @if($loan_transaction->transaction_type=='write_off')
                                {{trans_choice('general.write_off',1)}}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>{{trans_choice('general.date',1)}}</td>
                        <td>{{$loan_transaction->date}}</td>
                    </tr>
                    <tr>
                        <td>{{trans_choice('general.amount',1)}}</td>
                        <td>
                            @if($loan_transaction->credit>$loan_transaction->debit)
                                {{number_format($loan_transaction->credit,2)}}
                            @else
                                {{number_format($loan_transaction->debit,2)}}
                            @endif
                        </td>
                    </tr>
                    @if(!empty($loan_transaction->receipt))
                        <tr>
                            <td>{{trans_choice('general.receipt',1)}}</td>
                            <td>
                                {{$loan_transaction->receipt}}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td>{{trans_choice('general.note',2)}}</td>
                        <td>
                            {{$loan_transaction->notes}}
                        </td>
                    </tr>
                    @foreach($custom_fields as $key)
                        @if(!empty($key->custom_field))
                            <tr>
                                <td>
                                    {{$key->custom_field->name}}
                                </td>
                                <td>{{$key->name}}</td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            </div>
        </div>
        <!-- /.panel-body -->
        <div class="panel-footer">
            <div class="heading-elements">
                <a href="{{ url()->previous() }}"  class="btn btn-primary pull-right">{{trans_choice('general.back',1)}}</a>
            </div>
        </div>
    </div>
    <!-- /.box -->
@endsection

