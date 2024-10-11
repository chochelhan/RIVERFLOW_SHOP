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
        $tblName = config('tables.settingDelivery');
        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
            $table->string('name','40');
            $table->string('duse','3');
            $table->string('dmethod','6');
            $table->string('dpriceType','6');
            $table->integer('oprice')->nullable();
            $table->integer('fprice')->nullable();
            $table->integer('mprice')->nullable();
            $table->integer('localId')->nullable();
            $table->string('localUse','3');
            $table->integer('backPrice')->nullable();
            $table->string('backAddr','70');
            $table->text('dcontent')->nullable();
            $table->tinyInteger('drank');
            $table->index('drank');
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
        $tblName = config('tables.settingDelivery');
        Schema::dropIfExists($tblName);
    }
};
