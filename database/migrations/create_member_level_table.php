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
        $tblName = config('tables.memberLevel');
        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
            $table->string('gname','40');
            $table->string('gdesc')->nullable();
            $table->string('guse','3');
            $table->integer('gprice');
             $table->string('gbase','3')->default('no');
            $table->string('gservicePointUse','3');
            $table->integer('gservicePoint');
            $table->string('gpointUse','3');
            $table->integer('gpoint');
            $table->integer('gcoupon')->nullable();
            $table->tinyInteger('grank');
            $table->index('grank');
            $table->index('gbase')->default('no');
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
        $tblName = config('tables.memberLevel');
        Schema::dropIfExists($tblName);
    }
};
