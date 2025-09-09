<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GiftCard;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GiftCardsExport;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Storage;

class GiftCardController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->get('search');
        $giftCards = GiftCard::when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->Where('code', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%")
                  ->orWhere('balance', 'like', "%{$search}%");
            });
        })
        ->latest()
        ->paginate(10);
        return view('admin-views.gifts-card.gift_card_list', compact('giftCards', 'search'));
    }

    public function create()
    {
        return view('admin-views.gifts-card.gift_card_create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount'      => 'required|numeric|min:1',
            'balance'     => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date|after:today',
            'status'      => 'nullable|in:active,redeemed,expired',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = Helpers::upload(dir: 'gift-card/', format: 'png', image: $request->file('image'));
        }

        $giftCard = GiftCard::create([
            'code'        => GiftCard::generateCode(),
            'amount'      => $request->amount,
            'balance'     => $request->filled('balance') ? $request->balance : $request->amount,
            'expiry_date' => $request->expiry_date,
            'status'      => $request->input('status', 'active'),
            'image'       => $imagePath,
        ]);
        return response()->json([
            'success'   => true,
            'message'   => translate('Gift card created successfully!'),
            'gift_card' => $giftCard->loadMissing([]),
            'image_url' => $giftCard->image_full_url,
        ], 200);
    }

    public function edit(GiftCard $giftCard)
    {
        return view('admin-views.gifts-card.gift_card_edit', compact('giftCard'));
    }

    public function update(Request $request, GiftCard $giftCard)
    {
        $validator = Validator::make($request->all(), [
            'amount'        => 'required|numeric|min:1',
            'balance'       => 'required|numeric|min:0',
            'expiry_date'   => 'nullable|date',
            'status'        => 'required|in:active,redeemed,expired',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'remove_image'  => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        if ($request->boolean('remove_image') && $giftCard->image) {
            $path = $giftCard->image;
            if (!str_starts_with($path, 'gift-card/')) $path = 'gift-card/'.$path;
            Storage::disk('public')->delete($path);
            $giftCard->image = null;
        }
        if ($request->hasFile('image')) {
            if ($giftCard->image) {
                $old = $giftCard->image;
                if (!str_starts_with($old, 'gift-card/')) $old = 'gift-card/'.$old;
                Storage::disk('public')->delete($old);
            }
            $newPath = Helpers::upload(dir: 'gift-card/', format: 'png', image: $request->file('image'));
            $giftCard->image = $newPath;
        }
        $giftCard->amount = $request->amount;
        $giftCard->balance = $request->balance;
        $giftCard->expiry_date = $request->expiry_date;
        $giftCard->status = $request->status;
        $giftCard->save();
        return response()->json([
            'success'    => true,
            'message'    => translate('Gift card updated successfully!'),
            'gift_card'  => $giftCard->fresh(),
            'image_url'  => $giftCard->image_full_url,
        ], 200);
    }

    public function destroy(GiftCard $giftCard)
    {
        if ($giftCard->image) {
            $path = $giftCard->image;
            if ($path && !str_starts_with($path, 'gift-card/')) {
                $path = 'gift-card/'.$path;
            }
            Storage::disk('public')->delete($path);
        }

        $giftCard->delete();
        return response()->json([
            'success' => true,
            'message' => translate('Gift card deleted successfully!'),
        ], 200);
    }

    public function status(Request $request, $id)
    {
        $giftCard = GiftCard::findOrFail($id);

        if ($giftCard->status === 'active') {
            $giftCard->status = 'redeemed';
        } elseif ($giftCard->status === 'redeemed') {
            $giftCard->status = 'expired';
        } else {
            $giftCard->status = 'active';
        }
        $giftCard->save();
        return response()->json([
            'success' => true,
            'status' => $giftCard->status,
            'message' => 'Gift card status updated successfully'
        ]);
    }

    public function export(Request $request, $type)
    {
        $query = $request->all();

        if ($type === 'excel') {
            return Excel::download(new GiftCardsExport($query), 'gift_cards.xlsx');
        }
        if ($type === 'csv') {
            return Excel::download(new GiftCardsExport($query), 'gift_cards.csv');
        }
        abort(404);
    }

}
