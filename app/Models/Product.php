<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';
    protected $fillable = ['id', 'category_id', 'sub_category_id', 'product_name', 'quantity', 'product_code', 'box', 'price', 'description', 'image', 'image2', 'image3'];

     protected $appends = ['image_url', 'image2_url', 'image3_url'];

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
    public function subCategory()
    {
        return $this->hasOne(SubCategory::class, 'id', 'sub_category_id');
    }
    

    // ✅ Accessors

    public function getImageUrlAttribute()
{
    return $this->image ? url('upload/product/' . rawurlencode($this->image)) : null;
}

public function getImage2UrlAttribute()
{
    return $this->image2 ? url('upload/product/' . rawurlencode($this->image2)) : null;
}

public function getImage3UrlAttribute()
{
    return $this->image3 ? url('upload/product/' . rawurlencode($this->image3)) : null;
}
 

}
