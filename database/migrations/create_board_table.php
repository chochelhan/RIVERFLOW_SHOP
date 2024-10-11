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
        $table = config('tables.board');
        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->string('bname','40');
            $table->string('buse','3');
            $table->string('categoryUse','3');
            $table->json('categoryList')->nullable();
            $table->string('wauth','5');
            $table->string('secret','3');
            $table->string('btype','10');
            $table->string('replyUse','3');
            $table->string('rauth','5')->nullable();
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
        $table = config('tables.board');
        Schema::dropIfExists($table);
    }
};
