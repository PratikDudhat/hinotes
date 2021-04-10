<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialUsers extends Model
{
	use SoftDeletes;
	protected $table = 'social_users';
	
    public $fillable = ['user_id ','social_id','social_platform'];
	
	public function users(){
		return $this->belongsTo('App\User', 'user_id');
	}
	
	
	
}
