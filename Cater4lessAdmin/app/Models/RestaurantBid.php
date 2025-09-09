<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantBid extends Model
{
    use HasFactory;
    protected $table = 'restaurant_bids';
    protected $fillable = ['restaurant_id', 'bid_percentage', 'is_active'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
