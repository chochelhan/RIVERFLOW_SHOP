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
        $tblName = config('tables.order');
        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
            $table->string('is_member','3'); // 회원여부 ('yes',no)
            $table->string('user_code','20'); // 회원인경우 회원고유키, 비회원인경우 세션코드를 넣는다
            $table->string('order_code',20); // 주문번호
            $table->string('pg_code',40)->nullable(); // pg사 주문코드
            $table->integer('oamt'); // 구매수량
            $table->integer('price'); // 주문금액

            $table->string('paymethod','10'); // 결제방법
            $table->json('payInfo')->nullable(); // 결제정보
            $table->string('ostatus','8'); // 주문상태 (notpay('미입금'),income(입금)
            $table->string('oname','30'); // 주문자명
            $table->string('opcs','20'); // 주문자 전화번호
            $table->string('oemail','40')->nullable(); // 주문자 이메일
            $table->string('rname','30')->nullable(); // 배송받는사람이름
            $table->string('rpcs','20')->nullable(); // 배송지 전화번호
            $table->string('rpost','10')->nullable(); // 배송지 우편번호
            $table->string('raddr1','40')->nullable(); // 배송지 주소1
            $table->string('raddr2','40')->nullable(); // 배송지 주소2
            $table->text('rmessage')->nullable(); // 배송 메세지
            $table->integer('deliveryId')->nullable(); //배송비 정보
            $table->integer('usePoint')->nullable(); // 사용적립금
            $table->integer('reservePoint')->nullable(); // 받을적립금
            $table->integer('deliveryPrice')->default(0); //배송비
            $table->integer('localDeliveryPrice')->default(0); //추가배송비
            $table->integer('useCouponId')->nullable(); //
            $table->integer('useCouponPrice')->default(0); //
            $table->string('deliveryCompany',40)->nullable();
            $table->string('sendNumber',30)->nullable();
            $table->json('deliverTracker')->nullable(); //


            $table->index('is_member');
            $table->index('user_code');
            $table->index('order_code');
            $table->index('pg_code');
            $table->index('deliveryId');
            $table->index('useCouponId');
            $table->index('deliveryCompany');
            $table->index('sendNumber');
            $table->index('price');
            $table->index('ostatus');
            $table->index('oname'); // 주문자명
            $table->index('opcs'); // 주문자 전화번호
            $table->index('oemail'); // 주문자 이메일
            $table->index('rname'); // 배송받는사람이름
            $table->index('rpcs'); // 배송지 전화번호
            $table->index('raddr1'); // 배송지 주소1

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
        $tblName = config('tables.order');
        Schema::dropIfExists($tblName);
    }
};
