<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStripeFieldsUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('stripe_onboarded')->default(0)->after('stripe_customer_id');
            $table->boolean('charges_enabled')->default(0)->after('stripe_onboarded');
            $table->boolean('payouts_enabled')->default(0)->after('charges_enabled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['stripe_onboarded', 'charges_enabled', 'payouts_enabled']);
            
        });
    }
}
