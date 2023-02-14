<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\DoCustomSearchRank;
use DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $total_jobs=DB::select("SELECT count(rank_job_id) as 'total_job' FROM ranks_jobs");

        for($i=0;$i<$total_jobs[0]->total_job;$i=$i+8){

            $datas=DB::select('SELECT ranks_jobs.rank_job_id,ranks.rank_id,ranks_keywords.rank_keyword_id,ranks_keywords.rank_keyword_text,
            cities.city_id,cities.city_name,cities.city_latitude,cities.city_longitude,cities.country_id,countries.country_name,business_profiles.business_id,
            business_profiles.business_name,business_profiles.business_url,business_profiles.frequency FROM `ranks_jobs`JOIN ranks ON ranks_jobs.rank_id=ranks.rank_id LEFT JOIN
            ranks_keywords ON ranks.rank_keyword_id=ranks_keywords.rank_keyword_id LEFT JOIN cities ON 
            ranks.city_id=cities.city_id LEFT JOIN countries ON cities.country_id=countries.country_id 
            LEFT JOIN business_profiles ON ranks.business_id=business_profiles.business_id LIMIT '.$i.',8');

            if(sizeof($datas)>0){
                $frequency=$datas[0]->frequency;
                if(!empty($frequency)){
                    $schedule->call(function() use($datas) {
                        DoCustomSearchRank::dispatch($datas);
                    })->timezone('Asia/Kolkata')
                    ->$frequency();
                }
            }
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
