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
        $tblName = config('tables.comment');
        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
            $table->integer('user_id'); // 회원고유키
            $table->string('name','40'); // 회원d이름
            $table->integer('parentId'); // 댓글 최상위 글의 고유키
            $table->string('parentType','15'); // 최상위 글의 구분값 (orderReview: 후기글 , board: 게시판,event: 이벤트, special:기획전)
            $table->text('content'); // 내용
            $table->tinyInteger('depth')->default(1); // 일반 1, 댓글의 댓글 2
            $table->integer('pid')->nullable(); // // 댓글의 댓글일경우 상위댓글 고유키
            $table->string('is_delete')->default('no'); // 블라인트 처리시 yes or no

            $table->index('parentId');
            $table->index('parentType');
            $table->index('depth');
            $table->index('pid');
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
         $tblName = config('tables.comment');
        Schema::dropIfExists($tblName);
    }
};
