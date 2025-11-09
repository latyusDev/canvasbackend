<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
     protected $fillable = [
        'name',
        'width',
        'height',
        'canvas_data',
        'user_id',
    ];
}
