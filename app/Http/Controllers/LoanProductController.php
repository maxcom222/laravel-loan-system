<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Helpers\BulkSms;
use App\Helpers\GeneralHelper;
use App\Models\Charge;
use App\Models\ChartOfAccount;
use App\Models\CustomField;
use App\Models\Loan;
use App\Models\LoanDisbursedBy;
use App\Models\LoanFee;
use App\Models\LoanFeeMeta;
use App\Models\LoanOverduePenalty;
use App\Models\LoanProduct;
use App\Models\LoanProductCharge;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Clickatell\Api\ClickatellHttp;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class LoanProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['sentinel', 'branch']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = LoanProduct::all();

        return view('loan_product.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $charges = array();
        foreach (Charge::where('product', 'loan')->where('active', 1)->get() as $key) {
            $charges[$key->id] = $key->name;
        }
        $loan_disbursed_by = LoanDisbursedBy::all();
        $loan_overdue_penalties = array();
        foreach (LoanOverduePenalty::all() as $key) {
            $loan_overdue_penalties[$key->id] = $key->name;
        }
        $chart = [];
        $chart_expenses = array();
        foreach (ChartOfAccount::where('account_type', 'expense')->get() as $key) {
            $chart_expenses[$key->id] = $key->name;
        }
        $chart_income = array();
        foreach (ChartOfAccount::where('account_type', 'income')->get() as $key) {
            $chart_income[$key->id] = $key->name;
        }
        $chart_liability = array();
        foreach (ChartOfAccount::where('account_type', 'liability')->get() as $key) {
            $chart_liability[$key->id] = $key->name;
        }
        $chart_equity = array();
        foreach (ChartOfAccount::where('account_type', 'equity')->get() as $key) {
            $chart_equity[$key->id] = $key->name;
        }
        $chart_assets = array();
        foreach (ChartOfAccount::where('account_type', 'asset')->get() as $key) {
            $chart_assets[$key->id] = $key->name;
        }
        $chart[trans_choice('asset', 2)] = $chart_assets;
        $chart[trans_choice('income', 2)] = $chart_income;
        $chart[trans_choice('liability', 2)] = $chart_liability;
        $chart[trans_choice('equity', 2)] = $chart_equity;
        $chart[trans_choice('expense', 2)] = $chart_expenses;
        return view('loan_product.create',
            compact('loan_disbursed_by', 'loan_overdue_penalties', 'chart_expenses', 'chart_income',
                'chart_liability', 'chart_equity', 'chart_assets', 'chart', 'charges'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $loan_product = new LoanProduct();
        $loan_product->name = $request->name;

        if (empty($request->loan_disbursed_by_id)) {
            $loan_product->loan_disbursed_by_id = serialize(array());
        } else {
            $loan_product->loan_disbursed_by_id = serialize($request->loan_disbursed_by_id);
        }
        if (empty($request->repayment_order) || count($request->repayment_order) != 4) {
            Flash::warning(trans('general.repayment_order_error'));
            return redirect()->back()->withInput();
        } else {
            $loan_product->repayment_order = serialize($request->repayment_order);
        }
        $loan_product->minimum_principal = $request->minimum_principal;
        $loan_product->default_principal = $request->default_principal;
        $loan_product->maximum_principal = $request->maximum_principal;
        $loan_product->interest_method = $request->interest_method;
        $loan_product->default_interest_rate = $request->default_interest_rate;
        $loan_product->minimum_interest_rate = $request->minimum_interest_rate;
        $loan_product->maximum_interest_rate = $request->maximum_interest_rate;
        $loan_product->interest_period = $request->interest_period;
        $loan_product->default_loan_duration = $request->default_loan_duration;
        $loan_product->default_loan_duration_type = $request->default_loan_duration_type;
        $loan_product->repayment_cycle = $request->repayment_cycle;
        $loan_product->decimal_places = $request->decimal_places;
        $loan_product->override_interest = $request->override_interest;
        $loan_product->override_interest_amount = $request->override_interest_amount;
        $loan_product->grace_on_interest_charged = $request->grace_on_interest_charged;
        $loan_product->late_repayment_penalty_grace_period = $request->late_repayment_penalty_grace_period;
        $loan_product->after_maturity_date_penalty_grace_period = $request->after_maturity_date_penalty_grace_period;
        $loan_product->accounting_rule = $request->accounting_rule;
        $loan_product->chart_fund_source_id = $request->chart_fund_source_id;
        $loan_product->chart_loan_portfolio_id = $request->chart_loan_portfolio_id;
        $loan_product->chart_loan_over_payments_id = $request->chart_loan_over_payments_id;
        $loan_product->chart_income_interest_id = $request->chart_income_interest_id;
        $loan_product->chart_income_fee_id = $request->chart_income_fee_id;
        $loan_product->chart_income_penalty_id = $request->chart_income_penalty_id;
        $loan_product->chart_income_recovery_id = $request->chart_income_recovery_id;
        $loan_product->chart_loans_written_off_id = $request->chart_loans_written_off_id;
        $loan_product->chart_receivable_interest_id = $request->chart_receivable_interest_id;
        $loan_product->chart_receivable_fee_id = $request->chart_receivable_fee_id;
        $loan_product->chart_receivable_penalty_id = $request->chart_receivable_penalty_id;
        $loan_product->save();
        if (!empty($request->charges)) {
            //loop through the array
            foreach ($request->charges as $key) {
                $loan_product_charge = new LoanProductCharge();
                $loan_product_charge->loan_product_id = $loan_product->id;
                $loan_product_charge->user_id = Sentinel::getUser()->id;
                $loan_product_charge->charge_id = $key;
                $loan_product_charge->save();
            }
        }
        GeneralHelper::audit_trail("Added loan product with id:" . $loan_product->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/loan_product/data');
    }


    public function show($loan_product)
    {
        $charges = array();
        foreach (Charge::where('product', 'loan')->get() as $key) {
            $charges[$key->id] = $key->name;
        }
        return view('loan_product.show', compact('loan_product', 'charges'));
    }


    public function edit($loan_product)
    {
        $charges = array();
        foreach (Charge::where('product', 'loan')->where('active', 1)->get() as $key) {
            $charges[$key->id] = $key->name;
        }
        $loan_disbursed_by = LoanDisbursedBy::all();
        $repayment_order = array();
        foreach (unserialize($loan_product->repayment_order) as $key) {
            if ($key == "penalty") {
                $repayment_order['penalty'] = trans_choice('general.penalty', 1);
            }
            if ($key == "fees") {
                $repayment_order['fees'] = trans_choice('general.fee', 1);
            }
            if ($key == "interest") {
                $repayment_order['interest'] = trans_choice('general.interest', 1);
            }
            if ($key == "principal") {
                $repayment_order['principal'] = trans_choice('general.principal', 1);
            }
        }
        $loan_overdue_penalties = array();
        foreach (LoanOverduePenalty::all() as $key) {
            $loan_overdue_penalties[$key->id] = $key->name;
        }
        if (!empty($loan_product->after_maturity_date_penalties)) {
            $after_maturity_date_penalties = [];
            foreach (unserialize($loan_product->after_maturity_date_penalties) as $key) {
                array_push($after_maturity_date_penalties, (int)$key);
            }
        } else {
            $after_maturity_date_penalties = [];
        }
        $chart = [];
        $chart_expenses = array();
        foreach (ChartOfAccount::where('account_type', 'expense')->get() as $key) {
            $chart_expenses[$key->id] = $key->name;
        }
        $chart_income = array();
        foreach (ChartOfAccount::where('account_type', 'income')->get() as $key) {
            $chart_income[$key->id] = $key->name;
        }
        $chart_liability = array();
        foreach (ChartOfAccount::where('account_type', 'liability')->get() as $key) {
            $chart_liability[$key->id] = $key->name;
        }
        $chart_equity = array();
        foreach (ChartOfAccount::where('account_type', 'equity')->get() as $key) {
            $chart_equity[$key->id] = $key->name;
        }
        $chart_assets = array();
        foreach (ChartOfAccount::where('account_type', 'asset')->get() as $key) {
            $chart_assets[$key->id] = $key->name;
        }
        $chart[trans_choice('asset', 2)] = $chart_assets;
        $chart[trans_choice('income', 2)] = $chart_income;
        $chart[trans_choice('liability', 2)] = $chart_liability;
        $chart[trans_choice('equity', 2)] = $chart_equity;
        $chart[trans_choice('expense', 2)] = $chart_expenses;
        return view('loan_product.edit',
            compact('loan_product', 'loan_disbursed_by', 'repayment_order', 'loan_overdue_penalties',
                'after_maturity_date_penalties', 'chart_expenses', 'chart_income',
                'chart_liability', 'chart_equity', 'chart_assets', 'chart', 'charges'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $loan_product = LoanProduct::find($id);
        $loan_product->name = $request->name;
        if (empty($request->loan_disbursed_by_id)) {
            $loan_product->loan_disbursed_by_id = serialize(array());
        } else {
            $loan_product->loan_disbursed_by_id = serialize($request->loan_disbursed_by_id);
        }
        if (empty($request->repayment_order) || count($request->repayment_order) != 4) {
            Flash::warning(trans('general.repayment_order_error'));
            return redirect()->back()->withInput();
        } else {
            $loan_product->repayment_order = serialize($request->repayment_order);
        }
        $loan_product->minimum_principal = $request->minimum_principal;
        $loan_product->default_principal = $request->default_principal;
        $loan_product->maximum_principal = $request->maximum_principal;
        $loan_product->interest_method = $request->interest_method;
        $loan_product->default_interest_rate = $request->default_interest_rate;
        $loan_product->minimum_interest_rate = $request->minimum_interest_rate;
        $loan_product->maximum_interest_rate = $request->maximum_interest_rate;
        $loan_product->interest_period = $request->interest_period;
        $loan_product->default_loan_duration = $request->default_loan_duration;
        $loan_product->default_loan_duration_type = $request->default_loan_duration_type;
        $loan_product->repayment_cycle = $request->repayment_cycle;
        $loan_product->decimal_places = $request->decimal_places;
        $loan_product->loan_fees_schedule = $request->loan_fees_schedule;
        $loan_product->grace_on_interest_charged = $request->grace_on_interest_charged;
        $loan_product->late_repayment_penalty_grace_period = $request->late_repayment_penalty_grace_period;
        $loan_product->after_maturity_date_penalty_grace_period = $request->after_maturity_date_penalty_grace_period;
        $loan_product->accounting_rule = $request->accounting_rule;
        $loan_product->chart_fund_source_id = $request->chart_fund_source_id;
        $loan_product->chart_loan_portfolio_id = $request->chart_loan_portfolio_id;
        $loan_product->chart_loan_over_payments_id = $request->chart_loan_over_payments_id;
        $loan_product->chart_income_interest_id = $request->chart_income_interest_id;
        $loan_product->chart_income_fee_id = $request->chart_income_fee_id;
        $loan_product->chart_income_penalty_id = $request->chart_income_penalty_id;
        $loan_product->chart_income_recovery_id = $request->chart_income_recovery_id;
        $loan_product->chart_loans_written_off_id = $request->chart_loans_written_off_id;
        $loan_product->chart_receivable_interest_id = $request->chart_receivable_interest_id;
        $loan_product->chart_receivable_fee_id = $request->chart_receivable_fee_id;
        $loan_product->chart_receivable_penalty_id = $request->chart_receivable_penalty_id;
        $loan_product->save();
        LoanProductCharge::where('loan_product_id', $loan_product->id)->delete();
        if (!empty($request->charges)) {
            //loop through the array
            foreach ($request->charges as $key) {
                $loan_product_charge = new LoanProductCharge();
                $loan_product_charge->loan_product_id = $loan_product->id;
                $loan_product_charge->user_id = Sentinel::getUser()->id;
                $loan_product_charge->charge_id = $key;
                $loan_product_charge->save();
            }
        }
        GeneralHelper::audit_trail("Updated loan product with id:" . $loan_product->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/loan_product/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        LoanProduct::destroy($id);
        GeneralHelper::audit_trail("Deleted loan product with id:" . $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('loan/loan_product/data');
    }

    public function get_charge_detail($charge)
    {
        $json = [];
        $json["id"] = $charge->id;
        $json["name"] = $charge->name;
        if ($charge->charge_type == 'disbursement') {
            $json["collected_on"] = trans_choice('general.disbursement', 1);
        }
        if ($charge->charge_type == 'specified_due_date') {
            $json["collected_on"] = trans_choice('general.specified_due_date', 1);
        }
        if ($charge->charge_type == 'installment_fee') {
            $json["collected_on"] = trans_choice('general.installment_fee', 1);
        }
        if ($charge->charge_type == 'overdue_installment_fee') {
            $json["collected_on"] = trans_choice('general.overdue_installment_fee', 1);
        }
        if ($charge->charge_type == 'loan_rescheduling_fee') {
            $json["collected_on"] = trans_choice('general.loan_rescheduling_fee', 1);
        }
        if ($charge->charge_type == 'overdue_maturity') {
            $json["collected_on"] = trans_choice('general.overdue_maturity', 1);
        }
        if ($charge->charge_type == 'savings_activation') {
            $json["collected_on"] = trans_choice('general.savings_activation', 1);
        }
        if ($charge->charge_type == 'withdrawal_fee') {
            $json["collected_on"] = trans_choice('general.withdrawal_fee', 1);
        }
        if ($charge->charge_type == 'monthly_fee') {
            $json["collected_on"] = trans_choice('general.monthly_fee', 1);
        }
        if ($charge->charge_type == 'annual_fee') {
            $json["collected_on"] = trans_choice('general.annual_fee', 1);
        }
        if ($charge->charge_option == 'fixed') {
            $json["amount"] = $charge->amount . " " . trans_choice('general.fixed', 1);
        }
        if ($charge->charge_option == 'principal_due') {
            $json["amount"] = $charge->amount . " % " . trans_choice('general.principal',
                    1) . " " . trans_choice('general.due', 1);
        }
        if ($charge->charge_option == 'principal_interest') {
            $json["amount"] = $charge->amount . " % " . trans_choice('general.principal',
                    1) . " + " . trans_choice('general.interest', 1) . " " . trans_choice('general.due', 1);
        }
        if ($charge->charge_option == 'interest_due') {
            $json["amount"] = $charge->amount . " % " . trans_choice('general.interest',
                    1) . " " . trans_choice('general.due', 1);
        }
        if ($charge->charge_option == 'total_due') {
            $json["amount"] = $charge->amount . " % " . trans_choice('general.total',
                    1) . " " . trans_choice('general.due', 1);
        }
        if ($charge->charge_option == 'original_principal') {
            $json["amount"] = $charge->amount . " % " . trans_choice('general.original',
                    1) . " " . trans_choice('general.principal', 1);
        }

        return json_encode($json, JSON_UNESCAPED_SLASHES);
    }

}
