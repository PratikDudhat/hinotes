<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    /**
     * The attributes that are mass assignable.
     *    
     * @var array
     */
    protected $fillable = [
        'user_id', 'media_type', 'media_name', 'media_path'
    ];
	
	protected $appends = ['mediaUrl'];
	
	public function getmediaUrlAttribute()
	{
		return url($this->media_path);
	}
}