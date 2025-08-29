<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PointRedeem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'point_redeems';
    protected $fillable = ['user_profile_id', 'redeem'];

    public function userDetails()
    {
        return $this->hasOne(UserProfile::class, 'id', 'user_profile_id');
    }
}
