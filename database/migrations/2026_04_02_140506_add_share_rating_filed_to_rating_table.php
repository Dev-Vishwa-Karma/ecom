<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShareRatingFiledToRatingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->integer('variant_id')->nullable()->after('product_id');
            $table->enum('post_sharing', ['Google', 'Facebook'])->nullable()->after('comment');
            $table->string('posturl')->nullable()->after('post_sharing');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropColumn('variant_id');
            $table->dropColumn('post_sharing');
            $table->dropColumn('posturl');
        });
    }
}
