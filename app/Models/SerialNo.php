<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SerialNo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'serialno';
    protected $fillable = ['id', 'product_id', 'user_id', 'sn', 'cus_name', 'cus_village', 'cus_mobile', 'valid_to', 'valid_from', 'remarks', 'is_reject', 'is_replace', 'status', 'location_status'];

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id')->withDefault([
            'product_name' => '-',
        ]);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->withDefault([
            'name' => '-',
        ]);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
