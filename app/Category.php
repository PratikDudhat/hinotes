<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
	 use SoftDeletes;
	  
     public $fillable = ['category_name','category_logo'];

     protected $appends = ['categoryUrl','challange_count'];

     public function getcategoryUrlAttribute()
	{
		if(!empty($this->category_logo)){
			return url($this->category_logo);
		} else{
			return "";
		}	
	}
	
	public function getchallangeCountAttribute()
	{
			return $this->hasMany('App\Challange','category_id','id')->count();
	}
}
