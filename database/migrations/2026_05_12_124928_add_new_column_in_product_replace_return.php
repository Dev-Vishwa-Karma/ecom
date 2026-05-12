<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnInProductReplaceReturn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('replace')->default(0)->after('description');
            $table->text('return_policy')->nullable()->after('replace');
            $table->text('replace_policy')->nullable()->after('return_policy');
            $table->boolean('return')->default(0)->after('replace_policy');
            $table->integer('replace_days')->nullable()->after('replace');
            $table->integer('return_days')->nullable()->after('return');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('replace',  'return_policy','replace_policy','return','replace_days', 'return_days' );
        });
    }
}
