<?php
namespace App\Services;

use App\Models\Order;
use App\Models\Restaurant;

class BadgeCriteriaService
{
    public function evaluateBadge(Restaurant $restaurant)
    {
        $totalOrders = Order::where('restaurant_id', $restaurant->id)->count();
        $failedOrders = Order::where('restaurant_id', $restaurant->id)->where('status', 'failed')->count();
        $cancelledOrders = Order::where('restaurant_id', $restaurant->id)->where('status', 'cancelled')->count();
        $delayedOrders = Order::where('restaurant_id', $restaurant->id)->where('is_late', 1)->count();

        if ($totalOrders === 0) {
            return null; // no badge
        }

        if ($failedOrders == 0 && $cancelledOrders == 0 && $delayedOrders == 0) {
            // perfect record
            // you can apply your custom logic like last 30 days
            if ($totalOrders >= 100) {
                return 'caterstar';
            } elseif ($totalOrders >= 50) {
                return 'platinum';
            } else {
                return 'gold';
            }
        }

        return null; // no badge if criteria fail
    }
}

