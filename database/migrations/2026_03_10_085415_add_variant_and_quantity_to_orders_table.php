<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVariantAndQuantityToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->unsignedBigInteger('variant_id')->after('product_id');
        $table->integer('quantity')->default(1)->after('price');

        $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropForeign(['variant_id']);
        $table->dropColumn(['variant_id', 'quantity']);
    });
}

}
