<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMaster extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stock_masters';

    protected $fillable = ['id','product_id','quantity'];

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
