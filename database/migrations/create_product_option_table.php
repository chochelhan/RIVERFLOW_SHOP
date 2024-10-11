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


        $tblName = config('tables.productOption');
        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
             $table->integer('pid');
             $table->string('orequired','2');
            $table->string('option_name','60');
            $table->string('option_code','60');
            $table->string('name','60');
            $table->integer('price');
            $table->integer('dcprice')->nullable();
            $table->integer('amt')->nullable();
            $table->integer('add_amt')->nullable();
            $table->string('ouse','2')->default('Y');
            $table->string('manger_code','20')->nullable();

            $table->index('orequired');
            $table->index('pid');
            $table->index('option_code');
            $table->index('manger_code');
            $table->index('ouse');
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
        $tblName = config('tables.productOption');
        Schema::dropIfExists($tblName);
    }
};
