<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Events\RepaymentCreated;
use App\Helpers\GeneralHelper;
use App\Models\Borrower;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\JournalEntry;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\LoanSchedule;
use App\Models\LoanTransaction;
use App\Models\Saving;
use App\Models\SavingFee;
use App\Models\SavingProduct;
use App\Models\SavingsCharge;
use App\Models\SavingsProductCharge;
use App\Models\SavingTransaction;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Clickatell\Api\ClickatellHttp;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;
use PDF;

class SavingController extends Controller
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
        $data = Saving::where('branch_id', session('branch_id'))->get();

        return view('saving.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $borrowers = array();
        foreach (Borrower::all() as $key) {
            $borrowers[$key->id] = $key->first_name . ' ' . $key->last_name . '(' . $key->unique_number . ')';
        }
        $savings_products = array();
        foreach (SavingProduct::all() as $key) {
            $savings_products[$key->id] = $key->name;
        }
        if (isset($request->borrower_id)) {
            $borrower_id = $request->borrower_id;
        } else {
            $borrower_id = '';
        }
        if (isset($request->product_id)) {
            $savings_product = SavingProduct::find($request->product_id);
        } else {
            $savings_product = SavingProduct::first();
        }
        if (empty($savings_product)) {
            Flash::warning("No Savings product set. You must first set a savings product");
            return redirect()->back();
        }
        $charges = array();
        foreach (SavingsProductCharge::where('savings_product_id', $savings_product->id)->get() as $key) {
            if (!empty($key->charge)) {
                $charges[$key->id] = $key->charge->name;
            }

        }
        return view('saving.create',
            compact('savings_products', 'borrowers', 'borrower_id', 'charges', 'savings_product'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $saving = new Saving();
        $saving->user_id = Sentinel::getUser()->id;
        $saving->savings_product_id = $request->savings_product_id;
        $saving->borrower_id = $request->borrower_id;
        $saving->branch_id = session('branch_id');
        $saving->notes = $request->notes;
        $saving->date = $request->date;
        $date = explode('-', $request->date);
        $saving->month = $date[1];
        $saving->year = $date[0];
        $saving->save();
        if (!empty($request->charges)) {
            //loop through the array
            foreach ($request->charges as $key) {
                $amount = "charge_amount_" . $key;
                $date = "charge_date_" . $key;
                $savings_charge = new SavingsCharge();
                $savings_charge->savings_id = $saving->id;
                $savings_charge->user_id = Sentinel::getUser()->id;
                $savings_charge->charge_id = $key;
                $savings_charge->amount = $request->$amount;
                if (!empty($request->$date)) {
                    $savings_charge->date = $request->$date;
                }
                $savings_charge->save();
            }
        }
        //check for fees
        foreach (SavingsProductCharge::where('savings_product_id', $saving->savings_product_id)->get() as $tkey) {
            if (!empty($tkey->charge)) {
                //specified due date charge
                if ($tkey->charge->charge_type == "savings_activation") {
                    $amount = $tkey->charge->amount;
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
                        $journal->credit = $amount;
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
        Flash::success(trans('general.successfully_saved'));
        return redirect('saving/data');
    }


    public function show($saving)
    {
        //$transactions = SavingTransaction::where('savings_id', $saving->id)->orderBy('date', 'desc')->orderBy('time','desc')->get();
        $transactions = array();
        $balance = 0;
        foreach (SavingTransaction::where('savings_id', $saving->id)->orderBy('date', 'asc')->orderBy('time',
            'asc')->get() as $key) {
            $savings_transactions = array();
            if ($key->type == 'deposit' || $key->type == 'interest' || $key->type == 'dividend' || $key->type == 'guarantee_restored') {
                $balance = $balance + $key->amount;
            } else {
                $balance = $balance - $key->amount;
            }
            $savings_transactions['id'] = $key->id;
            $savings_transactions['type'] = $key->type;
            $savings_transactions['time'] = $key->time;
            $savings_transactions['date'] = $key->date;
            $savings_transactions['amount'] = $key->amount;
            $savings_transactions['notes'] = $key->notes;
            $savings_transactions['user'] = $key->user;
            $savings_transactions['balance'] = $balance;
            array_push($transactions, $savings_transactions);
        }
        $transactions = array_reverse($transactions);
        $custom_fields = CustomFieldMeta::where('category', 'savings')->where('parent_id',
            $saving->id)->get();
        return view('saving.show', compact('saving', 'custom_fields', 'transactions'));
    }


    public function edit($saving)
    {
        $borrowers = array();
        foreach (Borrower::all() as $key) {
            $borrowers[$key->id] = $key->first_name . ' ' . $key->last_name . '(' . $key->unique_number . ')';
        }
        $savings_products = array();
        foreach (SavingProduct::all() as $key) {
            $savings_products[$key->id] = $key->name;
        }
        return view('saving.edit', compact('saving', 'savings_products', 'borrowers'));
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
        $saving = Saving::find($id);
        //$saving->savings_product_id = $request->savings_product_id;
        //$saving->borrower_id = $request->borrower_id;
        $saving->branch_id = session('branch_id');
        $saving->notes = $request->notes;
        $saving->date = $request->date;
        $date = explode('-', $request->date);
        $saving->month = $date[1];
        $saving->year = $date[0];
        $saving->save();
        SavingsCharge::where('savings_id', $id)->delete();
        if (!empty($request->charges)) {
            //loop through the array
            foreach ($request->charges as $key) {
                $amount = "charge_amount_" . $key;
                $date = "charge_date_" . $key;
                $savings_charge = new SavingsCharge();
                $savings_charge->savings_id = $saving->id;
                $savings_charge->user_id = Sentinel::getUser()->id;
                $savings_charge->charge_id = $key;
                $savings_charge->amount = $request->$amount;
                if (!empty($request->$date)) {
                    $savings_charge->date = $request->$date;
                }
                $savings_charge->save();
            }
        }

        Flash::success(trans('general.successfully_saved'));
        return redirect('saving/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        Saving::destroy($id);
        SavingTransaction::where('savings_id', $id)->delete();
        Flash::success(trans('general.successfully_deleted'));
        return redirect('saving/data');
    }

    public function printStatement($saving)
    {
        $custom_fields = CustomFieldMeta::where('category', 'savings')->where('parent_id',
            $saving->id)->get();
        return view('saving.print', compact('saving', 'custom_fields', 'transactions'));
    }

    public function pdfStatement($saving)
    {

        $custom_fields = CustomFieldMeta::where('category', 'savings')->where('parent_id',
            $saving->id)->get();
        $pdf = PDF::loadView('saving.pdf_statement',
            compact('saving', 'custom_fields', 'transactions'));
        return $pdf->download($saving->borrower->title . ' ' . $saving->borrower->first_name . ' ' . $saving->borrower->last_name . " - Savings Statement.pdf");

    }

    public function transfer($saving)
    {
        $loans = array();
        foreach (Loan::where('borrower_id', $saving->borrower_id)->get() as $key) {
            $loans[$key->id] = $key->borrower->first_name . ' ' . $key->borrower->last_name . '(' . trans_choice('general.loan',
                    1) . '#' . $key->id . ',' . trans_choice('general.due',
                    1) . ':' . GeneralHelper::loan_total_balance($key->id) . ')';
        }

        return view('saving.transfer', compact('saving', 'loans'));
    }

    public function storeTransfer(Request $request, $saving)
    {
        $loan = Loan::find($request->loan_id);
        if ($request->amount > round(GeneralHelper::loan_total_balance($loan->id), 2)) {
            Flash::warning("Amount is more than the balance(" . GeneralHelper::loan_total_balance($loan->id) . ')');
            return redirect()->back()->withInput();

        }
        if ($request->date > date("Y-m-d")) {
            Flash::warning(trans_choice('general.future_date_error', 1));
            return redirect()->back()->withInput();

        }
        if ($request->date < $loan->disbursed_date) {
            Flash::warning(trans_choice('general.early_date_error', 1));
            return redirect()->back()->withInput();

        }
        $savings_transaction = new SavingTransaction();
        if (GeneralHelper::savings_account_balance($saving->id) < $request->amount && $saving->savings_product->allow_overdraw == 0) {
            Flash::warning(trans('general.withdrawal_more_than_balance'));
            return redirect()->back()->withInput();
        }
        $savings_transaction->user_id = Sentinel::getUser()->id;
        $savings_transaction->borrower_id = $saving->borrower_id;
        $savings_transaction->branch_id = session('branch_id');
        $savings_transaction->savings_id = $saving->id;
        $savings_transaction->type = "transfer_loan";
        $savings_transaction->reversible = 1;
        $savings_transaction->reference = $request->loan_id;
        $savings_transaction->date = $request->date;
        $savings_transaction->time = $request->time;
        $date = explode('-', $request->date);
        $savings_transaction->year = $date[0];
        $savings_transaction->month = $date[1];
        $savings_transaction->debit = $request->amount;
        if (empty($request->notes)) {
            $savings_transaction->notes = "Transferred amount to <a href='" . url('loan/' . $request->loan_id . '/show') . "''>Loan #" . $request->loan_id . "</a>";
        } else {
            $savings_transaction->notes = $request->notes;
        }
        $savings_transaction->save();
        if (!empty($saving->savings_product->chart_reference)) {
            $journal = new JournalEntry();
            $journal->user_id = Sentinel::getUser()->id;
            $journal->account_id = $saving->savings_product->chart_reference->id;
            $journal->branch_id = $savings_transaction->branch_id;
            $journal->date = $request->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->borrower_id = $savings_transaction->borrower_id;
            $journal->transaction_type = 'transfer_fund';
            $journal->name = "Transfer fund";
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
            $journal->transaction_type = 'transfer_fund';
            $journal->name = "Transfer fund";
            $journal->savings_id = $saving->id;
            $journal->debit = $request->amount;
            $journal->reference = $savings_transaction->id;
            $journal->save();
        }
        //store the loan payment
        //add interest transaction
        $loan_transaction = new LoanTransaction();
        $loan_transaction->user_id = Sentinel::getUser()->id;
        $loan_transaction->branch_id = session('branch_id');
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->borrower_id = $loan->borrower_id;
        $loan_transaction->transaction_type = "transfer_fund";
        $loan_transaction->receipt = $savings_transaction->id;
        $loan_transaction->date = $request->date;
        $loan_transaction->reversible = 1;
        $date = explode('-', $request->date);
        $loan_transaction->year = $date[0];
        $loan_transaction->month = $date[1];
        $loan_transaction->credit = $request->amount;
        $loan_transaction->notes = $request->notes;
        $loan_transaction->save();
        //fire payment added event
        //debit and credit the necessary accounts
        $allocation = GeneralHelper::loan_allocate_payment($loan_transaction);
        //return $allocation;
        //principal
        if ($allocation['principal'] > 0) {
            if (!empty($loan->loan_product->chart_loan_portfolio)) {
                $journal = new JournalEntry();
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $loan->loan_product->chart_loan_portfolio->id;
                $journal->branch_id = $loan->branch_id;
                $journal->date = $request->date;
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
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $loan->loan_product->chart_fund_source->id;
                $journal->branch_id = $loan->branch_id;
                $journal->date = $request->date;
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
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $loan->loan_product->chart_income_interest->id;
                $journal->branch_id = $loan->branch_id;
                $journal->date = $request->date;
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
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $loan->loan_product->chart_receivable_interest->id;
                $journal->branch_id = $loan->branch_id;
                $journal->date = $request->date;
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
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $loan->loan_product->chart_income_fee->id;
                $journal->branch_id = $loan->branch_id;
                $journal->date = $request->date;
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
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $loan->loan_product->chart_receivable_fee->id;
                $journal->branch_id = $loan->branch_id;
                $journal->date = $request->date;
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
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $loan->loan_product->chart_income_penalty->id;
                $journal->branch_id = $loan->branch_id;
                $journal->date = $request->date;
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
                $journal->user_id = Sentinel::getUser()->id;
                $journal->account_id = $loan->loan_product->chart_receivable_penalty->id;
                $journal->branch_id = $loan->branch_id;
                $journal->date = $request->date;
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

        Flash::success(trans('general.successfully_saved'));
        return redirect('saving/' . $saving->id . '/show');
    }
}
