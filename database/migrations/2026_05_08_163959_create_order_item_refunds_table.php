<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
        public function up(): void
    {
        Schema::create('order_item_refunds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('order_item_id');
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('refund_by'); // admin or seller id
            $table->decimal('refund_amount', 10, 2);
            $table->string('stripe_refund_id')->nullable();
            $table->string('stripe_charge_id')->nullable();
            $table->enum('status', ['pending','success','failed'])->default('pending');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade');
            $table->foreign('seller_id')->references('id')->on('users');
            $table->foreign('customer_id')->references('id')->on('users');
            $table->foreign('refund_by')->references('id')->on('users');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_item_refunds');
    }
}
