<?php

namespace App\Http\Controllers\Backend;

use App\Actions\SupportTickets\CreateSupportTicketAction;
use App\Actions\SupportTickets\DeleteSupportTicketAction;
use App\Actions\SupportTickets\UpdateSupportTicketAction;
use App\Enums\SupportTicketPriority;
use App\Enums\SupportTicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreSupportTicketRequest;
use App\Http\Requests\Backend\UpdateSupportTicketRequest;
use App\Models\Customer;
use App\Models\SupportTicket;
use App\Services\SupportTicketService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class SupportTicketController extends Controller
{
    public function __construct(private readonly SupportTicketService $supportTicketService) {}

    /**
     * Display a listing of the support tickets.
     */
    public function index(): Response
    {
        return Inertia::render('support-tickets/Index', [
            'supportTickets' => $this->supportTicketService->list(),
        ]);
    }

    /**
     * Show the form for creating a new support ticket.
     */
    public function create(): Response
    {
        return Inertia::render('support-tickets/Create', [
            'customers' => $this->customerOptions(),
            'priorities' => $this->priorityOptions(),
            'statuses' => $this->statusOptions(),
        ]);
    }

    /**
     * Store a newly created support ticket.
     */
    public function store(StoreSupportTicketRequest $request, CreateSupportTicketAction $action): RedirectResponse
    {
        try {
            $action->handle($request->safe()->except('attachment'), $request->file('attachment'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the support ticket. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Support ticket created.')]);

        return to_route('support-tickets.index');
    }

    /**
     * Show the form for editing the specified support ticket.
     */
    public function edit(SupportTicket $supportTicket): Response
    {
        return Inertia::render('support-tickets/Edit', [
            'supportTicket' => $supportTicket,
            'customers' => $this->customerOptions(),
            'priorities' => $this->priorityOptions(),
            'statuses' => $this->statusOptions(),
        ]);
    }

    /**
     * Update the specified support ticket.
     */
    public function update(UpdateSupportTicketRequest $request, SupportTicket $supportTicket, UpdateSupportTicketAction $action): RedirectResponse
    {
        try {
            $action->handle($supportTicket, $request->safe()->except('attachment'), $request->file('attachment'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the support ticket. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Support ticket updated.')]);

        return to_route('support-tickets.index');
    }

    /**
     * Remove the specified support ticket.
     */
    public function destroy(SupportTicket $supportTicket, DeleteSupportTicketAction $action): RedirectResponse
    {
        try {
            $action->handle($supportTicket);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the support ticket. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Support ticket deleted.')]);

        return to_route('support-tickets.index');
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function customerOptions(): array
    {
        return Customer::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Customer $customer) => ['value' => $customer->id, 'label' => $customer->name])
            ->all();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function priorityOptions(): array
    {
        return array_map(
            fn (SupportTicketPriority $case) => ['value' => $case->value, 'label' => $case->label()],
            SupportTicketPriority::cases(),
        );
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function statusOptions(): array
    {
        return array_map(
            fn (SupportTicketStatus $case) => ['value' => $case->value, 'label' => $case->label()],
            SupportTicketStatus::cases(),
        );
    }
}
