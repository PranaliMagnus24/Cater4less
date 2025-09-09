<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftCardHold extends Model
{
    use HasFactory;
    protected $table = 'gift_card_holds';
    protected $fillable = ['gift_card_id','user_id','order_id','amount','expires_at'];
}
