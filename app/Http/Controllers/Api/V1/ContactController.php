<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Contacts\CreateContactAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreContactRequest;
use App\Http\Resources\Api\V1\ContactResource;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    /**
     * Store a newly submitted contact message.
     */
    public function store(StoreContactRequest $request, CreateContactAction $action): JsonResponse
    {
        $data = $request->validated();
        $data['reply'] = null;
        $data['status'] = false;

        $contact = $action->handle($data);

        return (new ContactResource($contact))
            ->response()
            ->setStatusCode(201);
    }
}
