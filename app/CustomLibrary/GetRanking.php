<?php
namespace App\CustomLibrary;

use Exception;
use App\CustomLibrary\RankingApi;
use App\CustomLibrary\HelperLibrary;
use App\Http\Controllers\RankbotConrtroller;
class GetRanking
{
    protected $rankingApi,$rankBotController;

    public function __construct()
    {
        $this->rankingApi = new RankingApi();
        $this->rankBotController = new RankbotConrtroller();
    }

    public function getRankingResponse(array $rank_job_sheduled_objs) : bool
    {
        try
        {
            foreach($rank_job_sheduled_objs as $data){

                $response=$this->rankBotController->storeOrganicResponse($this->rankingApi->getOrganicRanking(array(
                    "keywords"=>$data->rank_keyword_text,
                    "city"=>$data->city_name,
                    "country_id"=>$data->country_id,
                    "country"=>$data->country_name,
                    "website"=>HelperLibrary::getFormatedURL($data->business_url))
                    ),$data->rank_id);
    
                $this->rankingApi->getCardRanking(array(
                    "latitude"=>$data->city_latitude,
                    "logitude"=>$data->city_longitude,
                    "keywords"=>$data->rank_keyword_text,
                    "city"=>$data->city_name,
                    "country_id"=>$data->country_id,
                    "country"=>$data->country_name,
                    "website"=>HelperLibrary::getFormatedURL($data->business_url))
                );
    
                //DB::table('ranks_jobs')->where('rank_job_id',$data->rank_job_id)->delete();
            }
        }catch(Exception $e)
        {
            return false;
        }
        
        return false;
    }


}


?>