<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loans', function ($table) {
            $table->enum('status', [
                'pending',
                'approved',
                'disbursed',
                'declined',
                'withdrawn',
                'written_off',
                'closed',
                'pending_reschedule',
                'rescheduled'
            ])->default('pending')->nullable();
            $table->decimal('applied_amount', 10, 2)->nullable();
            $table->decimal('approved_amount', 10, 2)->nullable();
            $table->text('approved_notes')->nullable();
            $table->text('disbursed_notes')->nullable();
            $table->text('withdrawn_notes')->nullable();
            $table->text('closed_notes')->nullable();
            $table->text('rescheduled_notes')->nullable();
            $table->text('declined_notes')->nullable();
            $table->text('written_off_notes')->nullable();
            $table->date('approved_date')->nullable();
            $table->date('disbursed_date')->nullable();
            $table->date('withdrawn_date')->nullable();
            $table->date('closed_date')->nullable();
            $table->date('rescheduled_date')->nullable();
            $table->date('declined_date')->nullable();
            $table->date('written_off_date')->nullable();
            $table->integer('approved_by_id')->nullable();
            $table->integer('disbursed_by_id')->nullable();
            $table->integer('withdrawn_by_id')->nullable();
            $table->integer('declined_by_id')->nullable();
            $table->integer('written_off_by_id')->nullable();
            $table->integer('rescheduled_by_id')->nullable();
            $table->integer('closed_by_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loans', function ($table) {
            $table->dropColumn([
                'status',
                'applied_amount',
                'approved_amount',
                'approved_notes',
                'disbursed_notes',
                'withdrawn_notes',
                'closed_notes',
                'rescheduled_notes',
                'declined_notes',
                'written_off_notes',
                'approved_date',
                'disbursed_date',
                'withdrawn_date',
                'closed_date',
                'rescheduled_date',
                'declined_date',
                'written_off_date',
                'approved_by_id',
                'disbursed_by_id',
                'withdrawn_by_id',
                'declined_by_id',
                'written_off_by_id',
                'rescheduled_by_id',
                'closed_by_id'
            ]);
        });
    }
}
