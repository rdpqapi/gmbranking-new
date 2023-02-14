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
			'states',
			function(Blueprint $table)
			{
				$table->char('country_id', 3)->nullable();
				$table->char('state_id', 10)->default('')->primary();
			
				$table->foreign('country_id', 'states_ibfk_1')->references('country_id')->on('countries')->onDelete('cascade')->onUpdate('cascade');
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
		Schema::dropIfExists('states');
	}
};
