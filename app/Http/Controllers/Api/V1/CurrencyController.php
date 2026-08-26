<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CurrencyResource;
use App\Models\Currency;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CurrencyController extends Controller
{
    /**
     * Display a paginated listing of the active currencies.
     */
    public function index(): AnonymousResourceCollection
    {
        return CurrencyResource::collection(
            Currency::query()->where('status', true)->orderBy('code')->paginate(15)->withQueryString(),
        );
    }

    /**
     * Display the specified active currency.
     */
    public function show(Currency $currency): CurrencyResource
    {
        abort_unless($currency->status, 404);

        return new CurrencyResource($currency);
    }
}
