<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateSellerPayoutEnumAndAddColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seller_payout', function (Blueprint $table) {
            DB::statement("
            ALTER TABLE seller_payouts 
            MODIFY status ENUM(
                'pending',
                'paid',
                'failed',
                'partial_refund',
                'refund'
            ) DEFAULT 'pending'
        ");

        Schema::table('seller_payouts', function (Blueprint $table) {

            $table->decimal('refund_amount', 10, 2)
                ->default(0)
                ->after('amount');

        });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seller_payout', function (Blueprint $table) {
            DB::statement("
            ALTER TABLE seller_payouts 
            MODIFY status ENUM(
                'pending',
                'paid',
                'failed'
            ) DEFAULT 'pending'
        ");

        Schema::table('seller_payouts', function (Blueprint $table) {

            $table->dropColumn('refund_amount');

        });
        });
    }
}
