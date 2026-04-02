<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');           // customer who ordered
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade'); // admin/seller
            $table->decimal('price', 10, 2);
            $table->string('customer_name');
            $table->text('address');
            $table->string('mobile');
            $table->string('email');
            $table->enum('payment_mode', ['cod', 'online'])->default('cod');
            $table->string('card_number')->nullable();      // encrypted in real app
            $table->string('card_cvv')->nullable();
            $table->string('card_expiry')->nullable();      // MM/YY
            $table->date('dispatch_date')->nullable();     // null until dispatched
            $table->enum('status', ['pending', 'processing', 'dispatched', 'delivered', 'cancelled'])->default('pending');
            $table->timestamp('order_date')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};