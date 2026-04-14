<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnFromOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn(['card_number', 'card_cvv', 'card_expiry']);
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
                $table->string('card_number', 16)->nullable();
                $table->string('card_cvv', 3)->nullable();
                $table->string('card_expiry', 5)->nullable();
        });
    }
}
