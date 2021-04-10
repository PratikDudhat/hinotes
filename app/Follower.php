<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    public $fillable = ['follow_for','follow_by','status'];
}
