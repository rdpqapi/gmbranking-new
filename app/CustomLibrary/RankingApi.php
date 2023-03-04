<?php

namespace App\CustomLibrary;

use Exception;
use Illuminate\Support\Facades\Http;
use App\CustomLibrary\HelperLibrary;

class RankingApi
{
    private $GOOGLE_CLOUD_API_KEY = null;
    private $GOOGLE_CUSTOM_SEARCH_API_KEY = null;

    public function __construct()
    {
        //Google Cloud API Key
        $this->GOOGLE_CLOUD_API_KEY = env("GOOGLE_CLOUD_API_KEY");
        //Google Custom Search Key
        $this->GOOGLE_CUSTOM_SEARCH_API_KEY = env("CSE_KEY");
    }

    public function getOrganicRanking(array $organic_ranking_objs) : array
    {
        $array_response=array();
        try {
            if (empty($organic_ranking_objs["keywords"])) {
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

    public function getCardRanking(array $msg): int
    {
        return 0;
    }


}
