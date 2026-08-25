<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Contacts\CreateContactAction;
use App\Actions\Contacts\DeleteContactAction;
use App\Actions\Contacts\UpdateContactAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreContactRequest;
use App\Http\Requests\Backend\UpdateContactRequest;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ContactController extends Controller
{
    /**
     * Display a listing of the contact messages.
     */
    public function index(): Response
    {
        return Inertia::render('contacts/Index', [
            'contacts' => Contact::query()->latest()->paginate(15)->withQueryString(),
        ]);
    }

    /**
     * Show the form for creating a new contact message.
     */
    public function create(): Response
    {
        return Inertia::render('contacts/Create');
    }

    /**
     * Store a newly created contact message.
     */
    public function store(StoreContactRequest $request, CreateContactAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the contact message. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Contact message created.')]);

        return to_route('contacts.index');
    }

    /**
     * Show the form for editing the specified contact message.
     */
    public function edit(Contact $contact): Response
    {
        return Inertia::render('contacts/Edit', [
            'contact' => $contact,
        ]);
    }

    /**
     * Update the specified contact message.
     */
    public function update(UpdateContactRequest $request, Contact $contact, UpdateContactAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($contact, $data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the contact message. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Contact message updated.')]);

        return to_route('contacts.index');
    }

    /**
     * Remove the specified contact message.
     */
    public function destroy(Contact $contact, DeleteContactAction $action): RedirectResponse
    {
        try {
            $action->handle($contact);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the contact message. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Contact message deleted.')]);

        return to_route('contacts.index');
    }
}
