@extends('layouts.master')
@section('title'){{trans_choice('general.saving',2)}} {{trans_choice('general.transaction',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.saving',2)}} {{trans_choice('general.transaction',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body">
            <div class="col-md-6">
                <table class="table table-striped table-hover">
                    <tr>
                        <td>{{trans_choice('general.borrower',1)}}</td>
                        <td class="">{{$savings_transaction->borrower->title}}
                            {{$savings_transaction->borrower->first_name}} {{$savings_transaction->borrower->last_name}}
                        </td>
                    </tr>
                    <tr>
                        <td>{{trans_choice('general.id',1)}}</td>
                        <td>{{$savings_transaction->id}}</td>
                    </tr>
                    <tr>
                        <td>{{trans_choice('general.type',1)}}</td>
                        <td>
                            @if($savings_transaction->type=='deposit')
                                {{trans_choice('general.deposit',1)}}
                            @endif
                            @if($savings_transaction->type=='withdrawal')
                                {{trans_choice('general.withdrawal',1)}}
                            @endif
                            @if($savings_transaction->type=='bank_fees')
                                {{trans_choice('general.charge',1)}}
                            @endif
                            @if($savings_transaction->type=='interest')
                                {{trans_choice('general.interest',1)}}
                            @endif
                            @if($savings_transaction->type=='dividend')
                                {{trans_choice('general.dividend',1)}}
                            @endif
                            @if($savings_transaction->type=='transfer')
                                {{trans_choice('general.transfer',1)}}
                            @endif
                            @if($savings_transaction->type=='transfer_fund')
                                {{trans_choice('general.transfer',1)}}
                            @endif
                            @if($savings_transaction->type=='transfer_loan')
                                {{trans_choice('general.transfer',1)}}
                            @endif
                            @if($savings_transaction->type=='guarantee')
                                {{trans_choice('general.on',1)}} {{trans_choice('general.hold',1)}}
                            @endif
                            @if($savings_transaction->reversed==1)
                                @if($savings_transaction->reversal_type=="user")
                                    <span class="text-danger"><b>({{trans_choice('general.user',1)}} {{trans_choice('general.reversed',1)}}
                                            )</b></span>
                                @endif
                                @if($savings_transaction->reversal_type=="system")
                                    <span class="text-danger"><b>({{trans_choice('general.system',1)}} {{trans_choice('general.reversed',1)}}
                                            )</b></span>
                                @endif
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>{{trans_choice('general.date',1)}}</td>
                        <td>{{$savings_transaction->date}} {{$savings_transaction->time}}</td>
                    </tr>
                    <tr>
                        <td>{{trans_choice('general.amount',1)}}</td>
                        <td>
                            @if($savings_transaction->credit>$savings_transaction->debit)
                                {{number_format($savings_transaction->credit,2)}}
                            @else
                                {{number_format($savings_transaction->debit,2)}}
                            @endif
                        </td>
                    </tr>
                    @if(!empty($savings_transaction->receipt))
                        <tr>
                            <td>{{trans_choice('general.receipt',1)}}</td>
                            <td>
                                {{$savings_transaction->receipt}}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td>{{trans_choice('general.note',2)}}</td>
                        <td>
                            {{$savings_transaction->notes}}
                        </td>
                    </tr>

                </table>
            </div>
        </div>
        <!-- /.panel-body -->
        <div class="panel-footer">
            <div class="heading-elements">
                <a href="{{ url()->previous() }}"
                   class="btn btn-primary pull-right">{{trans_choice('general.back',1)}}</a>
            </div>
        </div>
    </div>
    <!-- /.box -->
@endsection

