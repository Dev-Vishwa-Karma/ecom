<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCancelFieldsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
        $table->enum('cancelled_by_type', ['customer', 'admin', 'system'])->nullable()->after('status');
        $table->unsignedBigInteger('cancelled_by_id')->nullable()->after('cancelled_by_type');
        $table->timestamp('cancelled_at')->nullable()->after('cancelled_by_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
          $table->dropColumn(['cancelled_by_type', 'cancelled_by_id', 'cancelled_at']);
        });
    }
}
