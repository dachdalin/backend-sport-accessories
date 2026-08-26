<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CheckGiftCardRequest;
use App\Models\GiftCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class GiftCardController extends Controller
{
    /**
     * Validate a gift card code and return its remaining balance.
     */
    public function check(CheckGiftCardRequest $request): JsonResponse
    {
        $giftCard = GiftCard::query()
            ->where('code', $request->validated('code'))
            ->where('status', true)
            ->first();

        if (! $giftCard) {
            throw ValidationException::withMessages([
                'code' => [__('This gift card code is invalid.')],
            ]);
        }

        if ($giftCard->expires_at && $giftCard->expires_at->isPast()) {
            throw ValidationException::withMessages([
                'code' => [__('This gift card has expired.')],
            ]);
        }

        return response()->json([
            'data' => [
                'code' => $giftCard->code,
                'balance' => $giftCard->balance,
                'expires_at' => $giftCard->expires_at,
            ],
        ]);
    }
}
