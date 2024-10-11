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
         $tblName = config('tables.settingSite');
        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
            $table->json('delivery')->nullable();
            $table->json('member')->nullable();
            $table->json('company')->nullable();
            $table->json('agrees')->nullable();
            $table->json('images')->nullable();
            $table->json('points')->nullable();
            $table->json('coupons')->nullable();
            $table->json('logo')->nullable();
            $table->json('order')->nullable();
            $table->json('menu')->nullable();
            $table->json('mainPage')->nullable();
            $table->json('siteEnv')->nullable();
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
       $tblName = config('tables.settingSite');
        Schema::dropIfExists($tblName);
    }
};
