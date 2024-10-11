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
        $tblName = config('tables.productInfoNotice');
        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
            $table->string('pname','90');
            $table->string('code','3');
            $table->integer('pid');
            $table->json('datas');

            $table->index('pid');
            $table->index('code');
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
           $tblName = config('tables.productInfoNotice');

        Schema::dropIfExists($tblName);
    }
};
