<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->bigInteger("quantity");
            $table->bigInteger("user_id");
            $table->bigInteger("product_id");
            $table->bigInteger("item_price");
            $table->bigInteger("creator_id");
            $table->string("customer_name");
            $table->string("customer_address");
            $table->string("customer_phone");
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
        Schema::dropIfExists('order_details');
    }
}
