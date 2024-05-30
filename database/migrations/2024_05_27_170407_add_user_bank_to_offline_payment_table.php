<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offline_payments', function (Blueprint $table) {
            //
            $table->string('user_bank');
            $table->string('user_account_number');
            $table->string('iban')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offline_payments', function (Blueprint $table) {
            //
            $table->dropColumn('user_bank');
            $table->dropColumn('user_account_number');
            $table->dropColumn('iban');
        });
    }
};
