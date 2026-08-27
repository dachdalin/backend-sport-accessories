<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Messages\SendMessageAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreMessageRequest;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class MessageController extends Controller
{
    public function __construct(private readonly MessageService $messageService) {}

    /**
     * Display the conversation list and, if selected, an active thread.
     */
    public function index(Request $request): Response
    {
        $currentUser = $request->user();

        $selectedUser = User::query()
            ->whereKeyNot($currentUser->id)
            ->find($request->integer('user'));

        $messages = [];

        if ($selectedUser) {
            $this->messageService->markRead($currentUser, $selectedUser);
            $messages = $this->messageService->thread($currentUser, $selectedUser);
        }

        return Inertia::render('messages/Index', [
            'conversations' => $this->messageService->conversations($currentUser),
            'selectedUser' => $selectedUser,
            'messages' => $messages,
            'currentUserId' => $currentUser->id,
        ]);
    }

    /**
     * Send a new message.
     */
    public function store(StoreMessageRequest $request, SendMessageAction $action): RedirectResponse
    {
        try {
            $message = $action->handle($request->validated(), $request->user()->id);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not send the message. Please try again.')]);

            return back()->withInput();
        }

        return to_route('messages.index', ['user' => $message->receiver_id]);
    }
}
