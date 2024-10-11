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
    
        $table = config('tables.couponPublish');
        Schema::create($table, function (Blueprint $table) {
            $table->id();
             $table->integer('cid');
            $table->integer('user_id');
            $table->date('expireStdate');
            $table->date('expireEndate');
            $table->string('ctype','10');
            $table->string('cname','60');
            $table->string('pubtype','7');
            $table->string('couponMsg')->nullable();
            $table->string('gtype',10);

            $table->string('discountType','5');
            $table->integer('discountPrice')->nullable();
            $table->integer('discountRate')->nullable();
            $table->integer('discountRatePrice')->nullable();

            $table->string('publish_code','30')->nullable();
            $table->string('cuse','3')->default('no');
            $table->timestamps();

            $table->index('cid');
            $table->index('user_id');
            $table->index('cuse');
            $table->index('gtype');
            $table->index('ctype');
            $table->index('cname');
            $table->index('expireStdate');
            $table->index('expireEndate');
            $table->index('pubtype');
            $table->index('publish_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table = config('tables.couponPublish');
        Schema::dropIfExists($table);
    }
};
