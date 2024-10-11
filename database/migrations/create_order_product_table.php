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

        $tblName = config('tables.orderProduct');
        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
            $table->integer('oid'); // 주문고유키
            $table->integer('pid'); // 상품고유키
            $table->integer('user_id'); // 회원고유키
            $table->integer('couponId')->nullable(); // 상품쿠폰 pudId
            $table->string('pname','60');
            $table->string('serviceType','6');
            $table->integer('brandId')->nullable();
            $table->string('pcode','50');
            $table->string('category1','30');
            $table->string('category2','30')->nullable();
            $table->string('category3','30')->nullable();
            $table->string('opt_name','30')->nullable(); //옵션명
            $table->string('opt_id','25')->nullable(); //옵션고유키
            $table->string('opt_code','30')->nullable(); //옵션관리코드
            $table->integer('price');
            $table->integer('dcprice')->nullable();
            $table->integer('payprice');
            $table->json('optionSingleInfos')->nullable();
            $table->integer('oamt'); // 구매수량
            $table->integer('tempInvAmt')->default(0);
            $table->string('listImg','100')->nullable(); // 상품사진
            $table->string('ostatus','20'); // 주문상태 (notpay('미입금'),income(입금),dready(배송준비),ding(배송중),dcomplete(배송완료),ocomplete('구매확정'),ocancle(취소)
            $table->dateTime('claimDate', $precision = 0)->nullable();


            $table->index('oid');
            $table->index('pid');
            $table->index('pname');
            $table->index('user_id');
            $table->index('ostatus');
            $table->index('serviceType');
            $table->index('claimDate');
            $table->index('brandId');
            $table->index('category1');
            $table->index('category2');
            $table->index('category3');
            $table->index('opt_id');
            //$table->index('opt_code');
            //$table->index('price');
            $table->index('payprice');

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
        $tblName = config('tables.orderProduct');
        Schema::dropIfExists($tblName);
    }
};
