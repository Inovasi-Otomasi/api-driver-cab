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
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
            $table->string('code');
            $table->string('start_point')->nullable();
            $table->string('end_point')->nullable();
            $table->string('complete_route');
            $table->json('coordinates')->nullable();
            $table->json('vehicles')->nullable();

            // $table->double('total_income');
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
        Schema::dropIfExists('routes');
    }
};
