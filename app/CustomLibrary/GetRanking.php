<?php
namespace App\CustomLibrary;

use Exception;
use App\CustomLibrary\RankingApi;
use App\CustomLibrary\HelperLibrary;
use Carbon\Carbon;

class GetRanking
{
    private $rankingApi;

    public function __construct()
    {
        $this->rankingApi = new RankingApi();
    }

    /*
    * Ranking Response method will handle the Ranking Request and Job request will handle.
    * Prams will handle collection array of jobs
    */
    public function getRankingResponse(array $rank_job_sheduled_objs)// : bool
    {
        try
        {
            foreach($rank_job_sheduled_objs as $data){

               
                /*
                * RankingApi class has getOrganicRanking method for call the Organic search result reponses.
                * And retur the response if found organic response from the API.
                * After receiving the Array Response data will be pass to the storeOrganicResponse for update the
                * Response in database which is defined under RankBotController
                */
                $response=$this->rankingApi->getOrganicRanking(array(
                    "keywords"=>$data["rank_keyword_text"],
                    "city"=>$data["city_name"],
                    "country_id"=>$data["country_id"],
                    "country"=>$data["country_name"],
                    "website"=>HelperLibrary::getFormatedURL($data["business_url"]))
                );
                
               
                HelperLibrary::storeOrganicResponse($response,$data["rank_id"]);
                
                /*
                * For get the Card Response Param has passed to the getCardRanking() method of Ranking API class,
                * which will return
                * the no of results as an array obj.
                */
                $response_card=$this->rankingApi->getCardRanking(array(
                    "keywords"=>$data["rank_keyword_text"],
                    "city"=>$data["city_name"],
                    "country"=>$data["country_name"],
                    "website"=>HelperLibrary::getFormatedURL($data["business_url"]),
                    "latitude"=>$data["city_latitude"],
                    "logitude"=>$data["city_longitude"]
                    )
                );

               

                /*
                * If card api response is true and it carries data responses.
                */
                if($response_card["status"]){
                    
                    $compititors_list=array();
                    $is_card_found=0;
                    $rank=0;
                    foreach($response_card["result"] as $key=>$values)
                    {
                        /*
                        * Api returns available mathches up to 20 resulst but as per specification documentation
                        * we will be comparing top 3 results.
                        */
                        if($key<3){
                            /*
                            * For compare the respnose we are calling another API for compare the results using of
                            * pass the place_id as a parameter into the method with business_profile website url to
                            * compare and method will response as isFound (True/False) and if false the list of compititors.
                            */
                            $place_id_response=$this->rankingApi->getPlaceDetails($values["place_id"],HelperLibrary::getFormatedURL($data["business_url"]));
                           
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
                   
                    /*
                    * We are calling the storeCardResponse for updating the ranking records into table the method
                    * is defined in the rankBotController for updation of record.
                    */
                    HelperLibrary::storeCardResponse(array(
                        'rank_google_business' => $rank, 
                        'rank_card_none' => (sizeof($response_card["result"]) == 0) ? 1 : 0,
                        'rank_card_competitor'=>(sizeof($compititors_list)>0) ? 1 : 0,
                        'rank_card_found'=>$is_card_found,
                        'rank_last_test_utc'=>Carbon::now(),
                        'if_none_compititors'=>($is_card_found==0)?json_encode($compititors_list):NULL
                    ),$data["rank_id"]);
                }
                //DB::table('ranks_jobs')->where('rank_job_id',$data->rank_job_id)->delete();
            }

           // return true;
        }catch(Exception $e)
        {
            echo $e->getMessage();
            //return false;
        }
    }
}


?>