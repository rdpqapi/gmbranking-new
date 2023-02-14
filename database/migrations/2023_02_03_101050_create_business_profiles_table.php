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
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->mediumIncrements('business_id')->comment("The auto increment ID of the Bussiness table");
            $table->string('business_name', 150)->default('')->comment("The name of the Bussiness");
            $table->string('business_url', 50)->comment("Website of Bussiness required");
            $table->char('business_phone', 15)->nullable()->comment("The name of the region");
            $table->string('business_address', 150)->nullable()->comment("The name of the region");
            $table->string('business_place_id', 50)->nullable()->comment("The name of the region");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_profiles');
    }
};
