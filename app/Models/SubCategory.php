<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'sub_categories';
    protected $fillable = ['id','category_id','sub_category_name','image','distributor_discount','dealer_discount'];

    public function category()
    {
        return $this->hasOne(Category::class,'id','category_id');
    }

    public function product()
    {
        return $this->hasMany(Product::class,'sub_category_id','id');
    }
}
