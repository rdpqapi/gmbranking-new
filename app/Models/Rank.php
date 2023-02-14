<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    use HasFactory;
    protected $table = 'ranks';

    public $timestamps = false;
    protected $fillable = [
        'rank_id',
        'rank_keyword_id',
        'business_id',
        'region_id',
        'city_id',
        'rank_finder',
        'rank_organic',
        'rank_google_business',
        'rank_card_none',
        'rank_card_competitor',
        'rank_card_found',
        'rank_not_found',
        'if_none_compititors',
        'rank_last_test_utc',
        'rank_created_utc',
        'rank_modified_utc',
        'rank_deleted_utc'
    ];
}
