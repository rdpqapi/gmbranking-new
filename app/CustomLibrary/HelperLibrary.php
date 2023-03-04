<?php

namespace App\CustomLibrary;

class HelperLibrary
{
    public static function getFormatedURL(string $url): string
    {
        $business_url_meta = parse_url($url);
        if (sizeof($business_url_meta) > 1) {
            return HelperLibrary::getFormatedLink($business_url_meta['host']);
        } else {
            if (!empty($business_url_meta['path'])) {
                return HelperLibrary::getFormatedLink($business_url_meta['path']);
            }
        }
    }

    public static function getFormatedLink(string $url): string
    {
        $temp = explode(".", $url);
        if (sizeof($temp) > 2) {
            return $temp[1] . "." . $temp[2];
        } else {
            return $temp[0] . "." . $temp[1];
        }
    }

    public static function getFormatedOrganicResponse(array $api_responses_array_object, string $url): array
    {
        $rank_status = 0;
        $current_rank = 0;
        foreach ($api_responses_array_object as $key => $values) {
            if (HelperLibrary::getFormatedURL($values["displayLink"]) == $url) {
                $current_rank = ($key + 1);
                $rank_status = 1;
                break;
            }
        }

        return array(
            'rank_organic' => $current_rank,
            'rank_not_found' => ($rank_status == 0) ? 1 : 0,
        );
    }
}
