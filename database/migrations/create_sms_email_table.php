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
        $tblName = config('tables.smsEmailSetting');
        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
            $table->string('gtype',5); //
            $table->string('gid','20'); //
            $table->json('content')->nullable();
            $table->string('guse','3');
            $table->index('gtype');
            $table->index('gid');

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
        $tblName = config('tables.smsEmailSetting');
        Schema::dropIfExists($tblName);
    }
};

