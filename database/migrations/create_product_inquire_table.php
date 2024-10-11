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
         $tblName = config('tables.productInquire');
        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
            $table->integer('pid');
            $table->integer('user_id');
            $table->string('name','40');
            $table->string('secret','3')->default('no');
            $table->string('category','3')->nullable();
            $table->text('subject');
            $table->string('status','10')->default('wait');
            $table->text('content');

            $table->index('pid');
            $table->index('category');
            $table->index('user_id');
            $table->index('name');
            $table->index('status');
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
       // Schema::dropIfExists('product_category');
    }
};
