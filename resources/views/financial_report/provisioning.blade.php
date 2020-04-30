@extends('layouts.master')
@section('title')
    {{trans_choice('general.provisioning',1)}} {{trans_choice('general.report',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">
                {{trans_choice('general.provisioning',1)}} {{trans_choice('general.report',1)}}
                @if(!empty($end_date))
                    as at: <b> {{$end_date}}</b>
                @endif
            </h6>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body hidden-print">
            {!! Form::open(array('url' => Request::url(), 'method' => 'post','class'=>'form-horizontal', 'name' => 'form')) !!}
            <div class="row">
                <div class="col-md-4">
                    {!! Form::label('end_date',trans_choice('general.end',1).' '.trans_choice('general.date',1),array('class'=>'')) !!}
                    {!! Form::text('end_date',$end_date, array('class' => 'form-control date-picker', 'placeholder'=>"",'required'=>'required')) !!}
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12">

                        <button type="submit" class="btn btn-success">{{trans_choice('general.search',1)}}!
                        </button>


                        <a href="{{Request::url()}}"
                           class="btn btn-danger">{{trans_choice('general.reset',1)}}!</a>

                        <div class="btn-group">
                            <button type="button" class="btn bg-blue dropdown-toggle legitRipple"
                                    data-toggle="dropdown">{{trans_choice('general.download',1)}} {{trans_choice('general.report',1)}}
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="{{url('report/financial_report/provisioning/pdf?end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/financial_report/provisioning/excel?end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/financial_report/provisioning/csv?end_date='.$end_date)}}"
                                       target="_blank"><i
                                                class="icon-download"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.csv',1)}}
                                    </a></li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
            {!! Form::close() !!}

        </div>
        <!-- /.panel-body -->

    </div>
    <!-- /.box -->
    @if(!empty($end_date))
        <div class="panel panel-white">
            <div class="panel-body table-responsive no-padding">

                <table class="table table-bordered table-condensed table-hover ">
                    <thead>
                    <tr class="bg-green font-11" style="border: solid #ccc thin">
                        <th colspan="7" style="border: solid #ccc thin"></th>
                        <th colspan="5" style="border: solid #ccc thin">{{trans_choice('general.outstanding',1)}}</th>
                        <th colspan="2" style="border: solid #ccc thin">{{trans_choice('general.arrears',1)}}</th>
                        <th colspan="3" style="border: solid #ccc thin">{{trans_choice('general.provisioning',1)}}</th>
                    </tr>
                    <tr class="bg-green font-11">
                        <th style="border: solid #ccc thin">{{trans_choice('general.loan_officer',1)}}</th>
                        <th style="border: solid #ccc thin">{{trans_choice('general.borrower',1)}}</th>
                        <th style="border: solid #ccc thin">{{trans_choice('general.loan',1)}} {{trans_choice('general.id',1)}}</th>
                        <th style="border: solid #ccc thin">{{trans_choice('general.product',1)}}</th>
                        <th style="border: solid #ccc thin">{{trans_choice('general.amount',1)}}</th>
                        <th style="border: solid #ccc thin">{{trans_choice('general.disbursed',1)}}</th>
                        <th style="border: solid #ccc thin">{{trans_choice('general.maturity',1)}} {{trans_choice('general.date',1)}}</th>
                        <th style="border: solid #ccc thin">{{trans_choice('general.principal',1)}}</th>
                        <th style="border: solid #ccc thin">{{trans_choice('general.interest',1)}}</th>
                        <th style="border: solid #ccc thin">{{trans_choice('general.fee',2)}}</th>
                        <th style="border: solid #ccc thin">{{trans_choice('general.penalty',1)}}</th>
                        <th style="border: solid #ccc thin">{{trans_choice('general.total',1)}}</th>
                        <th style="border: solid #ccc thin">{{trans_choice('general.day',2)}}</th>
                        <th style="border: solid #ccc thin">{{trans_choice('general.amount',1)}}</th>
                        <th style="border: solid #ccc thin">{{trans_choice('general.percentage',1)}}</th>
                        <th style="border: solid #ccc thin">{{trans_choice('general.amount',1)}}</th>
                        <th style="border: solid #ccc thin">{{trans_choice('general.classification',1)}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total_outstanding = 0;
                    $total_arrears = 0;
                    $total_principal = 0;
                    $total_interest = 0;
                    $total_fees = 0;
                    $total_penalty = 0;
                    $total_provisioning_amount = 0;
                    $total_amount=0;
                    ?>
                    @foreach(\App\Models\Loan::where('release_date','<=',$end_date)->where('branch_id',
                    session('branch_id'))->where('status', 'disbursed')->orderBy('release_date','asc')->get() as $key)
                        <?php
                        $loan_due_items = \App\Helpers\GeneralHelper::loan_due_items($key->id);
                        $loan_due_items_arrears = \App\Helpers\GeneralHelper::loan_due_items($key->id,
                            $key->release_date, $end_date);
                        $loan_paid_items = \App\Helpers\GeneralHelper::loan_paid_items($key->id,
                            $key->release_date, $end_date);
                        $due = ($loan_due_items["principal"] + $loan_due_items["interest"] + $loan_due_items["fees"] + $loan_due_items["penalty"]) - ($loan_paid_items["principal"] + $loan_paid_items["interest"] + $loan_paid_items["fees"] + $loan_paid_items["penalty"]);
                        $principal = $loan_due_items["principal"] - $loan_paid_items["principal"];
                        $interest = $loan_due_items["interest"] - $loan_paid_items["interest"];
                        $fees = $loan_due_items["fees"] - $loan_paid_items["fees"];
                        $penalty = $loan_due_items["penalty"] - $loan_paid_items["penalty"];
                        $arrears = ($loan_due_items_arrears["principal"] + $loan_due_items_arrears["interest"] + $loan_due_items_arrears["fees"] + $loan_due_items_arrears["penalty"]) - ($loan_paid_items["principal"] + $loan_paid_items["interest"] + $loan_paid_items["fees"] + $loan_paid_items["penalty"]);
                        $total_outstanding = $total_outstanding + $due;
                        $total_arrears = $total_arrears + $arrears;
                        $total_principal = $total_principal + $principal;
                        $total_interest = $total_interest + $interest;
                        $total_fees = $total_fees + $fees;
                        $total_penalty = $total_penalty + $penalty;
                        $total_amount = $total_amount + $key->principal;
                        if ($due > 0) {
                            //lets find arrears information
                            $schedules = \App\Models\LoanSchedule::where('loan_id', $key->id)->where('due_date', '<=',
                                $end_date)->orderBy('due_date', 'asc')->get();
                            $payments = $loan_paid_items["principal"] + $loan_paid_items["interest"] + $loan_paid_items["fees"] + $loan_paid_items["penalty"];
                            if ($payments > 0) {
                                foreach ($schedules as $schedule) {
                                    if ($payments > $schedule->principal + $schedule->interest + $schedule->penalty + $schedule->fees) {
                                        $payments = $payments - ($schedule->principal + $schedule->interest + $schedule->penalty + $schedule->fees);
                                    } else {
                                        $payments = 0;
                                        $overdue_date = $schedule->due_date;
                                        break;
                                    }
                                }
                            } else {
                                $overdue_date = $schedules->first()->due_date;
                            }
                            $date1 = new DateTime($overdue_date);
                            $date2 = new DateTime($end_date);
                            $days_arrears = $date2->diff($date1)->format("%a");
                            $transaction = \App\Models\LoanTransaction::where('loan_id',
                                $key->id)->where('transaction_type',
                                'repayment')->where('reversed', 0)->orderBy('date', 'desc')->first();
                            if (!empty($transaction)) {
                                $date2 = new DateTime($transaction->date);
                                $date1 = new DateTime($end_date);
                                $days_last_payment = $date2->diff($date1)->format("%r%a");
                            } else {
                                $days_last_payment = 0;
                            }
                        } else {
                            $days_arrears = 0;
                        }
                        //find the classification
                        if ($days_arrears < 30 ) {
                            $classification = trans_choice('general.current',1);
                            $provision_rate = \App\Models\ProvisionRate::find(1)->rate;
                            $provision = $provision_rate * $principal/ 100;
                            $total_provisioning_amount = $total_provisioning_amount + $provision;
                        } elseif ($days_arrears > 30 && $days_arrears < 61) {
                            $classification =trans_choice('general.especially_mentioned',1);
                            $provision_rate = \App\Models\ProvisionRate::find(2)->rate;
                            $provision = $provision_rate * $principal/ 100;
                            $total_provisioning_amount = $total_provisioning_amount + $provision;
                        } elseif ($days_arrears > 60 && $days_arrears < 91) {
                            $classification = trans_choice('general.substandard',1);
                            $provision_rate = \App\Models\ProvisionRate::find(3)->rate;
                            $provision = $provision_rate * $principal/ 100;
                            $total_provisioning_amount = $total_provisioning_amount + $provision;
                        } elseif ($days_arrears > 90 && $days_arrears < 181) {
                            $classification = trans_choice('general.doubtful',1);
                            $provision_rate = \App\Models\ProvisionRate::find(4)->rate;
                            $provision = $provision_rate * $principal/ 100;
                            $total_provisioning_amount = $total_provisioning_amount + $provision;
                        } elseif ($days_arrears > 180) {
                            $classification = trans_choice('general.loss',1);
                            $provision_rate = \App\Models\ProvisionRate::find(5)->rate;
                            $provision = $provision_rate * $principal/ 100;
                            $total_provisioning_amount = $total_provisioning_amount + $provision;
                        }

                        ?>

                        <tr>
                            <td>
                                @if(!empty($key->loan_officer))
                                    <a href="{{url('user/'.$key->loan_officer_id.'/show')}}">{{$key->loan_officer->first_name}} {{$key->loan_officer->last_name}}</a>
                                @endif
                            </td>
                            <td>
                                @if(!empty($key->borrower))
                                    <a href="{{url('borrower/'.$key->borrower_id.'/show')}}">{{$key->borrower->first_name}} {{$key->borrower->last_name}}</a>
                                @endif
                            </td>
                            <td><a href="{{url('loan/'.$key->id.'/show')}}">{{$key->id}}</a></td>
                            <td>
                                @if(!empty($key->loan_product))
                                    {{$key->loan_product->name}}
                                @endif
                            </td>
                            <td>{{number_format($key->principal,2)}}</td>
                            <td>{{$key->release_date}}</td>
                            <td>{{$key->maturity_date}}</td>
                            <td>{{number_format($principal,2)}}</td>
                            <td>{{number_format($interest,2)}}</td>
                            <td>{{number_format($fees,2)}}</td>
                            <td>{{number_format($penalty,2)}}</td>
                            <td>{{number_format($due,2)}}</td>
                            <td>{{$days_arrears}}</td>
                            <td>{{number_format($arrears,2)}}</td>
                            <td>{{number_format($provision_rate,2)}}</td>
                            <td>{{number_format($provision,2)}}</td>
                            <td>{{$classification}}</td>
                        </tr>

                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>{{number_format($total_amount,2)}}</th>
                        <th></th>
                        <th></th>
                        <th>{{number_format($total_principal,2)}}</th>
                        <th>{{number_format($total_interest,2)}}</th>
                        <th>{{number_format($total_fees,2)}}</th>
                        <th>{{number_format($total_penalty,2)}}</th>
                        <th>{{number_format($total_outstanding,2)}}</th>
                        <th></th>
                        <th>{{number_format($total_arrears,2)}}</th>
                        <th></th>
                        <th>{{number_format($total_provisioning_amount,2)}}</th>
                        <th></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <script>
            $(document).ready(function () {
                $("body").addClass('sidebar-xs');
            });
        </script>
    @endif
@endsection
@section('footer-scripts')

@endsection
