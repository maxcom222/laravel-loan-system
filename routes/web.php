<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
//route model binding
Route::model('custom_field', 'App\Models\CustomField');
Route::model('borrower', 'App\Models\Borrower');
Route::model('setting', 'App\Models\Setting');
Route::model('status', 'App\Models\LoanStatus');
Route::model('loan_comment', 'App\Models\LoanComment');
Route::model('loan_disbursed_by', 'App\Models\LoanDisbursedBy');
Route::model('loan_product', 'App\Models\LoanProduct');
Route::model('loan_fee', 'App\Models\LoanFee');
Route::model('repayment', 'App\Models\LoanRepayment');
Route::model('loan', 'App\Models\Loan');
Route::model('user', 'App\Models\User');
Route::model('expense', 'App\Models\Expense');
Route::model('expense_type', 'App\Models\ExpenseType');
Route::model('collateral', 'App\Models\Collateral');
Route::model('collateral_type', 'App\Models\CollateralType');
Route::model('other_income', 'App\Models\OtherIncome');
Route::model('other_income_type', 'App\Models\OtherIncomeType');
Route::model('payroll', 'App\Models\Payroll');
Route::model('loan_repayment_method', 'App\Models\LoanRepaymentMethod');
Route::model('permission', 'App\Models\Permission');
Route::model('loan_application', 'App\Models\LoanApplication');
Route::model('saving', 'App\Models\Saving');
Route::model('savings_product', 'App\Models\SavingProduct');
Route::model('savings_fee', 'App\Models\SavingFee');
Route::model('savings_transaction', 'App\Models\SavingTransaction');
Route::model('asset', 'App\Models\Asset');
Route::model('asset_type', 'App\Models\AssetType');
Route::model('asset_valuation', 'App\Models\AssetValuation');
Route::model('capital', 'App\Models\Capital');
Route::model('guarantor', 'App\Models\Guarantor');
Route::model('borrower_group', 'App\Models\BorrowerGroup');
Route::model('provision', 'App\Models\ProvisionRate');
Route::model('bank', 'App\Models\BankAccount');
Route::model('branch', 'App\Models\Branch');
Route::model('sms_gateway', 'App\Models\SmsGateway');
Route::model('product', 'App\Models\Product');
Route::model('warehouse', 'App\Models\Warehouse');
Route::model('product_category', 'App\Models\ProductCategory');
Route::model('supplier', 'App\Models\Supplier');
Route::model('product_check_in', 'App\Models\ProductCheckin');
Route::model('product_check_out', 'App\Models\ProductCheckout');
Route::model('product_check_in_item', 'App\Models\ProductCheckinItem');
Route::model('product_check_out_item', 'App\Models\ProductCheckoutItem');
Route::model('loan_overdue_penalty', 'App\Models\LoanOverduePenalty');
Route::model('chart_of_account', 'App\Models\ChartOfAccount');
Route::model('charge', 'App\Models\Charge');
Route::model('loan_transaction', 'App\Models\LoanTransaction');
//route for installation
Route::get('install', 'InstallController@index');
Route::group(['prefix' => 'install'], function () {
    Route::get('start', 'InstallController@index');
    Route::get('requirements', 'InstallController@requirements');
    Route::get('permissions', 'InstallController@permissions');
    Route::any('database', 'InstallController@database');
    Route::any('installation', 'InstallController@installation');
    Route::get('complete', 'InstallController@complete');

});
//cron route
Route::get('cron', 'CronController@index');
Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    return redirect('/');

});
Route::get('/config-cache', function () {
    Artisan::call('config:cache');
    return redirect('/');
});
Route::get('/', 'HomeController@index');
Route::get('error', 'HomeController@error');
Route::get('login', 'HomeController@login');
Route::get('client', 'HomeController@clientLogin');
Route::post('client', 'HomeController@processClientLogin');
Route::get('client_logout', 'HomeController@clientLogout');
Route::get('admin', 'HomeController@adminLogin');

