<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaMaster extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'media_masters';
    protected $fillable = ['id','title','description','image','link'];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image
            ? url('upload/media/' . rawurlencode($this->image))
            : null;
    }
}
