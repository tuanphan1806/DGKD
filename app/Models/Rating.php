<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'product_id',
        'review',
        'rating',
        'status'
    ];
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }
}
