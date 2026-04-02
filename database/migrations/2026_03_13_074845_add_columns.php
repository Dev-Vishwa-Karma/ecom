<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notify_me', function (Blueprint $table) {
    if (!Schema::hasColumn('notify_me', 'seller_id')) {
        $table->unsignedBigInteger('seller_id');
    }

    if (!Schema::hasColumn('notify_me', 'user_id')) {
        $table->unsignedBigInteger('user_id');
    }

    if (!Schema::hasColumn('notify_me', 'variant_id')) {
        $table->unsignedBigInteger('variant_id');
    }
});
    }

    /**php artisan make:migration add_variant_and_quantity_to_orders_table --table=orders

     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notify_me', function (Blueprint $table) {
            $table->dropForeign(['seller_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['variant_id']);
            $table->dropColumn(['seller_id', 'user_id', 'variant_id']);
        });
    }
}
