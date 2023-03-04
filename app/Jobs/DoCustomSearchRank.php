<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\RankingApiConrtroller;
use Carbon\Carbon;
use DB;

class DoCustomSearchRank implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $ch = null;
    private $gc_key = null;
    private $gcsx_key = null;
    private $responseData = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($responseData)
    {
        $this->responseData=$responseData;
        // $this->datas = $datas;
        // //Google Cloud API Key
        // $this->gc_key = env("GOOGLE_CLOUD_API_KEY");
        // //Google Custom Search Key
        // $this->gcsx_key = env("CSE_KEY");
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $rankigApiController=new RankingApiConrtroller();
        $rankigApiController->processGMBData($this->responseData);
    }

}
