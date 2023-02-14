<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class fBusiness extends Model
{
    use HasFactory;

    protected $table = 'business_profiles';

    public $timestamps = false;
    protected $fillable = [
        'id',
        'b_name',
        'b_url'
    ];
}
