<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\DoCustomSearchRank;
use App\Http\Controllers\RankController;
use App\Models\RanksJob;
use App\Models\City;
use App\Models\Country;
use App\Models\Rank;
use App\Models\RanksKeyword;
use App\Models\RanksTest;
use App\Models\Region;
use App\Models\State;
use App\Models\BusinessProfile;
use App\CustomLibrary\GetRanking;
use Exception;
use PhpParser\Node\Stmt\TryCatch;

class RankbotConrtroller extends Controller
{
    protected $ranksJob, $getRanking;

    public function __construct()
    {
        $this->ranksJob = new RanksJob();
        $this->getRanking = new GetRanking();
    }

    public function getRank()
    {
        /*
        * Start function of Get Ranking Ranking
        * Get the total no of records from RanksJobs Model and call the getScheduleJob() Method
        */
        try{
        $this->getSheduledJob($this->ranksJob->getTotalJob());

        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    /*
    * Get The total no of Job Scheduled as a parameter
    */
    public function getSheduledJob(int $total_job_scheduled): bool
    {
        try{

        /*
        * For loop will repeat the No of Job by 8 no of records to perform rank operation we will
        * pass max of 8 no of records
        */
        for ($no_of_records_index = 0; $no_of_records_index < $total_job_scheduled; $no_of_records_index = $no_of_records_index + 1) 
        {
            /*
            * Ge the no of Secheduled Jobs with ranking data and business profile datas.
            */
            $result_rank_jobs = RanksJob::select('ranks_jobs.rank_job_id', 'ranks.rank_id', 'ranks_keywords.rank_keyword_id', 'ranks_keywords.rank_keyword_text',
                'cities.city_id', 'cities.city_name', 'cities.city_latitude', 'cities.city_longitude', 'cities.country_id', 'countries.country_name', 'business_profiles.business_id',
                'business_profiles.business_name', 'business_profiles.business_url', 'business_profiles.frequency')
                ->leftJoin('ranks', 'ranks_jobs.rank_id', '=', 'ranks.rank_id')
                ->leftJoin('ranks_keywords', 'ranks.rank_keyword_id', '=', 'ranks_keywords.rank_keyword_id')
                ->leftJoin('cities', 'ranks.city_id', '=', 'cities.city_id')
                ->leftJoin('countries', 'cities.country_id', '=', 'countries.country_id')
                ->leftJoin('business_profiles', 'ranks.business_id', '=', 'business_profiles.business_id')
                ->limit(1)->offset($no_of_records_index)
                ->get();
            
                /*
                * If no of job found then RankingResponse method will be called of GetRanking Class which
                * is for Handling the Ranking Process.
                */
                if(sizeof($result_rank_jobs)>0){
                    $this->getRanking->getRankingResponse($result_rank_jobs);
                }
        }
        }
        catch(Exception $e){
            echo $e->getMessage();
        }
        return true;
    }

    /*
    * storeCardResponse will update the final response of organic search ranking of business data and
    * will update in the rank table
    */
    public function storeOrganicResponse(array $responseData,$rank_id) : bool
    {
       try{
        $result = Rank::where('rank_id',$rank_id)->update($responseData["response"]);
       }catch(Exception $e){
        echo $e->getMessage();
       }
        return true;
    }

    /*
    * storeCardResponse will update the final response of ranking of business data will update in the rank table
    */
    public function storeCardResponse(array $responseData,string $rank_id) : bool
    {
        try{
            $result = Rank::where('rank_id',$rank_id)->update($responseData);
        }catch(Exception $e){
        echo $e->getMessage();
       }
        return true;
    }
}
