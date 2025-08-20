<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GiftCard;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GiftCardsExport;


class GiftCardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $giftCards = GiftCard::when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('admin-views.gifts-card.gift_card_list', compact('giftCards'));
    }


    public function create()
    {
        return view('admin-views.gifts-card.gift_card_create');
    }

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'amount'      => 'required|numeric|min:1',
        'expiry_date' => 'nullable|date|after:today',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()]);
    }

    $giftCard = GiftCard::create([
        'code'        => GiftCard::generateCode(),
        'amount'      => $request->amount,
        'balance'     => $request->amount,
        'expiry_date' => $request->expiry_date,
        'status'      => 'active',
    ]);

    return response()->json([
        'success'   => true,
        'message'   => translate('Gift card created successfully!'),
        'gift_card' => $giftCard,
    ], 200);
}


    public function edit(GiftCard $giftCard)
    {
        return view('admin-views.gifts-card.gift_card_edit', compact('giftCard'));
    }

    public function update(Request $request, GiftCard $giftCard)
    {
        $validator = Validator::make($request->all(), [
            'amount'      => 'required|numeric|min:1',
            'balance'     => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date|after:today',
            'status'      => 'required|in:active,redeemed,expired',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $giftCard->update($request->only(['amount', 'balance', 'expiry_date', 'status']));

        return response()->json([
            'success'   => true,
            'message'   => translate('Gift card updated successfully!'),
            'gift_card' => $giftCard,
        ], 200);
    }


   public function destroy(GiftCard $giftCard)
{
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
