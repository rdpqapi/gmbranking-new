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
        Schema::create('c_s_languages', function (Blueprint $table) {
            $table->mediumIncrements('lang_id')->comment("The autoincreament ID");
            $table->char('lang_code', 10)->comment(" Google API specified language code");
            $table->string('lang_name', 100)->default('')->comment(" Google API specified language");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('c_s_languages');
    }
};
