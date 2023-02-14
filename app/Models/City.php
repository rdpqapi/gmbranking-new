<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $table = 'cities';

    public $timestamps = false;
    protected $fillable = [
        'country_id',
        'state_id',
        'city_name',
        'city_latitude',
        'city_longitude'
    ];
}
