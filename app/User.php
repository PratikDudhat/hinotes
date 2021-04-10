<?php

namespace App;

use App\Notifications\Notification;
use App\Notifications\ResetPassword;
use App\Notifications\SendToken;
use App\Notifications\SendVerificationToken;
use App\Notifications\TwoFactorCode;
use GuzzleHttp\Client;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasRoles;
	use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','username','email', 'country_code', 'phone', 'password', 'user_type','gender','country','avatar','email_verified','description','latitude','longitude',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'two_factor_expires_at' => 'datetime',
    ];
    
    /// Please ADD this two methods at the end of the class
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    
    public function getJWTCustomClaims()
    {
        return [];
    }
    
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new SendToken($token));
    }
	
	public function sendVerificationNotification($token)
    {
        $this->notify(new SendVerificationToken($token));
    }
    
    public function sendTwoFactorCodeNotification()
    {
        $this->notify(new TwoFactorCode());
    }
    
    public function generateTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = rand(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(10);
        $this->save();
    }
    
    public function resetTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }
    /**
     * Get the index name for the model.
     *
     * @return string
    */
    public function media() {
        return $this->hasMany('App\Media','user_id','id') ;
    }
	
	protected $appends = ['profileUrl'];
	
	public function getprofileUrlAttribute()
	{
		if(!empty($this->avatar)){
			return url($this->avatar);
		} else{
			return "";
		}	
	}
	
	public static function user_list($uid)
    {
        // Location condition
        $where_arr = array(
            'u.user_type' => 'customer',
            //'u.status' => USER_STATUS_ACTIVE,
            'u.deleted_at' => NULL,
        );
		$user = User::find($uid);
        $latitude = $user->latitude;
		$longitude = $user->longitude;
        $circle_radius = 100;
		$users = "";
		if($latitude != Null && $longitude != Null){
			
			$users = DB::table('users as u')
									->select(DB::raw("u.*, ( 3959 * acos( cos( radians('$latitude') ) * cos( radians( u.latitude ) ) * cos( radians( u.longitude ) - radians('$longitude') ) + sin( radians('$latitude') ) * sin( radians( u.latitude ) ) ) ) AS distance"))->havingRaw('distance < '.$circle_radius)->orderBy('distance')
									->where($where_arr)
									->where('u.id','!=',$uid)
									->get();
			
			foreach($users as $keyU=>$userU) 
			{
				$users[$keyU]->distance = round(($userU->distance * 1.60934),1).' km away';
				$images = DB::table('media')->where(array(
					'user_id' => $userU->id,
					'media_type' => 1
				))->get();
				$users[$keyU]->images = $images->toArray();

				$videos = DB::table('media')
					->where(array('user_id' => $userU->id))
					->where(function($query) {
						$query->where('media_type', 2);
					})->get();
				$users[$keyU]->videos = $videos->toArray();

				$users[$keyU]->is_video_available = 0;
				if (sizeof($videos) > 0) 
				{
					$users[$keyU]->is_video_available = 1;
				}
			}
		}

        if(is_array($users))
        {
            return ['users'=> ($users ? $users : array())];
        }
        return ['users'=>($users ? $users->toArray() : array())];
    }
	
	public function social(){
		return $this->hasMany('App\SocialUsers','user_id')->latest();
	}
	     // users that are followed by this user
    public function following() {
        return $this->belongsToMany(User::class, 'followers', 'follow_by', 'follow_for')->select(['users.id','users.name','users.avatar','followers.status as is_follow']);
    }

    // users that follow this user
    public function followers() {
        return $this->belongsToMany(User::class, 'followers', 'follow_for', 'follow_by')->select(['users.id','users.name','users.avatar','followers.status as is_follow']);
    }
	
}