<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory ,SoftDeletes;
    protected $table = 'categories';
    protected $fillable = ['id','category_name','image'];

    
    public function product()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
}
