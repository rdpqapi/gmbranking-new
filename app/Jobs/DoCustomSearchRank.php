<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use DB;

class DoCustomSearchRank implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $ch = null;
    private $gc_key=null;
    private $gcsx_key=null;
    private $datas=null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($datas)
    {
        $this->datas=$datas;
        //Google Cloud API Key
        $this->gc_key=env("GOOGLE_CLOUD_API_KEY");
        //Google Custom Search Key
        $this->gcsx_key=env("CSE_KEY");
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->ch == null) {
            $this->ch = curl_init();
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($this->ch, CURLOPT_ENCODING, 'gzip, deflate');
            $headers = array();
            $headers[] = 'Accept: application/json';
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        }

        foreach($this->datas as $data){
            $this->doSearch($data->rank_id,$data->rank_keyword_text,$data->city_name,$data->country_name,
            $this->getFormatedURL($data->business_url));

            $this->doGMBSearch($data->rank_id,$data->city_latitude,$data->city_longitude,
            $data->rank_keyword_text,$data->city_name,$data->country_id,$data->country_name,
            $this->getFormatedURL($data->business_url));
        }
        curl_close($this->ch);
    }


    public function getFormatedURL($url){
        $business_url_meta = parse_url($url);
        if(sizeof($business_url_meta)>1){
            return $business_url=$this->getFormatedLink($business_url_meta['host']);
        }else{
            if(!empty($business_url_meta['path'])){
                return $business_url=$this->getFormatedLink($business_url_meta['path']);
            }
        }
    }

    public function getFormatedLink($url){
        $temp=explode(".",$url);
        if(sizeof($temp)>2){
            return $temp[1].".".$temp[2];
        }else{
            return $temp[0].".".$temp[1];
        }
    }

    //Call the Google Places and Place Details api
    public function doGMBSearch($rank_id,$lat,$lon,$query,$city,$country_code,$country_name,$business_profile_url){
        //&type=establishment
        $url_business_profile_api="https://maps.googleapis.com/maps/api/place/search/json?location=".$lat.",".$lon."&radius=50000&keyword=".urlencode($query." in ".$city.", ".$country_name)."&sensor=false&key=".$this->gc_key;
        curl_setopt($this->ch, CURLOPT_URL, $url_business_profile_api);
        $result = curl_exec($this->ch);
        $business_profileranking_datas=json_decode($result,true);
        $business_profileranking_datas=$business_profileranking_datas["results"];
        
        $rank_in_card=0;
        $rec_found_status=0;
        $compititors_list=array();

        foreach($business_profileranking_datas as $key=>$values){
            if($key<3){
                $url_business_profile_detail_api="https://maps.googleapis.com/maps/api/place/details/json?place_id=".$values["place_id"]."&fields=formatted_phone_number,website&key=".$this->gc_key;
                curl_setopt($this->ch, CURLOPT_URL, $url_business_profile_detail_api);
                $result = curl_exec($this->ch);
                $business_profile_raw=json_decode($result,true);
                
                if(!empty($business_profile_raw["result"]["website"])){
                    if($business_profile_url==$this->getFormatedURL($business_profile_raw["result"]["website"])){
                        $rank_in_card=($key+1);
                        $rec_found_status=1;
                        break;   
                    }else{
                        $compititors_list[]=array(
                            "rank"=>($key+1),
                            "name"=>$values["name"],
                            "place_id"=>$values["place_id"],
                            "website"=>$this->getFormatedURL($business_profile_raw["result"]["website"]),
                            "phone"=>(!empty($business_profile_raw["result"]["formatted_phone_number"]))? $business_profile_raw["result"]["formatted_phone_number"] : '0'
                        );
                    }
                }
            }
        }
        
        $updateUser = DB::table('ranks')->where('rank_id',$rank_id)->update(array(
            'rank_google_business' => $rank_in_card, 
            'rank_card_none' => (sizeof($business_profileranking_datas)==0)?1:0,
            'rank_card_competitor'=>($rec_found_status==0)?1:0,
            'rank_card_found'=>($rec_found_status==1)?1:0,
            'rank_last_test_utc'=>Carbon::now(),
            'if_none_compititors'=>($rec_found_status==0)?json_encode($compititors_list):NULL
            ));
    }    
    
    //Call the (Organic Search) custom search api
    public function doSearch($rank_id,$rank_keyword_text,$city_name,$country_name,$business_url){
        $business_ranking_datas=array();
        $cse_resultset1=array();
        
        $query="&gl=IND&lr=lang_en&num=10&q=".urlencode($rank_keyword_text." in ".$city_name.", ".$country_name)."&searchType=searchTypeUndefined";
        for($i=0;$i<2;$i++){
            $pagination=($i==0)?($i+1):($i+10);

            curl_setopt($this->ch, CURLOPT_URL, "https://customsearch.googleapis.com/customsearch/v1?c2coff=0&cx=".$this->gcsx_key."&filter=0".$query."&siteSearchFilter=e&start=1&key=".$this->gc_key);
            $result = curl_exec($this->ch);
            $business_ranking_data=json_decode($result,true);
            $business_ranking_data=$business_ranking_data["items"];

            if(sizeof($business_ranking_data)>0){
                if($i==0){
                    $cse_resultset1=$business_ranking_data;
                }else{
                    $business_ranking_datas=array_merge($cse_resultset1,$business_ranking_data);
                }
            }
        }
        
        $rank_status=0;
        $current_rank=0;
        foreach($business_ranking_datas as $key=>$values){
            if($this->getFormatedURL($values["displayLink"])==$business_url){
                $current_rank=($key+1);
                $rank_status=1;
                break;
            }
        }
        $updateUser = DB::table('ranks')->where('rank_id',$rank_id)->update(array(
            'rank_organic' =>$current_rank,
            'rank_not_found'=>($rank_status==0)?1:0,
            ));
    }
}
