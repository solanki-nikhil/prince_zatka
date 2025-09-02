<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'user_id','user_type','order_by','order_id','name','mobile','address',
        'total_pices','total_box','total_amount','bill_number','order_status',
        'lr_photo','lr_number','lr_date','cases','remarks','is_outstock'
    ];

    public function orderTransection()
    {
        return $this->hasMany(OrderTransection::class, 'order_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
