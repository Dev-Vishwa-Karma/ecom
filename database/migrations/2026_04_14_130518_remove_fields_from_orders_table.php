<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFieldsFromOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::table('orders', function (Blueprint $table) {

        // check before dropping FK
        $foreignKeys = Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableForeignKeys('orders');

        $fkNames = array_map(fn($fk) => $fk->getName(), $foreignKeys);

        if (in_array('orders_product_id_foreign', $fkNames)) {
            $table->dropForeign('orders_product_id_foreign');
        }

        if (in_array('orders_variant_id_foreign', $fkNames)) {
            $table->dropForeign('orders_variant_id_foreign');
        }

        if (in_array('orders_seller_id_foreign', $fkNames)) {
            $table->dropForeign('orders_seller_id_foreign');
        }

        // now safely drop columns (only if exist)
        $columns = [
            'product_id',
            'variant_id',
            'seller_id',
            'price',
            'quantity',
            'total_price',
            'card_number',
            'card_cvv',
            'card_expiry',
        ];

        $existing = array_filter($columns, function ($col) {
            return Schema::hasColumn('orders', $col);
        });

        if (!empty($existing)) {
            $table->dropColumn($existing);
        }
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
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('total_price', 10, 2)->default(0);
            $table->string('card_number')->nullable();
            $table->string('card_cvv')->nullable();
            $table->string('card_expiry')->nullable();
      
        });
    }
}
