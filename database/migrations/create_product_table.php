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

        $tblName = config('tables.product');

        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
            $table->string('pname','60');
            $table->string('serviceType','9');
            $table->string('adult','3')->nullable();
            $table->string('keyword','255')->nullable();
            $table->integer('brandId')->nullable();
            $table->string('pcode','50');
            $table->string('category1','30');
            $table->string('category2','30')->nullable();
            $table->string('category3','30')->nullable();
            $table->string('optionUse','3');
            $table->json('optionInfo')->nullable();
            $table->integer('price')->default(0);
            $table->integer('dcprice')->default(0);
            $table->integer('gamt')->nullable();
            $table->json('detailImgs');
            $table->string('listImg','100')->nullable();
            $table->longText('content');
            $table->integer('deliveryId')->nullable();
            $table->string('deliveryGroup','3')->nullable();

            $table->json('addInfos')->nullable(); // 추가정보 내용 json
            $table->integer('addInfoId')->nullable(); // 추가정보 고유키
            $table->string('description')->nullable(); // 간략설명
            $table->json('pInfoNoti'); // 상품정보고시 json
            $table->longText('dcontent'); // 배송 상세내용

            $table->string('pstatus','8');
            $table->string('platform','12');
            $table->date('periodStdate')->nullable();
            $table->date('periodEndate')->nullable();
            $table->string('salePeriod','7');
            $table->string('pointType','3');
            $table->integer('point')->nullable();
            $table->string('pointSet','5')->nullable();
            $table->string('pointUse','3');
            $table->integer('wish')->default(0);

            $table->json('relProducts')->nullable();
            $table->string('relUse','3')->default('no');
            $table->string('relType','10')->default('single');
            $table->string('relDeny','3')->default('no');


            $table->index('pstatus');
            $table->index('platform');
            $table->index('salePeriod');
            $table->index('periodStdate');
            $table->index('periodEndate');

            $table->index('pname');
            $table->index('keyword');
            $table->index('serviceType');
            $table->index('brandId');
            $table->index('price');
            $table->index('dcprice');
            $table->index('pcode');
            $table->index('category1');
            $table->index('category2');
            $table->index('category3');
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

        $tblName = config('tables.product');
        Schema::dropIfExists($tblName);
    }
};
