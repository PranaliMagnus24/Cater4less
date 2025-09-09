<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftCardShare extends Model
{
    use HasFactory;
    protected $table = 'gift_card_shares';
    protected $fillable = ['gift_card_id','from_user_id','to_user_id','to_email','share_token','message','status','accepted_at'];

    public function giftCard()
    {
        return $this->belongsTo(GiftCard::class);
    }
}
