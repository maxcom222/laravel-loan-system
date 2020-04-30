<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Models\Charge;
use App\Models\ChartOfAccount;
use App\Models\CustomField;
use App\Models\SavingProduct;
use App\Models\SavingsProductCharge;
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

class SavingProductController extends Controller
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
        $data = SavingProduct::all();

        return view('savings_product.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $charges = array();
        foreach (Charge::where('product', 'savings')->where('active', 1)->get() as $key) {
            $charges[$key->id] = $key->name;
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
        $interest_posting = array();
        $interest_posting[1] = trans('general.every_1_month');
        $interest_posting[2] = trans('general.every_2_month');
        $interest_posting[3] = trans('general.every_3_month');
        $interest_posting[4] = trans('general.every_3_month');
        $interest_posting[5] = trans('general.every_4_month');
        $interest_posting[6] = trans('general.every_6_month');
        $interest_posting[7] = trans('general.every_12_month');
        $interest_adding = array();
        $interest_adding[1] = trans('general.1st_month');
        $interest_adding[2] = trans('general.2nd_month');
        $interest_adding[3] = trans('general.3rd_month');
        $interest_adding[4] = trans('general.4th_month');
        $interest_adding[5] = trans('general.5th_month');
        $interest_adding[6] = trans('general.6th_month');
        $interest_adding[7] = trans('general.7th_month');
        $interest_adding[8] = trans('general.8th_month');
        $interest_adding[9] = trans('general.9th_month');
        $interest_adding[10] = trans('general.10th_month');
        $interest_adding[11] = trans('general.11th_month');
        $interest_adding[12] = trans('general.12th_month');
        $interest_adding[13] = trans('general.13th_month');
        $interest_adding[14] = trans('general.14th_month');
        $interest_adding[15] = trans('general.15th_month');
        $interest_adding[16] = trans('general.16th_month');
        $interest_adding[17] = trans('general.17th_month');
        $interest_adding[18] = trans('general.18th_month');
        $interest_adding[19] = trans('general.19th_month');
        $interest_adding[20] = trans('general.20th_month');
        $interest_adding[21] = trans('general.21th_month');
        $interest_adding[22] = trans('general.22th_month');
        $interest_adding[23] = trans('general.23th_month');
        $interest_adding[24] = trans('general.24th_month');
        $interest_adding[25] = trans('general.25th_month');
        $interest_adding[26] = trans('general.26th_month');
        $interest_adding[27] = trans('general.27th_month');
        $interest_adding[28] = trans('general.28th_month');
        $interest_adding[29] = trans('general.29th_month');
        $interest_adding[30] = trans('general.30th_month');
        $interest_adding[31] = trans('general.31st_month');
        $interest_adding[0] = trans('general.end_month');
        return view('savings_product.create', compact('interest_posting','interest_adding', 'chart_expenses', 'chart_income',
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
        $savings_product = new SavingProduct();
        $savings_product->user_id = Sentinel::getUser()->id;
        $savings_product->name = $request->name;
        $savings_product->notes = $request->notes;
        $savings_product->allow_overdraw = $request->allow_overdraw;
        $savings_product->interest_rate = $request->interest_rate;
        $savings_product->minimum_balance = $request->minimum_balance;
        $savings_product->interest_posting = $request->interest_posting;
        $savings_product->interest_adding = $request->interest_adding;
        $savings_product->accounting_rule = $request->accounting_rule;
        $savings_product->chart_reference_id = $request->chart_reference_id;
        $savings_product->chart_overdraft_portfolio_id = $request->chart_overdraft_portfolio_id;
        $savings_product->chart_savings_control_id = $request->chart_savings_control_id;
        $savings_product->chart_income_interest_id = $request->chart_income_interest_id;
        $savings_product->chart_income_fee_id = $request->chart_income_fee_id;
        $savings_product->chart_income_penalty_id = $request->chart_income_penalty_id;
        $savings_product->chart_expense_interest_id = $request->chart_expense_interest_id;
        $savings_product->chart_expense_written_off_id = $request->chart_expense_written_off_id;
        $savings_product->save();
        if (!empty($request->charges)) {
            //loop through the array
            foreach ($request->charges as $key) {
                $savings_product_charge = new SavingsProductCharge();
                $savings_product_charge->savings_product_id = $savings_product->id;
                $savings_product_charge->user_id = Sentinel::getUser()->id;
                $savings_product_charge->charge_id = $key;
                $savings_product_charge->save();
            }
        }
        Flash::success(trans('general.successfully_saved'));
        return redirect('saving/savings_product/data');
    }


    public function show($savings_product)
    {
        $charges = array();
        foreach (Charge::where('product', 'savings')->get() as $key) {
            $charges[$key->id] = $key->name;
        }
        return view('savings_product.show', compact('savings_product','charges'));
    }


    public function edit($savings_product)
    {
        $charges = array();
        foreach (Charge::where('product', 'savings')->where('active', 1)->get() as $key) {
            $charges[$key->id] = $key->name;
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
        $interest_posting = array();
        $interest_posting[1] = trans('general.every_1_month');
        $interest_posting[2] = trans('general.every_2_month');
        $interest_posting[3] = trans('general.every_3_month');
        $interest_posting[4] = trans('general.every_3_month');
        $interest_posting[5] = trans('general.every_4_month');
        $interest_posting[6] = trans('general.every_6_month');
        $interest_posting[7] = trans('general.every_12_month');
        $interest_adding = array();
        $interest_adding[1] = trans('general.1st_month');
        $interest_adding[2] = trans('general.2nd_month');
        $interest_adding[3] = trans('general.3rd_month');
        $interest_adding[4] = trans('general.4th_month');
        $interest_adding[5] = trans('general.5th_month');
        $interest_adding[6] = trans('general.6th_month');
        $interest_adding[7] = trans('general.7th_month');
        $interest_adding[8] = trans('general.8th_month');
        $interest_adding[9] = trans('general.9th_month');
        $interest_adding[10] = trans('general.10th_month');
        $interest_adding[11] = trans('general.11th_month');
        $interest_adding[12] = trans('general.12th_month');
        $interest_adding[13] = trans('general.13th_month');
        $interest_adding[14] = trans('general.14th_month');
        $interest_adding[15] = trans('general.15th_month');
        $interest_adding[16] = trans('general.16th_month');
        $interest_adding[17] = trans('general.17th_month');
        $interest_adding[18] = trans('general.18th_month');
        $interest_adding[19] = trans('general.19th_month');
        $interest_adding[20] = trans('general.20th_month');
        $interest_adding[21] = trans('general.21th_month');
        $interest_adding[22] = trans('general.22th_month');
        $interest_adding[23] = trans('general.23th_month');
        $interest_adding[24] = trans('general.24th_month');
        $interest_adding[25] = trans('general.25th_month');
        $interest_adding[26] = trans('general.26th_month');
        $interest_adding[27] = trans('general.27th_month');
        $interest_adding[28] = trans('general.28th_month');
        $interest_adding[29] = trans('general.29th_month');
        $interest_adding[30] = trans('general.30th_month');
        $interest_adding[31] = trans('general.31st_month');
        $interest_adding[0] = trans('general.end_month');
        return view('savings_product.edit', compact('savings_product','interest_posting','interest_adding', 'chart_expenses', 'chart_income',
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
        $savings_product = SavingProduct::find($id);
        $savings_product->name = $request->name;
        $savings_product->notes = $request->notes;
        $savings_product->allow_overdraw = $request->allow_overdraw;
        $savings_product->interest_rate = $request->interest_rate;
        $savings_product->minimum_balance = $request->minimum_balance;
        $savings_product->interest_posting = $request->interest_posting;
        $savings_product->interest_adding = $request->interest_adding;
        $savings_product->accounting_rule = $request->accounting_rule;
        $savings_product->chart_reference_id = $request->chart_reference_id;
        $savings_product->chart_overdraft_portfolio_id = $request->chart_overdraft_portfolio_id;
        $savings_product->chart_savings_control_id = $request->chart_savings_control_id;
        $savings_product->chart_income_interest_id = $request->chart_income_interest_id;
        $savings_product->chart_income_fee_id = $request->chart_income_fee_id;
        $savings_product->chart_income_penalty_id = $request->chart_income_penalty_id;
        $savings_product->chart_expense_interest_id = $request->chart_expense_interest_id;
        $savings_product->chart_expense_written_off_id = $request->chart_expense_written_off_id;
        $savings_product->save();
        SavingsProductCharge::where('savings_product_id', $savings_product->id)->delete();
        if (!empty($request->charges)) {
            //loop through the array
            foreach ($request->charges as $key) {
                $savings_product_charge = new SavingsProductCharge();
                $savings_product_charge->savings_product_id = $savings_product->id;
                $savings_product_charge->user_id = Sentinel::getUser()->id;
                $savings_product_charge->charge_id = $key;
                $savings_product_charge->save();
            }
        }

        Flash::success(trans('general.successfully_saved'));
        return redirect('saving/savings_product/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        SavingProduct::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('saving/savings_product/data');
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
