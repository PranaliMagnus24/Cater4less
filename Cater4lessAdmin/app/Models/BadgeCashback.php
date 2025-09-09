<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BadgeCashback extends Model
{
    use HasFactory;
    protected $table = "badge_cashbacks";
    protected $fillable = ['badge','cashback_percentage'];
}
