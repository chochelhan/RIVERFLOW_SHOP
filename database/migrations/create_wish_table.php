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
        $tblName = config('tables.wish');

        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
            $table->integer('pid');
            $table->string('type','10');
            $table->integer('user_id');

            $table->index('pid');
            $table->index('type');
            $table->index('user_id');
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
         $tblName = config('tables.wish');
        Schema::dropIfExists($tblName);
    }
};
