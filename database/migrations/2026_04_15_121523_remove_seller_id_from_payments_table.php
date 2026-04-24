<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            // drop foreign key first
            $table->dropForeign(['seller_id']);

        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            // rollback FK
            $table->foreign('seller_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};