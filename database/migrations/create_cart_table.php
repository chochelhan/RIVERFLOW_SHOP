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

        $tblName = config('tables.cart');
        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
            $table->string('user_code','20'); // 회원인경우 회원고유키, 비회원인경우 세션키를 넣는다
            $table->integer('pid'); // 상품 고유키
            $table->string('ctype','5'); // temp(직접주문 임시저장),base (기본)
            $table->integer('camt'); // 구매수량
            $table->integer('option_id')->nullable(); // 옵션고유키
            $table->json('singleOptionInfos')->nullable(); // 단독형 옵션정보

            $table->index('user_code');
            $table->index('pid');
            $table->index('ctype');
            $table->index('option_id');
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
        $tblName = config('tables.cart');
        Schema::dropIfExists($tblName);
    }
};
