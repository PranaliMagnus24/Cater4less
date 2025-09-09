<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftCardTransaction extends Model
{
    use HasFactory;
    protected $table = 'gift_card_transactions';
    protected $fillable = ['gift_card_id','user_id','type','amount','balance_before','balance_after','reference','meta'];
}
