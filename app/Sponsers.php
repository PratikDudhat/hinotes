<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sponsers extends Model
{
	 use SoftDeletes;
	 
     public $fillable = ['sponsers_name','sponsers_logo','sponsers_banner'];
}
