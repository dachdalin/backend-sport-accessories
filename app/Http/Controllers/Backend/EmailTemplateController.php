<?php

namespace App\Http\Controllers\Backend;

use App\Actions\EmailTemplates\CreateEmailTemplateAction;
use App\Actions\EmailTemplates\DeleteEmailTemplateAction;
use App\Actions\EmailTemplates\UpdateEmailTemplateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreEmailTemplateRequest;
use App\Http\Requests\Backend\UpdateEmailTemplateRequest;
use App\Models\EmailTemplate;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the email templates.
     */
    public function index(): Response
    {
        return Inertia::render('email-templates/Index', [
            'emailTemplates' => EmailTemplate::query()->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new email template.
     */
    public function create(): Response
    {
        return Inertia::render('email-templates/Create');
    }

    /**
     * Store a newly created email template.
     */
    public function store(StoreEmailTemplateRequest $request, CreateEmailTemplateAction $action): RedirectResponse
    {
        try {
            $action->handle($request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the email template. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Email template created.')]);

        return to_route('email-templates.index');
    }

    /**
     * Show the form for editing the specified email template.
     */
    public function edit(EmailTemplate $emailTemplate): Response
    {
        return Inertia::render('email-templates/Edit', [
            'emailTemplate' => $emailTemplate,
        ]);
    }

    /**
     * Update the specified email template.
     */
    public function update(UpdateEmailTemplateRequest $request, EmailTemplate $emailTemplate, UpdateEmailTemplateAction $action): RedirectResponse
    {
        try {
            $action->handle($emailTemplate, $request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the email template. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Email template updated.')]);

        return to_route('email-templates.index');
    }

    /**
     * Remove the specified email template.
     */
    public function destroy(EmailTemplate $emailTemplate, DeleteEmailTemplateAction $action): RedirectResponse
    {
        try {
            $action->handle($emailTemplate);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the email template. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Email template deleted.')]);

        return to_route('email-templates.index');
    }
}
