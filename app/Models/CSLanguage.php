<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CSLanguage extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'lang_id',
        'lang_code',
        'lang_name'
    ];
}
