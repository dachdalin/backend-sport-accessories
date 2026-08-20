<?php

namespace App\Http\Controllers\Backend;

use App\Actions\GiftCards\CreateGiftCardAction;
use App\Actions\GiftCards\DeleteGiftCardAction;
use App\Actions\GiftCards\UpdateGiftCardAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreGiftCardRequest;
use App\Http\Requests\Backend\UpdateGiftCardRequest;
use App\Models\GiftCard;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class GiftCardController extends Controller
{
    /**
     * Display a listing of the gift cards.
     */
    public function index(): Response
    {
        return Inertia::render('gift-cards/Index', [
            'giftCards' => GiftCard::query()->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new gift card.
     */
    public function create(): Response
    {
        return Inertia::render('gift-cards/Create');
    }

    /**
     * Store a newly created gift card.
     */
    public function store(StoreGiftCardRequest $request, CreateGiftCardAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the gift card. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Gift card created.')]);

        return to_route('gift-cards.index');
    }

    /**
     * Show the form for editing the specified gift card.
     */
    public function edit(GiftCard $giftCard): Response
    {
        return Inertia::render('gift-cards/Edit', [
            'giftCard' => $giftCard,
        ]);
    }

    /**
     * Update the specified gift card.
     */
    public function update(UpdateGiftCardRequest $request, GiftCard $giftCard, UpdateGiftCardAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($giftCard, $data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the gift card. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Gift card updated.')]);

        return to_route('gift-cards.index');
    }

    /**
     * Remove the specified gift card.
     */
    public function destroy(GiftCard $giftCard, DeleteGiftCardAction $action): RedirectResponse
    {
        try {
            $action->handle($giftCard);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the gift card. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Gift card deleted.')]);

        return to_route('gift-cards.index');
    }
}
