<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\SupportTickets\CreateSupportTicketAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreSupportTicketRequest;
use App\Http\Resources\Api\V1\SupportTicketResource;
use App\Models\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SupportTicketController extends Controller
{
    /**
     * Display a paginated listing of the authenticated customer's support tickets.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return SupportTicketResource::collection(
            SupportTicket::query()
                ->where('customer_id', $request->user()->id)
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        );
    }

    /**
     * Store a newly created support ticket for the authenticated customer.
     */
    public function store(StoreSupportTicketRequest $request, CreateSupportTicketAction $action): JsonResponse
    {
        $supportTicket = $action->handle(
            [...$request->safe()->except('attachment'), 'customer_id' => $request->user()->id],
            $request->file('attachment'),
        );

        return (new SupportTicketResource($supportTicket))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified support ticket, including the admin's reply, if the caller owns it.
     */
    public function show(Request $request, SupportTicket $supportTicket): SupportTicketResource
    {
        abort_unless($supportTicket->customer_id === $request->user()->id, 404);

        return new SupportTicketResource($supportTicket);
    }
}
