<?php

namespace App\Http\Controllers;

use App\Events\RepaymentCreated;
use App\Helpers\GeneralHelper;
use App\Models\Borrower;
use App\Models\Branch;
use App\Models\CustomFieldMeta;
use App\Models\Guarantor;
use App\Models\JournalEntry;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanProduct;
use App\Models\LoanRepayment;
use App\Models\LoanSchedule;
use App\Models\LoanTransaction;
use App\Models\Saving;
use App\Models\SavingTransaction;
use App\Models\Setting;
use PDF;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;
use Stripe\Stripe;

class ClientController extends Controller
{
    public function __construct(Request $request)
    {
        if (!$request->session()->has('uid')) {
            //user is logged in
            return redirect('client');
        }
    }

    public function clientDashboard(Request $request)
    {
        if ($request->session()->has('uid')) {
            $borrower = Borrower::find($request->session()->get('uid'));
            return view('client.dashboard', compact('borrower'));
        }
        return view('client_login');

    }

    public function clientProfile(Request $request)
    {
        if ($request->session()->has('uid')) {
            $borrower = Borrower::find($request->session()->get('uid'));
            return view('client.profile', compact('borrower'));
        }
        return view('client_login');

    }

    public function processClientProfile(Request $request)
    {
        if ($request->session()->has('uid')) {
            $rules = array(
                'repeatpassword' => 'required|same:password',
                'password' => 'required'
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                Flash::warning('Passwords do not match');
                return redirect()->back()->withInput()->withErrors($validator);

            } else {
                $borrower = Borrower::find($request->session()->get('uid'));
                $borrower->password = md5($request->password);
                $borrower->save();
                Flash::success('Successfully Saved');
                return redirect('client_dashboard')->with('msg', "Successfully Saved");
            }
            $borrower = Borrower::find($request->session()->get('uid'));
            return view('client.profile', compact('borrower'));
        }
        return view('client_login');

    }

    public function processClientRegister(Request $request)
    {
        if (Setting::where('setting_key', 'allow_self_registration')->first()->setting_value == 1) {
            $rules = array(
                'repeatpassword' => 'required|same:password|min:6',
                'password' => 'required|min:6',
                'first_name' => 'required',
                'last_name' => 'required',
                'gender' => 'required',
                'email' => 'required|email',
                'dob' => 'required',
                'username' => 'unique:borrowers',
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                Flash::warning('Validation errors occurred');
                return redirect()->back()->withInput()->withErrors($validator);

            } else {
                $borrower = new Borrower();
                $borrower->first_name = $request->first_name;
                $borrower->last_name = $request->last_name;
                $borrower->gender = $request->gender;
                $borrower->mobile = $request->mobile;
                $borrower->email = $request->email;
                $borrower->dob = $request->dob;
                $borrower->files = serialize(array());
                $borrower->working_status = $request->working_status;
                if (Setting::where('setting_key', 'client_auto_activate_account')->first()->setting_value == 1) {
                    $borrower->active = 1;
                } else {
                    $borrower->active = 0;
                }
                $borrower->source = 'online';
                $borrower->username = $request->username;
                $borrower->password = md5($request->password);
                $date = explode('-', date("Y-m-d"));
                $borrower->year = $date[0];
                $borrower->month = $date[1];
                $borrower->save();
                if ($borrower->active == 1) {
                    $request->session()->put('uid', $borrower->id);
                    Flash::success(trans('general.successfully_registered_logged_in'));
                    return redirect('client')->with('msg', trans('general.logged_in'));
                }
                Flash::success(trans('general.successfully_registered'));
                return redirect('client')->with('msg', trans('general.successfully_registered'));
            }
        } else {
            Flash::success("Registration disabled");
            return redirect()->back();
        }
    }

    public function indexApplication(Request $request)
    {

        $data = LoanApplication::where('borrower_id', $request->session()->has('uid'))->get();
        $borrower = Borrower::find($request->session()->get('uid'));
        return view('client.applications', compact('data', 'borrower'));
    }

    public function createApplication(Request $request)
    {
        $products = array();
        foreach (LoanProduct::all() as $key) {
            $products[$key->id] = $key->name . '(' . round($key->minimum_principal) . '-' . round($key->maximum_principal) . ')';
        }
        $branches = array();
        foreach (Branch::all() as $key) {
            $branches[$key->id] = $key->name;
        }
        $borrower = Borrower::find($request->session()->get('uid'));
        return view('client.apply', compact('borrower', 'products', 'branches'));
    }

    public function storeApplication(Request $request)
    {

        $application = new LoanApplication();
        $application->status = "pending";
        $application->loan_product_id = $request->loan_product_id;
        $application->branch_id = $request->branch_id;
        $application->borrower_id = $request->session()->get('uid');
        $application->amount = $request->amount;
        $application->notes = $request->notes;
        $application->save();
        Flash::success(trans_choice('general.successfully_saved', 1));
        return redirect('client/application/' . $application->id . '/show');
    }

