@extends('layouts.master')
@section('title')
    {{trans_choice('general.loan',1)}} {{trans_choice('general.calculator',2)}}
@endsection
@section('content')
    <?php
    //determine interest rate
    $interest_rate = 0;
    if ($request->repayment_cycle == 'annually') {
        //return the interest per year
        if ($request->interest_period == 'year') {
            $interest_rate = $request->interest_rate;
        }
        if ($request->interest_period == 'month') {
            $interest_rate = $request->interest_rate * 12;
        }
        if ($request->interest_period == 'week') {
            $interest_rate = $request->interest_rate * 52;
        }
        if ($request->interest_period == 'day') {
            $interest_rate = $request->interest_rate * 365;
        }
    }
    if ($request->repayment_cycle == 'semi_annually') {
        //return the interest per semi annually
        if ($request->interest_period == 'year') {
            $interest_rate = $request->interest_rate / 2;
        }
        if ($request->interest_period == 'month') {
            $interest_rate = $request->interest_rate * 6;
        }
        if ($request->interest_period == 'week') {
            $interest_rate = $request->interest_rate * 26;
        }
        if ($request->interest_period == 'day') {
            $interest_rate = $request->interest_rate * 182.5;
        }
    }
    if ($request->repayment_cycle == 'quarterly') {
        //return the interest per quaterly

        if ($request->interest_period == 'year') {
            $interest_rate = $request->interest_rate / 4;
        }
        if ($request->interest_period == 'month') {
            $interest_rate = $request->interest_rate * 3;
        }
        if ($request->interest_period == 'week') {
            $interest_rate = $request->interest_rate * 13;
        }
        if ($request->interest_period == 'day') {
            $interest_rate = $request->interest_rate * 91.25;
        }
    }
    if ($request->repayment_cycle == 'bi_monthly') {
        //return the interest per bi-monthly
        if ($request->interest_period == 'year') {
            $interest_rate = $request->interest_rate / 6;
        }
        if ($request->interest_period == 'month') {
            $interest_rate = $request->interest_rate * 2;
        }
        if ($request->interest_period == 'week') {
            $interest_rate = $request->interest_rate * 8.67;
        }
        if ($request->interest_period == 'day') {
            $interest_rate = $request->interest_rate * 58.67;
        }

    }

    if ($request->repayment_cycle == 'monthly') {
        //return the interest per monthly

        if ($request->interest_period == 'year') {
            $interest_rate = $request->interest_rate / 12;
        }
        if ($request->interest_period == 'month') {
            $interest_rate = $request->interest_rate * 1;
        }
        if ($request->interest_period == 'week') {
            $interest_rate = $request->interest_rate * 4.33;
        }
        if ($request->interest_period == 'day') {
            $interest_rate = $request->interest_rate * 30.4;
        }
    }
    if ($request->repayment_cycle == 'weekly') {
        //return the interest per weekly

        if ($request->interest_period == 'year') {
            $interest_rate = $request->interest_rate / 52;
        }
        if ($request->interest_period == 'month') {
            $interest_rate = $request->interest_rate / 4;
        }
        if ($request->interest_period == 'week') {
            $interest_rate = $request->interest_rate;
        }
        if ($request->interest_period == 'day') {
            $interest_rate = $request->interest_rate * 7;
        }
    }
    if ($request->repayment_cycle == 'daily') {
        //return the interest per day

        if ($request->interest_period == 'year') {
            $interest_rate = $request->interest_rate / 365;
        }
        if ($request->interest_period == 'month') {
            $interest_rate = $request->interest_rate / 30.4;
        }
        if ($request->interest_period == 'week') {
            $interest_rate = $request->interest_rate / 7.02;
        }
        if ($request->interest_period == 'day') {
            $interest_rate = $request->interest_rate * 1;
        }
    }
    $interest_rate = $interest_rate / 100;
    $period = 0;
    if ($request->repayment_cycle == 'annually') {
        if ($request->loan_duration_type == 'year') {
            $period = ceil($request->loan_duration);
        }
        if ($request->loan_duration_type == 'month') {
            $period = ceil($request->loan_duration * 12);
        }
        if ($request->loan_duration_type == 'week') {
            $period = ceil($request->loan_duration * 52);
        }
        if ($request->loan_duration_type == 'day') {
            $period = ceil($request->loan_duration * 365);
        }
    }
    if ($request->repayment_cycle == 'semi_annually') {
        if ($request->loan_duration_type == 'year') {
            $period = ceil($request->loan_duration * 2);
        }
        if ($request->loan_duration_type == 'month') {
            $period = ceil($request->loan_duration * 6);
        }
        if ($request->loan_duration_type == 'week') {
            $period = ceil($request->loan_duration * 26);
        }
        if ($request->loan_duration_type == 'day') {
            $period = ceil($request->loan_duration * 182.5);
        }
    }
    if ($request->repayment_cycle == 'quarterly') {
        if ($request->loan_duration_type == 'year') {
            $period = ceil($request->loan_duration);
        }
        if ($request->loan_duration_type == 'month') {
            $period = ceil($request->loan_duration * 12);
        }
        if ($request->loan_duration_type == 'week') {
            $period = ceil($request->loan_duration * 52);
        }
        if ($request->loan_duration_type == 'day') {
            $period = ceil($request->loan_duration * 365);
        }
    }
    if ($request->repayment_cycle == 'bi_monthly') {

    }

    if ($request->repayment_cycle == 'monthly') {
        if ($request->loan_duration_type == 'year') {
            $period = ceil($request->loan_duration * 12);
        }
        if ($request->loan_duration_type == 'month') {
            $period = ceil($request->loan_duration);
        }
        if ($request->loan_duration_type == 'week') {
            $period = ceil($request->loan_duration * 4.3);
        }
        if ($request->loan_duration_type == 'day') {
            $period = ceil($request->loan_duration * 30.4);
        }
    }
    if ($request->repayment_cycle == 'weekly') {
        if ($request->loan_duration_type == 'year') {
            $period = ceil($request->loan_duration * 52);
        }
        if ($request->loan_duration_type == 'month') {
            $period = ceil($request->loan_duration * 4);
        }
        if ($request->loan_duration_type == 'week') {
            $period = ceil($request->loan_duration * 1);
        }
        if ($request->loan_duration_type == 'day') {
            $period = ceil($request->loan_duration * 7);
        }
    }
    if ($request->repayment_cycle == 'daily') {
        if ($request->loan_duration_type == 'year') {
            $period = ceil($request->loan_duration * 365);
        }
        if ($request->loan_duration_type == 'month') {
            $period = ceil($request->loan_duration * 30.42);
        }
        if ($request->loan_duration_type == 'week') {
            $period = ceil($request->loan_duration * 7.02);
        }
        if ($request->loan_duration_type == 'day') {
            $period = ceil($request->loan_duration);
        }
    }
    ?>
    <div class="box box-info">
        <div class="panel-body table-responsive no-padding">
            <table id="" class="table table-bordered table-condensed table-hover">
                <thead>
                <tr style="background-color: #D1F9FF">
                    <th>{{trans_choice('general.released',1)}}</th>
                    <th>{{trans_choice('general.maturity',1)}}</th>
                    <th>{{trans_choice('general.repayment',1)}}</th>
                    <th>{{trans_choice('general.principal',1)}}</th>
                    <th>{{trans_choice('general.interest',1)}}%</th>
                    <th>{{trans_choice('general.interest',1)}}</th>
                    <th>{{trans_choice('general.fee',2)}}</th>
                    <th>{{trans_choice('general.due',1)}}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{$request->release_date}}</td>
                    <td>{{date_format(date_add(date_create($request->first_payment_date),
            date_interval_create_from_date_string($period .' '.$request->repayment_cycle. ' s')),'Y-m-d')}}</td>
                    <td id="repayment">
                        @if($request->repayment_cycle=='daily')
                            {{trans_choice('general.daily',1)}}
                        @endif
                        @if($request->repayment_cycle=='weekly')
                            {{trans_choice('general.weekly',1)}}
                        @endif
                        @if($request->repayment_cycle=='monthly')
                            {{trans_choice('general.monthly',1)}}
                        @endif
                        @if($request->repayment_cycle=='bi_monthly')
                            {{trans_choice('general.bi_monthly',1)}}
                        @endif
                        @if($request->repayment_cycle=='quarterly')
                            {{trans_choice('general.quarterly',1)}}
                        @endif
                        @if($request->repayment_cycle=='semi_annual')
                            {{trans_choice('general.semi_annually',1)}}
                        @endif
                        @if($request->repayment_cycle=='annually')
                            {{trans_choice('general.annual',1)}}
                        @endif
                    </td>
                    <td>{{number_format($request->principal,2)}}</td>
                    <td>  {{number_format($request->interest_rate,2)}}%/{{$request->interest_period}}</td>
                    <td id="interest"></td>
                    <td id="fees">0</td>
                    <td id="due"></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script type="text/javascript">
        function updatesum() {
            var principalTotal = 0;
            var interestTotal = 0;
            var feesTotal = 0;
            var penaltyTotal = 0;
            var inputTotalDueAmountTotal = 0;

            for (var i = 0; i < '{{$period}}'; i++) {
                var principal = document.getElementById("principal" + i).value;
                var interest = document.getElementById("interest" + i).value;
                var fees = document.getElementById("fees" + i).value;
                var penalty = document.getElementById("penalty" + i).value;

                if (principal == "")
                    principal = 0;
                if (interest == "")
                    interest = 0;
                if (fees == "")
                    fees = 0;
                if (penalty == "")
                    penalty = 0;

                var totaldue = parseFloat(principal) + parseFloat(interest) + parseFloat(fees) + parseFloat(penalty);
                document.getElementById("due" + i).value = Math.floor(totaldue * 100) / 100;

                principalTotal = parseFloat(principalTotal) + parseFloat(principal) * 100;
                interestTotal = parseFloat(interestTotal) + parseFloat(interest) * 100;
                feesTotal = parseFloat(feesTotal) + parseFloat(fees) * 100;
                penaltyTotal = parseFloat(penaltyTotal) + parseFloat(penalty) * 100;

                inputTotalDueAmountTotal = parseFloat(inputTotalDueAmountTotal) + parseFloat(totaldue) * 100;
            }
            document.getElementById("principalTotal").value = Math.floor(principalTotal * 100) / 10000;
            document.getElementById("interestTotal").value = Math.floor(interestTotal * 100) / 10000;
            document.getElementById("feesTotal").value = Math.floor(feesTotal * 100) / 10000;
            document.getElementById("penaltyTotal").value = Math.floor(penaltyTotal * 100) / 10000;
            document.getElementById("inputTotalDueAmountTotal").value = Math.floor(inputTotalDueAmountTotal * 100) / 10000;

            var total_principal_amount = 0;
            var pending_balance = 0;
            var principalTotal = document.getElementById("principalTotal").value;
            for (var i = 0; i < '{{$period}}'; i++) {
                var principal = document.getElementById("principal" + i).value;
                total_principal_amount = (parseFloat(total_principal_amount) + parseFloat(principal));
                pending_balance = parseFloat(principalTotal) - parseFloat(total_principal_amount);
                document.getElementById("principal_balance" + i).value = Math.ceil(pending_balance * 100) / 100;
            }

        }
    </script>
    <div class="box box-info">
        <div class="panel-heading">
            <h3 class="panel-title">{{trans_choice('general.edit',1)}} {{trans_choice('general.schedule',1)}}</h3>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('loan/loan_calculator/store'), 'method' => 'post','target'=>'_blank', 'class' => 'form-horizontal')) !!}
        <input type="hidden" name="release_date" value="{{$request->release_date}}">
        <input type="hidden" name="maturity_date" value="{{date_format(date_add(date_create($request->first_payment_date),
            date_interval_create_from_date_string($period .' '.$request->repayment_cycle. ' s')),'Y-m-d')}}">
        <input type="hidden" name="repayment_cycle" id="repayment_cycle" value="{{$request->repayment_cycle}}">
        <input type="hidden" name="interest_rate" id="interest_rate" value="{{$request->interest_rate}}">
        <input type="hidden" name="release_date" value="{{$request->release_date}}">
        <input type="hidden" name="interest_period" value="{{$request->interest_period}}">
        <input type="hidden" name="first_payment_date" value="{{$request->first_payment_date}}">
        <input type="hidden" name="principal_amount" value="{{$request->principal}}">
        <input type="hidden" name="total_interest" id="total_interest_field" value="">
        <input type="hidden" name="total_due" id="total_due_field" value="">

        <div class="panel-body table-responsive no-padding">

            <table class="table table-bordered table-condensed  table-hover">
                <thead>
                <tr class="bg-gray">
                    <th>#</th>
                    <th>{{trans_choice('general.due',1)}} {{trans_choice('general.date',1)}}</th>
                    <th>{{trans_choice('general.principal',1)}} {{trans_choice('general.amount',1)}}</th>
                    <th></th>
                    <th>{{trans_choice('general.interest',1)}} {{trans_choice('general.amount',1)}}</th>
                    <th></th>
                    <th>{{trans_choice('general.fee',2)}} {{trans_choice('general.amount',1)}}</th>
                    <th></th>
                    <th>{{trans_choice('general.penalty',1)}} {{trans_choice('general.amount',1)}}</th>
                    <th></th>
                    <th>{{trans_choice('general.due',1)}} {{trans_choice('general.amount',1)}}</th>
                    <th>{{trans_choice('general.principal',1)}} {{trans_choice('general.balance',1)}}</th>
                    <th>{{trans_choice('general.description',1)}}</th>
                </tr>
                </thead>
                <tbody>
                <input type="hidden" name="count" class="form-control"
                       value="{{$period}}">
                <?php
                $count = 0;
                $principal_balance = $request->principal;
                $balance = $principal_balance;
                $total_principal = 0;
                $total_interest = 0;
                $total_due = 0;
                $next_payment = $request->first_payment_date;
                $due = ($interest_rate * $principal_balance * pow((1 + $interest_rate),
                                        $period)) / (pow((1 + $interest_rate),
                                        $period) - 1);
                for ($i = 1; $i <= $period; $i++) {
                if ($request->interest_method == 'declining_balance_equal_installments') {
                    if ($request->decimal_places == 'round_off_to_two_decimal') {
                        //determine if we have grace period for interest

                        $interest = round(($interest_rate * $balance), 2);
                        $principal = round(($due - $interest), 2);
                        if ($request->grace_on_interest_charged >= $i) {
                            $interest = 0;
                        } else {
                            $interest = round($interest, 2);
                        }
                        $due = round($due, 2);
                        //determine next balance
                        $balance = round(($balance - $principal), 2);
                        $principal_balance = round($balance, 2);
                    } else {
                        //determine if we have grace period for interest

                        $interest = round(($interest_rate * $balance));
                        $principal = round(($due - $interest));
                        if ($request->grace_on_interest_charged >= $i) {
                            $interest = 0;
                        } else {
                            $interest = round($interest);
                        }
                        $due = round($due);
                        //determine next balance
                        $balance = round(($balance - $principal));
                        $principal_balance = round($balance);
                    }


                }
                //reducing balance equal principle
                if ($request->interest_method == 'declining_balance_equal_principal') {
                    $principal = $request->principal / $period;
                    if ($request->decimal_places == 'round_off_to_two_decimal') {

                        $interest = round(($interest_rate * $balance), 2);
                        $principal = round($principal, 2);
                        if ($request->grace_on_interest_charged >= $i) {
                            $interest = 0;
                        } else {
                            $interest = round($interest, 2);
                        }
                        $due = round(($principal + $interest), 2);
                        //determine next balance
                        $balance = round(($balance - ($principal + $interest)), 2);
                        $principal_balance = round($balance, 2);
                    } else {

                        $principal = round(($principal));

                        $interest = round(($interest_rate * $balance));
                        if ($request->grace_on_interest_charged >= $i) {
                            $interest = 0;
                        } else {
                            $interest = round($interest);
                        }
                        $due = round($principal + $interest);
                        //determine next balance
                        $balance = round(($balance - ($principal + $interest)));
                        $principal_balance = round($balance);
                    }

                }
                //flat  method
                if ($request->interest_method == 'flat_rate') {
                    $principal = $request->principal / $period;
                    if ($request->decimal_places == 'round_off_to_two_decimal') {
                        $interest = round(($interest_rate * $request->principal), 2);
                        $principal = round(($principal), 2);
                        if ($request->grace_on_interest_charged >= $i) {
                            $interest = 0;
                        } else {
                            $interest = round($interest, 2);
                        }
                        $principal = round(($principal), 2);
                        $due = round(($principal + $interest), 2);
                        //determine next balance
                        $balance = round(($balance - $principal), 2);
                        $principal_balance = round($balance, 2);
                    } else {
                        $interest = round(($interest_rate * $request->principal));
                        if ($request->grace_on_interest_charged >= $i) {
                            $interest = 0;
                        } else {
                            $interest = round($interest);
                        }
                        $principal = round($principal);
                        $due = round($principal + $interest);
                        //determine next balance
                        $balance = round(($balance - $principal));
                        $principal_balance = round($balance);
                    }
                }
                //interest only method
                if ($request->interest_method == 'interest_only') {
                    if ($i == $period) {
                        $principal = $request->principal;
                    } else {
                        $principal = 0;
                    }
                    if ($request->decimal_places == 'round_off_to_two_decimal') {
                        $interest = round(($interest_rate * $request->principal), 2);
                        if ($request->grace_on_interest_charged >= $i) {
                            $interest = 0;
                        } else {
                            $interest = round($interest, 2);
                        }
                        $principal = round(($principal), 2);
                        $due = round(($principal + $interest), 2);
                        //determine next balance
                        $balance = round(($balance - $principal), 2);
                        $principal_balance = round($balance, 2);
                    } else {
                        $interest = round(($interest_rate * $request->principal));
                        if ($request->grace_on_interest_charged >= $i) {
                            $interest = 0;
                        } else {
                            $interest = round($interest);
                        }
                        $principal = round($principal);
                        $due = round($principal + $interest);
                        //determine next balance
                        $balance = round(($balance - $principal));
                        $principal_balance = round($balance);
                    }
                }
                $due_date = $next_payment;
                ?>
                <tr>
                    <td>
                        {{$count+1}}<input type="hidden" name="collection_idArray[{{$count}}]" class="form-control"
                                           id="inputCollectionId" value="{{$count}}">
                    </td>
                    <td>
                        {!! Form::text('due_date['.$count.']',$due_date, array('class' => 'form-control date-picker-schedule','data-date-start-date'=>$request->release_date, 'id'=>"due_date".$count)) !!}
                    </td>
                    <td>
                        {!! Form::text('principal['.$count.']',round($principal,2), array('class' => 'form-control','onkeyup'=>"updatesum()",'onkeypress'=>"return isDecimalKey(this,event)", 'id'=>"principal".$count)) !!}
                    </td>
                    <td>+</td>
                    <td>
                        {!! Form::text('interest['.$count.']',round($interest,2), array('class' => 'form-control','onkeyup'=>"updatesum()",'onkeypress'=>"return isDecimalKey(this,event)", 'id'=>"interest".$count)) !!}
                    </td>
                    <td>+</td>
                    <td>
                        {!! Form::text('fees['.$count.']','0', array('class' => 'form-control','onkeyup'=>"updatesum()",'onkeypress'=>"return isDecimalKey(this,event)", 'id'=>"fees".$count)) !!}
                    </td>
                    <td>+</td>
                    <td>
                        {!! Form::text('penalty['.$count.']','0', array('class' => 'form-control','onkeyup'=>"updatesum()",'onkeypress'=>"return isDecimalKey(this,event)", 'id'=>"penalty".$count)) !!}
                    </td>
                    <td>=</td>
                    <td>
                        {!! Form::text('due['.$count.']',round(($principal+$interest),2), array('class' => 'form-control', 'id'=>"due".$count,'readonly'=>"")) !!}
                    </td>
                    <td>
                        {!! Form::text('principal_balance['.$count.']',round($principal_balance,2), array('class' => 'form-control','id'=>"principal_balance".$count,'readonly'=>"")) !!}
                    </td>
                    <td>
                        {!! Form::text('description['.$count.']',trans_choice('general.repayment',1), array('class' => 'form-control','id'=>"description".$count,'required'=>'')) !!}
                    </td>
                </tr>
                <?php
                //determine next due date
                if ($request->repayment_cycle == 'daily') {
                    $next_payment = date_format(date_add(date_create($next_payment),
                            date_interval_create_from_date_string('1 days')),
                            'Y-m-d');
                    $due_date = $next_payment;
                }
                if ($request->repayment_cycle == 'weekly') {
                    $next_payment = date_format(date_add(date_create($next_payment),
                            date_interval_create_from_date_string('1 weeks')),
                            'Y-m-d');
                    $due_date = $next_payment;
                }
                if ($request->repayment_cycle == 'monthly') {
                    $next_payment = date_format(date_add(date_create($next_payment),
                            date_interval_create_from_date_string('1 months')),
                            'Y-m-d');
                    $due_date = $next_payment;
                }
                if ($request->repayment_cycle == 'bi_monthly') {
                    $next_payment = date_format(date_add(date_create($request->first_payment_date),
                            date_interval_create_from_date_string($period . ' months')),
                            'Y-m-d');
                    $due_date = $next_payment;
                }
                if ($request->repayment_cycle == 'quarterly') {
                    $next_payment = date_format(date_add(date_create($next_payment),
                            date_interval_create_from_date_string('2 months')),
                            'Y-m-d');
                    $due_date = $next_payment;
                }
                if ($request->repayment_cycle == 'semi_annually') {
                    $next_payment = date_format(date_add(date_create($next_payment),
                            date_interval_create_from_date_string('6 months')),
                            'Y-m-d');
                    $due_date = $next_payment;
                }
                if ($request->repayment_cycle == 'yearly') {
                    $next_payment = date_format(date_add(date_create($next_payment),
                            date_interval_create_from_date_string('1 years')),
                            'Y-m-d');
                    $due_date = $next_payment;
                }
                if ($i == $period) {
                    $principal_balance = round($balance);
                }
                $total_principal = $total_principal + $principal;
                $total_interest = $interest + $total_interest;
                $total_due = $total_due + $interest + $principal;
                $count++;
                }
                ?>
                <tr>
                    <td>
                    </td>
                    <td>
                        <input type="text" class="form-control" value="Total" readonly="">
                    </td>
                    <td>
                        <input type="text" name="principalTotal" class="form-control"
                               id="principalTotal"
                               value="{{round($total_principal,2)}}"
                               readonly="">
                    </td>
                    <td>+</td>
                    <td>
                        <input type="text" name="interestTotal" class="form-control"
                               id="interestTotal"
                               value="{{round($total_interest,2)}}"
                               readonly="">
                    </td>
                    <td>+</td>
                    <td>
                        <input type="text" name="feesTotal" class="form-control"
                               id="feesTotal"
                               value="0"
                               readonly="">
                    </td>
                    <td>+</td>
                    <td>
                        <input type="text" name="penaltyTotal" class="form-control"
                               id="penaltyTotal"
                               value="0"
                               readonly="">
                    </td>
                    <td>=</td>
                    <td>
                        <input type="text" name="inputTotalDueAmountTotal" class="form-control"
                               id="inputTotalDueAmountTotal"
                               value="{{round($total_due,2)}}"
                               readonly="">
                    </td>
                    <td>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="row margin">
                <button type="submit" name="pdf" value="submit"
                        class="btn btn-info pull-right margin">{{trans_choice('general.download',1)}} {{trans_choice('general.pdf',1)}}
                </button>
                <button type="submit" name="print" value="submit"
                        class="btn btn-success pull-right margin"> {{trans_choice('general.print',1)}}
                </button>
            </div>
            <a type="button" class="btn btn-default pull-left margin"
               href="{{url('loan/loan_calculator/create')}}">
                {{trans_choice('general.back',1)}}
            </a>


        </div>
        <!-- /.panel-body -->
        {!! Form::close() !!}
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
        $('.date-picker-schedule').datepicker({
            orientation: "left",
            autoclose: true,
            format: "yyyy-mm-dd",
            startDate: '{!!$request->release_date!!}'
        });
    </script>
    <script>
        $(document).ready(function (e) {
            $('#interest').text("{!! $total_interest !!}");
            $('#due').text("{!! $total_due !!}")
            $('#total_interest_field').val("{!! $total_interest !!}")
            $('#total_due_field').val("{!! $total_due !!}")
            $('#repayment_cycle').val($('#repayment').text())
        })
    </script>
@endsection