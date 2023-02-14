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
			'ranks',
			function(Blueprint $table)
			{
				$table->bigIncrements('rank_id')->comment("The ID of the rank");
				$table->unsignedBigInteger('rank_keyword_id')->nullable()->comment("The ID of the keyword associated with this rank");
				$table->unsignedMediumInteger('business_id')->nullable()->comment("The ID of the business associated with this rank");
				$table->unsignedMediumInteger('region_id')->nullable()->comment("The ID of the region associated with this rank");
				$table->unsignedMediumInteger('city_id')->nullable()->comment("The ID of the city associated with this rank");
				$table->string('rank_finder', 30)->default('')->comment("The finder used to get the data. Refer to `RankFinderEnum` for details");
				$table->unsignedSmallInteger('rank_organic')->default(0)->comment("The position of the result within the organic results");
				$table->unsignedSmallInteger('rank_google_business')->default(0)->comment("The position of the Google Business Profil within the GBP results");
				$table->unsignedTinyInteger('rank_card_none')->default(0)->comment("If set to true, this means that no card was given by Google in the search results");
				$table->unsignedTinyInteger('rank_card_competitor')->default(0)->comment("If set to true, this means that a card was found but not related to this business");
				$table->unsignedTinyInteger('rank_card_found')->default(0)->comment("If set to true, this means that a card was found for this business");
				$table->unsignedTinyInteger('rank_not_found')->default(0)->comment("If set to true, this means that the business was not found in the search results");
				$table->dateTime('rank_last_test_utc', 6)->nullable()->comment("When the keyword was last test in UTC (date and time). Format: YYYY-MM-DD HH:MM:SS.UUUUUU");
				$table->dateTime('rank_created_utc', 6)->nullable()->comment("When the keyword was created in UTC (date and time). Format: YYYY-MM-DD HH:MM:SS.UUUUUU");
				$table->dateTime('rank_modified_utc', 6)->nullable()->comment("When the keyword was modified in UTC (date and time). Format: YYYY-MM-DD HH:MM:SS.UUUUUU");
				$table->dateTime('rank_deleted_utc', 6)->nullable()->comment("When the keyword was deleted in UTC (date and time). Format: YYYY-MM-DD HH:MM:SS.UUUUUU");

				$table->foreign(['rank_keyword_id'])->references('rank_keyword_id')->on('ranks_keywords')->onDelete('set null')->onUpdate('cascade');
				//$table->foreign(['business_id'])->references('business_id')->on('businesses')->onDelete('set null')->onUpdate('cascade');
				$table->foreign(['region_id'])->references('region_id')->on('regions')->onDelete('set null')->onUpdate('cascade');
				$table->foreign(['city_id'])->references('city_id')->on('cities')->onDelete('set null')->onUpdate('cascade');
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
		Schema::dropIfExists('ranks');
	}
};
