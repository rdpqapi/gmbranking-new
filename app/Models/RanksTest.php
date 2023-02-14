<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RanksTest extends Model
{
    use HasFactory;
    protected $table = 'ranks_tests';

    public $timestamps = false;
    protected $fillable = [
        'rank_test_id',
        'rank_id',
        'user_id',
        'rank_tested_utc'
    ];
}
