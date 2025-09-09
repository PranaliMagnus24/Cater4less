<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use App\CentralLogics\Helpers;
use App\Models\RestaurantGiftPoint;
use App\Models\RestaurantBid;


class PromotionController extends Controller
{
   public function index()
{
    $restaurant = Helpers::get_restaurant_data()->load(['currentBid', 'currentGiftPoint']);

    if (!$restaurant) {
        Toastr::error(translate('No restaurant found.'));
        return back();
    }

    return view('vendor-views.promotion.index', compact('restaurant'));
}


   public function update(Request $request)
{
    $restaurant = Helpers::get_restaurant_data();

    if (!$restaurant) {
        Toastr::error(translate('No restaurant found.'));
        return back();
    }

    $request->validate([
        'is_bid_enabled' => 'nullable|boolean',
        'promotion_bid' => 'nullable|numeric|min:5|max:20',
        'gift_point_percentage' => 'nullable|numeric|min:2|max:10',
    ]);

    $isEnabled = $request->has('is_bid_enabled') ? 1 : 0;

    // Purane active bids/gift points disable karo
    RestaurantBid::where('restaurant_id', $restaurant->id)->update(['is_active' => false]);
    RestaurantGiftPoint::where('restaurant_id', $restaurant->id)->update(['is_active' => false]);

    if ($isEnabled) {
        if ($request->filled('promotion_bid')) {
            RestaurantBid::create([
                'restaurant_id'   => $restaurant->id,
                'bid_percentage'  => $request->promotion_bid,
                'is_active'       => true,
            ]);
        }

        if ($request->filled('gift_point_percentage')) {
            RestaurantGiftPoint::create([
                'restaurant_id'         => $restaurant->id,
                'gift_point_percentage' => $request->gift_point_percentage,
                'is_active'             => true,
            ]);
        }
    }

    Toastr::success(translate('Promotion & Gift Points updated successfully.'));
    return back();
}


}
