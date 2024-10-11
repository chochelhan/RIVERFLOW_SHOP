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
        $tblName = config('tables.inventoryHistory');
        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
            $table->integer('ivt_id');
            $table->string('type','10'); // 현재주문상태
            $table->json('content');   // 변경 내용
            $table->index('ivt_id');
            $table->index('type');
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
        $tblName = config('tables.inventoryHistory');
        Schema::dropIfExists($tblName);
    }
};
