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
        $tblName = config('tables.productCategory');
        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
            $table->string('pcode','15');
            $table->index('pcode');
            $table->string('code','15');
            $table->index('code');
            $table->string('cname','60');
            $table->string('cuse','3');
            $table->tinyInteger('depth');
            $table->tinyInteger('crank');
            $table->index('crank');
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
       $tblName = config('tables.productCategory');
        Schema::dropIfExists($tblName);
    }
};
