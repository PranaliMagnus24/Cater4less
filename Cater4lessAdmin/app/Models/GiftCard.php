<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GiftCard extends Model
{
    use HasFactory;
    protected $table = 'gift_cards';
    protected $fillable = ['code', 'amount', 'balance', 'expiry_date', 'status','image','owner_id'];

    public static function generateCode()
    {
        return 'GFT-' . strtoupper(Str::random(8));
    }
    // Model
public function getImageFullUrlAttribute()
{
    $value = $this->image;
    if (!$value) {
        return asset('storage/gift-card/default.png');
    }
    return \App\CentralLogics\Helpers::get_full_url('gift-card', $value, 'public');
}

public function shares()
    {
        return $this->hasMany(GiftCardShare::class);
    }

}
