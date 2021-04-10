<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserTokens extends Model
{
  protected $fillable = ["user_id","token","platform"];
}
