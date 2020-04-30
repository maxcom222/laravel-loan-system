<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToSavingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('savings', function ($table) {
            $table->enum('status', ['active', 'closed', 'pending', 'declined', 'withdrawn'])->default('pending');
            $table->integer('loan_officer_id')->nullable();
            $table->string('year')->nullable();
            $table->string('month')->nullable();
            $table->decimal('overdraft_limit', 65, 4)->nullable();
            $table->date('approved_date')->nullable();
            $table->date('declined_date')->nullable();
            $table->date('closed_date')->nullable();
            $table->text('approved_notes')->nullable();
            $table->text('declined_notes')->nullable();
            $table->text('closed_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('savings', function ($table) {
            $table->dropColumn('status');
            $table->dropColumn('loan_officer_id');
            $table->dropColumn('year');
            $table->dropColumn('month');
            $table->dropColumn('overdraft_limit');
            $table->dropColumn('approved_date');
            $table->dropColumn('declined_date');
            $table->dropColumn('closed_date');
            $table->dropColumn('approved_notes');
            $table->dropColumn('declined_notes');
            $table->dropColumn('closed_notes');
        });
    }
}
