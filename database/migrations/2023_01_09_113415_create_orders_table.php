<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->enum('user_type',['0','1'])->comment('0 = self 1 = other');
            $table->string('order_id')->nullable();
            $table->string('name')->nullable();
            $table->string('mobile')->nullable();
            $table->string('address')->nullable();
            $table->integer('total_pices');
            $table->integer('total_box');
            $table->integer('total_amount');
            $table->enum('order_status',['0','1','2','3','4'])->comment('0 = pending 1 = confirm 2 = reject 3 = dispatch 4 = deliver');
            $table->softDeletes();
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
        Schema::dropIfExists('orders');
    }
};
