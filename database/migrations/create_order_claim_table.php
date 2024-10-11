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
        $table = config('tables.orderClaim');
        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->string('user_code');
            $table->integer('oid');
            $table->string('oldOstatus','10');
            $table->string('claimType','2');
            $table->string('bankName','40')->nullable();
            $table->string('bankAccount','40')->nullable();
            $table->string('bankOwner','40')->nullable();
            $table->text('claimMessage')->nullable();
            $table->integer('recoverPrice')->default(0); //
            $table->integer('recoverPoint')->default(0); //
            $table->integer('recoverCouponId')->default(0); //
            $table->integer('recoverDeliveryPrice')->default(0); //

            $table->index('user_code');
            $table->index('oid');
            $table->index('claimType');

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
        $table = config('tables.orderClaim');
        Schema::dropIfExists($table);
    }
};
