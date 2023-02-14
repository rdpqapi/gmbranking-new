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
			'ranks_keywords',
			function(Blueprint $table)
			{
				$table->bigIncrements('rank_keyword_id')->comment("The ID of the keyword");
				$table->char('locale_id', 7)->nullable()->comment("The locale associated with this keyword");
				$table->string('rank_keyword_text', 250)->default('')->comment("The keyword");
				$table->dateTime('rank_keyword_created_utc', 6)->nullable()->comment("When the keyword was created in UTC (date and time). Format: YYYY-MM-DD HH:MM:SS.UUUUUU");
				$table->dateTime('rank_keyword_modified_utc', 6)->nullable()->comment("When the keyword was modified in UTC (date and time). Format: YYYY-MM-DD HH:MM:SS.UUUUUU");

				$table->unique(['rank_keyword_text']);
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
		Schema::dropIfExists('ranks_keywords');
	}
};
