<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cities';
    protected $fillable = ['id', 'country_id', 'state_id', 'state_name'];

    public function country()
    {
        return $this->hasOne('App\Models\Country', 'id', 'country_id');
    }
    
    public function state()
    {
        return $this->hasOne('App\Models\State', 'id', 'state_id');
    }
}
