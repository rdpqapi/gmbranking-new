<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessProfile extends Model
{
    protected $table = 'business_profiles';

    public $timestamps = false;
    protected $fillable = [
        'business_id',
        'business_name',
        'business_url',
        'business_phone',
        'business_address',
        'business_place_id'
    ];
}
