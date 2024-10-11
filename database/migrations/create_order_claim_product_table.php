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
        $table = config('tables.orderClaimProduct');
        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->integer('claim_id');
            $table->integer('opid');
            $table->string('oldOstatus','10');

            $table->index('opid');
            $table->index('claim_id');
           
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
        $table = config('tables.orderClaimProduct');
        Schema::dropIfExists($table);
    }
};
