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

    
        $table = config('tables.boardArticle');
        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->integer('bid');
            $table->string('btype','10');
            $table->string('subject','50');
            $table->string('category','10')->nullable();
            $table->text('content');
            $table->string('secret','3')->nullable();
            $table->string('img','120')->nullable();
            $table->integer('commentCnt')->nullable();
            $table->integer('hit')->default(0);
            $table->integer('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_pass')->nullable();
            $table->index('bid');
            $table->index('btype');
            $table->index('subject');
            $table->index('category');
            $table->index('user_id');
            $table->index('user_pass');
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
        $table = config('tables.boardArticle');
        Schema::dropIfExists($table);
    }
};
