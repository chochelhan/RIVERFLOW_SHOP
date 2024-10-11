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
        $tblName = config('tables.shipping');
        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('title',50); //
            $table->string('rname','30'); // 배송받는사람이름
            $table->string('rpcs','20'); // 배송지 전화번호
            $table->string('rpost','10'); // 배송지 우편번호
            $table->string('raddr1','40'); // 배송지 주소1
            $table->string('raddr2','40'); // 배송지 주소2
            $table->string('jibunAddr','45')->nullable();
            $table->string('defaultShipping','3')->default('no');//기본배송지
            $table->index('user_id');
            $table->index('defaultShipping');
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
        $tblName = config('tables.shipping');
        Schema::dropIfExists($tblName);
    }
};
