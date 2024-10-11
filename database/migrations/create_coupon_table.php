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
        $table = config('tables.coupon');

        Schema::create($table, function (Blueprint $table) {
            $table->id();

            $table->date('pubStdate');
            $table->date('pubEndate');
            $table->date('expireStdate')->nullable();
            $table->date('expireEndate')->nullable();
            $table->string('ctype','10');
            $table->string('cname','60');
            $table->integer('camt');
            $table->string('pubtype','7');
            $table->string('mlevel','10');
            $table->integer('mlimit')->nullable();
            $table->string('minPriceUse','3');
            $table->integer('minPrice')->nullable();
            $table->string('maxPriceUse','3');
            $table->integer('maxPrice')->nullable();
            $table->string('discountType','5');
            $table->integer('discountPrice')->nullable();
            $table->integer('discountRate')->nullable();
            $table->integer('discountRatePrice')->nullable();
            $table->string('pointDeny','3');
            $table->string('ptType','10');
            $table->json('ptInData')->nullable();
            $table->json('ptOutData')->nullable();
            $table->string('ptDeny','3');
            $table->string('cplatform','20');
            $table->string('useExpireType','7');
            $table->integer('afterDay')->nullable();
            $table->timestamps();

            $table->index('pubStdate');
            $table->index('mlevel');
            $table->index('pubEndate');
            $table->index('expireStdate');
            $table->index('expireEndate');
            $table->index('ctype');
            $table->index('cplatform');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table = config('tables.coupon');

        Schema::dropIfExists($table);
    }
};
