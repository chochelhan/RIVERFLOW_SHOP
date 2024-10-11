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
        $tblName = config('tables.orderHistory');
        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
            $table->integer('oid'); // 주문고유키
            $table->integer('opid'); // 주문상품 고유키
            $table->string('nowstatus','10'); // 현재주문상태
            $table->string('oldstatus','10'); // 과거주문상태
            $table->json('content')->nullable();   // 변경 내용
            $table->index('oid');
            $table->index('opid');
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
        $tblName = config('tables.orderHistory');
        Schema::dropIfExists($tblName);
    }
};
