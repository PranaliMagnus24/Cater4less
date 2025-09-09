<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GiftCard;
use App\Models\GiftCardShare;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\GiftCardHold;
use App\Models\GiftCardPayment;
use App\Models\GiftCardTransaction;
use App\Library\Payer;
use App\Traits\Payment;
use App\Library\Receiver;
use App\Library\Payment as PaymentInfo;
use App\Models\BusinessSetting;
use App\Mail\GiftCardShareMail;
use Illuminate\Support\Facades\Mail;
use App\CentralLogics\Helpers;



class GiftCardController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|integer|min:1',
            'offset' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 422);
        }

        $search = $request->get('search');

        $paginator = GiftCard::when($search, function ($query) use ($search) {
            $query->where('code', 'like', "%{$search}%");
        })
            ->where('status', 'active')
            ->latest()
            ->paginate($request->limit, ['*'], 'page', $request->offset);

        $data = [
            'total_size' => $paginator->total(),
            'limit' => (int) $request->limit,
            'offset' => (int) $request->offset,
            'gift_cards' => $paginator->map(function ($giftCard) {
                return [
                    'id' => $giftCard->id,
                    'code' => $giftCard->code,
                    'amount' => (float) $giftCard->amount,
                    'balance' => (float) $giftCard->balance,
                    'expiry_date' => $giftCard->expiry_date,
                    'status' => $giftCard->status,
                    'created_at' => $giftCard->created_at,
                    'updated_at' => $giftCard->updated_at,
                ];
            }),
        ];

        return response()->json($data, 200);
    }


    ////Purchase gift card

    public function purchase(Request $request)
{
    $validator = Validator::make($request->all(), [
        'amount' => 'required|numeric|min:1',
        'payment_method' => 'required|string',
        'payment_platform' => 'nullable|string',
        'callback' => 'nullable|url'
    ]);

    if ($validator->fails()) {
        // mirror wallet behaviour (errors formatted)
        return response()->json(['errors' => Helpers::error_processor($validator)], 403);
    }

    // check digital payment enabled (like wallet controller)
    $digital_payment = Helpers::get_business_settings('digital_payment');
    if ($digital_payment['status'] == 0) {
        return response()->json(['errors' => ['message' => 'digital_payment_is_disable']], 403);
    }

    try {
        $customer = $request->user();
        $amount = $request->amount;

        if (!isset($customer)) {
            return response()->json(['errors' => ['message' => 'Customer not found']], 403);
        }

        if (!isset($amount)) {
            return response()->json(['errors' => ['message' => 'Amount not found']], 403);
        }

        if (!$request->has('payment_method')) {
            return response()->json(['errors' => ['message' => 'Payment not found']], 403);
        }

        // create pending payment record (like WalletPayment)
        $payment = new GiftCardPayment();
        $payment->user_id = $customer->id;
        $payment->amount = $amount;
        $payment->payment_status = 'pending';
        $payment->payment_method = $request->payment_method;
        $payment->payment_platform = $request->payment_platform ?? null;
        $payment->save();

        // payer
        $payer = new Payer(
            $customer->f_name . ' ' . $customer->l_name,
            $customer->email,
            $customer->phone,
            ''
        );

        $currency = BusinessSetting::where(['key' => 'currency'])->first()->value ?? 'USD';
        $additional_data = [
            'business_name' => BusinessSetting::where(['key' => 'business_name'])->first()?->value,
            'business_logo' => dynamicStorage('storage/app/public/business') . '/' . BusinessSetting::where(['key' => 'logo'])->first()?->value
        ];

        $payment_info = new PaymentInfo(
            success_hook: 'giftcard_success',
            failure_hook: 'giftcard_failed',
            currency_code: $currency,
            payment_method: $request->payment_method,
            payment_platform: $request->payment_platform,
            payer_id: $customer->id,
            receiver_id: '100',
            additional_data: $additional_data,
            payment_amount: $amount,
            external_redirect_link: $request->has('callback') ? $request['callback'] : session('callback'),
            attribute: 'gift_card_payments',
            attribute_id: $payment->id
        );

        $receiver_info = new Receiver('company_name', 'logo.png');

        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

        return response()->json(['redirect_link' => $redirect_link], 200);

    } catch (\Exception $e) {
        // keep parity with wallet controller (returns 200 with error message)
        return response()->json(['error' => $e->getMessage()], 200);
    }
}


    public function userGiftCards(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|integer|min:1',
            'offset' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $search = $request->get('search');

        // Return admin-created gift cards (same as index), for authenticated user view
        $paginator = GiftCard::when($search, function ($query) use ($search) {
            $query->where('code', 'like', "%{$search}%");
        })
            ->where('status', 'active')
            ->latest()
            ->paginate($request->limit, ['*'], 'page', $request->offset);

        $data = [
            'total_size' => $paginator->total(),
            'limit' => (int) $request->limit,
            'offset' => (int) $request->offset,
            'gift_cards' => $paginator->map(function ($giftCard) {
                return [
                    'id' => $giftCard->id,
                    'code' => $giftCard->code,
                    'amount' => (float) $giftCard->amount,
                    'balance' => (float) $giftCard->balance,
                    'expiry_date' => $giftCard->expiry_date,
                    'status' => $giftCard->status,
                    'created_at' => $giftCard->created_at,
                    'updated_at' => $giftCard->updated_at,
                ];
            }),
        ];

        return response()->json($data, 200);
    }


    public function apply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'order_total' => 'required|numeric|min:0.01',
            'order_id' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $giftCard = GiftCard::where('code', $request->code)->first();
        if (!$giftCard) {
            return response()->json(['error' => 'Invalid gift card code'], 404);
        }

        // ownership check: only owner can use (adjust if your logic allows other users)
        if ($giftCard->owner_id != $request->user()->id) {
            return response()->json(['error' => 'This gift card does not belong to you'], 403);
        }

        if ($giftCard->status !== 'active') {
            return response()->json(['error' => 'Gift card not active'], 400);
        }

        if ($giftCard->expiry_date && Carbon::now()->gt(Carbon::parse($giftCard->expiry_date))) {
            return response()->json(['error' => 'Gift card expired'], 400);
        }

        if ($giftCard->balance <= 0) {
            return response()->json(['error' => 'Gift card has zero balance'], 400);
        }

        // amount to apply
        $applyAmount = min($giftCard->balance, $request->order_total);

        // create a hold (recommended) - release on timeout (e.g., 15 mins) or on consume
        $hold = GiftCardHold::create([
            'gift_card_id' => $giftCard->id,
            'user_id' => $request->user()->id,
            'order_id' => $request->order_id,
            'amount' => $applyAmount,
            'expires_at' => Carbon::now()->addMinutes(15)
        ]);

        return response()->json([
            'success' => true,
            'applied_amount' => number_format($applyAmount, 2, '.', ''),
            'remaining_balance' => number_format($giftCard->balance - $applyAmount, 2, '.', ''),
            'hold_id' => $hold->id,
            'message' => 'Gift card applied (reserved) for 15 minutes'
        ], 200);
    }
    public function consume(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'order_id' => 'required|integer',
            'hold_id' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            $giftCard = GiftCard::where('code', $request->code)->lockForUpdate()->first();
            if (!$giftCard) {
                return response()->json(['error' => 'Invalid gift card'], 404);
            }

            // check owner
            if ($giftCard->owner_id != $request->user()->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $amountToConsume = (float) $request->amount;

            if ($giftCard->balance < $amountToConsume) {
                return response()->json(['error' => 'Insufficient gift card balance'], 400);
            }

            $before = $giftCard->balance;
            $giftCard->balance = $giftCard->balance - $amountToConsume;
            if ($giftCard->balance <= 0) {
                $giftCard->status = 'redeemed'; // or 'used'
            }
            $giftCard->save();

            // remove hold if provided
            if ($request->filled('hold_id')) {
                $hold = GiftCardHold::find($request->hold_id);
                if ($hold)
                    $hold->delete();
            }

            // transaction log
            GiftCardTransaction::create([
                'gift_card_id' => $giftCard->id,
                'user_id' => $request->user()->id,
                'type' => 'consume',
                'amount' => $amountToConsume,
                'balance_before' => $before,
                'balance_after' => $giftCard->balance,
                'reference' => 'order_' . $request->order_id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'remaining_balance' => number_format($giftCard->balance, 2, '.', '')
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function share(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gift_card_id' => 'required|integer',
            'to_email' => 'required|email',
            'message' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $giftCard = GiftCard::find($request->gift_card_id);
        if (!$giftCard) {
            return response()->json(['error' => 'Gift card not found'], 404);
        }

        // Ensure the caller actually purchased this card earlier (prevents random sharing)
        $hasPurchased = GiftCardTransaction::where('gift_card_id', $giftCard->id)
            ->where('user_id', $request->user()->id)
            ->where('type', 'purchase')
            ->exists();

        if (!$hasPurchased) {
            return response()->json(['error' => 'You cannot share this card because you did not purchase it.'], 403);
        }

        // Prevent sharing inactive/expired cards
        if ($giftCard->status !== 'active') {
            return response()->json(['error' => 'Gift card not active'], 400);
        }
        if ($giftCard->expiry_date && Carbon::now()->gt(Carbon::parse($giftCard->expiry_date))) {
            return response()->json(['error' => 'Gift card expired'], 400);
        }

        // create a single-use token with expiry
        $token = Str::random(40);
        $expiresAt = Carbon::now()->addDays(2);

        $share = GiftCardShare::create([
            'gift_card_id' => $giftCard->id,
            'from_user_id' => $request->user()->id,
            'to_email' => $request->to_email,
            'message' => $request->message,
            'share_token' => $token,
            'status' => 'pending',
            'expires_at' => $expiresAt,
        ]);

        // queue email (optional) — you can implement actual Mail later
        try {
            $acceptUrl = config('app.frontend_url', url('/')) . "/gift/accept?token={$token}";
            $toName = $request->to_email;
            $fromName = $request->user()->f_name ?? 'Customer';
            Mail::to($request->to_email)->send(new GiftCardShareMail($acceptUrl, $toName, $fromName));
        } catch (\Throwable $ex) {
            \Log::error('Failed to queue share email: ' . $ex->getMessage());
        }

        return response()->json([
            'success' => true,
            'share_token' => $token,
            'expires_at' => $expiresAt->toDateTimeString(),
            'message' => 'Share link generated and (optionally) email queued'
        ], 200);
    }

    ///Accept gift card share
    public function acceptShare(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $recipient = $request->user();
        if (!$recipient) {
            return response()->json(['error' => 'Please login to accept the shared gift card'], 401);
        }

        $share = GiftCardShare::where('share_token', $request->token)->first();
        if (!$share || $share->status !== 'pending') {
            return response()->json(['error' => 'Invalid or already used token'], 400);
        }

        // check expiry
        if ($share->expires_at && Carbon::now()->gt(Carbon::parse($share->expires_at))) {
            $share->status = 'expired';
            $share->save();
            return response()->json(['error' => 'This share token has expired'], 400);
        }

        // security: ensure the recipient email matches
        if ($share->to_email && strtolower($share->to_email) !== strtolower($recipient->email)) {
            return response()->json(['error' => 'This share was sent to a different email. Please accept with that account.'], 403);
        }


        // mark accepted (we do NOT change gift_cards.owner_id because you said you don't use it)
        $share->status = 'accepted';
        $share->accepted_at = now();
        $share->to_user_id = $recipient->id;
        $share->save();

        // return the gift card details so recipient can use/apply it (code is the authority)
        $giftCard = GiftCard::find($share->gift_card_id);

        return response()->json([
            'success' => true,
            'message' => 'Share accepted',
            'gift_card' => [
                'id' => $giftCard->id,
                'code' => $giftCard->code,
                'amount' => number_format($giftCard->amount, 2, '.', ''),
                'balance' => number_format($giftCard->balance, 2, '.', ''),
                'expiry_date' => $giftCard->expiry_date,
                'status' => $giftCard->status
            ]
        ], 200);
    }



}