Route::get('logout', 'HomeController@logout');
Route::post('login', 'HomeController@processLogin');
Route::post('register', 'HomeController@register');
Route::post('reset', 'HomeController@passwordReset');
Route::get('reset/{id}/{code}', 'HomeController@confirmReset');
Route::post('reset/{id}/{code}', 'HomeController@completeReset');
Route::get('check/{id}', 'HomeController@checkStatus');
Route::get('no_branch', [
    'middleware' => 'sentinel',
    function () {
        $error = "You don't have permission to access any branch. Please contact your system administrator.";
        return view('no_branch', compact('error'));
    }
]);
Route::get('dashboard', [
    'middleware' => ['sentinel', 'branch'],
    function () {
        $loans_released_monthly = array();
        $loan_collections_monthly = array();
        $date = date("Y-m-d");
        $start_date1 = date_format(date_sub(date_create($date),
            date_interval_create_from_date_string('1 years')),
            'Y-m-d');
        $start_date2 = date_format(date_sub(date_create($date),
            date_interval_create_from_date_string('1 years')),
            'Y-m-d');
        $monthly_actual_expected_data = [];
        $monthly_disbursed_loans_data = [];
        $loop_date = date_format(date_sub(date_create($date),
            date_interval_create_from_date_string('1 years')),
            'Y-m-d');
        for ($i = 1; $i < 14; $i++) {
            $d = explode('-', $loop_date);
            $actual = 0;
            $expected = 0;
            $principal = 0;
            $actual = $actual + \App\Models\LoanTransaction::where('transaction_type',
                    'repayment')->where('reversed', 0)->where('year',
                    $d[0])->where('month',
                    $d[1])->where('branch_id',
                    session('branch_id'))->sum('credit');
            foreach (\App\Models\Loan::select("loan_schedules.principal", "loan_schedules.interest", "loan_schedules.penalty",
                "loan_schedules.fees")->where('loans.branch_id',
                session('branch_id'))->whereIn('loans.status',
                ['disbursed', 'closed', 'written_off'])->join('loan_schedules', 'loans.id', '=',
                'loan_schedules.loan_id')->where('loan_schedules.deleted_at', NULL)->where('loan_schedules.year',
                $d[0])->where('loan_schedules.month',
                $d[1])->get() as $key) {
                $expected = $expected + $key->interest + $key->penalty + $key->fees + $key->principal;
                $principal = $principal + $key->principal;

            }
            array_push($monthly_actual_expected_data, array(
                'month' => date_format(date_create($loop_date),
                    'M' . ' ' . $d[0]),
                'actual' => $actual,
                'expected' => $expected
            ));
            array_push($monthly_disbursed_loans_data, array(
                'month' => date_format(date_create($loop_date),
                    'M' . ' ' . $d[0]),
                'value' => $principal,
            ));
            //add 1 month to start date
            $loop_date = date_format(date_add(date_create($loop_date),
                date_interval_create_from_date_string('1 months')),
                'Y-m-d');
        }
        //daily users
        $loan_statuses = [];
        array_push($loan_statuses, array(
            'label' => trans_choice('general.pending', 1),
            'value' => \App\Models\Loan::where('status', 'pending')->count(),
            'color' => "#FF8A65",
            'highlight' => "#FF8A65",
            'link' => url('loan/data?status=pending'),
            'class' => "warning-300",

        ));
        array_push($loan_statuses, array(
            'label' => trans_choice('general.approved', 1),
            'value' => \App\Models\Loan::where('status', 'approved')->count(),
            'color' => "#64B5F6",
            'highlight' => "#64B5F6",
            'link' => url('loan/data?status=approved'),
            'class' => "primary-300",

        ));

        array_push($loan_statuses, array(
            'label' => trans_choice('general.disbursed', 1),
            'value' => \App\Models\Loan::where('status', 'disbursed')->count(),
            'color' => "#1565C0",
            'highlight' => "#1565C0",
            'link' => url('loan/data?status=disbursed'),
            'class' => "primary-800",

        ));
        array_push($loan_statuses, array(
            'label' => trans_choice('general.rescheduled', 1),
            'value' => \App\Models\Loan::where('status', 'rescheduled')->count(),
            'color' => "#00ACC1",
            'highlight' => "#00ACC1",
            'link' => url('loan/data?status=rescheduled'),
            'class' => "info-600",

        ));
        array_push($loan_statuses, array(
            'label' => trans_choice('general.written_off', 1),
            'value' => \App\Models\Loan::where('status', 'written_off')->count(),
            'color' => "#D32F2F",
            'highlight' => "#D32F2F",
            'link' => url('loan/data?status=written_off'),
            'class' => "danger-700",

        ));
        array_push($loan_statuses, array(
            'label' => trans_choice('general.declined', 1),
            'value' => \App\Models\Loan::where('status', 'declined')->count(),
            'color' => "#EF5350",
            'highlight' => "#EF5350",
            'link' => url('loan/data?status=declined'),
            'class' => "danger-400",

        ));
        array_push($loan_statuses, array(
            'label' => trans_choice('general.closed', 1),
            'value' => \App\Models\Loan::where('status', 'closed')->count(),
            'color' => "#66BB6A",
            'highlight' => "#66BB6A",
            'link' => url('loan/data?status=closed'),
            'class' => "success-400",

        ));
        $monthly_actual_expected_data = json_encode($monthly_actual_expected_data);
        $monthly_disbursed_loans_data = json_encode($monthly_disbursed_loans_data);
        $loan_statuses = json_encode($loan_statuses);
        $loans_released_monthly = json_encode($loans_released_monthly);
        $loan_collections_monthly = json_encode($loan_collections_monthly);
        //test mpesa here


        return view('dashboard', compact('monthly_actual_expected_data', 'monthly_disbursed_loans_data', 'loan_statuses'));
    }
]);
//route for custom fields
Route::group(['prefix' => 'custom_field'], function () {

    Route::get('data', 'CustomFieldController@index');
    Route::get('create', 'CustomFieldController@create');
    Route::post('store', 'CustomFieldController@store');
    Route::get('{custom_field}/show', 'CustomFieldController@show');
    Route::get('{custom_field}/edit', 'CustomFieldController@edit');
    Route::post('{id}/update', 'CustomFieldController@update');
    Route::get('{id}/delete', 'CustomFieldController@delete');

});
//route for borrowers
Route::group(['prefix' => 'borrower'], function () {

    Route::get('data', 'BorrowerController@index');
    Route::get('pending', 'BorrowerController@pending');
    Route::get('create', 'BorrowerController@create');
    Route::post('store', 'BorrowerController@store');
    Route::get('{borrower}/show', 'BorrowerController@show');
    Route::get('{borrower}/edit', 'BorrowerController@edit');
    Route::post('{id}/update', 'BorrowerController@update');
    Route::get('{id}/delete', 'BorrowerController@delete');
    Route::get('{id}/approve', 'BorrowerController@approve');
    Route::get('{id}/decline', 'BorrowerController@decline');
    Route::get('{id}/delete_file', 'BorrowerController@deleteFile');
    Route::get('{id}/blacklist', 'BorrowerController@blacklist');
    Route::get('{id}/unblacklist', 'BorrowerController@unBlacklist');
    //borrower group
    Route::get('group/data', 'BorrowerGroupController@index');
    Route::get('group/create', 'BorrowerGroupController@create');
    Route::post('group/store', 'BorrowerGroupController@store');
    Route::get('group/{borrower_group}/show', 'BorrowerGroupController@show');
    Route::get('group/{borrower_group}/edit', 'BorrowerGroupController@edit');
    Route::post('group/{id}/update', 'BorrowerGroupController@update');
    Route::get('group/{id}/delete', 'BorrowerGroupController@delete');
    Route::post('group/{id}/add_borrower', 'BorrowerGroupController@addBorrower');
    Route::get('group/{id}/remove_borrower', 'BorrowerGroupController@removeBorrower');
});
//route for guarantors
Route::group(['prefix' => 'guarantor'], function () {

    Route::get('data', 'GuarantorController@index');
    Route::get('pending', 'GuarantorController@pending');
    Route::get('create', 'GuarantorController@create');
    Route::post('store', 'GuarantorController@store');
    Route::get('{guarantor}/show', 'GuarantorController@show');
    Route::get('{guarantor}/edit', 'GuarantorController@edit');
    Route::post('{id}/update', 'GuarantorController@update');
    Route::get('{id}/delete', 'GuarantorController@delete');

});

Route::get('update',
    function () {
        \Illuminate\Support\Facades\Artisan::call('migrate');
        \Laracasts\Flash\Flash::success("Successfully Updated");
        return redirect('/');
    });
Route::get('migrate_seed',
    function () {
        \Illuminate\Support\Facades\Artisan::call('migrate');
        \Illuminate\Support\Facades\Artisan::call('db:seed');
        \Laracasts\Flash\Flash::success("Successfully Installed");
        return redirect('/');
    });
