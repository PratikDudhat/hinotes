<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;
	 
    public $fillable = ['title','tag_logo'];
	
	public function video(){
        return $this->belongsToMany('App\Challange', 'App\ChallangeTag','tags_id','challange_id');      	
    }
	
	public function challangevideo(){
        return $this->belongsToMany('App\Challange', 'App\ChallangeTag','tags_id','challange_id');      
    }
	
	protected $appends = ['date','sponser','tagUrl'];
	

	public function getdateAttribute()
	{
			return "Until March 27 2021";
	}

	public function getsponserAttribute()
	{
			return "P55 Digital Edition";
	}
	
	public function gettagUrlAttribute()
	{
		if(!empty($this->tag_logo)){
			return url($this->tag_logo);
		} else{
			return "";
		}	
	}
}