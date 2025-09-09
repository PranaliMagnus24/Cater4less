<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftCardPayment extends Model
{
    use HasFactory;
    protected $table = 'gift_card_payments';
    protected $fillable = ['user_id','amount','payment_status','payment_method','payment_platform','gateway_reference','meta'];
}
