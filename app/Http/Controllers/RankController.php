<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RanksJob;
use App\Jobs\GetRank;
use Illuminate\Http\Request;
use App\CustomLibrary\GetRanking;
use Exception;

class RankController extends Controller
{
    protected $ranksJob,$getRanking;
    public function __construct()
    {
        $this->ranksJob = new RanksJob();
        $this->getRanking=new GetRanking();
    }

    public function getRanks()
    {

        $total_job = $this->ranksJob->getTotalJob();
        try
        {

            echo $this->getRanking->processData("20");
        }catch(Exception $e)
        {
            dd($e);
        }
        // for ($i = 0; $i < $total_jobs[0]->total_job; $i = $i + 1) {
        //     //$datas = ;

        //     if (sizeof($datas) > 0) {
        //         RanksBot::dispatch($datas);
        //     }
        // }
    }
}
