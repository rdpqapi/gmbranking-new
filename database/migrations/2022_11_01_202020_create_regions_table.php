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
	public function up(): void
	{
		Schema::create(
			'regions',
			function(Blueprint $table)
			{
				$table->mediumIncrements('region_id')->comment("The ID of the region.");
                $table->string('country_id', 3)->nullable()->comment("The country linked to this region");
                $table->string('state_id', 10)->nullable()->comment("The state linked to this region");
                $table->string('region_name', 150)->default('')->comment("The name of the region");

                $table->foreign('country_id')->references('country_id')->on('countries')->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('state_id')->references('state_id')->on('states')->onDelete('cascade')->onUpdate('cascade');

				$table->index(['country_id']);
				$table->index(['state_id']);
			}
		);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(): void
	{
		Schema::dropIfExists('regions');
	}
};
