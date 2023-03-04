<?php
namespace App\CustomLibrary;

use Exception;
use App\CustomLibrary\RankingApi;
use App\CustomLibrary\HelperLibrary;
use App\Http\Controllers\RankbotConrtroller;
use Carbon\Carbon;

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
    
                $response_card=$this->rankingApi->getCardRanking(array(
                    "keywords"=>$data->rank_keyword_text,
                    "city"=>$data->city_name,
                    "country"=>$data->country_name,
                    "website"=>HelperLibrary::getFormatedURL($data->business_url),
                    "latitude"=>$data->city_latitude,
                    "logitude"=>$data->city_longitude
                    )
                );


                if($response_card["status"]){
                    $compititors_list=array();
                    $is_card_found=0;
                    $rank=0;
                    foreach($response_card["result"] as $key=>$values)
                    {
                        if($key<3){
                            $place_id_response=$this->rankingApi->getPlaceDetails($values["place_id"],HelperLibrary::getFormatedURL($data->business_url));
                            
                            if((bool)$place_id_response["status"]){
                                $rank=$key+1;
                                $is_card_found=$place_id_response["isfound"];
                                $compititors_list[]=$place_id_response["compititors"];  
                            }
                        }

                        if($is_card_found==1)
                        {
                            break;
                        }
                    }

                    $this->rankBotController->storeCardResponse(array(
                        'rank_google_business' => $rank, 
                        'rank_card_none' => (sizeof($response_card["result"]) == 0) ? 1 : 0,
                        'rank_card_competitor'=>(sizeof($compititors_list)>0) ? 1 : 0,
                        'rank_card_found'=>$is_card_found,
                        'rank_last_test_utc'=>Carbon::now(),
                        'if_none_compititors'=>($is_card_found==0)?json_encode($compititors_list):NULL
                    ),$data->rank_id);
                }
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