Route::group(['prefix' => 'update'], function () {
    Route::get('download', 'UpdateController@download');
    Route::get('install', 'UpdateController@install');
    Route::get('clean', 'UpdateController@clean');
    Route::get('finish', 'UpdateController@finish');
});
Route::get('fix', 'UpdateController@fix');
Route::get('fix_schedules', 'UpdateController@fix_schedules');
Route::get('set_default_branch', 'UpdateController@set_default_branch');
Route::get('update_2_0', 'UpdateController@update_2_0');
Route::get('update_2_0_1', 'UpdateController@update_2_0_1');
Route::get('update_2_0_2', 'UpdateController@update_2_0_2');
Route::get('update_2_0_3', 'UpdateController@update_2_0_3');
//route for setting
Route::group(['prefix' => 'setting'], function () {
    Route::get('data', 'SettingController@index');
    Route::post('update', 'SettingController@update');
    Route::get('update_system', 'SettingController@updateSystem');
});
//route for user
Route::group(['prefix' => 'user'], function () {
    Route::get('data', 'UserController@index');
    Route::get('create', 'UserController@create');
    Route::post('store', 'UserController@store');
    Route::get('{user}/edit', 'UserController@edit');
    Route::get('{user}/show', 'UserController@show');
    Route::post('{id}/update', 'UserController@update');
    Route::get('{id}/delete', 'UserController@delete');
    Route::get('profile', 'UserController@profile');
    Route::post('profile', 'UserController@profileUpdate');
    //manage permissions
    Route::get('permission/data', 'UserController@indexPermission');
    Route::get('permission/create', 'UserController@createPermission');
    Route::post('permission/store', 'UserController@storePermission');
    Route::get('permission/{permission}/edit', 'UserController@editPermission');
    Route::post('permission/{id}/update', 'UserController@updatePermission');
    Route::get('permission/{id}/delete', 'UserController@deletePermission');
    //manage roles
    Route::get('role/data', 'UserController@indexRole');
    Route::get('role/create', 'UserController@createRole');
    Route::post('role/store', 'UserController@storeRole');
    Route::get('role/{id}/edit', 'UserController@editRole');
    Route::post('role/{id}/update', 'UserController@updateRole');
    Route::get('role/{id}/delete', 'UserController@deleteRole');
});
//route for loans
Route::group(['prefix' => 'loan'], function () {
    //main loan routes
    Route::get('data', 'LoanController@index');
    Route::get('pending_approval', 'LoanController@pendingApproval');
    Route::get('pending_disbursement', 'LoanController@pendingDisbursement');
    Route::get('declined', 'LoanController@declined');
    Route::get('withdrawn', 'LoanController@withdrawn');
    Route::get('written_off', 'LoanController@writtenOff');
    Route::get('closed', 'LoanController@closed');
    Route::get('pending_reschedule', 'LoanController@pendingReschedule');
    Route::get('{id}/reschedule', 'LoanController@reschedule');
    Route::post('{id}/reschedule/store', 'LoanController@rescheduleStore');
    Route::post('{id}/approve', 'LoanController@approve');
    Route::get('{id}/unapprove', 'LoanController@unapprove');
    Route::post('{id}/decline', 'LoanController@decline');
    Route::post('{loan}/disburse', 'LoanController@disburse');
    Route::get('{id}/undisburse', 'LoanController@undisburse');
    Route::post('{id}/withdraw', 'LoanController@withdraw');
    Route::post('{id}/write_off', 'LoanController@write_off');
    Route::post('{id}/reschedule', 'LoanController@reschedule');
    Route::get('{id}/unwithdraw', 'LoanController@unwithdraw');
    Route::get('{id}/unwrite_off', 'LoanController@unwrite_off');
    Route::post('{loan}/waive_interest', 'LoanController@waiveInterest');
    Route::post('{loan}/add_charge', 'LoanController@addCharge');
    Route::get('create', 'LoanController@create');
    Route::post('store', 'LoanController@store');
    Route::get('{loan}/edit', 'LoanController@edit');
    Route::get('{loan}/show', 'LoanController@show');
    Route::any('{loan}/override', 'LoanController@override');
    Route::post('{id}/update', 'LoanController@update');
    Route::get('{id}/delete', 'LoanController@delete');
    Route::get('{id}/delete_file', 'LoanController@deleteFile');
    Route::get('{loan}/loan_statement/print', 'LoanController@printLoanStatement');
    Route::get('{loan}/loan_statement/pdf', 'LoanController@pdfLoanStatement');
    Route::get('{loan}/loan_statement/email', 'LoanController@emailLoanStatement');
    Route::get('{borrower}/borrower_statement/print', 'LoanController@printBorrowerStatement');
    Route::get('{borrower}/borrower_statement/pdf', 'LoanController@pdfBorrowerStatement');
    Route::get('{borrower}/borrower_statement/email', 'LoanController@emailBorrowerStatement');
    //loan repayment routes
    Route::get('repayment/{id}/reverse', 'LoanController@reverseRepayment');
    Route::get('{loan}/repayment/data', 'LoanController@indexRepayment');
    Route::get('{loan}/repayment/create', 'LoanController@createRepayment');
    Route::get('repayment/create', 'LoanController@addRepayment');
    Route::post('{loan}/repayment/store', 'LoanController@storeRepayment');
    Route::get('repayment/{loan_transaction}/edit', 'LoanController@editRepayment');
    Route::get('repayment/show', 'LoanController@showRepayment');
    Route::post('repayment/{id}/update', 'LoanController@updateRepayment');
    Route::get('repayment/{loan_transaction}/pdf', 'LoanController@pdfRepayment');
    Route::get('repayment/{loan_transaction}/email', 'LoanController@emailRepayment');
    Route::get('repayment/{loan_transaction}/print', 'LoanController@printRepayment');
    //transactions
    Route::get('transaction/{loan_transaction}/show', 'LoanController@showTransaction');
    Route::get('transaction/{id}/waive', 'LoanController@waiveTransaction');
    Route::get('transaction/{loan_transaction}/print', 'LoanController@printTransaction');
    Route::get('transaction/{loan_transaction}/pdf', 'LoanController@pdfTransaction');
    Route::get('transaction/{loan_transaction}/email', 'LoanController@emailTransaction');
    //comment routes
    Route::get('loan_comment/data', 'LoanCommentController@index');
    Route::get('{id}/loan_comment/create', 'LoanCommentController@create');
    Route::post('{id}/loan_comment/store', 'LoanCommentController@store');
    Route::get('{id}/loan_comment/{loan_comment}/edit', 'LoanCommentController@edit');
    Route::get('{id}/loan_comment/{loan_comment}/show', 'LoanCommentController@show');
    Route::post('{id}/loan_comment/{cid}/update', 'LoanCommentController@update');
    Route::get('{id}/loan_comment/{cid}/delete', 'LoanCommentController@delete');
    //status routes
    Route::get('loan_status/data', 'LoanStatusController@index');
    Route::get('loan_status/create', 'LoanStatusController@create');
    Route::post('loan_status/store', 'LoanStatusController@store');
    Route::get('loan_status/{loan_status}/edit', 'LoanStatusController@edit');
    Route::get('loan_status/{loan_status}/show', 'LoanStatusController@show');
    Route::post('loan_status/{id}/update', 'LoanStatusController@update');
    Route::get('loan_status/{id}/delete', 'LoanStatusController@delete');
    //routes for disbursed by
    Route::get('loan_disbursed_by/data', 'LoanDisbursedByController@index');
    Route::get('loan_disbursed_by/create', 'LoanDisbursedByController@create');
    Route::post('loan_disbursed_by/store', 'LoanDisbursedByController@store');
    Route::get('loan_disbursed_by/{loan_disbursed_by}/edit', 'LoanDisbursedByController@edit');
    Route::get('loan_disbursed_by/{loan_disbursed_by}/show', 'LoanDisbursedByController@show');
    Route::post('loan_disbursed_by/{id}/update', 'LoanDisbursedByController@update');
    Route::get('loan_disbursed_by/{id}/delete', 'LoanDisbursedByController@delete');
    //routes for repayment method
    Route::get('loan_repayment_method/data', 'LoanRepaymentMethodController@index');
    Route::get('loan_repayment_method/create', 'LoanRepaymentMethodController@create');
    Route::post('loan_repayment_method/store', 'LoanRepaymentMethodController@store');
    Route::get('loan_repayment_method/{loan_repayment_method}/edit', 'LoanRepaymentMethodController@edit');
    Route::get('loan_repayment_method/{loan_repayment_method}/show', 'LoanRepaymentMethodController@show');
    Route::post('loan_repayment_method/{id}/update', 'LoanRepaymentMethodController@update');
    Route::get('loan_repayment_method/{id}/delete', 'LoanRepaymentMethodController@delete');
    //routes for loan product
    Route::get('loan_product/data', 'LoanProductController@index');
    Route::get('loan_product/create', 'LoanProductController@create');
    Route::post('loan_product/store', 'LoanProductController@store');
    Route::get('loan_product/{loan_product}/edit', 'LoanProductController@edit');
    Route::get('loan_product/{loan_product}/show', 'LoanProductController@show');
    Route::get('loan_product/get_charge_detail/{charge}', 'LoanProductController@get_charge_detail');
    Route::post('loan_product/{id}/update', 'LoanProductController@update');
    Route::get('loan_product/{id}/delete', 'LoanProductController@delete');
    //route for managing schedules
    Route::get('{loan}/schedule/edit', 'LoanController@editSchedule');
    Route::post('{loan}/schedule/update', 'LoanController@updateSchedule');
    Route::get('{loan}/schedule/print', 'LoanController@printSchedule');
    Route::get('{loan}/schedule/pdf', 'LoanController@pdfSchedule');
    Route::get('{loan}/schedule/email', 'LoanController@emailLoanSchedule');
    //routes for repayment method
    Route::get('loan_fee/data', 'LoanFeeController@index');
    Route::get('loan_fee/create', 'LoanFeeController@create');
    Route::post('loan_fee/store', 'LoanFeeController@store');
    Route::get('loan_fee/{loan_fee}/edit', 'LoanFeeController@edit');
    Route::get('loan_fee/{loan_fee}/show', 'LoanFeeController@show');
    Route::post('loan_fee/{id}/update', 'LoanFeeController@update');
    Route::get('loan_fee/{id}/delete', 'LoanFeeController@delete');
    //routes for repayment method
    Route::get('loan_overdue_penalty/data', 'LoanOverduePenaltyController@index');
    Route::get('loan_overdue_penalty/create', 'LoanOverduePenaltyController@create');
    Route::post('loan_overdue_penalty/store', 'LoanOverduePenaltyController@store');
    Route::get('loan_overdue_penalty/{loan_overdue_penalty}/edit', 'LoanOverduePenaltyController@edit');
    Route::get('loan_overdue_penalty/{loan_overdue_penalty}/show', 'LoanOverduePenaltyController@show');
    Route::post('loan_overdue_penalty/{id}/update', 'LoanOverduePenaltyController@update');
    Route::get('loan_overdue_penalty/{id}/delete', 'LoanOverduePenaltyController@delete');
    //routes for applications
    Route::get('loan_application/data', 'LoanController@indexApplication');
    Route::get('loan_application/{id}/decline', 'LoanController@declineApplication');
    Route::get('loan_application/{id}/approve', 'LoanController@approveApplication');
    Route::post('loan_application/{id}/store', 'LoanController@storeApproveApplication');
    //routes for guarantors
    Route::get('{loan}/guarantor/data', 'GuarantorController@index');
    Route::post('{loan}/guarantor/add', 'LoanController@add_guarantor');
    Route::get('guarantor/{id}/remove', 'LoanController@remove_guarantor');

    //loan calculator
    Route::get('loan_calculator/create', 'LoanController@createLoanCalculator');
    Route::post('loan_calculator/show', 'LoanController@showLoanCalculator');
    Route::post('loan_calculator/store', 'LoanController@storeLoanCalculator');
    //routes for provision rates
    Route::get('provision/data', 'ProvisionRateController@index');
    Route::get('provision/create', 'ProvisionRateController@create');
    Route::post('provision/store', 'ProvisionRateController@store');
    Route::get('provision/{provision}/edit', 'ProvisionRateController@edit');
    Route::get('provision/{provision}/show', 'ProvisionRateController@show');
    Route::post('provision/{id}/update', 'ProvisionRateController@update');
    Route::get('provision/{id}/delete', 'ProvisionRateController@delete');
});
//loan repayment list
Route::get('repayment/data', 'LoanController@indexRepayment');
Route::get('repayment/create', 'LoanController@addRepayment');
Route::get('repayment/bulk/create', 'LoanController@createBulkRepayment');
Route::post('repayment/bulk/store', 'LoanController@storeBulkRepayment');
//route for tax
Route::group(['prefix' => 'tax'], function () {
    Route::get('data', 'TaxController@index');
    Route::get('create', 'TaxController@create');
    Route::post('store', 'TaxController@store');
    Route::get('{tax}/edit', 'TaxController@edit');
    Route::get('{id}/show', 'TaxController@show');
    Route::post('{id}/update', 'TaxController@update');
    Route::get('{id}/delete', 'TaxController@destroy');
});
//route for payroll
Route::group(['prefix' => 'payroll'], function () {
    Route::get('data', 'PayrollController@index');
    Route::get('create', 'PayrollController@create');
    Route::post('store', 'PayrollController@store');
    Route::get('{payroll}/show', 'PayrollController@show');
    Route::get('{payroll}/edit', 'PayrollController@edit');
    Route::post('{id}/update', 'PayrollController@update');
    Route::get('{id}/delete', 'PayrollController@delete');
    Route::get('getUser/{id}', 'PayrollController@getUser');
    Route::get('{payroll}/payslip', 'PayrollController@pdfPayslip');
    Route::get('{user}/data', 'PayrollController@staffPayroll');
//template
    Route::any('template', 'PayrollController@indexTemplate');
    Route::get('template/{id}/edit', 'PayrollController@editTemplate');
    Route::post('template/{id}/update', 'PayrollController@updateTemplate');
    Route::get('template/{id}/delete_meta', 'PayrollController@deleteTemplateMeta');
    Route::post('template/{id}/add_row', 'PayrollController@addTemplateRow');
});
//route for expenses
Route::group(['prefix' => 'expense'], function () {
    Route::get('data', 'ExpenseController@index');
    Route::get('create', 'ExpenseController@create');
    Route::post('store', 'ExpenseController@store');
    Route::get('{expense}/edit', 'ExpenseController@edit');
    Route::get('{expense}/show', 'ExpenseController@show');
    Route::post('{id}/update', 'ExpenseController@update');
    Route::get('{id}/delete', 'ExpenseController@delete');
    Route::get('{id}/delete_file', 'ExpenseController@deleteFile');

    //expense types
    Route::get('type/data', 'ExpenseController@indexType');
    Route::get('type/create', 'ExpenseController@createType');
    Route::post('type/store', 'ExpenseController@storeType');
    Route::get('type/{expense_type}/edit', 'ExpenseController@editType');
    Route::get('type/{expense_type}/show', 'ExpenseController@showType');
    Route::post('type/{id}/update', 'ExpenseController@updateType');
    Route::get('type/{id}/delete', 'ExpenseController@deleteType');
});
//route for other income
Route::group(['prefix' => 'other_income'], function () {
    Route::get('data', 'OtherIncomeController@index');
    Route::get('create', 'OtherIncomeController@create');
    Route::post('store', 'OtherIncomeController@store');
    Route::get('{other_income}/edit', 'OtherIncomeController@edit');
    Route::get('{other_income}/show', 'OtherIncomeController@show');
    Route::post('{id}/update', 'OtherIncomeController@update');
    Route::get('{id}/delete', 'OtherIncomeController@delete');
    Route::get('{id}/delete_file', 'OtherIncomeController@deleteFile');
    //income types
    Route::get('type/data', 'OtherIncomeController@indexType');
    Route::get('type/create', 'OtherIncomeController@createType');
    Route::post('type/store', 'OtherIncomeController@storeType');
    Route::get('type/{other_income_type}/edit', 'OtherIncomeController@editType');
    Route::get('type/{other_income_type}/show', 'OtherIncomeController@showType');
    Route::post('type/{id}/update', 'OtherIncomeController@updateType');
    Route::get('type/{id}/delete', 'OtherIncomeController@deleteType');
});
//route for collateral
Route::group(['prefix' => 'collateral'], function () {
    Route::get('data', 'CollateralController@index');
    Route::get('{id}/create', 'CollateralController@create');
    Route::post('{loan}/store', 'CollateralController@store');
    Route::get('{collateral}/edit', 'CollateralController@edit');
    Route::get('{collateral}/show', 'CollateralController@show');
    Route::post('{id}/update', 'CollateralController@update');
    Route::get('{id}/delete', 'CollateralController@delete');
    Route::get('{id}/delete_file', 'CollateralController@deleteFile');
    // types
    Route::get('type/data', 'CollateralController@indexType');
    Route::get('type/fix/create', 'CollateralController@createType');
    Route::post('type/fix/store', 'CollateralController@storeType');
    Route::get('type/{collateral_type}/edit', 'CollateralController@editType');
    Route::get('type/{collateral_type}/show', 'CollateralController@showType');
    Route::post('type/{id}/update', 'CollateralController@updateType');
    Route::get('type/{id}/delete', 'CollateralController@deleteType');
});
//route for reports
Route::group(['prefix' => 'report'], function () {
    Route::any('borrower_report', 'ReportController@borrower_report');
    Route::any('loan_report', 'ReportController@loan_report');
    Route::any('financial_report', 'ReportController@financial_report');
    Route::any('company_report', 'ReportController@company_report');
    Route::any('savings_report', 'ReportController@savings_report');
    Route::group(['prefix' => 'financial_report'], function () {
        Route::any('trial_balance', 'ReportController@trial_balance');
        Route::any('trial_balance/pdf', 'ReportController@trial_balance_pdf');
        Route::any('trial_balance/excel', 'ReportController@trial_balance_excel');
        Route::any('trial_balance/csv', 'ReportController@trial_balance_csv');
        Route::any('ledger', 'ReportController@ledger');
        Route::any('journal', 'ReportController@journal');
        Route::any('income_statement', 'ReportController@income_statement');
        Route::any('income_statement/pdf', 'ReportController@income_statement_pdf');
        Route::any('income_statement/excel', 'ReportController@income_statement_excel');
        Route::any('income_statement/csv', 'ReportController@income_statement_csv');
        Route::any('balance_sheet', 'ReportController@balance_sheet');
        Route::any('balance_sheet/pdf', 'ReportController@balance_sheet_pdf');
        Route::any('balance_sheet/excel', 'ReportController@balance_sheet_excel');
        Route::any('balance_sheet/csv', 'ReportController@balance_sheet_csv');
        Route::any('cash_flow', 'ReportController@cash_flow');
        Route::any('provisioning', 'ReportController@provisioning');
        Route::any('provisioning/pdf', 'ReportController@provisioning_pdf');
        Route::any('provisioning/excel', 'ReportController@provisioning_excel');
        Route::any('provisioning/csv', 'ReportController@provisioning_csv');
    });
    Route::group(['prefix' => 'loan_report'], function () {
        Route::any('expected_repayments', 'ReportController@expected_repayments');
        Route::any('expected_repayments/pdf', 'ReportController@expected_repayments_pdf');
        Route::any('expected_repayments/excel', 'ReportController@expected_repayments_excel');
        Route::any('expected_repayments/csv', 'ReportController@expected_repayments_csv');
        Route::any('repayments_report', 'ReportController@repayments_report');
        Route::any('repayments_report/pdf', 'ReportController@repayments_report_pdf');
        Route::any('repayments_report/excel', 'ReportController@repayments_report_excel');
        Route::any('repayments_report/csv', 'ReportController@repayments_report_csv');
        Route::any('collection_sheet', 'ReportController@collection_sheet');
        Route::any('collection_sheet/pdf', 'ReportController@collection_sheet_pdf');
        Route::any('collection_sheet/excel', 'ReportController@collection_sheet_excel');
        Route::any('collection_sheet/csv', 'ReportController@collection_sheet_csv');
        Route::any('arrears_report', 'ReportController@arrears_report');
        Route::any('arrears_report/pdf', 'ReportController@arrears_report_pdf');
        Route::any('arrears_report/excel', 'ReportController@arrears_report_excel');
        Route::any('arrears_report/csv', 'ReportController@arrears_report_csv');
        Route::any('disbursed_loans', 'ReportController@disbursed_loans');
        Route::any('disbursed_loans/pdf', 'ReportController@disbursed_loans_pdf');
        Route::any('disbursed_loans/excel', 'ReportController@disbursed_loans_excel');
        Route::any('disbursed_loans/csv', 'ReportController@disbursed_loans_csv');
        Route::any('cash_flow', 'ReportController@cash_flow');

    });
    Route::group(['prefix' => 'borrower_report'], function () {
        Route::any('borrower_numbers', 'ReportController@borrower_numbers');
        Route::any('borrower_numbers/pdf', 'ReportController@borrower_numbers_pdf');
        Route::any('borrower_numbers/excel', 'ReportController@borrower_numbers_excel');
        Route::any('borrower_numbers/csv', 'ReportController@borrower_numbers_csv');
        Route::any('top_borrowers', 'ReportController@top_borrowers');
        Route::any('top_borrowers/pdf', 'ReportController@top_borrowers_pdf');
        Route::any('top_borrowers/excel', 'ReportController@top_borrowers_excel');
        Route::any('top_borrowers/csv', 'ReportController@top_borrowers_csv');
        Route::any('borrowers_overview', 'ReportController@borrowers_overview');
        Route::any('borrowers_overview/pdf', 'ReportController@borrowers_overview_pdf');
        Route::any('borrowers_overview/excel', 'ReportController@borrowers_overview_excel');
        Route::any('borrowers_overview/csv', 'ReportController@borrowers_overview_csv');


    });
    Route::group(['prefix' => 'company_report'], function () {
        Route::any('products_summary', 'ReportController@products_summary');
        Route::any('products_summary/pdf', 'ReportController@products_summary_pdf');
        Route::any('products_summary/excel', 'ReportController@products_summary_excel');
        Route::any('products_summary/csv', 'ReportController@products_summary_csv');
        Route::any('general_report', 'ReportController@general_report');
        Route::any('top_borrowers', 'ReportController@top_borrowers');
        Route::any('top_borrowers/pdf', 'ReportController@top_borrowers_pdf');
        Route::any('top_borrowers/excel', 'ReportController@top_borrowers_excel');
        Route::any('top_borrowers/csv', 'ReportController@top_borrowers_csv');
        Route::any('borrowers_overview', 'ReportController@borrowers_overview');
        Route::any('borrowers_overview/pdf', 'ReportController@borrowers_overview_pdf');
        Route::any('borrowers_overview/excel', 'ReportController@borrowers_overview_excel');
        Route::any('borrowers_overview/csv', 'ReportController@borrowers_overview_csv');


    });
    Route::group(['prefix' => 'savings_report'], function () {
        Route::any('savings_transactions', 'ReportController@savings_transactions');
        Route::any('savings_transactions/pdf', 'ReportController@savings_transactions_pdf');
        Route::any('savings_transactions/excel', 'ReportController@savings_transactions_excel');
        Route::any('savings_transactions/csv', 'ReportController@savings_transactions_csv');
        Route::any('savings_balance', 'ReportController@savings_balance');
        Route::any('savings_balance/pdf', 'ReportController@savings_balance_pdf');
        Route::any('savings_balance/excel', 'ReportController@savings_balance_excel');
        Route::any('savings_balance/csv', 'ReportController@savings_balance_csv');


    });
    Route::any('cash_flow', 'ReportController@cash_flow');
    Route::any('collection', 'ReportController@collection_report');
    Route::any('loan_product', 'ReportController@profit_loss');

    Route::any('loan_list', 'ReportController@loan_list');
    Route::any('loan_balance', 'ReportController@loan_balance');
    Route::any('loan_arrears', 'ReportController@loan_arrears');
    Route::any('loan_transaction', 'ReportController@loan_transaction');
    Route::any('loan_classification', 'ReportController@loan_classification');
    Route::any('loan_projection', 'ReportController@loan_projection');
    //Route::any('borrower_report', 'ReportController@borrower_report');
    Route::any('collection_sheet', 'ReportController@collection_sheet');

});
//route for communication
Route::group(['prefix' => 'communication'], function () {
    Route::get('email', 'CommunicationController@indexEmail');
    Route::get('sms', 'CommunicationController@indexSms');
    Route::get('email/create', 'CommunicationController@createEmail');
    Route::post('email/store', 'CommunicationController@storeEmail');
    Route::get('email/{id}/delete', 'CommunicationController@deleteEmail');
    Route::get('sms/create', 'CommunicationController@createSms');
    Route::post('sms/store', 'CommunicationController@storeSms');
    Route::get('sms/{id}/delete', 'CommunicationController@deleteSms');

});
//routes for clients

