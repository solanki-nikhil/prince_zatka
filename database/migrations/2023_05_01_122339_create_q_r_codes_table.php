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
        Schema::create('q_r_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique()->comment('generate unique qrcode');
            $table->enum('is_used', ['0', '1'])->default(0)->comment('0 = not used 1 = used');
            $table->integer('used_by')->default(0);
            $table->date('used_date')->nullable();
            $table->float('amount', 8, 2)->nullable();
            $table->string('batch_number')->comment('generate batch number for qrcode');
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
        Schema::dropIfExists('q_r_codes');
    }
};
