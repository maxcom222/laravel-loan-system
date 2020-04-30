@extends('layouts.master')
@section('title')
    {{ trans_choice('general.setting',2) }}
@endsection
@section('content')
    <div class="panel panel-white">
        {!! Form::open(array('url' => url('setting/update'), 'method' => 'post', 'name' => 'form','class'=>"form-horizontal","enctype"=>"multipart/form-data")) !!}
        <div class="panel-heading">
            <h6 class="panel-title">{{ trans_choice('general.setting',2) }}</h6>

            <div class="heading-elements">
                <button type="submit" class="btn btn-info">{{ trans('general.save') }}</button>
            </div>
        </div>
        <div class="panel-body">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li><a href="#general" data-toggle="tab">{{ trans('general.general') }}</a></li>
                    <li><a href="#sms" data-toggle="tab">{{ trans('general.sms') }}</a></li>
                    <li><a href="#email_templates"
                           data-toggle="tab">{{ trans_choice('general.email',1) }} {{ trans_choice('general.template',2) }}</a>
                    </li>
                    <li><a href="#sms_templates"
                           data-toggle="tab">{{ trans_choice('general.sms',1) }} {{ trans_choice('general.template',2) }}</a>
                    </li>
                    <li class="active"><a href="#system" data-toggle="tab">{{ trans_choice('general.system',1) }}</a>
                    </li>
                    <li><a href="#payments" data-toggle="tab">{{ trans_choice('general.payment',2) }}</a></li>
                    <li><a href="#update" data-toggle="tab">{{ trans_choice('general.update',2) }}</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane" id="general">
                        <div class="form-group">
                            {!! Form::label('company_name',trans('general.company_name'),array('class'=>'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('company_name',\App\Models\Setting::where('setting_key','company_name')->first()->setting_value,array('class'=>'form-control','required'=>'required')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('company_email',trans('general.company_email'),array('class'=>'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::email('company_email',\App\Models\Setting::where('setting_key','company_email')->first()->setting_value,array('class'=>'form-control','required'=>'required')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('company_website',trans('general.company_website'),array('class'=>'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('company_website',\App\Models\Setting::where('setting_key','company_website')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('company_address',trans('general.company_address'),array('class'=>'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::textarea('company_address',\App\Models\Setting::where('setting_key','company_address')->first()->setting_value,array('class'=>'form-control','rows'=>'2')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('company_country',trans('general.country'),array('class'=>'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::select('company_country',$countries,\App\Models\Setting::where('setting_key','company_country')->first()->setting_value,array('class'=>' select2','placeholder'=>'','required'=>'required')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('portal_address',trans('general.portal_address'),array('class'=>'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('portal_address',\App\Models\Setting::where('setting_key','portal_address')->first()->setting_value,array('class'=>'form-control','required'=>'required')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('company_currency',trans('general.currency'),array('class'=>'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('company_currency',\App\Models\Setting::where('setting_key','company_currency')->first()->setting_value,array('class'=>'form-control','required'=>'required')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('currency_symbol',trans('general.currency_symbol'),array('class'=>'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::text('currency_symbol',\App\Models\Setting::where('setting_key','currency_symbol')->first()->setting_value,array('class'=>'form-control','required'=>'required')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('currency_position',trans('general.currency_position'),array('class'=>'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::select('currency_position',array('left'=>trans('general.left'),'right'=>trans('general.right')),\App\Models\Setting::where('setting_key','currency_position')->first()->setting_value,array('class'=>'form-control','required'=>'required')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('company_logo',trans('general.company_logo'),array('class'=>'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                @if(!empty(\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value))
                                    <img src="{{ url(asset('uploads/'.\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value)) }}"
                                         class="img-responsive"/>

                                @endif
                                {!! Form::file('company_logo','',array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('client_login_background',trans('general.client_login_background'),array('class'=>'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                @if(!empty(\App\Models\Setting::where('setting_key','client_login_background')->first()->setting_value))
                                    <img src="{{ url(asset('uploads/'.\App\Models\Setting::where('setting_key','client_login_background')->first()->setting_value)) }}"
                                         class="img-responsive"/>

                                @endif
                                {!! Form::file('client_login_background','',array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-info">{{ trans('general.save') }}</button>
                            </div>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="sms">
                        <div class="form-group">
                            {!! Form::label('sms_enabled',trans('general.sms_enabled'),array('class'=>'col-sm-2 control-label')) !!}

                            <div class="col-sm-10">
                                {!! Form::select('sms_enabled',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','sms_enabled')->first()->setting_value,array('class'=>'form-control','required'=>'required')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('active_sms',trans('general.active_sms'),array('class'=>'col-sm-2 control-label')) !!}
                            <div class="col-sm-10">
                                {!! Form::select('active_sms',$sms_gateways,\App\Models\Setting::where('setting_key','active_sms')->first()->setting_value,array('class'=>'form-control','placeholder'=>'')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-info">{{ trans('general.save') }}</button>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="email_templates">
                        <p>Universal tags to use: <span class="label label-info">{borrowerFirstName}</span> <span
                                    class="label label-info">{borrowerLastName}</span> <span class="label label-info">{borrowerFirstName}</span>
                            <span class="label label-info">{borrowerTitle}</span> <span class="label label-info">{borrowerMobile}</span>
                            <span class="label label-info">{borrowerEmail}</span> <span class="label label-info">{borrowerUniqueNumber}</span>
                            <span class="label label-info">{borrowerPhone}</span>
                        </p>

                        <p class="bg-navy disabled color-palette">{{trans_choice('general.payment',2)}}</p>

                        <p>You can use any of the following tags: <span class="label label-info">{paymentAmount}</span>
                            <span
                                    class="label label-info">{loanNumber}</span> <span class="label label-info">{loanBalance}</span>
                            <span class="label label-info">{paymentDate}</span></p>

                        <div class="form-group">
                            {!! Form::label('payment_received_email_subject',trans('general.payment_received_subject'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::text('payment_received_email_subject',\App\Models\Setting::where('setting_key','payment_received_email_subject')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('payment_received_email_template',trans('general.payment_received_template'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::textarea('payment_received_email_template',\App\Models\Setting::where('setting_key','payment_received_email_template')->first()->setting_value,array('class'=>'form-control tinymce')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('payment_email_subject',trans('general.payment_receipt_subject'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::text('payment_email_subject',\App\Models\Setting::where('setting_key','payment_email_subject')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('payment_email_template',trans('general.payment_receipt_template'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::textarea('payment_email_template',\App\Models\Setting::where('setting_key','payment_email_template')->first()->setting_value,array('class'=>'form-control tinymce')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('loan_payment_reminder_subject',trans('general.payment_reminder_subject'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::text('loan_payment_reminder_subject',\App\Models\Setting::where('setting_key','loan_payment_reminder_subject')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('loan_payment_reminder_email_template',trans('general.payment_reminder_template'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::textarea('loan_payment_reminder_email_template',\App\Models\Setting::where('setting_key','loan_payment_reminder_email_template')->first()->setting_value,array('class'=>'form-control tinymce')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('missed_payment_email_subject',trans('general.missed_payment_subject'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::text('missed_payment_email_subject',\App\Models\Setting::where('setting_key','missed_payment_email_subject')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('missed_payment_email_template',trans('general.missed_payment_template'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::textarea('missed_payment_email_template',\App\Models\Setting::where('setting_key','missed_payment_email_template')->first()->setting_value,array('class'=>'form-control tinymce')) !!}
                            </div>
                        </div>
                        <p class="bg-navy disabled color-palette">{{trans_choice('general.loan',2)}}</p>

                        <p>You can use any of the following tags: <span class="label label-info">{loanDue}</span> <span
                                    class="label label-info">{loanNumber}</span> <span class="label label-info">{loanBalance}</span>
                            <span class="label label-info">{loanPayments}</span> <span class="label label-info">{loansPayments}</span>
                            <span class="label label-info">{loansDue}</span> <span class="label label-info">{loansBalance}</span>
                        </p>

                        <div class="form-group">
                            {!! Form::label('borrower_statement_email_subject',trans('general.borrower_statement_subject'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::text('borrower_statement_email_subject',\App\Models\Setting::where('setting_key','borrower_statement_email_subject')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('borrower_statement_email_template',trans('general.borrower_statement_template'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::textarea('borrower_statement_email_template',\App\Models\Setting::where('setting_key','borrower_statement_email_template')->first()->setting_value,array('class'=>'form-control tinymce')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('loan_statement_email_subject',trans('general.loan_statement_subject'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::text('loan_statement_email_subject',\App\Models\Setting::where('setting_key','loan_statement_email_subject')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('loan_statement_email_template',trans('general.loan_statement_template'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::textarea('loan_statement_email_template',\App\Models\Setting::where('setting_key','loan_statement_email_template')->first()->setting_value,array('class'=>'form-control tinymce')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('loan_schedule_email_subject',trans('general.loan_schedule_subject'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::text('loan_schedule_email_subject',\App\Models\Setting::where('setting_key','loan_schedule_email_subject')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('loan_schedule_email_template',trans('general.loan_schedule_template'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::textarea('loan_schedule_email_template',\App\Models\Setting::where('setting_key','loan_schedule_email_template')->first()->setting_value,array('class'=>'form-control tinymce')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('loan_overdue_email_subject',trans('general.loan_overdue_subject'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::text('loan_overdue_email_subject',\App\Models\Setting::where('setting_key','loan_overdue_email_subject')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('loan_overdue_email_template',trans('general.loan_overdue_template'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::textarea('loan_overdue_email_template',\App\Models\Setting::where('setting_key','loan_overdue_email_template')->first()->setting_value,array('class'=>'form-control tinymce')) !!}
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="sms_templates">
                        <p>Universal tags to use: <span class="label label-info">{borrowerFirstName}</span> <span
                                    class="label label-info">{borrowerLastName}</span> <span class="label label-info">{borrowerFirstName}</span>
                            <span class="label label-info">{borrowerTitle}</span> <span class="label label-info">{borrowerMobile}</span>
                            <span class="label label-info">{borrowerEmail}</span> <span class="label label-info">{borrowerUniqueNumber}</span>
                            <span class="label label-info">{borrowerPhone}</span>
                        </p>

                        <p class="bg-navy disabled color-palette">{{trans_choice('general.payment',2)}}</p>

                        <p>You can use any of the following tags: <span class="label label-info">{paymentAmount}</span>
                            <span
                                    class="label label-info">{loanNumber}</span> <span class="label label-info">{loanBalance}</span>
                            <span class="label label-info">{paymentDate}</span></p>

                        <div class="form-group">
                            {!! Form::label('payment_received_sms_template',trans('general.payment_received_template'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::textarea('payment_received_sms_template',\App\Models\Setting::where('setting_key','payment_received_sms_template')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('loan_payment_reminder_sms_template',trans('general.payment_reminder_template'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::textarea('loan_payment_reminder_sms_template',\App\Models\Setting::where('setting_key','loan_payment_reminder_sms_template')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('missed_payment_sms_template',trans('general.missed_payment_template'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::textarea('missed_payment_sms_template',\App\Models\Setting::where('setting_key','missed_payment_sms_template')->first()->setting_value,array('class'=>'form-control ')) !!}
                            </div>
                        </div>
                        <p class="bg-navy disabled color-palette">{{trans_choice('general.loan',2)}}</p>

                        <p>You can use any of the following tags: <span class="label label-info">{loanDue}</span> <span
                                    class="label label-info">{loanNumber}</span> <span class="label label-info">{loanBalance}</span>
                            <span class="label label-info">{loanPayments}</span> <span class="label label-info">{loansPayments}</span>
                            <span class="label label-info">{loansDue}</span> <span class="label label-info">{loansBalance}</span>
                        </p>

                        <div class="form-group">
                            {!! Form::label('loan_overdue_sms_template',trans('general.loan_overdue_template'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::textarea('loan_overdue_sms_template',\App\Models\Setting::where('setting_key','loan_overdue_sms_template')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane active" id="system">
                        <div class="form-group">
                            {!! Form::label('enable_cron',trans('general.cron_enabled'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('enable_cron',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','enable_cron')->first()->setting_value,array('class'=>'form-control')) !!}
                                <small>Last
                                    Run:@if(!empty(\App\Models\Setting::where('setting_key','cron_last_run')->first()->setting_value)) {{\App\Models\Setting::where('setting_key','cron_last_run')->first()->setting_value}} @else
                                        Never @endif</small>
                                <br>
                                <small>Cron Url: {{url('cron')}}</small>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('auto_apply_penalty',trans('general.auto_apply_penalty'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('auto_apply_penalty',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','auto_apply_penalty')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('auto_payment_receipt_email',trans('general.auto_payment_receipt_email'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('auto_payment_receipt_email',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','auto_payment_receipt_email')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('auto_payment_receipt_sms',trans('general.auto_payment_receipt_sms'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('auto_payment_receipt_sms',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','auto_payment_receipt_sms')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('auto_repayment_sms_reminder',trans('general.auto_repayment_sms_reminder'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('auto_repayment_sms_reminder',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','auto_repayment_sms_reminder')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('auto_repayment_email_reminder',trans('general.auto_repayment_email_reminder'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('auto_repayment_email_reminder',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','auto_repayment_email_reminder')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('auto_repayment_days',trans('general.auto_repayment_days'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::number('auto_repayment_days',\App\Models\Setting::where('setting_key','auto_repayment_days')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('auto_overdue_repayment_sms_reminder',trans('general.auto_overdue_repayment_sms_reminder'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('auto_overdue_repayment_sms_reminder',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','auto_overdue_repayment_sms_reminder')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('auto_overdue_repayment_email_reminder',trans('general.auto_overdue_repayment_email_reminder'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('auto_overdue_repayment_email_reminder',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','auto_overdue_repayment_email_reminder')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('auto_overdue_repayment_days',trans('general.auto_overdue_repayment_days'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::number('auto_overdue_repayment_days',\App\Models\Setting::where('setting_key','auto_overdue_repayment_days')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('auto_overdue_loan_sms_reminder',trans('general.auto_overdue_loan_sms_reminder'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('auto_overdue_loan_sms_reminder',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','auto_overdue_loan_sms_reminder')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('auto_overdue_loan_email_reminder',trans('general.auto_overdue_loan_email_reminder'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('auto_overdue_loan_email_reminder',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','auto_overdue_loan_email_reminder')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('auto_overdue_loan_days',trans('general.auto_overdue_loan_days'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::number('auto_overdue_loan_days',\App\Models\Setting::where('setting_key','auto_overdue_loan_days')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('allow_self_registration',trans('general.allow_self_registration'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('allow_self_registration',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','allow_self_registration')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('client_auto_activate_account',trans('general.client_auto_activate_account'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('client_auto_activate_account',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','client_auto_activate_account')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('allow_client_login',trans('general.allow_client_login'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('allow_client_login',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','allow_client_login')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('allow_client_apply',trans('general.allow_client_apply'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('allow_client_apply',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','allow_client_apply')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('auto_post_savings_interest',trans('general.auto_post_savings_interest'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('auto_post_savings_interest',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','auto_post_savings_interest')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('allow_bank_overdraw',trans('general.allow_bank_overdraw'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('allow_bank_overdraw',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','allow_bank_overdraw')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('payroll_chart_id',trans('general.payroll').' '.trans_choice('general.account',1),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('payroll_chart_id',$chart_expenses,\App\Models\Setting::where('setting_key','payroll_chart_id')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('welcome_note',trans('general.welcome_note'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::textarea('welcome_note',\App\Models\Setting::where('setting_key','welcome_note')->first()->setting_value,array('class'=>'form-control','rows'=>'3')) !!}
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane " id="payments">

                        <div class="form-group">
                            {!! Form::label('enable_online_payment',trans('general.enable_online_payment'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('enable_online_payment',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','enable_online_payment')->first()->setting_value,array('class'=>'form-control')) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('paypal_enabled',trans('general.paypal_enabled'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('paypal_enabled',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','paypal_enabled')->first()->setting_value,array('class'=>'form-control','id'=>'paypal_enabled')) !!}
                            </div>
                        </div>
                        <div class="form-group" id="paypalDiv">
                            {!! Form::label('paypal_email',trans('general.paypal_email'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::text('paypal_email',\App\Models\Setting::where('setting_key','paypal_email')->first()->setting_value,array('class'=>'form-control','id'=>'paypal_email')) !!}
                                <p>Paypal Loan IPN URL:{{url('client/loan/pay/paypal/ipn')}}</p>

                                <p>Paypal Savings IPN URL:{{url('client/saving/pay/paypal/ipn')}}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('paynow_enabled',trans('general.paynow_enabled'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('paynow_enabled',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','paynow_enabled')->first()->setting_value,array('class'=>'form-control','id'=>'paynow_enabled')) !!}
                            </div>
                        </div>
                        <div id="paynowDiv">
                            <div class="form-group">
                                {!! Form::label('paynow_id',trans('general.paynow_id'),array('class'=>'col-sm-3 control-label')) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('paynow_id',\App\Models\Setting::where('setting_key','paynow_id')->first()->setting_value,array('class'=>'form-control','id'=>'paynow_id')) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('paynow_key',trans('general.paynow_key'),array('class'=>'col-sm-3 control-label')) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('paynow_key',\App\Models\Setting::where('setting_key','paynow_key')->first()->setting_value,array('class'=>'form-control','id'=>'paynow_key')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('stripe_enabled',trans('general.stripe_enabled'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('stripe_enabled',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','stripe_enabled')->first()->setting_value,array('class'=>'form-control','id'=>'stripe_enabled')) !!}
                            </div>
                        </div>
                        <div id="stripeDiv">
                            <div class="form-group">
                                {!! Form::label('stripe_secret_key',trans('general.stripe_secret_key'),array('class'=>'col-sm-3 control-label')) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('stripe_secret_key',\App\Models\Setting::where('setting_key','stripe_secret_key')->first()->setting_value,array('class'=>'form-control','id'=>'stripe_secret_key')) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('stripe_publishable_key',trans('general.stripe_publishable_key'),array('class'=>'col-sm-3 control-label')) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('stripe_publishable_key',\App\Models\Setting::where('setting_key','stripe_publishable_key')->first()->setting_value,array('class'=>'form-control','id'=>'stripe_publishable_key')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('mpesa_enabled',trans('general.mpesa_enabled'),array('class'=>'col-sm-3 control-label')) !!}
                            <div class="col-sm-9">
                                {!! Form::select('mpesa_enabled',array('1'=>trans('general.yes'),'0'=>trans('general.no')),\App\Models\Setting::where('setting_key','mpesa_enabled')->first()->setting_value,array('class'=>'form-control','id'=>'mpesa_enabled')) !!}
                            </div>
                        </div>
                        <div id="mpesaDiv">
                            <div class="form-group">
                                {!! Form::label('mpesa_consumer_key',trans('general.mpesa_consumer_key'),array('class'=>'col-sm-3 control-label')) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('mpesa_consumer_key',\App\Models\Setting::where('setting_key','mpesa_consumer_key')->first()->setting_value,array('class'=>'form-control','id'=>'mpesa_consumer_key')) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('mpesa_consumer_secret',trans('general.mpesa_consumer_secret'),array('class'=>'col-sm-3 control-label')) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('mpesa_consumer_secret',\App\Models\Setting::where('setting_key','mpesa_consumer_secret')->first()->setting_value,array('class'=>'form-control','id'=>'mpesa_consumer_secret')) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('mpesa_shortcode',trans('general.mpesa_shortcode'),array('class'=>'col-sm-3 control-label')) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('mpesa_shortcode',\App\Models\Setting::where('setting_key','mpesa_shortcode')->first()->setting_value,array('class'=>'form-control','id'=>'mpesa_shortcode')) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('mpesa_endpoint',trans('general.mpesa_endpoint'),array('class'=>'col-sm-3 control-label')) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('mpesa_endpoint',\App\Models\Setting::where('setting_key','mpesa_endpoint')->first()->setting_value,array('class'=>'form-control','id'=>'mpesa_endpoint')) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('mpesa_initiator',trans('general.mpesa_initiator'),array('class'=>'col-sm-3 control-label')) !!}
                                <div class="col-sm-9">
                                    {!! Form::text('mpesa_initiator',\App\Models\Setting::where('setting_key','mpesa_initiator')->first()->setting_value,array('class'=>'form-control','id'=>'mpesa_initiator')) !!}
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane" id="update">
                        <div class="form-group">
                            <div class="col-sm-4 text-right">Local Version:</div>

                            <div class="col-sm-4">
                                <span class="label label-primary">{{\App\Models\Setting::where('setting_key','system_version')->first()->setting_value}}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4 text-right">Server Version:</div>

                            <div class="col-sm-4">
                                <button class="btn btn-info btn-sm" type="button" id="checkUpdate">Check Version
                                </button>
                                <br>
                                <span class="label label-primary" id="serverVersion"></span>
                            </div>
                        </div>
                        <div id="updateMessage"></div>
                    </div>
                </div>
                <!-- /.tab-content -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.panel-body -->
        <div class="panel-footer">
            <button type="submit" class="btn btn-info pull-right">{{ trans('general.save') }}</button>
        </div>
        {!! Form::close() !!}
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')
    <script>
        $('#checkUpdate').click(function (e) {
            $.ajax({
                type: 'POST',
                url: '{{\App\Models\Setting::where('setting_key','update_url')->first()->setting_value}}',
                dataType: 'json',
                success: function (data) {
                    if ("{!! \App\Models\Setting::where('setting_key','system_version')->first()->setting_value !!}}" < data.version) {
                        swal({
                            title: '{{trans_choice('general.update_available',1)}}<br>v' + data.version,
                            html: data.notes,
                            type: 'success',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: '{{trans_choice('general.download',1)}}',
                            cancelButtonText: '{{trans_choice('general.cancel',1)}}'
                        }).then(function () {
                            //curl function to download update
                            //notify user that update is in progress, do not navigate from page
                            $('#updateMessage').html("<div class='alert alert-warning'>{{trans_choice('general.do_not_navigate_from_page',1)}}</div>");
                            window.location = "{{url('update/download?url=')}}" + data.url;
                        });
                        $('#serverVersion').html(data.version);
                    } else {
                        swal({
                            title: '{{trans_choice('general.no_update_available',1)}}',
                            text: '{{trans_choice('general.system_is_up_to_date',1)}}',
                            type: 'warning',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: '{{trans_choice('general.ok',1)}}',
                            cancelButtonText: '{{trans_choice('general.cancel',1)}}'
                        })
                    }
                }
                ,
                error: function (e) {
                    alert("There was an error connecting to the server")
                }
            });
        })
    </script>
@endsection
