<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use HasFactory;
    protected $table = 'cart';
    protected $fillable = ['id', 'user_id', 'product_id', 'quantity'];

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

}
