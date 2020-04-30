@extends('layouts.master')
@section('title'){{trans_choice('general.add',1)}} {{trans_choice('general.payroll',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">{{trans_choice('general.add',1)}} {{trans_choice('general.payroll',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        {!! Form::open(array('url' => url('payroll/'.$payroll->id.'/update'), 'method' => 'post', 'name' => 'form',"enctype"=>"multipart/form-data")) !!}
        <input type="hidden" name="template_id" value="{{$template->id}}">

        <div class="panel-body">
            <div class="form-group">
                {!! Form::label('user_id',trans_choice('general.staff',1),array('class'=>'')) !!}
                {!! Form::select('user_id',$user,$payroll->user_id, array('class' => 'form-control select2','id'=>'user_id','placeholder'=>'Select')) !!}
            </div>

            <div class="">
                <table width="100%">
                    <tbody>
                    <tr>
                        <td style="padding-bottom:10px;">
                            <table width="100%" class="borderOk">
                                <tbody>
                                <tr>
                                    <td style="vertical-align: top;" width="50%">

                                        <table width="100%" id="payslip_employee_header">
                                            <tbody>
                                            <tr>
                                                <td width="50%"
                                                    class="cell_format">{{trans_choice('general.employee',1)}} {{trans_choice('general.name',1)}}</td>
                                                <td width="50%" class="cell_format">
                                                    <div class="margin text-bold">
                                                        {!! Form::text('employee_name',$payroll->employee_name, array('class' => 'form-control', 'id'=>"employee_name",'required'=>"required")) !!}
                                                    </div>
                                                </td>
                                            </tr>
                                            @foreach($top_left as $key)
                                                <tr>
                                                    <td width="50%" class="cell_format">{{$key->name}}</td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                            <input type="text" name="{{$key->id}}"
                                                                   value="@if(\App\Models\PayrollMeta::where('payroll_template_meta_id',$key->id)->where('payroll_id',$payroll->id)->first()) {{\App\Models\PayrollMeta::where('payroll_template_meta_id',$key->id)->where('payroll_id',$payroll->id)->first()->value}} @endif"
                                                                   class="form-control">
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </td>

                                    <td style="vertical-align: top" width="50%">

                                        <table width="100%" id="pay_period_and_salary">

                                            <tbody>
                                            <tr>
                                                <td width="50%" class="cell_format">
                                                    <div class="margin">
                                                        <b>{{trans_choice('general.payroll',1)}} {{trans_choice('general.date',1)}}</b>
                                                    </div>
                                                </td>
                                                <td width="50%" class="cell_format">
                                                    <div class="margin text-bold">
                                                        {!! Form::text('date',$payroll->date, array('class' => 'form-control date-picker', 'required'=>"required")) !!}
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="50%"
                                                    class="cell_format">{{trans_choice('general.business',1)}} {{trans_choice('general.name',1)}}</td>
                                                <td width="50%" class="cell_format">
                                                    <div class="margin text-bold">
                                                        {!! Form::text('business_name',$payroll->business_name, array('class' => 'form-control', 'id'=>"business_name",'required'=>"required")) !!}
                                                    </div>
                                                </td>
                                            </tr>
                                            @foreach($top_right as $key)
                                                <tr>
                                                    <td width="50%" class="cell_format">{{$key->name}}</td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                            <input type="text" name="{{$key->id}}"
                                                                   value="@if(\App\Models\PayrollMeta::where('payroll_template_meta_id',$key->id)->where('payroll_id',$payroll->id)->first()) {{\App\Models\PayrollMeta::where('payroll_template_meta_id',$key->id)->where('payroll_id',$payroll->id)->first()->value}} @endif"
                                                                   class="form-control">
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <!--Pay Period and Salary-->
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <table width="100%" class="borderOk">
                                <tbody>
                                <tr>
                                    <td style="vertical-align: top" width="50%" class="borderRight">

                                        <table width="100%" id="hours_and_earnings">
                                            <tbody>
                                            <tr>
                                                <td width="50%" class="bg-navy">
                                                    <b>{{trans_choice('general.description',1)}}</b></td>
                                                <td width="50%" class="bg-navy">
                                                    <b>{{trans_choice('general.amount',1)}}</b></td>
                                            </tr>
                                            <?php
                                            $count = 0;
                                            foreach($bottom_left as $key){
                                            ?>
                                            <tr>
                                                <td width="50%" class="cell_format">{{$key->name}}</td>
                                                <td width="50%" class="cell_format">
                                                    <div class="margin text-bold">
                                                        <input type="text" onkeyup="refresh_totals()"
                                                               name="{{$key->id}}"
                                                               value="@if(\App\Models\PayrollMeta::where('payroll_template_meta_id',$key->id)->where('payroll_id',$payroll->id)->first()) {{\App\Models\PayrollMeta::where('payroll_template_meta_id',$key->id)->where('payroll_id',$payroll->id)->first()->value}} @endif"
                                                               class="form-control" id="bottom_left{{$count}}">
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                            $count++;
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                        <!--Hours and Earnings-->
                                    </td>

                                    <td width="50%" valign="top">
                                        <table width="100%" id="pre_tax_deductions">
                                            <tbody>
                                            <tr>
                                                <td width="50%" class="bg-navy">
                                                    <b>{{trans_choice('general.description',1)}}</b></td>
                                                <td width="50%" class="bg-navy">
                                                    <b>{{trans_choice('general.amount',1)}}</b></td>
                                            </tr>
                                            <?php
                                            $count = 0;
                                            foreach($bottom_right as $key){
                                            ?>
                                            <tr>
                                                <td width="50%" class="cell_format">{{$key->name}}</td>
                                                <td width="50%" class="cell_format">
                                                    <div class="margin text-bold">
                                                        <input type="text" onkeyup="refresh_totals()"
                                                               name="{{$key->id}}"
                                                               value="@if(\App\Models\PayrollMeta::where('payroll_template_meta_id',$key->id)->where('payroll_id',$payroll->id)->first()) {{\App\Models\PayrollMeta::where('payroll_template_meta_id',$key->id)->where('payroll_id',$payroll->id)->first()->value}} @endif"
                                                               class="form-control" id="bottom_right{{$count}}">
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                            $count++;
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                        <!--Pre-Tax Deductions-->
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" class="bg-gray">
                                        <table width="100%" id="gross_pay">
                                            <tbody>
                                            <tr>
                                                <td width="50%" class="cell_format">
                                                    <div class="margin">
                                                        <b>{{trans_choice('general.total',1)}} {{trans_choice('general.pay',1)}}</b>
                                                    </div>
                                                </td>
                                                <td width="50%" class="cell_format">
                                                    <div class="margin text-bold">
                                                        {!! Form::text('total_pay',\App\Helpers\GeneralHelper::single_payroll_total_pay($payroll->id), array('class' => 'form-control', 'readonly'=>"",'id'=>'total_pay')) !!}
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>

                                    </td>
                                    <td width="50%" class="bg-gray">

                                        <table width="100%" id="gross_pay">
                                            <tbody>
                                            <tr>
                                                <td width="50%" class="cell_format">
                                                    <div class="margin">
                                                        <b>{{trans_choice('general.total',1)}} {{trans_choice('general.deduction',2)}}</b>
                                                    </div>
                                                </td>
                                                <td width="50%" class="cell_format">
                                                    <div class="margin text-bold">
                                                        {!! Form::text('total_deductions',\App\Helpers\GeneralHelper::single_payroll_total_deductions($payroll->id), array('class' => 'form-control', 'readonly'=>"",'id'=>'total_deductions')) !!}
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%">
                                        <br>
                                    </td>
                                    <td width="50%" class="bg-gray">
                                        <table width="100%" id="gross_pay">
                                            <tbody>
                                            <tr>
                                                <td width="50%" class="cell_format">
                                                    <div class="margin">
                                                        <b>{{trans_choice('general.net',1)}} {{trans_choice('general.pay',1)}}</b>
                                                    </div>
                                                </td>
                                                <td width="50%" class="cell_format">
                                                    <div class="margin text-bold">
                                                        {!! Form::text('net_pay',(\App\Helpers\GeneralHelper::single_payroll_total_pay($payroll->id)-\App\Helpers\GeneralHelper::single_payroll_total_deductions($payroll->id)), array('class' => 'form-control', 'readonly'=>"",'id'=>'net_pay')) !!}
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top:10px;">
                            <table width="100%" class="borderOk" id="net_pay_distribution">
                                <tbody>
                                <tr>
                                    <td colspan="5" class="bg-navy">
                                        <b>{{trans_choice('general.net_pay_distribution',1)}}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="20%" class="cell_format">
                                        <div class="margin">
                                            <b>{{trans_choice('general.payment',1)}} {{trans_choice('general.method',1)}}</b>
                                        </div>
                                    </td>
                                    <td width="20%" class="cell_format">
                                        <div class="margin">
                                            <b>{{trans_choice('general.bank',1)}} {{trans_choice('general.name',1)}}</b>
                                        </div>
                                    </td>
                                    <td width="20%" class="cell_format">
                                        <div class="margin">
                                            <b>{{trans_choice('general.account',1)}} {{trans_choice('general.number',1)}}</b>
                                        </div>
                                    </td>
                                    <td width="20%" class="cell_format">
                                        <div class="margin">
                                            <b>{{trans_choice('general.description',1)}}</b>
                                        </div>
                                    </td>
                                    <td width="20%" class="cell_format">
                                        <div class="margin">
                                            <b>{{trans_choice('general.paid',1)}} {{trans_choice('general.amount',1)}}</b>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="20%" class="cell_format">
                                        <div class="margin text-bold">
                                            {!! Form::text('payment_method',$payroll->payment_method, array('class' => 'form-control', 'required'=>"")) !!}
                                        </div>
                                    </td>
                                    <td width="20%" class="cell_format">
                                        <div class="margin text-bold">
                                            {!! Form::text('bank_name',$payroll->bank_name, array('class' => 'form-control', ''=>"")) !!}
                                        </div>
                                    </td>
                                    <td width="20%" class="cell_format">
                                        <div class="margin text-bold">
                                            {!! Form::text('account_number',$payroll->account_number, array('class' => 'form-control', ''=>"")) !!}
                                        </div>
                                    </td>
                                    <td width="20%" class="cell_format">
                                        <div class="margin text-bold">
                                            {!! Form::text('description',$payroll->description, array('class' => 'form-control', ''=>"")) !!}
                                        </div>
                                    </td>
                                    <td width="20%" class="cell_format">
                                        <div class="margin text-bold">
                                            {!! Form::text('paid_amount',$payroll->paid_amount, array('class' => 'form-control', 'id'=>"paid_amount")) !!}

                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <!--Net Pay Distribution-->
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" class="borderOk" style="margin-top:10px" id="messages">
                                <tbody>
                                <tr>
                                    <td width="100%" class="cell_format">
                                        <div class="margin"><b>{{trans_choice('general.comment',2)}}</b></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="100%" class="cell_format">
                                        <div class="margin text-bold">
                                            {!! Form::textarea('comments',$payroll->comments, array('class' => 'form-control', ''=>"")) !!}
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <!--Messages-->
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    {!! Form::label('Recurring',null,array('class'=>'active')) !!}
                    {!! Form::select('recurring', array('1'=>trans_choice('general.yes',1),'0'=>trans_choice('general.no',1)),$payroll->recurring, array('class' => 'form-control','id'=>'recurring')) !!}
                </div>
                <div id="recur">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-line">
                                    {!! Form::label(trans_choice('general.recur_frequency',1),null,array('class'=>'')) !!}
                                    {!! Form::number('recur_frequency',$payroll->recur_frequency, array('class' => 'form-control','id'=>'recurF')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-line">
                                    {!! Form::label(trans_choice('general.recur_type',1),null,array('class'=>'active')) !!}
                                    {!! Form::select('recur_type', array('day'=>'Day(s)','week'=>'Week(s)','month'=>'Month(s)','year'=>'Year(s)'),$payroll->recur_type, array('class' => 'form-control','id'=>'recurT')) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-line">
                                    {!! Form::label(trans_choice('general.recur_starts',1),null,array('class'=>'')) !!}
                                    {!! Form::text('recur_start_date',$payroll->recur_start_date, array('class' => 'form-control date-picker','id'=>'recur_start_date')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-line">
                                    {!! Form::label(trans_choice('general.recur_ends',1),null,array('class'=>'')) !!}
                                    {!! Form::text('recur_end_date',$payroll->recur_end_date, array('class' => 'form-control date-picker','id'=>'recur_end_date')) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.panel-body -->
        <div class="panel-footer">
            <div class="heading-elements">
                <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <!-- /.box -->
@endsection

@section('footer-scripts')

    <script>
        $('#user_id').change(function (e) {
            $.ajax({
                type: 'GET',
                url: '{!! url('payroll/getUser') !!}/' + $('#user_id').val(),
                success: function (data) {
                    $('#employee_name').val(data);
                }
            });
        })
        $(document).ready(function (e) {
            if ($('#recurring').val() == '1') {
                $('#recur').show();
                $('#recurT').attr('required', 'required');
                $('#recur_start_date').attr('required', 'required');
                $('#recurF').attr('required', 'required');
            } else {
                $('#recur').hide();
                $('#recurT').removeAttr('required');
                $('#recur_start_date').removeAttr('required');
                $('#recurF').removeAttr('required');
            }
            $('#recurring').change(function () {
                if ($('#recurring').val() == '1') {
                    $('#recur').show();
                    $('#recurT').attr('required', 'required');
                    $('#recurF').attr('required', 'required');
                    $('#recur_start_date').attr('required', 'required');
                } else {
                    $('#recur').hide();
                    $('#recurT').removeAttr('required');
                    $('#recur_start_date').removeAttr('required');
                    $('#recurF').removeAttr('required');
                }
            })
        })
        function refresh_totals(e) {
            var totalPay = 0;
            var totalDeductions = 0;
            var totalPaid = 0;
            var netPay = 0;
            for (var i = 0; i < '{{count($bottom_left)}}'; i++) {
                var pay = document.getElementById("bottom_left" + i).value;
                if (pay == "")
                    pay = 0;
                totalPay = parseFloat(totalPay) + parseFloat(pay);
            }
            for (var i = 0; i < '{{count($bottom_right)}}'; i++) {
                var deduction = document.getElementById("bottom_right" + i).value;
                if (deduction == "")
                    deduction = 0;
                totalDeductions = parseFloat(totalDeductions) + parseFloat(deduction);
            }

            document.getElementById("total_pay").value = totalPay;
            document.getElementById("total_deductions").value = totalDeductions;
            document.getElementById("net_pay").value = totalPay - totalDeductions;
            document.getElementById("paid_amount").value = totalPay - totalDeductions;
        }
    </script>
@endsection