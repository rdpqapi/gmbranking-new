<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\CustomLibrary\GetRanking;
use Exception;
use Illuminate\Support\Facades\Log;

class ProcessRankJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $rank_jobs;
    private $getRanking;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($result_rank_jobs)
    {
        $this->rank_jobs=$result_rank_jobs;
        $this->getRanking = new GetRanking();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        try {
            $this->getRanking->getRankingResponse($this->rank_jobs);
        }
        catch(Exception $e) {
            $this->failed($e);
        }
    }

    public function failed($exception)
    {
        Log::info($exception->getMessage());
        // etc...
    }
}
