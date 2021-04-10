<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChallangeVote extends Model
{
    public $fillable = ['vote_by','challange_id'];
}
