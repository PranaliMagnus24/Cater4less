<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodApprovalNotification extends Model
{
    use HasFactory;
    protected $table = 'food_approval_notifications';

    protected $fillable = [
        'food_id',
        'vendor_id',
        'restaurant_id',
        'type',
        'message',
        'denial_reason',
        'is_read'
    ];
    protected $casts = [
        'is_read' => 'boolean',
    ];
    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopePending($query)
    {
        return $query->where('type', 'pending');
    }
}
