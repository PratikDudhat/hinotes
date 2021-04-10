<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{	 
    public $fillable = ['sender_id','receiver_id','last_message','status'];
	
}