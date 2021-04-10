<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
     public $fillable = ['sender_id','receiver_id','message','file','thumbnail','file_url','file_type','file_size','conversation_id','is_read','delete_status'];
}
