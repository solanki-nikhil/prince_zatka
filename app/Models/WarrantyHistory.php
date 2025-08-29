<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_no_id', 'category_id', 'product_id',
        'customer_name', 'customer_village', 'customer_mobile', 'warranty_date'
    ];

    public function serialNo()
    {
        return $this->belongsTo(SerialNo::class);
    }
}
