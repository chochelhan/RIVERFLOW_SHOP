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
        $tblName = config('tables.productBrand');
        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
            $table->string('bname','40');
            $table->string('buse','3');
            $table->tinyInteger('brank');
            $table->index('brank');
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
        $tblName = config('tables.productBrand');
        Schema::dropIfExists($tblName);
    }
};
