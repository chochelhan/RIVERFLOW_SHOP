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
        $tblName = config('tables.users');
        Schema::create($tblName, function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('uid','70')->unique();
            $table->string('nick','20')->nullable();
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('point')->nullable();
            $table->integer('lvid')->nullable();
            $table->string('admin',3)->default('no');
            $table->string('pcs',20)->nullable();
            $table->string('sns',5)->nullable();
            $table->string('img',255)->nullable();
            $table->string('mstatus',5)->default('ing');
            $table->string('remember_token')->nullable();

            $table->index('name');
            $table->index('nick');
            $table->index('lvid');
            $table->index('pcs');
            $table->index('admin');
            $table->index('mstatus');

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
                $tblName = config('tables.users');
                 Schema::dropIfExists($tblName);
             }
};
