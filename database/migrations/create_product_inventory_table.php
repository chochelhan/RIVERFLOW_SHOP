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

        $tblName = config('tables.productInventory');

        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
            $table->integer('pid'); // 상품고유키
            $table->string('optionUse','3'); // 옵션사용여부
            $table->integer('oid')->nullable(); // 옵션고윺키

            $table->integer('disable_amt')->nullable(); // 판매불가능 재고
            $table->integer('able_amt')->nullable(); // 판매가능한 재고
            $table->integer('total_amt')->nullable(); // 총 재고
            $table->integer('sale_amt')->nullable(); // 판매 갯수
            $table->string('manger_code','20')->nullable();

            $table->index('pid');
            $table->index('oid');
            $table->index('optionUse');
            $table->index('manger_code');
            $table->index('able_amt');
            $table->index('total_amt');
            $table->index('sale_amt');
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
        $tblName = config('tables.productInventory');
        Schema::dropIfExists($tblName);
    }
};
