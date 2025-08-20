<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GiftCard extends Model
{
    use HasFactory;
    protected $table = 'gift_cards';
    protected $fillable = ['code', 'amount', 'balance', 'expiry_date', 'status'];

    public static function generateCode()
    {
        return 'GFT-' . strtoupper(Str::random(8));
    }

}
