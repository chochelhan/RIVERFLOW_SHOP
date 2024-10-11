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
    return;
        $table = config('tables.point');
        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('ptype','5');
            $table->integer('point');
            $table->integer('oid')->nullable(); // 주문고유키
            $table->string('pointMsg');
            $table->string('pcode','10');

            $table->index('user_id');
            $table->index('ptype');
            $table->index('oid');
            $table->index('pcode');

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
        $table = config('tables.point');
        Schema::dropIfExists($table);
    }
};
