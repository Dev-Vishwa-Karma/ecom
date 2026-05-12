<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCancelledByTypeInOrderCancellation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_cancellations', function (Blueprint $table) {
            $table->string('cancelled_by_type')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_cancellations', function (Blueprint $table) {
        $table->dropColumn(['cancelled_by_type']);

        });
    }
}
