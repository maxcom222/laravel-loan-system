@extends('layouts.master')
@section('title')
    {{trans_choice('general.edit',1)}} {{trans_choice('general.schedule',1)}}
@endsection
@section('content')

    <script type="text/javascript">
        function updatesum() {
            var principalTotal = 0;
            var interestTotal = 0;
            var feesTotal = 0;
            var penaltyTotal = 0;
            var inputTotalDueAmountTotal = 0;

            for (var i = 0; i < '{{\App\Models\LoanSchedule::where('loan_id',$loan->id)->count()+$rows}}'; i++) {
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
            for (var i = 0; i < '{{\App\Models\LoanSchedule::where('loan_id',$loan->id)->count()+$rows}}'; i++) {
                var principal = document.getElementById("principal" + i).value;
                total_principal_amount = (parseFloat(total_principal_amount) + parseFloat(principal));
                pending_balance = parseFloat(principalTotal) - parseFloat(total_principal_amount);
                document.getElementById("principal_balance" + i).value = Math.ceil(pending_balance * 100) / 100;
            }

        }
    </script>
    <div class="box box-info">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.edit',1)}} {{trans_choice('general.schedule',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('loan/'.$loan->id.'/schedule/update'), 'method' => 'post', 'class' => 'form-horizontal')) !!}

        <div class="panel-body table-responsive no-padding">
            <ol>
                <li><b>Due Date</b> can not be less than the Loan Release Date of <b>{{$loan->release_date}}</b>
                </li>
                <li>Description is optional</li>
                <li>If you have set automated loan penalties and you see <b>System Generated Penalty</b> rows
                    below, you can delete them by visiting <b>Edit Loan</b>, checking <b>Overriding System
                        Generated Penalties</b> field and putting 0 in <b>Manual Penalty Amount</b></li>
            </ol>
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
                       value="{{\App\Models\LoanSchedule::where('loan_id',$loan->id)->count()+$rows}}">
                <?php
                $count = 0;
                $principal_balance = \App\Models\LoanSchedule::where('loan_id',
                        $loan->id)->sum('principal');

                foreach ($schedules as $schedule) {
                $principal_balance = $principal_balance - $schedule->principal;
                ?>
                <tr>
                    <td>
                        {{$count+1}}<input type="hidden" name="collection_idArray[{{$count}}]" class="form-control"
                                           id="inputCollectionId" value="{{$count}}">
                    </td>
                    <td>
                        {!! Form::text('due_date['.$count.']',$schedule->due_date, array('class' => 'form-control date-picker-schedule','data-date-start-date'=>$loan->release_date, 'id'=>"due_date".$count)) !!}
                    </td>
                    <td>
                        {!! Form::text('principal['.$count.']',round($schedule->principal,2), array('class' => 'form-control','onkeyup'=>"updatesum()",'onkeypress'=>"return isDecimalKey(this,event)", 'id'=>"principal".$count)) !!}
                    </td>
                    <td>+</td>
                    <td>
                        {!! Form::text('interest['.$count.']',round($schedule->interest,2), array('class' => 'form-control','onkeyup'=>"updatesum()",'onkeypress'=>"return isDecimalKey(this,event)", 'id'=>"interest".$count)) !!}
                    </td>
                    <td>+</td>
                    <td>
                        {!! Form::text('fees['.$count.']',round($schedule->fees,2), array('class' => 'form-control','onkeyup'=>"updatesum()",'onkeypress'=>"return isDecimalKey(this,event)", 'id'=>"fees".$count)) !!}
                    </td>
                    <td>+</td>
                    <td>
                        {!! Form::text('penalty['.$count.']',round($schedule->penalty,2), array('class' => 'form-control','onkeyup'=>"updatesum()",'onkeypress'=>"return isDecimalKey(this,event)", 'id'=>"penalty".$count)) !!}
                    </td>
                    <td>=</td>
                    <td>
                        {!! Form::text('due['.$count.']',round(($schedule->principal+$schedule->interest+$schedule->fees+$schedule->penalty),2), array('class' => 'form-control', 'id'=>"due".$count,'readonly'=>"")) !!}
                    </td>
                    <td>
                        {!! Form::text('principal_balance['.$count.']',round($principal_balance,2), array('class' => 'form-control','id'=>"principal_balance".$count,'readonly'=>"")) !!}
                    </td>
                    <td>
                        {!! Form::text('description['.$count.']',$schedule->description, array('class' => 'form-control','id'=>"description".$count,'required'=>'')) !!}
                    </td>
                </tr>
                <?php
                $count++;
                }
                if ($rows > 0) {
                for ($i = 1; $i <= $rows; $i++) {
                ?>
                <tr>
                    <td>
                        {{$count+1}}<input type="hidden" name="collection_idArray[{{$count}}]" class="form-control"
                                           id="inputCollectionId" value="{{$count}}">
                    </td>
                    <td>
                        {!! Form::text('due_date['.$count.']',null, array('class' => 'form-control date-picker-schedule','data-date-start-date'=>$loan->release_date, 'id'=>"due_date".$count,'placeholder'=>'yyyy-mm-dd')) !!}
                    </td>
                    <td>
                        {!! Form::text('principal['.$count.']',null, array('class' => 'form-control','onkeyup'=>"updatesum()",'onkeypress'=>"return isDecimalKey(this,event)", 'id'=>"principal".$count,'placeholder'=>'Principal Amount')) !!}
                    </td>
                    <td>+</td>
                    <td>
                        {!! Form::text('interest['.$count.']',null, array('class' => 'form-control','onkeyup'=>"updatesum()",'onkeypress'=>"return isDecimalKey(this,event)", 'id'=>"interest".$count,'placeholder'=>'Interest Amount')) !!}
                    </td>
                    <td>+</td>
                    <td>
                        {!! Form::text('fees['.$count.']',null, array('class' => 'form-control','onkeyup'=>"updatesum()",'onkeypress'=>"return isDecimalKey(this,event)", 'id'=>"fees".$count,'placeholder'=>'Fees Amount')) !!}
                    </td>
                    <td>+</td>
                    <td>
                        {!! Form::text('penalty['.$count.']',null, array('class' => 'form-control','onkeyup'=>"updatesum()",'onkeypress'=>"return isDecimalKey(this,event)", 'id'=>"penalty".$count,'placeholder'=>'Penalty Amount')) !!}
                    </td>
                    <td>=</td>
                    <td>
                        {!! Form::text('due['.$count.']',0, array('class' => 'form-control', 'id'=>"due".$count,'required'=>'required','readonly'=>"")) !!}
                    </td>
                    <td>
                        {!! Form::text('principal_balance['.$count.']',null, array('class' => 'form-control','id'=>"principal_balance".$count,'readonly'=>"")) !!}
                    </td>
                    <td>
                        {!! Form::text('description['.$count.']',null, array('class' => 'form-control','id'=>"description".$count,'required'=>'','placeholder'=>'Description')) !!}
                    </td>
                </tr>
                <?php
                $count++;
                }
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
                               value="{{round(\App\Helpers\GeneralHelper::loan_total_principal($loan->id),2)}}"
                               readonly="">
                    </td>
                    <td>+</td>
                    <td>
                        <input type="text" name="interestTotal" class="form-control"
                               id="interestTotal"
                               value="{{round(\App\Helpers\GeneralHelper::loan_total_interest($loan->id),2)}}"
                               readonly="">
                    </td>
                    <td>+</td>
                    <td>
                        <input type="text" name="feesTotal" class="form-control"
                               id="feesTotal"
                               value="{{round(\App\Helpers\GeneralHelper::loan_total_fees($loan->id),2)}}"
                               readonly="">
                    </td>
                    <td>+</td>
                    <td>
                        <input type="text" name="penaltyTotal" class="form-control"
                               id="penaltyTotal"
                               value="{{round(\App\Helpers\GeneralHelper::loan_total_penalty($loan->id),2)}}"
                               readonly="">
                    </td>
                    <td>=</td>
                    <td>
                        <input type="text" name="inputTotalDueAmountTotal" class="form-control"
                               id="inputTotalDueAmountTotal"
                               value="{{round(\App\Helpers\GeneralHelper::loan_total_due_amount($loan->id),2)}}"
                               readonly="">
                    </td>
                    <td>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="row margin">
                <button type="submit" name="submit" value="submit"
                        class="btn btn-info pull-right">{{trans_choice('general.submit',1)}}
                </button>
            </div>
            <a type="button" class="btn btn-default pull-left margin"
               href="{{url('loan/'.$loan->id.'/show')}}">
                {{trans_choice('general.back',1)}}
            </a>
            <button type="submit" name="submit" value="add_row"
                    class="btn btn-default  pull-right margin">{{trans_choice('general.add',1)}}
                {{trans_choice('general.row',1)}}
            </button>

            <div class="col-sm-1 pull-right">
                <select name="addrows" id="AddRows" class="form-control margin">
                    <?php
                    $count = 1;
                    while ($count <= 100) {
                        echo '<option value="' . $count . '">' . $count . '</option>';
                        $count++;
                    }
                    ?>
                </select>
            </div>

        </div>
        <!-- /.panel-body -->
        {!! Form::close() !!}
    </div>
@endsection
@section('footer-scripts')
    <script>
        $('.date-picker-schedule').datepicker({
            orientation: "left",
            autoclose: true,
            format: "yyyy-mm-dd",
            startDate: '{!!$loan->release_date!!}'
        });
    </script>
@endsection