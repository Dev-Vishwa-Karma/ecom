<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::create('business_details', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');

        $table->string('email');
        $table->string('business_name');
        $table->string('business_phone');

        $table->string('bank_account_number');
        $table->string('ifsc_code');
        $table->string('account_holder_name');

        $table->text('business_address');

        // documents (cloudinary URLs)
        $table->string('pan_card')->nullable();
        $table->string('gst_certificate')->nullable();

        // status
        $table->enum('status', ['pending','approved','rejected'])->default('pending');
        $table->timestamp('approved_at')->nullable();

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_details');
    }
}
