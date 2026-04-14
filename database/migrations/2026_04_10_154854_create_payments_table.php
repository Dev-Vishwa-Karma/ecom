<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();

            // Amount Details
            $table->decimal('amount', 10, 2);
            $table->decimal('platform_fee', 10, 2)->default(0);
            $table->decimal('seller_earnings', 10, 2)->default(0);

            // Currency
            $table->string('currency')->default('usd');

            // Payment Method
            $table->enum('payment_method', ['card', 'upi', 'netbanking', 'wallet'])->nullable();

            // Stripe Fields
            $table->string('stripe_session_id')->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_charge_id')->nullable();

            // Status
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');

            // Paid Time
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            // ⚡ Indexes
            $table->index('order_id');
            $table->index('user_id');
            $table->index('seller_id');
            $table->index('status');
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
}
