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
			'ranks_jobs',
			function(Blueprint $table)
			{
				$table->bigIncrements('rank_job_id')->comment("The ID of the job");
				$table->unsignedBigInteger('rank_id')->nullable()->comment("The ID of the rank to search");
				$table->unsignedMediumInteger('user_id')->nullable()->comment("The ID of the user who created this task. Will be `NULL` if created by the system");
				$table->dateTime('rank_job_created_utc', 6)->nullable()->comment("When the job was created in UTC (date and time). Format: YYYY-MM-DD HH:MM:SS.UUUUUU");

				$table->foreign(['rank_id'])->references('rank_id')->on('ranks')->onDelete('set null')->onUpdate('cascade');
				//$table->foreign(['user_id'])->references('user_id')->on('users')->onDelete('set null')->onUpdate('cascade');
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
		Schema::dropIfExists('ranks_jobs');
	}
};
