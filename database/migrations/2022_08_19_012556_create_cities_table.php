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
        Schema::create(
            'cities',
            function(Blueprint $table)
            {
                $table->mediumIncrements('city_id');
                $table->char('country_id', 3)->nullable();
                $table->char('state_id', 10)->nullable();
                $table->string('city_name', 100)->default('')->comment("The name of the city");
                $table->double('city_latitude', 10, 8)->default(0.0)->comment("The latitude of the city");
                $table->double('city_longitude', 10, 8)->default(0.0)->comment("The longitude of the city");

                $table->foreign('country_id')->references('country_id')->on('countries')->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('state_id')->references('state_id')->on('states')->onDelete('cascade')->onUpdate('cascade');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities');
    }
};
