<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\CentralLogics\Helpers;

class ThirdPartyCompany extends Model
{
    use HasFactory;
    protected $table = 'third_party_companies';
    protected $fillable = ['company_name','company_email','company_phone','company_address','status','image'];
     protected $appends = ['image_full_url'];
     public function getImageFullUrlAttribute()
{
    $value = $this->image;

    if (!$value) {
        return asset('storage/company/default.png'); // fallback image
    }

    return Helpers::get_full_url('company', $value, 'public');
}


}
