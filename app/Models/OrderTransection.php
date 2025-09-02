<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderTransection extends Model
{
    use HasFactory ,SoftDeletes;

    protected $table = 'order_transections';
    protected $fillable = ['id', 'order_id', 'category_id', 'sub_category_id', 'product_id', 'amount', 'box', 'per_box_pices', 'pices', 'discount','total_amount'];

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
    public function subCategory()
    {
        return $this->hasOne(SubCategory::class, 'id', 'sub_category_id');
    }
    
    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function order()
{
    return $this->belongsTo(Order::class, 'order_id', 'id');
}

}