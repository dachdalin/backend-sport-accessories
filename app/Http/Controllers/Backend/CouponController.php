<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Coupons\CreateCouponAction;
use App\Actions\Coupons\DeleteCouponAction;
use App\Actions\Coupons\UpdateCouponAction;
use App\Enums\CouponType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreCouponRequest;
use App\Http\Requests\Backend\UpdateCouponRequest;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class CouponController extends Controller
{
    /**
     * Display a listing of the coupons.
     */
    public function index(): Response
    {
        return Inertia::render('coupons/Index', [
            'coupons' => Coupon::query()->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new coupon.
     */
    public function create(): Response
    {
        return Inertia::render('coupons/Create', [
            'types' => $this->typeOptions(),
        ]);
    }

    /**
     * Store a newly created coupon.
     */
    public function store(StoreCouponRequest $request, CreateCouponAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the coupon. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Coupon created.')]);

        return to_route('coupons.index');
    }

    /**
     * Show the form for editing the specified coupon.
     */
    public function edit(Coupon $coupon): Response
    {
        return Inertia::render('coupons/Edit', [
            'coupon' => $coupon,
            'types' => $this->typeOptions(),
        ]);
    }

    /**
     * Update the specified coupon.
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon, UpdateCouponAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($coupon, $data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the coupon. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Coupon updated.')]);

        return to_route('coupons.index');
    }

    /**
     * Remove the specified coupon.
     */
    public function destroy(Coupon $coupon, DeleteCouponAction $action): RedirectResponse
    {
        try {
            $action->handle($coupon);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the coupon. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Coupon deleted.')]);

        return to_route('coupons.index');
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function typeOptions(): array
    {
        return array_map(
            fn (CouponType $case) => ['value' => $case->value, 'label' => $case->label()],
            CouponType::cases(),
        );
    }
}
