<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RanksKeyword extends Model
{
    use HasFactory;
    protected $table = 'ranks_keywords';
    public $timestamps = false;
    protected $fillable = [
        'rank_keyword_id',
        'locale_id',
        'rank_keyword_text',
        'rank_keyword_created_utc',
        'rank_keyword_modified_utc'
    ];
}
