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
        $tblName = config('tables.settingDeliveryLocal');
        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
            $table->string('name','40');
            $table->json('localData');
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
        $tblName = config('tables.settingDeliveryLocal');
        Schema::dropIfExists($tblName);
    }
};
