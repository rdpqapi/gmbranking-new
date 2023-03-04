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

class RankbotConrtroller extends Controller
{
    protected $ranksJob, $getRanking;

    public function __construct()
    {
        $this->ranksJob = new RanksJob();
        $this->getRanking = new GetRanking();
    }

    public function getRanks()
    {
        $this->getSheduledJob($this->ranksJob->getRanks());
    }

    public function getSheduledJob(int $total_job_scheduled): bool
    {

        for ($no_of_records_index = 0; $no_of_records_index < $total_job_scheduled; $no_of_records_index = $no_of_records_index + 8) 
        {
            $result_rank_jobs = RanksJob::select('ranks_jobs.rank_job_id', 'ranks.rank_id', 'ranks_keywords.rank_keyword_id', 'ranks_keywords.rank_keyword_text',
                'cities.city_id', 'cities.city_name', 'cities.city_latitude', 'cities.city_longitude', 'cities.country_id', 'countries.country_name', 'business_profiles.business_id',
                'business_profiles.business_name', 'business_profiles.business_url', 'business_profiles.frequency')
                ->leftJoin('ranks', 'ranks_jobs.rank_id', '=', 'ranks.rank_id')
                ->leftJoin('ranks_keywords', 'ranks.rank_keyword_id', '=', 'ranks_keywords.rank_keyword_id')
                ->leftJoin('cities', 'ranks.city_id', '=', 'cities.city_id')
                ->leftJoin('countries', 'cities.country_id', '=', 'countries.country_id')
                ->leftJoin('business_profiles', 'ranks.business_id', '=', 'business_profiles.business_id')
                ->limit(30)->offset($no_of_records_index)
                ->get();
            
                if(sizeof($result_rank_jobs)>0){
                    $this->getRanking->getRankingResponse($result_rank_jobs);
                }
        }
        return true;
    }

    public function getGMBRanking()
    {
        //$client = new Client();
        //$response = $client->get('http://api.openweathermap.org/data/2.5/weather?q=London&appid=YOUR_API_KEY');
        //$responseData = json_decode($response->getBody(), true)['main']['temp'];

        //Dispatch a job to process the temperature data
        //DoCustomSearchRank::dispatch($responseData);

        // Return a response to the API call
        return response()->json(['message' => 'Temperature data processing in progress']);
    }

    public function storeOrganicResponse(array $responseData,$rank_id) : bool
    {
        // Process the temperature data
        //$processedData = // Process temperature data here

        // Store the processed data in the database or send to an external system
        // ...
        $updateUser = Rank::where('rank_id',$rank_id)->update($responseData["response"]);
        return true;
    }

    public function storeCardResponse($responseData)
    {
        // 
    }
}
