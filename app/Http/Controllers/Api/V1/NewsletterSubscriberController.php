<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\NewsletterSubscribers\CreateNewsletterSubscriberAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreNewsletterSubscriberRequest;
use App\Http\Resources\Api\V1\NewsletterSubscriberResource;
use Illuminate\Http\JsonResponse;

class NewsletterSubscriberController extends Controller
{
    /**
     * Store a newly subscribed newsletter subscriber.
     */
    public function store(StoreNewsletterSubscriberRequest $request, CreateNewsletterSubscriberAction $action): JsonResponse
    {
        $subscriber = $action->handle($request->validated());

        return (new NewsletterSubscriberResource($subscriber))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }
}
