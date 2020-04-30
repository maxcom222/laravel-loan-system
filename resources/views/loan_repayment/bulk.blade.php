@extends('layouts.master')
@section('title'){{trans_choice('general.add',1)}} {{trans_choice('general.bulk',1)}} {{trans_choice('general.repayment',2)}}
@endsection
@section('content')
    <script type="text/javascript">

        function updatesum() {
            var inputRepaymentAmountTotal = 0;

            for (var i = 1; i <= 20; i++) {
                var inputRepaymentAmount = document.getElementById("inputRepaymentAmount" + i).value;

                if (inputRepaymentAmount == "")
                    inputRepaymentAmount = 0;

                inputRepaymentAmountTotal = parseFloat(inputRepaymentAmountTotal) + parseFloat(inputRepaymentAmount) * 100;
            }
            document.getElementById("RepaymentAmountTotal").innerHTML = numberWithCommas((inputRepaymentAmountTotal / 100).toFixed(2));
        }
        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>
    {!! Form::open(array('url' => url('repayment/bulk/store'), 'method' => 'post','id'=>'form', 'class' => 'form-horizontal')) !!}
    <input type="hidden" name="bulk_upload" value="1">

    <div class="panel panel-white">
        <div class="panel-body">
            <p>{{trans_choice('general.bulk_repayments_msg',1)}}</p>

            <table id="editrow" class="table table-striped table-condensed table-hover">
                <thead>
                <tr>
                    <th>{{trans_choice('general.row',1)}}</th>
                    <th>{{trans_choice('general.loan',1)}}</th>
                    <th>{{trans_choice('general.amount',1)}}</th>
                    <th>{{trans_choice('general.method',1)}}</th>
                    <th>{{trans_choice('general.receipt',1)}}</th>
                    <th>{{trans_choice('general.collection',1)}} {{trans_choice('general.date',1)}}</th>
                    <th>{{trans_choice('general.description',1)}} ({{trans_choice('general.optional',1)}})</th>
                </tr>
                </thead>
                <tbody>
                <?php
                for ($count = 1; $count <= 20; $count++){
                ?>
                <tr>
                    <td>
                        {{$count}}
                    </td>
                    <td>
                        {{Form::select('loan_id'.$count,$loans,null,array('class'=>'form-control select2','id'=>'inputLoanId'.$count,'placeholder'=>''))}}
                    </td>
                    <td>
                        {!! Form::text('repayment_amount'.$count,null, array('class' => 'form-control touchspin', 'id'=>"inputRepaymentAmount".$count,'onKeyUp'=>'updatesum()')) !!}
                    </td>
                    <td>
                        {{Form::select('repayment_method_id'.$count,$repayment_methods,null,array('class'=>'form-control select2','id'=>'inputRepaymentMethodId'.$count,'style'=>''))}}
                        <small><a href="#" id="SetDefaultMethods">{{trans_choice('general.set_default',1)}}</a></small>
                        <script type="text/javascript">
                            $("#SetDefaultMethods").click(function () {
                                var inputRepaymentMethodId1 = $("#inputRepaymentMethodId1 option:selected").index() + 1;
                                for (var i = 2; i <= 20; i++) {
                                    $("#inputRepaymentMethodId" + i + " :nth-child(" + inputRepaymentMethodId1 + ")").prop("selected", true);
                                }
                            });
                        </script>
                    </td>
                    <td>
                        {!! Form::text('receipt'.$count,null, array('class' => 'form-control', 'id'=>"receipt".$count,'onKeyUp'=>'')) !!}
                    </td>
                    <td>
                        {!! Form::text('repayment_collected_date'.$count,date("Y-m-d"), array('class' => 'form-control date-picker', 'id'=>"inputRepaymentDate".$count,)) !!}

                        <small><a href="#" id="SetDefaultDates">Set Default</a></small>
                        <script type="text/javascript">
                            $("#SetDefaultDates").click(function () {
                                var inputRepaymentDate1 = document.getElementById("inputRepaymentDate1").value;
                                for (var i = 2; i <= 20; i++) {
                                    $("#inputRepaymentDate" + i).val(inputRepaymentDate1);
                                }
                            });
                        </script>
                    </td>
                    <td>
                        {!! Form::text('repayment_description'.$count,null, array('class' => 'form-control', 'id'=>"inputDescription".$count,)) !!}

                    </td>
                </tr>
                <?php

                }
                ?>

                <tr>
                    <td>&nbsp;</td>
                    <td class="text-bold text-right">
                        {{trans_choice('general.total',1)}}:
                    </td>
                    <td class="text-bold text-right">
                        <div id="RepaymentAmountTotal">0</div>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                </tbody>
            </table>
            <button type="submit" class="btn btn-info pull-right" class="btn btn-info pull-right"
                    data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please Wait. This can take a few minutes.">
                {{trans_choice('general.submit',1)}}
            </button>

            <script type="text/javascript">
                $('#form').on('submit', function (e) {

                    $(this).find('button[type=submit]').prop('disabled', true);
                    $('.btn').prop('disabled', true);
                    $('.btn').button('loading');
                    return true;
                });
            </script>
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.box -->
    {!! Form::close() !!}

@endsection

