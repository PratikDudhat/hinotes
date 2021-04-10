<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallangeTag extends Model
{
      use SoftDeletes;
	  
     public $fillable = ['challange_id','tags_id'];
}
