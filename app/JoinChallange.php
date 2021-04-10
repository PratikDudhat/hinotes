<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JoinChallange extends Model
{
    public $fillable = ['user_id','challange_id','status'];
}
