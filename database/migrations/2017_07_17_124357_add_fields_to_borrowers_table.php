<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToBorrowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('borrowers', function ($table) {
            $table->enum('source', array('online', 'admin'))->default('admin')->nullable();
            $table->tinyInteger('active')->default(1)->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('borrowers', function ($table) {
            $table->dropColumn([
                'source',
                'active'
            ]);
        });
    }
}
