<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_profiles';
    protected $fillable = ['id','user_id','country_id','company_name','gst','address', 'state_id','city_id'];

    public function userInfo()
    {
        return $this->hasMany(User::class,'id','user_id');
    }

    // public function user()
    // {
    //     return $this->hasOne(User::class,'id','user_id');
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->hasOne(Country::class,'id','country_id');
    }

    public function state()
    {
        return $this->hasOne(State::class,'id','state_id');
    }

    public function city()
    {
        return $this->hasOne(City::class,'id','city_id');
    }
}