Route::get('client_dashboard', 'ClientController@clientDashboard');
Route::get('client_profile', 'ClientController@clientProfile');
Route::post('client_register', 'ClientController@processClientRegister');
Route::post('client_profile', 'ClientController@processClientProfile');
Route::get('mpesa', 'ClientController@mpesa');
Route::group(['prefix' => 'client'], function () {
    Route::get('register', 'HomeController@clientRegister');
    Route::post('register', 'HomeController@processClientRegister');
    Route::get('application/data', 'ClientController@indexApplication');
    Route::get('application/create', 'ClientController@createApplication');
    Route::get('application/{loan_application}/show', 'ClientController@showApplication');
    Route::get('application/{loan_application}/guarantor/create', 'ClientController@createGuarantor');
    Route::post('application/{loan_application}/guarantor/store', 'ClientController@storeGuarantor');
    Route::post('application/store', 'ClientController@storeApplication');
    Route::get('guarantor/data', 'ClientController@indexGuarantor');
    Route::get('guarantor/{id}/decline', 'ClientController@declineGuarantor');
    Route::post('guarantor/{id}/accept', 'ClientController@acceptGuarantor');
    Route::get('loan/{loan}/show', 'ClientController@showLoan');
    Route::get('loan/{loan}/pay', 'ClientController@pay');
    Route::post('loan/{loan}/pay/paynow', 'ClientController@paynow');
    Route::post('loan/{loan}/pay/stripe', 'ClientController@stripe');
    Route::any('loan/{loan}/pay/paynow/return', 'ClientController@paynowReturn');
    Route::any('loan/{loan}/pay/paynow/result', 'ClientController@paynowResult');
    Route::any('loan/{loan}/pay/paypal/done', 'ClientController@paypalDone');
    Route::any('loan/pay/paypal/ipn', 'ClientController@paypalIPN');
    Route::get('saving/data', 'ClientController@indexSaving');
    Route::get('saving/{saving}/show', 'ClientController@showSaving');
    Route::get('saving/{saving}/statement/print', 'ClientController@printSavingStatement');
    Route::get('saving/{saving}/statement/pdf', 'ClientController@pdfSavingStatement');
    Route::get('saving/{saving}/pay', 'ClientController@paySaving');
    Route::post('saving/{saving}/pay/paynow', 'ClientController@paynowSaving');
    Route::post('saving/{saving}/pay/stripe', 'ClientController@stripeSaving');
    Route::any('saving/{saving}/pay/paynow/return', 'ClientController@paynowReturnSaving');
    Route::any('saving/{saving}/pay/paynow/result', 'ClientController@paynowResultSaving');
    Route::any('saving/pay/paypal/done', 'ClientController@paypalDoneSaving');
    Route::any('saving/pay/paypal/ipn', 'ClientController@paypalIPNSaving');
    //mpesa
    Route::post('loan/{loan}/pay/mpesa', 'ClientController@mpesa');
    Route::get('loan/mpesa/confirm', 'ClientController@confirm_mpesa_loan');
    Route::get('loan/mpesa/validate', 'ClientController@validate_mpesa_loan');
    Route::post('saving/{saving}/pay/mpesa', 'ClientController@mpesa_saving');
    Route::get('saving/mpesa/confirm', 'ClientController@confirm_mpesa_saving');
    Route::get('saving/mpesa/validate', 'ClientController@validate_mpesa_saving');
    //statements
    Route::get('loan/{loan}/loan_statement/print', 'ClientController@printLoanStatement');
    Route::get('loan/{loan}/loan_statement/pdf', 'ClientController@pdfLoanStatement');
    Route::get('loan/{loan}/loan_statement/email', 'ClientController@emailLoanStatement');
    Route::get('loan/{borrower}/borrower_statement/print', 'ClientController@printBorrowerStatement');
    Route::get('loan/{borrower}/borrower_statement/pdf', 'ClientController@pdfBorrowerStatement');
    Route::get('loan/{borrower}/borrower_statement/email', 'ClientController@emailBorrowerStatement');
    Route::get('loan/{loan}/schedule/print', 'ClientController@printSchedule');
    Route::get('loan/{loan}/schedule/pdf', 'ClientController@pdfSchedule');
});
//route for savings
Route::group(['prefix' => 'saving'], function () {
    Route::get('data', 'SavingController@index');
    Route::get('create', 'SavingController@create');
    Route::post('store', 'SavingController@store');
    Route::get('{saving}/edit', 'SavingController@edit');
    Route::get('{saving}/show', 'SavingController@show');
    Route::post('{id}/update', 'SavingController@update');
    Route::get('{id}/delete', 'SavingController@delete');
    Route::get('{saving}/statement/print', 'SavingController@printStatement');
    Route::get('{saving}/statement/pdf', 'SavingController@pdfStatement');
    Route::get('{saving}/transfer/create', 'SavingController@transfer');
    Route::post('{saving}/transfer/store', 'SavingController@storeTransfer');
    //saving products
    Route::get('savings_product/data', 'SavingProductController@index');
    Route::get('savings_product/create', 'SavingProductController@create');
    Route::post('savings_product/store', 'SavingProductController@store');
    Route::get('savings_product/{savings_product}/edit', 'SavingProductController@edit');
    Route::post('savings_product/{id}/update', 'SavingProductController@update');
    Route::get('savings_product/{id}/delete', 'SavingProductController@delete');
    //saving fees
    Route::get('savings_fee/data', 'SavingFeeController@index');
    Route::get('savings_fee/create', 'SavingFeeController@create');
    Route::post('savings_fee/store', 'SavingFeeController@store');
    Route::get('savings_fee/{savings_fee}/edit', 'SavingFeeController@edit');
    Route::post('savings_fee/{id}/update', 'SavingFeeController@update');
    Route::get('savings_fee/{id}/delete', 'SavingFeeController@delete');
    //saving transactions
    Route::get('savings_transaction/data', 'SavingTransactionController@index');
    Route::get('{saving}/savings_transaction/create', 'SavingTransactionController@create');
    Route::post('{saving}/savings_transaction/store', 'SavingTransactionController@store');
    Route::get('savings_transaction/{savings_transaction}/edit', 'SavingTransactionController@edit');
    Route::get('savings_transaction/{savings_transaction}/show', 'SavingTransactionController@show');
    Route::post('savings_transaction/{id}/update', 'SavingTransactionController@update');
    Route::get('savings_transaction/{id}/delete', 'SavingTransactionController@delete');
    Route::get('savings_transaction/{savings_transaction}/pdf', 'SavingTransactionController@pdf_transaction');
    Route::get('savings_transaction/{savings_transaction}/email', 'SavingTransactionController@email_transaction');
    Route::get('savings_transaction/{savings_transaction}/print', 'SavingTransactionController@print_transaction');
    Route::get('savings_transaction/{id}/reverse', 'SavingTransactionController@reverse');
});
//routes for assets
Route::group(['prefix' => 'asset'], function () {
    Route::get('data', 'AssetController@index');
    Route::get('create', 'AssetController@create');
    Route::post('store', 'AssetController@store');
    Route::get('{asset}/edit', 'AssetController@edit');
    Route::get('{asset}/show', 'AssetController@show');
    Route::post('{id}/update', 'AssetController@update');
    Route::get('{id}/delete', 'AssetController@delete');
    Route::get('{id}/delete_file', 'AssetController@deleteFile');

    //expense types
    Route::get('type/data', 'AssetController@indexType');
    Route::get('type/create', 'AssetController@createType');
    Route::post('type/store', 'AssetController@storeType');
    Route::get('type/{asset_type}/edit', 'AssetController@editType');
    Route::get('type/{asset_type}/show', 'AssetController@showType');
    Route::post('type/{id}/update', 'AssetController@updateType');
    Route::get('type/{id}/delete', 'AssetController@deleteType');
});
//route for capital
Route::group(['prefix' => 'capital'], function () {
    Route::get('data', 'CapitalController@index');
    Route::get('create', 'CapitalController@create');
    Route::post('store', 'CapitalController@store');
    Route::get('{capital}/edit', 'CapitalController@edit');
    Route::get('{id}/show', 'CapitalController@show');
    Route::post('{id}/update', 'CapitalController@update');
    Route::get('{id}/delete', 'CapitalController@delete');
    //bank accounts
    Route::get('bank/data', 'BankAccountController@index');
    Route::get('bank/create', 'BankAccountController@create');
    Route::post('bank/store', 'BankAccountController@store');
    Route::get('bank/{bank}/edit', 'BankAccountController@edit');
    Route::get('bank/{id}/show', 'BankAccountController@show');
    Route::post('bank/{id}/update', 'BankAccountController@update');
    Route::get('bank/{id}/delete', 'BankAccountController@delete');
});
Route::get('audit_trail/data', 'AuditTrailController@index');
//routes branches
Route::group(['prefix' => 'branch'], function () {

    Route::get('data', 'BranchController@index');
    Route::get('create', 'BranchController@create');
    Route::post('store', 'BranchController@store');
    Route::get('{branch}/show', 'BranchController@show');
    Route::get('{branch}/edit', 'BranchController@edit');
    Route::post('{id}/update', 'BranchController@update');
    Route::get('{id}/delete', 'BranchController@delete');
    Route::get('{id}/delete_file', 'BranchController@deleteFile');
    Route::post('{id}/add_user', 'BranchController@addUser');
    Route::get('{id}/remove_user', 'BranchController@removeUser');
    Route::get('change', 'BranchController@change');
    Route::post('change', 'BranchController@updateChange');
});
//routes for sms gateways
Route::group(['prefix' => 'sms_gateway'], function () {

    Route::get('data', 'SmsGatewayController@index');
    Route::get('create', 'SmsGatewayController@create');
    Route::post('store', 'SmsGatewayController@store');
    Route::get('{sms_gateway}/show', 'SmsGatewayController@show');
    Route::get('{sms_gateway}/edit', 'SmsGatewayController@edit');
    Route::post('{id}/update', 'SmsGatewayController@update');
    Route::get('{id}/delete', 'SmsGatewayController@delete');
});
//route for suppliers
Route::group(['prefix' => 'supplier'], function () {

    Route::get('data', 'SupplierController@index');
    Route::get('create', 'SupplierController@create');
    Route::post('store', 'SupplierController@store');
    Route::get('{supplier}/show', 'SupplierController@show');
    Route::get('{supplier}/edit', 'SupplierController@edit');
    Route::post('{id}/update', 'SupplierController@update');
    Route::get('{id}/delete', 'SupplierController@delete');

});
//route for warehouses
Route::group(['prefix' => 'warehouse'], function () {

    Route::get('data', 'WarehouseController@index');
    Route::get('create', 'WarehouseController@create');
    Route::post('store', 'WarehouseController@store');
    Route::get('{warehouse}/show', 'WarehouseController@show');
    Route::get('{warehouse}/edit', 'WarehouseController@edit');
    Route::post('{id}/update', 'WarehouseController@update');
    Route::get('{id}/delete', 'WarehouseController@delete');

});
//route for suppliers
Route::group(['prefix' => 'product'], function () {

    Route::get('data', 'ProductController@index');
    Route::get('create', 'ProductController@create');
    Route::post('store', 'ProductController@store');
    Route::get('{product}/show', 'ProductController@show');
    Route::get('{product}/edit', 'ProductController@edit');
    Route::post('{id}/update', 'ProductController@update');
    Route::get('{id}/delete', 'ProductController@delete');
//category routes
    Route::get('category/data', 'ProductCategoryController@index');
    Route::get('category/create', 'ProductCategoryController@create');
    Route::post('category/store', 'ProductCategoryController@store');
    Route::get('category/{product_category}/edit', 'ProductCategoryController@edit');
    Route::get('category/{product_category}/show', 'ProductCategoryController@show');
    Route::post('category/{id}/update', 'ProductCategoryController@update');
    Route::get('category/{id}/delete', 'ProductCategoryController@delete');
});
//routes for check in
Route::group(['prefix' => 'check_in'], function () {

    Route::get('data', 'ProductCheckinController@index');
    Route::get('create', 'ProductCheckinController@create');
    Route::post('store', 'ProductCheckinController@store');
    Route::get('{product_check_in}/show', 'ProductCheckinController@show');
    Route::get('{product_check_in}/edit', 'ProductCheckinController@edit');
    Route::post('{id}/update', 'ProductCheckinController@update');
    Route::get('{id}/delete', 'ProductCheckinController@delete');
    Route::get('{product}/get_product_data', 'ProductCheckinController@get_product_data');
    Route::get('payment/data', 'ProductCheckinController@indexPayment');

});
//routes for check out
Route::group(['prefix' => 'check_out'], function () {

    Route::get('data', 'ProductCheckoutController@index');
    Route::get('create', 'ProductCheckoutController@create');
    Route::post('store', 'ProductCheckoutController@store');
    Route::get('{product_check_out}/show', 'ProductCheckoutController@show');
    Route::get('{product_check_out}/edit', 'ProductCheckoutController@edit');
    Route::post('{id}/update', 'ProductCheckoutController@update');
    Route::get('{id}/delete', 'ProductCheckoutController@delete');
    Route::any('overview', 'ProductCheckoutController@overview');

});
//route for chart of accounts
Route::group(['prefix' => 'chart_of_account'], function () {

    Route::get('data', 'ChartOfAccountController@index');
    Route::get('create', 'ChartOfAccountController@create');
    Route::post('store', 'ChartOfAccountController@store');
    Route::get('{chart_of_account}/show', 'ChartOfAccountController@show');
    Route::get('{chart_of_account}/edit', 'ChartOfAccountController@edit');
    Route::post('{id}/update', 'ChartOfAccountController@update');
    Route::get('{id}/delete', 'ChartOfAccountController@delete');

});
Route::group(['prefix' => 'accounting'], function () {

    Route::any('trial_balance', 'AccountingController@trial_balance');
    Route::any('ledger', 'AccountingController@ledger');
    Route::any('journal', 'AccountingController@journal');
    Route::get('manual_entry/create', 'AccountingController@create_manual_entry');
    Route::post('manual_entry/store', 'AccountingController@store_manual_entry');
});
//route for charges
Route::group(['prefix' => 'charge'], function () {

    Route::get('data', 'ChargeController@index');
    Route::get('create', 'ChargeController@create');
    Route::post('store', 'ChargeController@store');
    Route::get('{charge}/show', 'ChargeController@show');
    Route::get('{charge}/edit', 'ChargeController@edit');
    Route::post('{id}/update', 'ChargeController@update');
    Route::get('{id}/delete', 'ChargeController@delete');

});