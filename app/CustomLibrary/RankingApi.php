<?php

namespace App\CustomLibrary;

use Exception;
use Illuminate\Support\Facades\Http;
use App\CustomLibrary\HelperLibrary;

class RankingApi
{
    private $GOOGLE_CLOUD_API_KEY = null;
    private $GOOGLE_CUSTOM_SEARCH_API_KEY = null;
    private $MAX_RADIUS = 50000;

    public function __construct()
    {
        //Google Cloud API Key
        $this->GOOGLE_CLOUD_API_KEY = env("GOOGLE_CLOUD_API_KEY");
        //Google Custom Search Key
        $this->GOOGLE_CUSTOM_SEARCH_API_KEY = env("CSE_KEY");
    }

    public function getOrganicRanking(array $organic_ranking_objs) : array
    {
        try {

            if (empty($organic_ranking_objs["keywords"]))
            {
                throw new Exception('Empty keywords');
            }

            if (empty($organic_ranking_objs["city"])) {
                throw new Exception('Empty city');
            }

            if (empty($organic_ranking_objs["country_id"])) {
                throw new Exception('Empty Country_ID');
            }

            if (empty($organic_ranking_objs["country"])) {
                throw new Exception('Empty Country');
            }

            if (empty($organic_ranking_objs["website"])) {
                throw new Exception('Empty Business Website');
            }

            $business_ranking_datas = array();
            $cse_resultset1 = array();

            $searchable_query = "&gl=" . $organic_ranking_objs["country_id"] . "&lr=lang_en&num=10&q=" . urlencode($organic_ranking_objs["keywords"] . " in " . $organic_ranking_objs["city"] . ", " . $organic_ranking_objs["country"]) . "&searchType=searchTypeUndefined";
            for ($i = 0; $i < 2; $i++) {
                $pagination = ($i == 0) ? ($i + 1) : ($i + 10);

                $api_response = Http::get("https://customsearch.googleapis.com/customsearch/v1?c2coff=0&cx=" . $this->GOOGLE_CUSTOM_SEARCH_API_KEY . "&filter=0" . $searchable_query . "&siteSearchFilter=e&start=".$pagination."&key=" . $this->GOOGLE_CLOUD_API_KEY);

                if ($api_response->successful()) {
                    $business_ranking_data = json_decode($api_response->body(), true);
                    $business_ranking_data = $business_ranking_data["items"];

                    if (sizeof($business_ranking_data) > 0) {
                        if ($i == 0) {
                            $cse_resultset1 = $business_ranking_data;
                        } else {
                            $business_ranking_datas = array_merge($cse_resultset1, $business_ranking_data);
                        }
                    }
                }

                if ($api_response->failed()) {
                    
                }
            }

            return array(
                "status"=>true,
                "message"=>"success",
                "response"=>HelperLibrary::getFormatedOrganicResponse($business_ranking_datas,$organic_ranking_objs["website"])
            );
        } 
        catch (Exception $e)
        {
            return array(
                "status"=>false,
                "message"=>"failed! ".$e->getMessage(),
                "response"=>array()
            );
        }
    }

    public function getCardRanking(array $card_param_objs): array
    {
        $response_array=array();
        try 
        {
            if (empty($card_param_objs["keywords"]))
            {
                throw new Exception('Empty keywords');
            }

            if (empty($card_param_objs["city"])) {
                throw new Exception('Empty city');
            }

            if (empty($card_param_objs["country"])) {
                throw new Exception('Empty Country');
            }

            if (empty($card_param_objs["website"])) {
                throw new Exception('Empty Country_ID');
            }

            if (empty($card_param_objs["logitude"])) {
                throw new Exception('Empty logitude');
            }

            if (empty($card_param_objs["latitude"])) {
                throw new Exception('Empty latitude');
            }


        

        $api_response = Http::get("https://maps.googleapis.com/maps/api/place/search/json?location=".$card_param_objs["latitude"].",".$card_param_objs["logitude"]."&radius=".$MAX_RADIUS."&keyword=".urlencode($card_param_objs["keywords"]." in ".$card_param_objs["city"].", ".$card_param_objs["country"])."&sensor=false&key=".$GOOGLE_CLOUD_API_KEY);

        if ($api_response->successful()) {
            $business_profileranking_datas = json_decode($api_response->body(), true);
            $business_profileranking_datas = $business_profileranking_datas["results"];

            $rank_in_card=0;
            $rec_found_status=0;
            $compititors_list=array();

            if (sizeof($business_profileranking_datas) > 0) {
                $response_array["status"]=true;
                $response_array["message"]="success";
                $response_array["result"]=$business_profileranking_datas;
            }
        }

        if ($api_response->failed()) {
            $response_array["status"]=false;
            $response_array["message"]="fail";
            $response_array["result"]=array();
        }

        } catch (\Throwable $th) 
        {
            $response_array["status"]=false;
            $response_array["message"]="fail";
            $response_array["result"]=array();
        }

        return $response_array;
    }

    public function getPlaceDetails(string $place_id,string $business_url): array
    {
        $response=array();
        $business_is_found=0;
        $compititors_list=array();
        try {
        
            $api_response = Http::get("https://maps.googleapis.com/maps/api/place/details/json?place_id=".$place_id."&fields=formatted_phone_number,website&key=".$GOOGLE_CLOUD_API_KEY);

            if ($api_response->successful()) {
                $url_business_profile_detail_api = json_decode($api_response->body(), true);

                if(!empty($url_business_profile_detail_api["result"]["website"])){
                    if($business_url==HelperLibrary::getFormatedURL($url_business_profile_detail_api["result"]["website"]))
                    {
                        $business_is_found=1;
                    }
                    else
                    {
                        $compititors_list=array(
                            "place_id"=>$place_id,
                            "website"=>HelperLibrary::getFormatedURL($url_business_profile_detail_api["result"]["website"]),
                            "phone"=>(!empty($url_business_profile_detail_api["result"]["formatted_phone_number"]))? $url_business_profile_detail_api["result"]["formatted_phone_number"] : '0'
                        );
                    }
                }
                
                return array(
                    "status"=>true,
                    "isfound"=>$business_is_found,
                    "compititors"=>$compititors_list
                );
            }

            if ($api_response->failed()) {
                return array(
                    "status"=>false,
                    "isfound"=>$business_is_found,
                    "compititors"=>$compititors_list
                );
            }

        } catch (\Throwable $th) {
            return array(
                "status"=>false,
                "isfound"=>$business_is_found,
                "compititors"=>$compititors_list
            );
        }
    }

}
