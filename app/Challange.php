<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\ChallangeLike;

class Challange extends Model
{
     use SoftDeletes;
	  
     public $fillable = ['user_id','category_id','sponser_id','thumbnail','video_file','is_lock','password','latitude','longitude','totallikes','totalvotes'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    protected $appends = ['thumbnailUrl','videoUrl','challangedate','followers','islike','isvote'];
	
	public function getvideoUrlAttribute()
	{
		if(!empty($this->video_file)){
			return url($this->video_file);
		} else{
			return "";
		}	
	}

	public function getthumbnailUrlAttribute()
	{
		if(!empty($this->thumbnail)){
			return url($this->thumbnail);
		} else{
			return "";
		}	
	}

	public function getchallangedateAttribute()
	{
		if(!empty($this->created_at)){
       return  "Until ".\Carbon\Carbon::parse($this->created_at)->format('F d, Y');;
        
		} else{
			return "";
		}	
	}
	
	public function challangeTag(){
    	return $this->hasMany('App\ChallangeTag', 'challange_id')->select(['challange_tags.id', 'challange_tags.tags_id','challange_tags.challange_id','tags.title'])->join('tags','challange_tags.tags_id','=','tags.id');
    }

    public function categoryname()
    {
        return $this->belongsTo('App\Category','category_id','id');
    }
	
    public function users()
    {
        
       $data = $this->belongsTo('App\User','user_id','id')->select('id','username','avatar');
       if(!empty($data->avatar)){
			return $data->profileUrl = url($data->avatar);
		} else{
			return $data;
		}	
    }

	/* public function getTotalLikesAttribute()
	{
		$number = $this->hasMany('App\ChallangeLike')->groupBy('challange_likes.challange_id')->count();
		if($number >= 1000){
		 $number = $value / 1000;
        return $newVal = number_format($number,2) . 'k';
		}
		else{
			return  $number;
		}
    //if you want 2 decimal digits

	}
	public function getTotalvotesAttribute()
	{
		$number = $this->hasMany('App\ChallangeVote')->groupBy('challange_votes.challange_id')->count();
		if($number >= 1000){
		$number = $value / 1000;
        return $newVal = number_format($number,2) . 'k';
		}
		else{
			return  $number;
		}
	} */
	
	public function getFollowersAttribute()
	{
		return $this->hasMany('App\Follower','follow_for','user_id')->count();
	}
	
	public function islikes()
    {
        $user_id = auth('api')->id();
        return $this->hasOne('App\ChallangeLike', 'challange_id', 'id')->where('like_by',$user_id);
    }
    
    public function getIslikeAttribute()
    {
        return $this->islikes?true:false; 
    }
	
	public function isvotes()
    {
        $user_id = auth('api')->id();
        return $this->hasOne('App\ChallangeVote', 'challange_id', 'id')->where('vote_by',$user_id);
    }
    
    public function getIsvoteAttribute()
    {
        return $this->isvotes?true:false; 
    }
}
