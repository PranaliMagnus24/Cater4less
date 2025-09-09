<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BadgeCashback;
use App\Models\Restaurant;
use Brian2694\Toastr\Facades\Toastr;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RestaurantBadgesExport;

class BadgeCashbackController extends Controller
{
    public function index()
    {
        $badges = BadgeCashback::all();
        return view('admin-views.badge.index', compact('badges'));
    }
public function update(Request $request)
{
    foreach ($request->cashbacks as $badge => $percentage) {
        BadgeCashback::updateOrCreate(
            ['badge' => $badge],
            ['cashback_percentage' => $percentage]
        );
    }

    if ($request->ajax()) {
        return response()->json([
            'status'  => 'success',
            'message' => translate('messages.badge_cashback_updated_successfully')
        ]);
    }

    Toastr::success(translate('messages.badge_cashback_updated_successfully'));
    return back();

}
public function restaurantBadges(Request $request)
    {
        $query = Restaurant::select('id', 'name', 'badge');

        // 🔎 Search filter
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // 🏅 Badge filter
        if ($request->has('badge') && $request->badge != 'all') {
            if ($request->badge == 'none') {
                $query->whereNull('badge');
            } else {
                $query->where('badge', $request->badge);
            }
        }

        // 📄 Pagination
        $restaurants = $query->paginate(10);

        // 📊 Badge counts
        $badgeCounts = [
            'gold'      => Restaurant::where('badge', 'gold')->count(),
            'platinum'  => Restaurant::where('badge', 'platinum')->count(),
            'caterstar' => Restaurant::where('badge', 'caterstar')->count(),
            'none'      => Restaurant::whereNull('badge')->count(),
            'total'     => Restaurant::count(),
        ];

        return view('admin-views.badge.restaurant-list', compact('restaurants', 'badgeCounts'));
    }

     public function exportRestaurants(Request $request)
    {
        $ext = $request->type === 'excel' ? 'xlsx' : 'csv';
        $fileName = 'restaurant-badges-' . now()->format('Y-m-d_H-i') . '.' . $ext;

        return Excel::download(
            new RestaurantBadgesExport($request->search, $request->badge),
            $fileName
        );
    }


}
