<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Rank;

class RanksJob extends Model
{
    use HasFactory;
    protected $table = 'ranks_jobs';
    public $timestamps = false;
    protected $fillable = [
        'rank_job_id',
        'rank_id',
        'user_id',
        'rank_job_created_utc'
    ];

    public function getTotalJob()
    {
        return $this->Select('rank_job_id')->get()->count();
    }
    
}
