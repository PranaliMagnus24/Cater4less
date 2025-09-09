<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Restaurant;
use App\Models\RestaurantGiftPoint;
use App\Models\RestaurantBid;

class PromotionController extends Controller
{
  public function index(Request $request)
{
    $search = $request->input('search');

    $restaurants = Restaurant::with(['currentBid', 'currentGiftPoint'])
    ->select('restaurants.id', 'restaurants.name')
    ->when($search, function ($query) use ($search) {
        $query->where('restaurants.name', 'like', "%{$search}%");
    })
    ->leftJoin('restaurant_bids', function ($join) {
        $join->on('restaurants.id', '=', 'restaurant_bids.restaurant_id')
             ->where('restaurant_bids.is_active', 1);
    })
    ->orderByRaw('CASE WHEN restaurant_bids.bid_percentage IS NOT NULL THEN 0 ELSE 1 END')
    ->orderBy('restaurants.name', 'asc')
    ->paginate(15);


    if ($request->ajax()) {
        return response()->json([
            'html' => view('admin-views.promotion.partials._table', compact('restaurants'))->render()
        ]);
    }

    return view('admin-views.promotion.index', compact('restaurants'));
}


    public function update(Request $request, $id)
{
    $request->validate([
        'promotion_bid' => 'nullable|numeric|min:5|max:20',
        'gift_point_percentage' => 'nullable|numeric|min:2|max:10',
        'is_bid_enabled' => 'nullable|boolean'
    ]);

    $restaurant = Restaurant::findOrFail($id);

    $isEnabled = $request->has('is_bid_enabled') ? 1 : 0;

    // deactivate old bids/gift points
    RestaurantBid::where('restaurant_id', $restaurant->id)->update(['is_active' => false]);
    RestaurantGiftPoint::where('restaurant_id', $restaurant->id)->update(['is_active' => false]);

    if ($request->filled('promotion_bid')) {
        RestaurantBid::create([
            'restaurant_id'   => $restaurant->id,
            'bid_percentage'  => $request->promotion_bid,
            'is_active'       => $isEnabled,
        ]);
    }

    if ($request->filled('gift_point_percentage')) {
        RestaurantGiftPoint::create([
            'restaurant_id'         => $restaurant->id,
            'gift_point_percentage' => $request->gift_point_percentage,
            'is_active'             => $isEnabled,
        ]);
    }

    Toastr::success(translate('Promotion & Gift Points updated successfully.'));
    return back();
}



}