    public function showApplication(Request $request, $loan_application)
    {
        if ($loan_application->borrower_id != $request->session()->get('uid')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $borrower = Borrower::find($request->session()->get('uid'));
        return view('client.show_application', compact('loan_application', 'borrower'));
    }

    public function createGuarantor(Request $request, $loan_application)
    {
        $borrower = Borrower::find($request->session()->get('uid'));
        $borrowers = array();
        foreach (Borrower::all() as $key) {
            $borrowers[$key->id] = $key->first_name . ' ' . $key->last_name . '(' . $key->unique_number . ')';
        }
        return view('client.create_guarantor', compact('loan_application', 'borrower', 'borrowers'));
    }

    public function storeGuarantor(Request $request, $loan_application)
    {
        $guarantor = new Guarantor();
        $guarantor->loan_application_id = $loan_application->id;
        $guarantor->borrower_id = $loan_application->borrower_id;
        $guarantor->guarantor_id = $request->guarantor_id;
        $guarantor->amount = $request->amount;
        $guarantor->date = date("Y-m-d");
        $guarantor->save();
        Flash::success(trans_choice('general.successfully_saved', 1));
        return redirect('client/application/' . $loan_application->id . '/show');
    }

    public function acceptGuarantor(Request $request, $id)
    {
        $guarantor = Guarantor::find($id);
        if ($guarantor->guarantor_id != $request->session()->get('uid')) {
            Flash::warning(trans_choice('general.identity_error', 1));
            return redirect()->back();
        }
        $guarantor->status = "accepted";
        $guarantor->accepted_amount = $request->amount;
        $guarantor->save();
        Flash::success(trans_choice('general.successfully_saved', 1));
        return redirect()->back();
    }

    public function indexGuarantor(Request $request)
    {
        $borrower = Borrower::find($request->session()->get('uid'));
        $data = Guarantor::where('guarantor_id', $borrower->id)->get();
        return view('client.index_guarantor', compact('borrower', 'data'));
    }

    public function indexSaving(Request $request)
    {
        $borrower = Borrower::find($request->session()->get('uid'));
        $data = Saving::where('borrower_id', $borrower->id)->get();
        return view('client.index_saving', compact('borrower', 'data'));
    }

    public function showSaving(Request $request, $saving)
    {
        $borrower = Borrower::find($request->session()->get('uid'));
        if (empty(Saving::where('borrower_id', $borrower->id)->first())) {
            Flash::warning(trans_choice('general.no_saving_account', 1));
            return redirect()->back();
        }


        return view('client.show_saving', compact('borrower', 'saving'));
    }

    public function printSavingStatement($saving)
    {
        //$transactions = SavingTransaction::where('savings_id', $saving->id)->orderBy('date', 'desc')->orderBy('time','desc')->get();
        $transactions = array();
        $balance = 0;

        return view('saving.print', compact('saving', 'custom_fields', 'transactions'));
    }

    public function pdfSavingStatement($saving)
    {

        $custom_fields = CustomFieldMeta::where('category', 'savings')->where('parent_id',
            $saving->id)->get();
        $pdf = PDF::loadView('saving.pdf_statement',
            compact('saving', 'custom_fields', 'transactions'));
        return $pdf->download($saving->borrower->title . ' ' . $saving->borrower->first_name . ' ' . $saving->borrower->last_name . " - Savings Statement.pdf");

    }

    public function paySaving(Request $request, $saving)
    {
        if ($request->session()->has('uid') != $saving->borrower_id) {
            //user is trying to view wrong loan
            return redirect('client');
        }

        $borrower = Borrower::find($request->session()->get('uid'));
        $methods = array();
        if (Setting::where('setting_key',
                'paypal_enabled')->first()->setting_value == 1 && !empty(Setting::where('setting_key',
                'paypal_email')->first()->setting_value)
        ) {
            $methods["paypal"] = 'Paypal';
        }
        if (Setting::where('setting_key',
                'paynow_enabled')->first()->setting_value == 1 && !empty(Setting::where('setting_key',
                'paynow_key')->first()->setting_value) && !empty(Setting::where('setting_key',
                'paynow_id')->first()->setting_value)
        ) {
            $methods["paynow"] = 'Paynow';
        }
        if (Setting::where('setting_key',
                'stripe_enabled')->first()->setting_value == 1 && !empty(Setting::where('setting_key',
                'stripe_secret_key')->first()->setting_value) && !empty(Setting::where('setting_key',
                'stripe_publishable_key')->first()->setting_value)
        ) {
            $methods["stripe"] = 'Stripe';
        }
        if (Setting::where('setting_key',
                'mpesa_enabled')->first()->setting_value == 1 && !empty(Setting::where('setting_key',
                'mpesa_consumer_key')->first()->setting_value) && !empty(Setting::where('setting_key',
                'mpesa_consumer_secret')->first()->setting_value) && !empty(Setting::where('setting_key',
                'mpesa_shortcode')->first()->setting_value) && !empty(Setting::where('setting_key',
                'mpesa_endpoint')->first()->setting_value)
        ) {
            $methods["mpesa_kenya"] = 'Mpesa';
        }
        return view('client.pay_saving', compact('borrower', 'saving', 'methods'));
    }

    public function paynowSaving(Request $request, $saving)
    {

        $values = array(
            "id" => Setting::where('setting_key', 'paynow_id')->first()->setting_value,
            "reference" => $saving->id,
            "amount" => $request->amount,
            "returnurl" => url('client/saving/' . $saving->id . '/pay/paynow/return'),
            "resulturl" => url('client/saving/' . $saving->id . '/pay/paynow/result'),
            "status" => "Message"
        );
        //generate hash
        $string = "";
        foreach ($values as $key => $value) {
            $string .= $value;
        }
        $integrationkey = Setting::where('setting_key', 'paynow_key')->first()->setting_value;
        $string .= $integrationkey;
        $hash = hash("sha512", $string);
        $values['hash'] = strtoupper($hash);
        $ch = curl_init();
        $url = "https://www.paynow.co.zw/Interface/InitiateTransaction";
        // 2. set the options, including the url
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

// 3. execute and fetch the resulting HTML output
        $output = curl_exec($ch);
        if ($output) {
            $parts = explode("&", $output);
            $result = array();
            foreach ($parts as $i => $value) {
                $bits = explode("=", $value, 2);
                $result[$bits[0]] = urldecode($bits[1]);
            }
            curl_close($ch);
            //print_r($result);
            if ($result['status'] == 'Ok') {
                Flash::success("Success");
                return redirect($result['browserurl']);
            } else {
                Flash::warning("There was an error processing your request. Please try again");
                return redirect()->back();
            }
        } else {
            Flash::warning("There was an error processing your request. Please try again" . curl_error($ch));
            return redirect()->back();
        }
    }

    public function paynowReturnSaving(Request $request, $saving)
    {
        $status = $request->status;
        if ($status == "Paid" || $status = "Awaiting Delivery" || $status = "Delivered") {
            Flash::success(trans("general.payment_success"));
            return redirect('client/saving/' . $saving->id . '/show');
        } else {
            //payment was unsuccessful
            Flash::warning("There was an error processing your payment");
            return redirect('client/saving/' . $saving->id . '/show');
        }

    }

    public function paynowResultSaving(Request $request, $saving)
    {
        $status = $request->status;
        if ($status == "Paid" || $status = "Awaiting Delivery" || $status = "Delivered") {
            //payment successful update database and show download
            $savings_transaction = new SavingTransaction();
            $savings_transaction->borrower_id = $saving->borrower_id;
            $savings_transaction->branch_id = $saving->branch_id;
            $savings_transaction->receipt = $request->paynowreference;
            $savings_transaction->savings_id = $saving->id;
            $savings_transaction->type = "deposit";
            $savings_transaction->reversible = 1;
            $savings_transaction->date = date("Y-m-d");
            $savings_transaction->time = date("H:i");
            $date = explode('-', date("Y-m-d"));
            $savings_transaction->year = $date[0];
            $savings_transaction->month = $date[1];
            $savings_transaction->credit = $request->amount;
            $savings_transaction->notes = "Paynow:" . $request->paynowreference;
            $savings_transaction->save();
            //make journal transactions
            if (!empty($saving->savings_product->chart_reference)) {
                $journal = new JournalEntry();
                $journal->account_id = $saving->savings_product->chart_reference->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = date("Y-m-d");
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $savings_transaction->borrower_id;
                $journal->transaction_type = 'deposit';
                $journal->name = "Deposit";
                $journal->savings_id = $saving->id;
                $journal->debit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
            if (!empty($saving->savings_product->chart_control)) {
                $journal = new JournalEntry();
                $journal->account_id = $saving->savings_product->chart_control->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = date("Y-m-d");
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $savings_transaction->borrower_id;
                $journal->transaction_type = 'deposit';
                $journal->name = "Deposit";
                $journal->savings_id = $saving->id;
                $journal->credit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
        } else {
            //payment was unsuccessful
        }

    }

    public function stripeSaving(Request $request, $saving)
    {
        $stripe = array(
            "secret_key" => Setting::where('setting_key', 'stripe_secret_key')->first()->setting_value,
            "publishable_key" => Setting::where('setting_key', 'stripe_publishable_key')->first()->setting_value
        );
        $json = array();
        Stripe::setApiKey($stripe['secret_key']);
        try {
            $token = $request->token;
            $customer = \Stripe\Customer::create(array(
                'email' => $saving->borrower->email,
                'source' => $token
            ));

            $charge = \Stripe\Charge::create(array(
                'customer' => $customer->id,
                'amount' => $request->amount * 100,
                'currency' => 'usd'
            ));
            //payment successful
            $savings_transaction = new SavingTransaction();
            $savings_transaction->borrower_id = $saving->borrower_id;
            $savings_transaction->branch_id = $saving->branch_id;
            $savings_transaction->receipt = $charge["id"];
            $savings_transaction->savings_id = $saving->id;
            $savings_transaction->type = "deposit";
            $savings_transaction->reversible = 1;
            $savings_transaction->date = date("Y-m-d");
            $savings_transaction->time = date("H:i");
            $date = explode('-', date("Y-m-d"));
            $savings_transaction->year = $date[0];
            $savings_transaction->month = $date[1];
            $savings_transaction->credit = $charge["amount"] / 100;
            $savings_transaction->notes = "Stripe:" . $charge["id"];
            $savings_transaction->save();
            //make journal transactions
            if (!empty($saving->savings_product->chart_reference)) {
                $journal = new JournalEntry();
                $journal->account_id = $saving->savings_product->chart_reference->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = date("Y-m-d");
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $savings_transaction->borrower_id;
                $journal->transaction_type = 'deposit';
                $journal->name = "Deposit";
                $journal->savings_id = $saving->id;
                $journal->debit = $charge["amount"] / 100;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
            if (!empty($saving->savings_product->chart_control)) {
                $journal = new JournalEntry();
                $journal->account_id = $saving->savings_product->chart_control->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = date("Y-m-d");
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $savings_transaction->borrower_id;
                $journal->transaction_type = 'deposit';
                $journal->name = "Deposit";
                $journal->savings_id = $saving->id;
                $journal->credit = $charge["amount"] / 100;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
            $json["success"] = 1;
            $json["msg"] = "Successfully Paid";
        } catch (\Exception $e) {
            $json["success"] = 0;
            $json["msg"] = "An error occurred";
        }


    }

    public function paypalDoneSaving(Request $request, $saving)
    {


        // Thank the user for the purchase
        Flash::success(trans('general.payment_success'));
        return redirect('client/saving/' . $saving->id . '/show');
    }

    public function paypalCancelSaving(Request $request, $saving)
    {
        // Curse and humiliate the user for cancelling this most sacred payment (yours)
        Flash::warning(trans('general.payment_cancel'));
        return redirect('client/saving/' . $saving->id . '/show');
    }

    public function paypalIPNSaving(Request $request)
    {
        // read the post from PayPal system and add 'cmd'
        $req = 'cmd=_notify-validate';
        foreach ($_POST as $key => $value) {
            $value = urlencode(stripslashes($value));
            $req .= "&$key=$value";
        }
// post back to PayPal system to validate
        $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

        $fp = fsockopen('ssl://www.paypal.com', 443, $errno, $errstr, 30);


        if (!$fp) {
// HTTP ERROR
        } else {
            fputs($fp, $header . $req);
            while (!feof($fp)) {
                $res = fgets($fp, 1024);
                if (strcmp($res, "VERIFIED") == 0) {

// PAYMENT VALIDATED & VERIFIED!
                    $item_name = $request['item_name'];
                    $item_number = $request['item_number'];
                    $payment_status = $request['payment_status'];
                    $payment_amount = $request['mc_gross'];
                    $payment_currency = $request['mc_currency'];
                    $txn_id = $request['txn_id'];
                    $receiver_email = $request['receiver_email'];
                    $payer_email = $request['payer_email'];
                    $notes = 'Paypal: txn_id=' . $txn_id . '.<br>Payer Email:' . $payer_email . '.<br>Currency:' . $payment_currency;
                    if ($payment_status == 'Completed' || $payment_status == 'Processed' || $payment_status == 'Sent' || $payment_status == 'Pending') {
                        $saving = Saving::find($item_number);
                        $savings_transaction = new SavingTransaction();
                        $savings_transaction->borrower_id = $saving->borrower_id;
                        $savings_transaction->amount = $payment_amount;
                        $savings_transaction->savings_id = $saving->id;
                        $savings_transaction->type = "deposit";
                        $savings_transaction->date = date("Y-m-d");
                        $savings_transaction->time = date("H:i");
                        $date = explode('-', date("Y-m-d"));
                        $savings_transaction->year = $date[0];
                        $savings_transaction->month = $date[1];
                        $savings_transaction->notes = $notes;
                        $savings_transaction->save();


                        //notify admin


                        //notify client that we have received payment
                    }

                } else {
                    if (strcmp($res, "INVALID") == 0) {

// PAYMENT INVALID & INVESTIGATE MANUALY!
                        //notify admin that payment was unsuccessful

                    }
                }
            }
            fclose($fp);
        }
    }

    public function showLoan(Request $request, $loan)
    {
        if ($request->session()->has('uid') != $loan->borrower_id) {
            //user is trying to view wrong loan
            return redirect('client');
        }
        $borrower = Borrower::find($request->session()->get('uid'));
        $schedules = LoanSchedule::where('loan_id', $loan->id)->orderBy('due_date', 'asc')->get();
        $custom_fields = CustomFieldMeta::where('category', 'loans')->where('parent_id', $loan->id)->get();
        return view('client.show_loan', compact('borrower', 'loan', 'schedules', 'payments', 'custom_fields'));
    }

    public function pay(Request $request, $loan)
    {
        if ($request->session()->has('uid') != $loan->borrower_id) {
            //user is trying to view wrong loan
            return redirect('client');
        }
        $due_items = GeneralHelper::loan_due_items($loan->id, $loan->release_date, date("Y-m-d"));
        $paid_items = GeneralHelper::loan_paid_items($loan->id, $loan->release_date, date("Y-m-d"));
        $due = $due_items["principal"] + $due_items["interest"] + $due_items["fees"] + $due_items["penalty"] - $paid_items["principal"] - $paid_items["interest"] - $paid_items["fees"] - $paid_items["penalty"];
        if ($due < 0) {
            $due = "";
        }
        $borrower = Borrower::find($request->session()->get('uid'));
        $methods = array();
        if (Setting::where('setting_key',
                'paypal_enabled')->first()->setting_value == 1 && !empty(Setting::where('setting_key',
                'paypal_email')->first()->setting_value)
        ) {
            $methods["paypal"] = 'Paypal';
        }
        if (Setting::where('setting_key',
                'paynow_enabled')->first()->setting_value == 1 && !empty(Setting::where('setting_key',
                'paynow_key')->first()->setting_value) && !empty(Setting::where('setting_key',
                'paynow_id')->first()->setting_value)
        ) {
            $methods["paynow"] = 'Paynow';
        }
        if (Setting::where('setting_key',
                'stripe_enabled')->first()->setting_value == 1 && !empty(Setting::where('setting_key',
                'stripe_secret_key')->first()->setting_value) && !empty(Setting::where('setting_key',
                'stripe_publishable_key')->first()->setting_value)
        ) {
            $methods["stripe"] = 'Stripe';
        }

        if (Setting::where('setting_key',
                'mpesa_enabled')->first()->setting_value == 1 && !empty(Setting::where('setting_key',
                'mpesa_consumer_key')->first()->setting_value) && !empty(Setting::where('setting_key',
                'mpesa_consumer_secret')->first()->setting_value) && !empty(Setting::where('setting_key',
                'mpesa_shortcode')->first()->setting_value) && !empty(Setting::where('setting_key',
                'mpesa_endpoint')->first()->setting_value)
        ) {
            $methods["mpesa_kenya"] = 'Mpesa';
        }
        return view('client.pay', compact('borrower', 'loan', 'due', 'methods'));
    }

    public function paynow(Request $request, $loan)
    {

        $values = array(
            "id" => Setting::where('setting_key', 'paynow_id')->first()->setting_value,
            "reference" => $loan->id,
            "amount" => $request->amount,
            "returnurl" => url('client/loan/' . $loan->id . '/pay/paynow/return'),
            "resulturl" => url('client/loan/' . $loan->id . '/pay/paynow/result'),
            "status" => "Message"
        );
        //generate hash
        $string = "";
        foreach ($values as $key => $value) {
            $string .= $value;
        }
        $integrationkey = Setting::where('setting_key', 'paynow_key')->first()->setting_value;
        $string .= $integrationkey;
        $hash = hash("sha512", $string);
        $values['hash'] = strtoupper($hash);
        $ch = curl_init();
        $url = "https://www.paynow.co.zw/Interface/InitiateTransaction";
        // 2. set the options, including the url
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

// 3. execute and fetch the resulting HTML output
        $output = curl_exec($ch);
        if ($output) {
            $parts = explode("&", $output);
            $result = array();
            foreach ($parts as $i => $value) {
                $bits = explode("=", $value, 2);
                $result[$bits[0]] = urldecode($bits[1]);
            }
            curl_close($ch);
            //print_r($result);
            if ($result['status'] == 'Ok') {
                Flash::success("Success");
                return redirect($result['browserurl']);
            } else {
                Flash::warning("There was an error processing your request. Please try again");
                return redirect()->back();
            }
        } else {
            Flash::warning("There was an error processing your request. Please try again" . curl_error($ch));
            return redirect()->back();
        }
    }

    public function paynowReturn(Request $request, $loan)
    {
        $status = $request->status;
        if ($status == "Paid" || $status = "Awaiting Delivery" || $status = "Delivered") {
            Flash::success(trans("general.payment_success"));
            return redirect('client/loan/' . $loan->id . '/show');
        } else {
            //payment was unsuccessful
            Flash::warning("There was an error processing your payment");
            return redirect('client/loan/' . $loan->id . '/show');
        }

    }

    public function paynowResult(Request $request, $loan)
    {
        $status = $request->status;
        if ($status == "Paid" || $status = "Awaiting Delivery" || $status = "Delivered") {
            //payment successful update database and show download
            //payment successful
            $loan_transaction = new LoanTransaction();
            $loan_transaction->branch_id = $loan->branch_id;
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->borrower_id = $loan->borrower_id;
            $loan_transaction->transaction_type = "repayment";
            $loan_transaction->receipt = $request->paynowreference;
            $loan_transaction->date = date("Y-m-d");
            $loan_transaction->reversible = 1;
            //$loan_transaction->repayment_method_id = $request->repayment_method_id;
            $date = explode('-', date("Y-m-d"));
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            $loan_transaction->credit = $request->amount;
            $loan_transaction->notes = "Paynow" . $request->paynowreference;
            $loan_transaction->save();
            //fire payment added event
            //debit and credit the necessary accounts
            $allocation = GeneralHelper::loan_allocate_payment($loan_transaction);
            //return $allocation;
            //principal
            if ($allocation['principal'] > 0) {
                if (!empty($loan->loan_product->chart_loan_portfolio)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_loan_portfolio->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->transaction_sub_type = 'repayment_principal';
                    $journal->name = "Principal Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->credit = $allocation['principal'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
                if (!empty($loan->loan_product->chart_fund_source)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_fund_source->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->name = "Principal Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->debit = $allocation['principal'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
            }
            //interest
            if ($allocation['interest'] > 0) {
                if (!empty($loan->loan_product->chart_income_interest)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_income_interest->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->transaction_sub_type = 'repayment_interest';
                    $journal->name = "Interest Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->credit = $allocation['interest'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
                if (!empty($loan->loan_product->chart_receivable_interest)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_receivable_interest->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->name = "Interest Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->debit = $allocation['interest'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
            }
            //fees
            if ($allocation['fees'] > 0) {
                if (!empty($loan->loan_product->chart_income_fee)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_income_fee->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->transaction_sub_type = 'repayment_fees';
                    $journal->name = "Fees Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->credit = $allocation['fees'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
                if (!empty($loan->loan_product->chart_receivable_fee)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_receivable_fee->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->name = "Fees Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->debit = $allocation['fees'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
            }
            if ($allocation['penalty'] > 0) {
                if (!empty($loan->loan_product->chart_income_penalty)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_income_penalty->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->transaction_sub_type = 'repayment_penalty';
                    $journal->name = "Penalty Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->credit = $allocation['penalty'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
                if (!empty($loan->loan_product->chart_receivable_penalty)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_receivable_penalty->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->name = "Penalty Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->debit = $allocation['penalty'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
            }

            //update loan status if need be
            if (round(GeneralHelper::loan_total_balance($loan->id)) <= 0) {
                $l = Loan::find($loan->id);
                $l->status = "closed";
                $l->save();
            }
            event(new RepaymentCreated($loan_transaction));

        } else {
            //payment was unsuccessful
        }

    }

//stripe payments
    public function stripe(Request $request, $loan)
    {
        $stripe = array(
            "secret_key" => Setting::where('setting_key', 'stripe_secret_key')->first()->setting_value,
            "publishable_key" => Setting::where('setting_key', 'stripe_publishable_key')->first()->setting_value
        );
        $json = array();
        Stripe::setApiKey($stripe['secret_key']);
        try {
            $token = $request->token;
            $customer = \Stripe\Customer::create(array(
                'email' => $loan->borrower->email,
                'source' => $token
            ));

            $charge = \Stripe\Charge::create(array(
                'customer' => $customer->id,
                'amount' => $request->amount * 100,
                'currency' => 'usd'
            ));
            //payment successful
            $loan_transaction = new LoanTransaction();
            $loan_transaction->branch_id = $loan->branch_id;
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->borrower_id = $loan->borrower_id;
            $loan_transaction->transaction_type = "repayment";
            $loan_transaction->receipt = $charge["id"];
            $loan_transaction->date = date("Y-m-d");
            $loan_transaction->reversible = 1;
            //$loan_transaction->repayment_method_id = $request->repayment_method_id;
            $date = explode('-', date("Y-m-d"));
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            $loan_transaction->credit = $charge["amount"] / 100;
            $loan_transaction->notes = "Paid via Stripe";
            $loan_transaction->save();
            //fire payment added event
            //debit and credit the necessary accounts
            $allocation = GeneralHelper::loan_allocate_payment($loan_transaction);
            //return $allocation;
            //principal
            if ($allocation['principal'] > 0) {
                if (!empty($loan->loan_product->chart_loan_portfolio)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_loan_portfolio->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->transaction_sub_type = 'repayment_principal';
                    $journal->name = "Principal Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->credit = $allocation['principal'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
                if (!empty($loan->loan_product->chart_fund_source)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_fund_source->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->name = "Principal Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->debit = $allocation['principal'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
            }
            //interest
            if ($allocation['interest'] > 0) {
                if (!empty($loan->loan_product->chart_income_interest)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_income_interest->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->transaction_sub_type = 'repayment_interest';
                    $journal->name = "Interest Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->credit = $allocation['interest'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
                if (!empty($loan->loan_product->chart_receivable_interest)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_receivable_interest->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->name = "Interest Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->debit = $allocation['interest'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
            }
            //fees
            if ($allocation['fees'] > 0) {
                if (!empty($loan->loan_product->chart_income_fee)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_income_fee->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->transaction_sub_type = 'repayment_fees';
                    $journal->name = "Fees Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->credit = $allocation['fees'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
                if (!empty($loan->loan_product->chart_receivable_fee)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_receivable_fee->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->name = "Fees Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->debit = $allocation['fees'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
            }
            if ($allocation['penalty'] > 0) {
                if (!empty($loan->loan_product->chart_income_penalty)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_income_penalty->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->transaction_sub_type = 'repayment_penalty';
                    $journal->name = "Penalty Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->credit = $allocation['penalty'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
                if (!empty($loan->loan_product->chart_receivable_penalty)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_receivable_penalty->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->name = "Penalty Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->debit = $allocation['penalty'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
            }

            //update loan status if need be
            if (round(GeneralHelper::loan_total_balance($loan->id)) <= 0) {
                $l = Loan::find($loan->id);
                $l->status = "closed";
                $l->save();
            }
            event(new RepaymentCreated($loan_transaction));
            $json["success"] = 1;
            $json["msg"] = "Successfully Paid";
        } catch (\Exception $e) {
            $json["success"] = 0;
            $json["msg"] = "An error occurred";
        }


    }

    public function paypalDone(Request $request, $loan)
    {


        // Thank the user for the purchase
        Flash::success(trans('general.payment_success'));
        return redirect('client/loan/' . $loan->id . '/show');
    }

    public function paypalCancel(Request $request, $loan)
    {
        // Curse and humiliate the user for cancelling this most sacred payment (yours)
        Flash::warning(trans('general.payment_cancel'));
        return redirect('client/loan/' . $loan->id . '/show');
    }

    public function paypalIPN(Request $request)
    {
        // read the post from PayPal system and add 'cmd'
        $req = 'cmd=_notify-validate';
        foreach ($_POST as $key => $value) {
            $value = urlencode(stripslashes($value));
            $req .= "&$key=$value";
        }
// post back to PayPal system to validate
        $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

        $fp = fsockopen('ssl://www.paypal.com', 443, $errno, $errstr, 30);


        if (!$fp) {
// HTTP ERROR
        } else {
            fputs($fp, $header . $req);
            while (!feof($fp)) {
                $res = fgets($fp, 1024);
                if (strcmp($res, "VERIFIED") == 0) {

// PAYMENT VALIDATED & VERIFIED!
                    $item_name = $request['item_name'];
                    $item_number = $request['item_number'];
                    $payment_status = $request['payment_status'];
                    $payment_amount = $request['mc_gross'];
                    $payment_currency = $request['mc_currency'];
                    $txn_id = $request['txn_id'];
                    $receiver_email = $request['receiver_email'];
                    $payer_email = $request['payer_email'];
                    $notes = 'Paypal: txn_id=' . $txn_id . '.<br>Payer Email:' . $payer_email . '.<br>Currency:' . $payment_currency;
                    if ($payment_status == 'Completed' || $payment_status == 'Processed' || $payment_status == 'Sent' || $payment_status == 'Pending') {
                        $loan = Loan::find($item_number);
                        $loan_transaction = new LoanTransaction();
                        $loan_transaction->branch_id = $loan->branch_id;
                        $loan_transaction->loan_id = $loan->id;
                        $loan_transaction->borrower_id = $loan->borrower_id;
                        $loan_transaction->transaction_type = "repayment";
                        $loan_transaction->receipt = $txn_id;
                        $loan_transaction->date = date("Y-m-d");
                        $loan_transaction->reversible = 1;
                        //$loan_transaction->repayment_method_id = $request->repayment_method_id;
                        $date = explode('-', date("Y-m-d"));
                        $loan_transaction->year = $date[0];
                        $loan_transaction->month = $date[1];
                        $loan_transaction->credit = $payment_amount;
                        $loan_transaction->notes = "Paypal:" . $txn_id;
                        $loan_transaction->save();
                        //fire payment added event
                        //debit and credit the necessary accounts
                        $allocation = GeneralHelper::loan_allocate_payment($loan_transaction);
                        //return $allocation;
                        //principal
                        if ($allocation['principal'] > 0) {
                            if (!empty($loan->loan_product->chart_loan_portfolio)) {
                                $journal = new JournalEntry();
                                $journal->account_id = $loan->loan_product->chart_loan_portfolio->id;
                                $journal->branch_id = $loan->branch_id;
                                $journal->date = date("Y-m-d");
                                $journal->year = $date[0];
                                $journal->month = $date[1];
                                $journal->borrower_id = $loan->borrower_id;
                                $journal->transaction_type = 'repayment';
                                $journal->transaction_sub_type = 'repayment_principal';
                                $journal->name = "Principal Repayment";
                                $journal->loan_id = $loan->id;
                                $journal->loan_transaction_id = $loan_transaction->id;
                                $journal->credit = $allocation['principal'];
                                $journal->reference = $loan_transaction->id;
                                $journal->save();
                            }
                            if (!empty($loan->loan_product->chart_fund_source)) {
                                $journal = new JournalEntry();
                                $journal->account_id = $loan->loan_product->chart_fund_source->id;
                                $journal->branch_id = $loan->branch_id;
                                $journal->date = date("Y-m-d");
                                $journal->year = $date[0];
                                $journal->month = $date[1];
                                $journal->borrower_id = $loan->borrower_id;
                                $journal->transaction_type = 'repayment';
                                $journal->name = "Principal Repayment";
                                $journal->loan_id = $loan->id;
                                $journal->loan_transaction_id = $loan_transaction->id;
                                $journal->debit = $allocation['principal'];
                                $journal->reference = $loan_transaction->id;
                                $journal->save();
                            }
                        }
                        //interest
                        if ($allocation['interest'] > 0) {
                            if (!empty($loan->loan_product->chart_income_interest)) {
                                $journal = new JournalEntry();
                                $journal->account_id = $loan->loan_product->chart_income_interest->id;
                                $journal->branch_id = $loan->branch_id;
                                $journal->date = date("Y-m-d");
                                $journal->year = $date[0];
                                $journal->month = $date[1];
                                $journal->borrower_id = $loan->borrower_id;
                                $journal->transaction_type = 'repayment';
                                $journal->transaction_sub_type = 'repayment_interest';
                                $journal->name = "Interest Repayment";
                                $journal->loan_id = $loan->id;
                                $journal->loan_transaction_id = $loan_transaction->id;
                                $journal->credit = $allocation['interest'];
                                $journal->reference = $loan_transaction->id;
                                $journal->save();
                            }
                            if (!empty($loan->loan_product->chart_receivable_interest)) {
                                $journal = new JournalEntry();
                                $journal->account_id = $loan->loan_product->chart_receivable_interest->id;
                                $journal->branch_id = $loan->branch_id;
                                $journal->date = date("Y-m-d");
                                $journal->year = $date[0];
                                $journal->month = $date[1];
                                $journal->borrower_id = $loan->borrower_id;
                                $journal->transaction_type = 'repayment';
                                $journal->name = "Interest Repayment";
                                $journal->loan_id = $loan->id;
                                $journal->loan_transaction_id = $loan_transaction->id;
                                $journal->debit = $allocation['interest'];
                                $journal->reference = $loan_transaction->id;
                                $journal->save();
                            }
                        }
                        //fees
                        if ($allocation['fees'] > 0) {
                            if (!empty($loan->loan_product->chart_income_fee)) {
                                $journal = new JournalEntry();
                                $journal->account_id = $loan->loan_product->chart_income_fee->id;
                                $journal->branch_id = $loan->branch_id;
                                $journal->date = date("Y-m-d");
                                $journal->year = $date[0];
                                $journal->month = $date[1];
                                $journal->borrower_id = $loan->borrower_id;
                                $journal->transaction_type = 'repayment';
                                $journal->transaction_sub_type = 'repayment_fees';
                                $journal->name = "Fees Repayment";
                                $journal->loan_id = $loan->id;
                                $journal->loan_transaction_id = $loan_transaction->id;
                                $journal->credit = $allocation['fees'];
                                $journal->reference = $loan_transaction->id;
                                $journal->save();
                            }
                            if (!empty($loan->loan_product->chart_receivable_fee)) {
                                $journal = new JournalEntry();
                                $journal->account_id = $loan->loan_product->chart_receivable_fee->id;
                                $journal->branch_id = $loan->branch_id;
                                $journal->date = date("Y-m-d");
                                $journal->year = $date[0];
                                $journal->month = $date[1];
                                $journal->borrower_id = $loan->borrower_id;
                                $journal->transaction_type = 'repayment';
                                $journal->name = "Fees Repayment";
                                $journal->loan_id = $loan->id;
                                $journal->loan_transaction_id = $loan_transaction->id;
                                $journal->debit = $allocation['fees'];
                                $journal->reference = $loan_transaction->id;
                                $journal->save();
                            }
                        }
                        if ($allocation['penalty'] > 0) {
                            if (!empty($loan->loan_product->chart_income_penalty)) {
                                $journal = new JournalEntry();
                                $journal->account_id = $loan->loan_product->chart_income_penalty->id;
                                $journal->branch_id = $loan->branch_id;
                                $journal->date = date("Y-m-d");
                                $journal->year = $date[0];
                                $journal->month = $date[1];
                                $journal->borrower_id = $loan->borrower_id;
                                $journal->transaction_type = 'repayment';
                                $journal->transaction_sub_type = 'repayment_penalty';
                                $journal->name = "Penalty Repayment";
                                $journal->loan_id = $loan->id;
                                $journal->loan_transaction_id = $loan_transaction->id;
                                $journal->credit = $allocation['penalty'];
                                $journal->reference = $loan_transaction->id;
                                $journal->save();
                            }
                            if (!empty($loan->loan_product->chart_receivable_penalty)) {
                                $journal = new JournalEntry();
                                $journal->account_id = $loan->loan_product->chart_receivable_penalty->id;
                                $journal->branch_id = $loan->branch_id;
                                $journal->date = date("Y-m-d");
                                $journal->year = $date[0];
                                $journal->month = $date[1];
                                $journal->borrower_id = $loan->borrower_id;
                                $journal->transaction_type = 'repayment';
                                $journal->name = "Penalty Repayment";
                                $journal->loan_id = $loan->id;
                                $journal->loan_transaction_id = $loan_transaction->id;
                                $journal->debit = $allocation['penalty'];
                                $journal->reference = $loan_transaction->id;
                                $journal->save();
                            }
                        }

                        //update loan status if need be
                        if (round(GeneralHelper::loan_total_balance($loan->id)) <= 0) {
                            $l = Loan::find($loan->id);
                            $l->status = "closed";
                            $l->save();
                        }
                        event(new RepaymentCreated($loan_transaction));

                        //notify admin


                        //notify client that we have received payment
                    }

                } else {
                    if (strcmp($res, "INVALID") == 0) {

// PAYMENT INVALID & INVESTIGATE MANUALY!
                        //notify admin that payment was unsuccessful

                    }
                }
            }
            fclose($fp);
        }
    }

    public function mpesa(Request $request, $loan)
    {
        $url = Setting::where('setting_key',
                'mpesa_endpoint')->first()->setting_value . '/oauth/v1/generate?grant_type=client_credentials';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        $credentials = base64_encode(Setting::where('setting_key',
                'mpesa_consumer_key')->first()->setting_value . ':' . Setting::where('setting_key',
                'mpesa_consumer_secret')->first()->setting_value);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array('Authorization: Basic ' . $credentials, 'Content-type: application/json')); //setting a custom header
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        $result = json_decode($result);
        $access_token = $result->access_token;
        curl_close($curl);
        $url = Setting::where('setting_key', 'mpesa_endpoint')->first()->setting_value . '/mpesa/c2b/v1/registerurl';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array('Content-Type:application/json', "Authorization:Bearer $access_token")); //setting custom header
        $curl_post_data = array(
            //Fill in the request parameters with valid values
            'ShortCode' => Setting::where('setting_key', 'mpesa_shortcode')->first()->setting_value,
            'ResponseType' => 'Cancelled',
            'ConfirmationURL' => secure_url('client/loan/mpesa/confirm'),
            'ValidationURL' => secure_url('client/loan/mpesa/validate')
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $curl_response = curl_exec($curl);
        $url = Setting::where('setting_key', 'mpesa_endpoint')->first()->setting_value . '/mpesa/c2b/v1/simulate';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array('Content-Type:application/json', "Authorization:Bearer $access_token")); //setting custom header
        $curl_post_data = array(
            //Fill in the request parameters with valid values
            'ShortCode' => Setting::where('setting_key', 'mpesa_shortcode')->first()->setting_value,
            'CommandID' => 'CustomerPayBillOnline',
            'Amount' => $request->amount,
            'Msisdn' => $request->mobile,
            'BillRefNumber' => $loan->id
        );
        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $curl_response = curl_exec($curl);
        Flash::success(trans_choice("general.mpesa_awaiting_payment", 1));
        return redirect('client/loan/' . $loan->id . '/show');
    }

    public function validate_mpesa_loan()
    {
        try {
            //Set the response content type to application/json
            header("Content-Type:application/json");
            $resp = '{"ResultCode":0,"ResultDesc":"Validation passed successfully"}';
            //read incoming request
            $postData = file_get_contents('php://input');

            $jdata = json_decode($postData, true);

        } catch (\Exception $ex) {
            //append exception to file
            //set failure response
            $resp = '{"ResultCode": 1, "ResultDesc":"Validation failure due to internal service error"}';
        }
        echo $resp;
    }

    public function confirm_mpesa_loan()
    {
        try {
            //Set the response content type to application/json
            header("Content-Type:application/json");
            $resp = '{"ResultCode":0,"ResultDesc":"Confirmation recieved successfully"}';
            //read incoming request
            $postData = file_get_contents('php://input');
            //Parse payload to json
            $jdata = json_decode($postData, true);
            //perform business operations on $jdata here
            //add interest transaction
            $loan = Loan::find($jdata["BillRefNumber"]);
            $loan_transaction = new LoanTransaction();
            $loan_transaction->branch_id = $loan->branch_id;
            $loan_transaction->loan_id = $jdata["BillRefNumber"];
            $loan_transaction->borrower_id = $loan->borrower_id;
            $loan_transaction->transaction_type = "repayment";
            $loan_transaction->receipt = $jdata["TransID"];
            $loan_transaction->date = date("Y-m-d");
            $loan_transaction->reversible = 1;
            //$loan_transaction->repayment_method_id = $request->repayment_method_id;
            $date = explode('-', date("Y-m-d"));
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            $loan_transaction->credit = $jdata["TransAmount"];
            $loan_transaction->notes = "Mpesa:" . $jdata["MSISDN"];
            $loan_transaction->save();
            //fire payment added event
            //debit and credit the necessary accounts
            $allocation = GeneralHelper::loan_allocate_payment($loan_transaction);
            //return $allocation;
            //principal
            if ($allocation['principal'] > 0) {
                if (!empty($loan->loan_product->chart_loan_portfolio)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_loan_portfolio->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->transaction_sub_type = 'repayment_principal';
                    $journal->name = "Principal Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->credit = $allocation['principal'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
                if (!empty($loan->loan_product->chart_fund_source)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_fund_source->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->name = "Principal Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->debit = $allocation['principal'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
            }
            //interest
            if ($allocation['interest'] > 0) {
                if (!empty($loan->loan_product->chart_income_interest)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_income_interest->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->transaction_sub_type = 'repayment_interest';
                    $journal->name = "Interest Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->credit = $allocation['interest'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
                if (!empty($loan->loan_product->chart_receivable_interest)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_receivable_interest->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->name = "Interest Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->debit = $allocation['interest'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
            }
            //fees
            if ($allocation['fees'] > 0) {
                if (!empty($loan->loan_product->chart_income_fee)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_income_fee->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->transaction_sub_type = 'repayment_fees';
                    $journal->name = "Fees Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->credit = $allocation['fees'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
                if (!empty($loan->loan_product->chart_receivable_fee)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_receivable_fee->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->name = "Fees Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->debit = $allocation['fees'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
            }
            if ($allocation['penalty'] > 0) {
                if (!empty($loan->loan_product->chart_income_penalty)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_income_penalty->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->transaction_sub_type = 'repayment_penalty';
                    $journal->name = "Penalty Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->credit = $allocation['penalty'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
                if (!empty($loan->loan_product->chart_receivable_penalty)) {
                    $journal = new JournalEntry();
                    $journal->account_id = $loan->loan_product->chart_receivable_penalty->id;
                    $journal->branch_id = $loan->branch_id;
                    $journal->date = date("Y-m-d");
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->borrower_id = $loan->borrower_id;
                    $journal->transaction_type = 'repayment';
                    $journal->name = "Penalty Repayment";
                    $journal->loan_id = $loan->id;
                    $journal->loan_transaction_id = $loan_transaction->id;
                    $journal->debit = $allocation['penalty'];
                    $journal->reference = $loan_transaction->id;
                    $journal->save();
                }
            }

            //update loan status if need be
            if (round(GeneralHelper::loan_total_balance($loan->id)) <= 0) {
                $l = Loan::find($loan->id);
                $l->status = "closed";
                $l->save();
            }
            event(new RepaymentCreated($loan_transaction));

        } catch (\Exception $ex) {
            //append exception to errorLog

        }
        echo $resp;
    }

    public function mpesa_saving(Request $request, $saving)
    {
        $url = Setting::where('setting_key',
                'mpesa_endpoint')->first()->setting_value . '/oauth/v1/generate?grant_type=client_credentials';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        $credentials = base64_encode(Setting::where('setting_key',
                'mpesa_consumer_key')->first()->setting_value . ':' . Setting::where('setting_key',
                'mpesa_consumer_secret')->first()->setting_value);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array('Authorization: Basic ' . $credentials, 'Content-type: application/json')); //setting a custom header
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        $result = json_decode($result);
        $access_token = $result->access_token;
        curl_close($curl);
        $url = Setting::where('setting_key', 'mpesa_endpoint')->first()->setting_value . '/mpesa/c2b/v1/registerurl';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array('Content-Type:application/json', "Authorization:Bearer $access_token")); //setting custom header
        $curl_post_data = array(
            //Fill in the request parameters with valid values
            'ShortCode' => Setting::where('setting_key', 'mpesa_shortcode')->first()->setting_value,
            'ResponseType' => 'Cancelled',
            'ConfirmationURL' => secure_url('client/saving/mpesa/confirm'),
            'ValidationURL' => secure_url('client/saving/mpesa/validate')
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $curl_response = curl_exec($curl);
        $url = Setting::where('setting_key', 'mpesa_endpoint')->first()->setting_value . '/mpesa/c2b/v1/simulate';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array('Content-Type:application/json', "Authorization:Bearer $access_token")); //setting custom header
        $curl_post_data = array(
            //Fill in the request parameters with valid values
            'ShortCode' => Setting::where('setting_key', 'mpesa_shortcode')->first()->setting_value,
            'CommandID' => 'CustomerPayBillOnline',
            'Amount' => $request->amount,
            'Msisdn' => $request->mobile,
            'BillRefNumber' => $saving->id
        );
        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $curl_response = curl_exec($curl);
        Flash::success(trans_choice("general.mpesa_awaiting_payment", 1));
        return redirect('client/saving/' . $saving->id . '/show');
    }

    public function validate_mpesa_saving()
    {
        try {
            //Set the response content type to application/json
            header("Content-Type:application/json");
            $resp = '{"ResultCode":0,"ResultDesc":"Validation passed successfully"}';
            //read incoming request
            $postData = file_get_contents('php://input');

            $jdata = json_decode($postData, true);

        } catch (\Exception $ex) {
            //append exception to file
            //set failure response
            $resp = '{"ResultCode": 1, "ResultDesc":"Validation failure due to internal service error"}';
        }
        echo $resp;
    }

    public function confirm_mpesa_saving()
    {
        try {
            //Set the response content type to application/json
            header("Content-Type:application/json");
            $resp = '{"ResultCode":0,"ResultDesc":"Confirmation recieved successfully"}';
            //read incoming request
            $postData = file_get_contents('php://input');
            //Parse payload to json
            $jdata = json_decode($postData, true);
            //perform business operations on $jdata here
            $saving = Saving::find($jdata["BillRefNumber"]);
            $savings_transaction = new SavingTransaction();
            $savings_transaction->borrower_id = $saving->borrower_id;
            $savings_transaction->branch_id = $saving->branch_id;
            $savings_transaction->receipt = $jdata["TransID"];
            $savings_transaction->savings_id = $saving->id;
            $savings_transaction->type = "deposit";
            $savings_transaction->reversible = 1;
            $savings_transaction->date = date("Y-m-d");
            $savings_transaction->time = date("H:i");
            $date = explode('-', date("Y-m-d"));
            $savings_transaction->year = $date[0];
            $savings_transaction->month = $date[1];
            $savings_transaction->credit = $jdata["TransAmount"];
            $savings_transaction->notes = "Mpesa:" . $jdata["MSISDN"];
            $savings_transaction->save();
            //make journal transactions
            if (!empty($saving->savings_product->chart_reference)) {
                $journal = new JournalEntry();
                $journal->account_id = $saving->savings_product->chart_reference->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = date("Y-m-d");
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $savings_transaction->borrower_id;
                $journal->transaction_type = 'deposit';
                $journal->name = "Deposit";
                $journal->savings_id = $saving->id;
                $journal->debit = $jdata["TransAmount"];
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
            if (!empty($saving->savings_product->chart_control)) {
                $journal = new JournalEntry();
                $journal->account_id = $saving->savings_product->chart_control->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = date("Y-m-d");
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $savings_transaction->borrower_id;
                $journal->transaction_type = 'deposit';
                $journal->name = "Deposit";
                $journal->savings_id = $saving->id;
                $journal->credit = $jdata["TransAmount"];
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }

        } catch (\Exception $ex) {
            //append exception to errorLog

        }
        echo $resp;
    }
    public function pdfSchedule($loan)
    {

        $schedules = LoanSchedule::where('loan_id', $loan->id)->orderBy('due_date', 'asc')->get();
        $pdf = PDF::loadView('loan.pdf_schedule', compact('loan', 'schedules'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download($loan->borrower->title . ' ' . $loan->borrower->first_name . ' ' . $loan->borrower->last_name . " - Loan Repayment Schedule.pdf");

    }
    public function printSchedule($loan)
    {
        $schedules = LoanSchedule::where('loan_id', $loan->id)->orderBy('due_date', 'asc')->get();
        return view('loan.print_schedule', compact('loan', 'schedules'));
    }

    public function pdfLoanStatement($loan)
    {
        $pdf = PDF::loadView('loan.pdf_loan_statement', compact('loan', 'payments'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download($loan->borrower->title . ' ' . $loan->borrower->first_name . ' ' . $loan->borrower->last_name . " - Loan Statement.pdf");

    }

    public function printLoanStatement($loan)
    {
        return view('loan.print_loan_statement', compact('loan', 'payments'));
    }

    public function pdfBorrowerStatement($borrower)
    {
        $loans = Loan::where('borrower_id', $borrower->id)->orderBy('release_date', 'asc')->get();
        $pdf = PDF::loadView('loan.pdf_borrower_statement', compact('loans'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download($borrower->title . ' ' . $borrower->first_name . ' ' . $borrower->last_name . " - Client Statement.pdf");

    }

    public function printBorrowerStatement($borrower)
    {
        $loans = Loan::where('borrower_id', $borrower->id)->orderBy('release_date', 'asc')->get();
        return view('loan.print_borrower_statement', compact('loans'));
    }
}
