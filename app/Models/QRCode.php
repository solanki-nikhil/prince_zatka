<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QRCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'q_r_codes';
    protected $fillable = ['user_id','code','is_used','used_by','used_date','amount','batch_number'];
}
