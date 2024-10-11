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

        $tblName = config('tables.orderReview');
        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
            $table->integer('oid'); // 주문고유키
            $table->integer('user_id'); // 회원고유키
            $table->integer('pid'); // 상품 고유키
            $table->tinyInteger('grade'); // 평점
            $table->text('content'); // 내용
            $table->integer('point')->nullable(); // 적립금
            $table->json('imgs')->nullable();   // 이미지
            $table->integer('commentCnt')->nullable(); // 댓글수
            $table->string('is_delete','5')->default('no'); // 삭제여부

            $table->index('user_id');
            $table->index('oid');
            $table->index('pid');
            $table->index('grade');

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
         $tblName = config('tables.orderReview');
        Schema::dropIfExists($tblName);
    }
};
