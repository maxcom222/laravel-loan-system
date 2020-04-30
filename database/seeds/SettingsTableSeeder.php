<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('settings')->insert([
            [
                'setting_key' => 'company_name',
                'setting_value' => 'Loan',
            ],
            [
                'setting_key' => 'company_address',
                'setting_value' => 'Suite 608',
            ],
            [
                'setting_key' => 'company_currency',
                'setting_value' => 'ZAR',
            ],
            [
                'setting_key' => 'company_website',
                'setting_value' => 'http://www.webstudio.co.zw',
            ],
            [
                'setting_key' => 'company_country',
                'setting_value' => '1',
            ],
            [
                'setting_key' => 'system_version',
                'setting_value' => '1.0',
            ],
            [
                'setting_key' => 'sms_enabled',
                'setting_value' => '1',
            ],
            [
                'setting_key' => 'active_sms',
                'setting_value' => 'clickatell',
            ],
            [
                'setting_key' => 'portal_address',
                'setting_value' => 'http://www.',
            ],
            [
                'setting_key' => 'company_email',
                'setting_value' => 'info@webstudio.co.zw',
            ],
            [
                'setting_key' => 'currency_symbol',
                'setting_value' => '$',
            ],
            [
                'setting_key' => 'currency_position',
                'setting_value' => 'left',
            ],
            [
                'setting_key' => 'company_logo',
                'setting_value' => 'logo.jpg',
            ],
            [
                'setting_key' => 'twilio_sid',
                'setting_value' => '',
            ],
            [
                'setting_key' => 'twilio_token',
                'setting_value' => '',
            ],
            [
                'setting_key' => 'twilio_phone_number',
                'setting_value' => '',
            ],
            [
                'setting_key' => 'routesms_host',
                'setting_value' => '',
            ],
            [
                'setting_key' => 'routesms_username',
                'setting_value' => '',
            ],
            [
                'setting_key' => 'routesms_password',
                'setting_value' => '',
            ],
            [
                'setting_key' => 'routesms_port',
                'setting_value' => '',
            ],
            [
                'setting_key' => 'sms_sender',
                'setting_value' => '',
            ],
            [
                'setting_key' => 'clickatell_username',
                'setting_value' => '',
            ],
            [
                'setting_key' => 'clickatell_password',
                'setting_value' => '',
            ],
            [
                'setting_key' => 'clickatell_api_id',
                'setting_value' => '',
            ],
            [
                'setting_key' => 'paypal_email',
                'setting_value' => '',
            ],
            [
                'setting_key' => 'currency',
                'setting_value' => 'USD',
            ],
            [
                'setting_key' => 'password_reset_subject',
                'setting_value' => 'Password reset instructions',
            ],
            [
                'setting_key' => 'password_reset_template',
                'setting_value' => 'Password reset instructions',
            ],
            [
                'setting_key' => 'payment_received_sms_template',
                'setting_value' => 'Dear {borrowerFirstName}, we have received your payment of ${paymentAmount} for loan {loanNumber}. New loan balance:${loanBalance}. Thank you',
            ],
            [
                'setting_key' => 'payment_received_email_template',
                'setting_value' => 'Dear {borrowerFirstName}, we have received your payment of ${paymentAmount} for loan {loanNumber}. New loan balance:${loanBalance}. Thank you',
            ],
            [
                'setting_key' => 'payment_received_email_subject',
                'setting_value' => 'Payment Received',
            ],
            [
                'setting_key' => 'payment_email_subject',
                'setting_value' => 'Payment Receipt',
            ],
            [
                'setting_key' => 'payment_email_template',
                'setting_value' => 'Dear {borrowerFirstName}, find attached receipt of your payment of ${paymentAmount} for loan {loanNumber} on {paymentDate}. New loan balance:${loanBalance}. Thank you',
            ],
            [
                'setting_key' => 'borrower_statement_email_subject',
                'setting_value' => 'Client Statement',
            ],
            [
                'setting_key' => 'borrower_statement_email_template',
                'setting_value' => 'Dear {borrowerFirstName}, find attached statement of your loans with us. Thank you',
            ],
            [
                'setting_key' => 'loan_statement_email_subject',
                'setting_value' => 'Loan Statement',
            ],
            [
                'setting_key' => 'loan_statement_email_template',
                'setting_value' => 'Dear {borrowerFirstName}, find attached loan statement for loan {loanNumber}. Thank you',
            ],
            [
                'setting_key' => 'loan_schedule_email_subject',
                'setting_value' => 'Loan Schedule',
            ],
            [
                'setting_key' => 'loan_schedule_email_template',
                'setting_value' => 'Dear {borrowerFirstName}, find attached loan schedule for loan {loanNumber}. Thank you',
            ],
            [
                'setting_key' => 'cron_last_run',
                'setting_value' => '',
            ],
            [
                'setting_key' => 'auto_apply_penalty',
                'setting_value' => '0',
            ],
            [
                'setting_key' => 'auto_payment_receipt_sms',
                'setting_value' => '0',
            ],
            [
                'setting_key' => 'auto_payment_receipt_email',
                'setting_value' => '1',
            ],
            [
                'setting_key' => 'auto_repayment_sms_reminder',
                'setting_value' => '0',
            ],
            [
                'setting_key' => 'auto_repayment_email_reminder',
                'setting_value' => '1',
            ],
            [
                'setting_key' => 'auto_repayment_days',
                'setting_value' => '4',
            ],
            [
                'setting_key' => 'auto_overdue_repayment_sms_reminder',
                'setting_value' => '0',
            ],
            [
                'setting_key' => 'auto_overdue_repayment_email_reminder',
                'setting_value' => '1',
            ],
            [
                'setting_key' => 'auto_overdue_repayment_days',
                'setting_value' => '2',
            ],
            [
                'setting_key' => 'auto_overdue_loan_sms_reminder',
                'setting_value' => '0',
            ],
            [
                'setting_key' => 'auto_overdue_loan_email_reminder',
                'setting_value' => '1',
            ],
            [
                'setting_key' => 'auto_overdue_loan_days',
                'setting_value' => '2',
            ],
            [
                'setting_key' => 'loan_overdue_email_subject',
                'setting_value' => 'Loan Overdue',
            ],
            [
                'setting_key' => 'loan_overdue_email_template',
                'setting_value' => 'Dear {borrowerFirstName}, Your loan {loanNumber} is overdue. Please make your payment. Thank you',
            ],
            [
                'setting_key' => 'loan_overdue_sms_template',
                'setting_value' => 'Dear {borrowerFirstName}, Your loan {loanNumber} is overdue. Please make your payment. Thank you',
            ],
            [
                'setting_key' => 'loan_payment_reminder_subject',
                'setting_value' => 'Upcoming Payment Reminder',
            ],
            [
                'setting_key' => 'loan_payment_reminder_email_template',
                'setting_value' => 'Dear {borrowerFirstName},You have an upcoming payment of {paymentAmount} due on {paymentDate} for loan {loanNumber}. Please make your payment. Thank you',
            ],
            [
                'setting_key' => 'loan_payment_reminder_sms_template',
                'setting_value' => 'Dear {borrowerFirstName},You have an upcoming payment of {paymentAmount} due on {paymentDate} for loan {loanNumber}. Please make your payment. Thank you',
            ],
            [
                'setting_key' => 'missed_payment_email_subject',
                'setting_value' => 'Missed Payment',
            ],
            [
                'setting_key' => 'missed_payment_email_template',
                'setting_value' => 'Dear {borrowerFirstName},You missed  payment of {paymentAmount} which was due on {paymentDate} for loan {loanNumber}. Please make your payment. Thank you',
            ],
            [
                'setting_key' => 'missed_payment_sms_template',
                'setting_value' => 'Dear {borrowerFirstName},You missed  payment of {paymentAmount} which was due on {paymentDate} for loan {loanNumber}. Please make your payment. Thank you',
            ],
            [
                'setting_key' => 'enable_cron',
                'setting_value' => '0',
            ],
            [
                'setting_key' => 'infobip_username',
                'setting_value' => '',
            ],
            [
                'setting_key' => 'infobip_password',
                'setting_value' => '',
            ],

        ]);
    }
}
