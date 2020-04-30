<style>
    .borderOk {

        border-right: solid 1px #000000;
        border-left: solid 1px #000000;
        border-top: solid 1px #000000;
        border-bottom: solid 1px #000000;
    }

    table #hours_and_earnings td, table #tax_deductions td, table #pre_tax_deductions td, table #after_tax_deductions td, table #payslip_employee_header td, table #payslip_employer_header td, table #pay_period_and_salary td, table #summary td, table #net_pay_distribution td, table #messages td {
        padding: 2px;
    }

    .bg-navy {
        background-color: #001f3f;
        color: #fff;
    }

    .bg-gray {
        color: #000;
        background-color: #d2d6de;
    }

    .text-bold, .text-bold.table td, .text-bold.table th {
        font-weight: 700;
    }

    .margin {
        margin: 10px;
    }

    .text-center {
        text-align: center;
    }
</style>
<h3 class="text-center"><b>{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</b></h3>

<h3 class="text-center"><b>{{trans_choice('general.payslip',1)}}</b></h3>
<table width="100%">
    <tbody>
    <tr style="margin: 20px">
        <td style="padding-bottom:10px;">
            <table width="100%" class="borderOk">
                <tbody>
                <tr>
                    <td style="vertical-align: top;" width="50%">

                        <table width="100%" id="payslip_employee_header">
                            <tbody>
                            <tr>
                                <td width="50%" class="cell_format">
                                    <div class="margin"> {{trans_choice('general.employee',1)}} {{trans_choice('general.name',1)}}
                                    </div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                        {!! $payroll->employee_name !!}
                                    </div>
                                </td>
                            </tr>
                            @foreach($top_left as $key)
                                <tr>
                                    <td width="50%" class="cell_format">
                                        <div class="margin">
                                            @if(!empty($key->payroll_template_meta))
                                                {{$key->payroll_template_meta->name}}
                                            @endif
                                        </div>
                                    </td>
                                    <td width="50%" class="cell_format">
                                        <div class="margin text-bold">
                                            {!! $key->value !!}
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
                                    <div class="margin"><b>{{trans_choice('general.payroll',1)}} {{trans_choice('general.date',1)}}</b></div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                        {!! $payroll->date !!}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td width="50%" class="cell_format">
                                    <div class="margin">{{trans_choice('general.business',1)}} {{trans_choice('general.name',1)}}</div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                        {!! \App\Models\Setting::where('setting_key', 'company_name')->first()->setting_value !!}
                                    </div>
                                </td>
                            </tr>
                            @foreach($top_right as $key)
                                <tr>
                                    <td width="50%" class="cell_format">
                                        <div class="margin">
                                            @if(!empty($key->payroll_template_meta))
                                                {{$key->payroll_template_meta->name}}
                                            @endif
                                        </div>
                                    </td>
                                    <td width="50%" class="cell_format">
                                        <div class="margin text-bold">
                                            {!! $key->value !!}
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
    <tr style="height: 20px">
        <td></td>
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
                                <td width="50%" class="bg-navy"><b>{{trans_choice('general.description',1)}}</b></td>
                                <td width="50%" class="bg-navy"><b>{{trans_choice('general.amount',1)}}</b></td>
                            </tr>
                            <?php
                            $count = 0;
                            foreach($bottom_left as $key){
                            ?>
                            <tr>
                                <td width="50%" class="cell_format">
                                    <div class="margin">
                                        @if(!empty($key->payroll_template_meta))
                                            {{$key->payroll_template_meta->name}}
                                        @endif
                                    </div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                        {!! $key->value !!}
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
                                <td width="50%" class="bg-navy"><b>{{trans_choice('general.description',1)}}</b></td>
                                <td width="50%" class="bg-navy"><b>{{trans_choice('general.amount',1)}}</b></td>
                            </tr>
                            <?php
                            $count = 0;
                            foreach($bottom_right as $key){
                            ?>
                            <tr>
                                <td width="50%" class="cell_format">
                                    <div class="margin">
                                        @if(!empty($key->payroll_template_meta))
                                            {{$key->payroll_template_meta->name}}
                                        @endif
                                    </div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                        {!! $key->value !!}
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
                                    <div class="margin"><b>{{trans_choice('general.total',1)}} {{trans_choice('general.pay',1)}}</b></div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                        {!! \App\Helpers\GeneralHelper::single_payroll_total_pay($payroll->id) !!}
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
                                    <div class="margin"><b>{{trans_choice('general.total',1)}} {{trans_choice('general.deduction',2)}}</b></div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                        {!! \App\Helpers\GeneralHelper::single_payroll_total_deductions($payroll->id) !!}
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
                                    <div class="margin"><b>{{trans_choice('general.net',1)}} {{trans_choice('general.pay',1)}}</b></div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                        {!! \App\Helpers\GeneralHelper::single_payroll_total_pay($payroll->id)-\App\Helpers\GeneralHelper::single_payroll_total_deductions($payroll->id) !!}
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
    <tr style="height: 20px">
        <td></td>
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
                            {!! $payroll->payment_method !!}
                        </div>
                    </td>
                    <td width="20%" class="cell_format">
                        <div class="margin text-bold">
                            {!! $payroll->bank_name !!}
                        </div>
                    </td>
                    <td width="20%" class="cell_format">
                        <div class="margin text-bold">
                            {!! $payroll->account_number !!}
                        </div>
                    </td>
                    <td width="20%" class="cell_format">
                        <div class="margin text-bold">
                            {!! $payroll->description !!}
                        </div>
                    </td>
                    <td width="20%" class="cell_format">
                        <div class="margin text-bold">
                            {!! $payroll->paid_amount !!}

                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <!--Net Pay Distribution-->
        </td>
    </tr>
    @if(!empty($payroll->comments))
        <tr style="height: 20px">
            <td></td>
        </tr>
        <tr>
            <td>
                <table width="100%" class="borderOk" style="margin-top:10px;padding: 10px" id="messages">
                    <tbody>
                    <tr>
                        <td width="100%" class="cell_format">
                            <div class="margin"><b>{{trans_choice('general.comment',2)}}</b></div>
                        </td>
                    </tr>
                    <tr>
                        <td width="100%" class="cell_format">
                            <div class="margin text-bold">
                                {!! $payroll->comments !!}
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <!--Messages-->
            </td>
        </tr>
    @endif
    </tbody>
</table>