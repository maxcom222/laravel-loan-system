<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IncreaseDecimalLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE assets CHANGE purchase_price purchase_price DECIMAL(65,4)');
        DB::statement('ALTER TABLE assets CHANGE replacement_value replacement_value DECIMAL(65,4)');
        DB::statement('ALTER TABLE asset_valuations CHANGE amount amount DECIMAL(65,4)');
        DB::statement('ALTER TABLE collateral CHANGE value value DECIMAL(65,4)');
        DB::statement('ALTER TABLE expenses CHANGE amount amount DECIMAL(65,4)');
        DB::statement('ALTER TABLE loans CHANGE principal principal DECIMAL(65,4)');
        DB::statement('ALTER TABLE loans CHANGE balance balance DECIMAL(65,4)');
        DB::statement('ALTER TABLE loans CHANGE applied_amount applied_amount DECIMAL(65,4)');
        DB::statement('ALTER TABLE loans CHANGE approved_amount approved_amount DECIMAL(65,4)');
        DB::statement('ALTER TABLE loans CHANGE processing_fee processing_fee DECIMAL(65,4)');
        DB::statement('ALTER TABLE loan_applications CHANGE amount amount DECIMAL(65,4)');
        DB::statement('ALTER TABLE loan_products CHANGE minimum_principal minimum_principal DECIMAL(65,4)');
        DB::statement('ALTER TABLE loan_products CHANGE default_principal default_principal DECIMAL(65,4)');
        DB::statement('ALTER TABLE loan_products CHANGE maximum_principal maximum_principal DECIMAL(65,4)');
        DB::statement('ALTER TABLE other_income CHANGE amount amount DECIMAL(65,4)');
        DB::statement('ALTER TABLE payroll CHANGE paid_amount paid_amount DECIMAL(65,4)');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
