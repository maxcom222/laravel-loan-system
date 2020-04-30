<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Models\CustomField;
use App\Models\JournalEntry;
use App\Models\LoanRepaymentMethod;
use App\Models\SavingFee;
use App\Models\SavingProduct;
use App\Models\SavingsProductCharge;
use App\Models\SavingTransaction;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use PDF;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class SavingTransactionController extends Controller
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
        $data = SavingTransaction::where('branch_id', session('branch_id'))->where('reversal_type','none')->get();

        return view('savings_transaction.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($saving)
    {
        $repayment_methods = array();
        foreach (LoanRepaymentMethod::all() as $key) {
            $repayment_methods[$key->id] = $key->name;
        }

        return view('savings_transaction.create', compact('saving', 'repayment_methods'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $saving)
    {
        $savings_transaction = new SavingTransaction();
        if ($request->type == "withdrawal" && GeneralHelper::savings_account_balance($saving->id) < $request->amount && $saving->savings_product->allow_overdraw == 0) {
            Flash::warning(trans('general.withdrawal_more_than_balance'));
            return redirect()->back()->withInput();
        }
        $savings_transaction->user_id = Sentinel::getUser()->id;
        $savings_transaction->borrower_id = $saving->borrower_id;
        $savings_transaction->branch_id = session('branch_id');
        $savings_transaction->payment_method_id = $request->payment_method_id;
        $savings_transaction->receipt = $request->receipt;
        $savings_transaction->savings_id = $saving->id;
        $savings_transaction->type = $request->type;
        $savings_transaction->reversible = 1;
        $savings_transaction->date = $request->date;
        $savings_transaction->time = $request->time;
        $date = explode('-', $request->date);
        $savings_transaction->year = $date[0];
        $savings_transaction->month = $date[1];
        $savings_transaction->notes = $request->notes;
        if ($request->type == "withdrawal") {
            $savings_transaction->debit = $request->amount;
        }
        if ($request->type == "deposit") {
            $savings_transaction->credit = $request->amount;
        }
        if ($request->type == "interest") {
            $savings_transaction->credit = $request->amount;
        }
        if ($request->type == "bank_fees") {
            $savings_transaction->debit = $request->amount;
        }
        $savings_transaction->save();
        //make journal transactions
        if ($request->type == "deposit") {
            if (!empty($saving->savings_product->chart_reference)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $saving->savings_product->chart_reference->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = $request->date;
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
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $saving->savings_product->chart_control->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = $request->date;
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
        }
        if ($request->type == "withdrawal") {

            if (!empty($saving->savings_product->chart_reference)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $saving->savings_product->chart_reference->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = $request->date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $savings_transaction->borrower_id;
                $journal->transaction_type = 'withdrawal';
                $journal->name = "Withdrawal";
                $journal->savings_id = $saving->id;
                $journal->credit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
            if (!empty($saving->savings_product->chart_control)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $saving->savings_product->chart_control->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = $request->date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $savings_transaction->borrower_id;
                $journal->transaction_type = 'withdrawal';
                $journal->name = "Withdrawal";
                $journal->savings_id = $saving->id;
                $journal->debit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
            //check for fees
            foreach (SavingsProductCharge::where('savings_product_id', $saving->savings_product_id)->get() as $tkey) {
                if (!empty($tkey->charge)) {
                    //specified due date charge
                    if ($tkey->charge->charge_type == "withdrawal_fee") {
                        if ($tkey->charge->charge_option == "fixed") {
                            $amount=$tkey->charge->amount;
                        }else{
                            $amount=$tkey->charge->amount*$request->amount/100;
                        }
                        $savings_transaction = new SavingTransaction();
                        $savings_transaction->user_id = Sentinel::getUser()->id;
                        $savings_transaction->borrower_id = $saving->borrower_id;
                        $savings_transaction->branch_id = $saving->branch_id;
                        $savings_transaction->savings_id = $saving->id;
                        $savings_transaction->type = "bank_fees";
                        $savings_transaction->reversible = 1;
                        $savings_transaction->date = date("Y-m-d");
                        $savings_transaction->time = date("H:i");
                        $date = explode('-', date("Y-m-d"));
                        $savings_transaction->year = $date[0];
                        $savings_transaction->month = $date[1];
                        $savings_transaction->debit = $amount;
                        $savings_transaction->save();
                        if (!empty($saving->savings_product->chart_reference)) {
                            $journal = new JournalEntry();
                            $journal->user_id = Sentinel::getUser()->id;
                            $journal->account_id = $saving->savings_product->chart_reference->id;
                            $journal->branch_id = $savings_transaction->branch_id;
                            $journal->date = date("Y-m-d");
                            $journal->year = $date[0];
                            $journal->month = $date[1];
                            $journal->borrower_id = $savings_transaction->borrower_id;
                            $journal->transaction_type = 'pay_charge';
                            $journal->name = "Charge";
                            $journal->savings_id = $saving->id;
                            $journal->credit =$amount;
                            $journal->reference = $savings_transaction->id;
                            $journal->save();
                        }
                        if (!empty($saving->savings_product->chart_control)) {
                            $journal = new JournalEntry();
                            $journal->user_id = Sentinel::getUser()->id;
                            $journal->account_id = $saving->savings_product->chart_control->id;
                            $journal->branch_id = $savings_transaction->branch_id;
                            $journal->date = date("Y-m-d");
                            $journal->year = $date[0];
                            $journal->month = $date[1];
                            $journal->borrower_id = $savings_transaction->borrower_id;
                            $journal->transaction_type = 'pay_charge';
                            $journal->name = "Charge";
                            $journal->savings_id = $saving->id;
                            $journal->debit = $amount;
                            $journal->reference = $savings_transaction->id;
                            $journal->save();
                        }
                    }


                }
            }
        }
        if ($request->type == "bank_fees") {
            if (!empty($saving->savings_product->chart_reference)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $saving->savings_product->chart_reference->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = $request->date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $savings_transaction->borrower_id;
                $journal->transaction_type = 'pay_charge';
                $journal->name = "Charge";
                $journal->savings_id = $saving->id;
                $journal->credit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
            if (!empty($saving->savings_product->chart_control)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $saving->savings_product->chart_control->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = $request->date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $savings_transaction->borrower_id;
                $journal->transaction_type = 'pay_charge';
                $journal->name = "Charge";
                $journal->savings_id = $saving->id;
                $journal->debit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
        }
        if ($request->type == "interest") {
            if (!empty($saving->savings_product->chart_expense_interest)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $saving->savings_product->chart_expense_interest->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = $request->date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $savings_transaction->borrower_id;
                $journal->transaction_type = 'interest';
                $journal->name = "Savings Interest";
                $journal->savings_id = $saving->id;
                $journal->debit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
            if (!empty($saving->savings_product->chart_control)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $saving->savings_product->chart_control->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = $request->date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $savings_transaction->borrower_id;
                $journal->transaction_type = 'interest';
                $journal->name = "Savings Interest";
                $journal->savings_id = $saving->id;
                $journal->credit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
        }
        Flash::success(trans('general.successfully_saved'));
        return redirect('saving/' . $saving->id . '/show');
    }


    public function show($savings_transaction)
    {

        return view('savings_transaction.show', compact('savings_transaction'));
    }


    public function edit($savings_transaction)
    {
        $repayment_methods = array();
        foreach (LoanRepaymentMethod::all() as $key) {
            $repayment_methods[$key->id] = $key->name;
        }
        if ($savings_transaction->type == "withdrawal") {
            $amount = $savings_transaction->debit;
        }
        if ($savings_transaction->type == "deposit") {
            $amount = $savings_transaction->credit;
        }
        if ($savings_transaction->type == "interest") {
            $amount = $savings_transaction->credit;
        }
        return view('savings_transaction.edit', compact('savings_transaction', 'repayment_methods', 'amount'));
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
        $savings_transaction = SavingTransaction::find($id);
        $savings_transaction->reversible = 0;
        $savings_transaction->reversed = 1;
        $savings_transaction->reversal_type = "user";
        if ($savings_transaction->debit > $savings_transaction->credit) {
            $savings_transaction->credit = $savings_transaction->debit;
        } else {
            $savings_transaction->debit = $savings_transaction->credit;
        }
        $savings_transaction->save();
        //reverse journal transactions
        foreach (JournalEntry::where('reference', $id)->where('savings_id',
            $savings_transaction->savings_id)->get() as $key) {
            $journal = JournalEntry::find($key->id);
            if ($key->debit > $key->credit) {
                $journal->credit = $journal->debit;
            } else {
                $journal->debit = $journal->credit;
            }
            $journal->reversed = 1;
            $journal->save();
        }
        $old = $savings_transaction;
        $savings_transaction = new SavingTransaction();
        $savings_transaction->user_id = Sentinel::getUser()->id;
        $savings_transaction->borrower_id = $old->borrower_id;
        $savings_transaction->branch_id = session('branch_id');
        $savings_transaction->payment_method_id = $request->payment_method_id;
        $savings_transaction->receipt = $request->receipt;
        $savings_transaction->savings_id = $old->savings_id;
        $savings_transaction->type = $old->type;
        $savings_transaction->reversible = 1;
        $savings_transaction->date = $request->date;
        $savings_transaction->time = $request->time;
        $date = explode('-', $request->date);
        $savings_transaction->year = $date[0];
        $savings_transaction->month = $date[1];
        $savings_transaction->notes = $request->notes;
        if ($old->type == "withdrawal") {
            $savings_transaction->debit = $request->amount;
        }
        if ($old->type == "bank_fees") {
            $savings_transaction->debit = $request->amount;
        }
        if ($old->type == "deposit") {
            $savings_transaction->credit = $request->amount;
        }
        if ($old->type == "interest") {
            $savings_transaction->credit = $request->amount;
        }
        $savings_transaction->save();
        //make journal transactions
        if ($savings_transaction->type == "deposit") {
            if (!empty($savings_transaction->savings->savings_product->chart_reference)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $savings_transaction->savings->savings_product->chart_reference->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = $request->date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $savings_transaction->borrower_id;
                $journal->transaction_type = 'deposit';
                $journal->name = "Deposit";
                $journal->savings_id = $savings_transaction->saving_id;
                $journal->debit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
            if (!empty($savings_transaction->savings->savings_product->chart_control)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $savings_transaction->savings->savings_product->chart_control->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = $request->date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $savings_transaction->borrower_id;
                $journal->transaction_type = 'deposit';
                $journal->name = "Deposit";
                $journal->savings_id = $savings_transaction->saving_id;
                $journal->credit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
        }
        if ($savings_transaction->type == "withdrawal") {
            if (!empty($savings_transaction->savings->savings_product->chart_reference)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $savings_transaction->savings->savings_product->chart_reference->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = $request->date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $savings_transaction->borrower_id;
                $journal->transaction_type = 'withdrawal';
                $journal->name = "Withdrawal";
                $journal->savings_id = $savings_transaction->saving_id;
                $journal->credit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
            if (!empty($savings_transaction->savings->savings_product->chart_control)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $savings_transaction->savings->savings_product->chart_control->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = $request->date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $savings_transaction->borrower_id;
                $journal->transaction_type = 'withdrawal';
                $journal->name = "Withdrawal";
                $journal->savings_id = $savings_transaction->saving_id;
                $journal->debit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
        }
        if ($savings_transaction->type == "bank_fees") {
            if (!empty($savings_transaction->savings->savings_product->chart_reference)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $savings_transaction->savings->savings_product->chart_reference->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = $request->date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $savings_transaction->borrower_id;
                $journal->transaction_type = 'pay_charge';
                $journal->name = "Charge";
                $journal->savings_id = $savings_transaction->saving_id;
                $journal->credit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
            if (!empty($savings_transaction->savings->savings_product->chart_control)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $savings_transaction->savings->savings_product->chart_control->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = $request->date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $savings_transaction->borrower_id;
                $journal->transaction_type = 'pay_charge';
                $journal->name = "Charge";
                $journal->savings_id = $savings_transaction->saving_id;
                $journal->debit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
        }
        if ($savings_transaction->type == "interest") {
            if (!empty($savings_transaction->savings->savings_product->chart_expense_interest)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $savings_transaction->savings->savings_product->chart_expense_interest->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = $request->date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $savings_transaction->borrower_id;
                $journal->transaction_type = 'interest';
                $journal->name = "Savings Interest";
                $journal->savings_id = $savings_transaction->saving_id;
                $journal->debit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
            if (!empty($savings_transaction->savings->savings_product->chart_control)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $savings_transaction->savings->savings_product->chart_control->id;
                $journal->branch_id = $savings_transaction->branch_id;
                $journal->date = $request->date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->borrower_id = $savings_transaction->borrower_id;
                $journal->transaction_type = 'interest';
                $journal->name = "Savings Interest";
                $journal->savings_id = $savings_transaction->saving_id;
                $journal->credit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
        }

        Flash::success(trans('general.successfully_saved'));
        return redirect('saving/' . $savings_transaction->savings_id . '/show');
    }

    public function reverse($id)
    {
        if (!Sentinel::hasAccess('repayments.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect('/');
        }
        $savings_transaction = SavingTransaction::find($id);
        $savings_transaction->reversible = 0;
        $savings_transaction->reversed = 1;
        $savings_transaction->reversal_type = "user";
        if ($savings_transaction->debit > $savings_transaction->credit) {
            $savings_transaction->credit = $savings_transaction->debit;
        } else {
            $savings_transaction->debit = $savings_transaction->credit;
        }
        $savings_transaction->save();
        //reverse journal transactions
        foreach (JournalEntry::where('reference', $id)->where('savings_id',
            $savings_transaction->savings_id)->get() as $key) {
            $journal = JournalEntry::find($key->id);
            if ($key->debit > $key->credit) {
                $journal->credit = $journal->debit;
            } else {
                $journal->debit = $journal->credit;
            }
            $journal->reversed = 1;
            $journal->save();
        }
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        SavingTransaction::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();
    }
//    print repayment
    public function pdf_transaction($savings_transaction)
    {
        $pdf = PDF::loadView('savings_transaction.pdf', compact('savings_transaction'));
        return $pdf->download($savings_transaction->borrower->title . ' ' . $savings_transaction->borrower->first_name . ' ' . $savings_transaction->borrower->last_name . " - Savings Transactions.pdf");

    }

    public function print_transaction($savings_transaction)
    {

        return view('savings_transaction.print', compact('savings_transaction'));
    }

}
