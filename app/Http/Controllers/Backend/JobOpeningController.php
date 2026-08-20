<?php

namespace App\Http\Controllers\Backend;

use App\Actions\JobOpenings\CreateJobOpeningAction;
use App\Actions\JobOpenings\DeleteJobOpeningAction;
use App\Actions\JobOpenings\UpdateJobOpeningAction;
use App\Enums\EmploymentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreJobOpeningRequest;
use App\Http\Requests\Backend\UpdateJobOpeningRequest;
use App\Models\JobOpening;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class JobOpeningController extends Controller
{
    /**
     * Display a listing of the job openings.
     */
    public function index(): Response
    {
        return Inertia::render('job-openings/Index', [
            'jobOpenings' => JobOpening::query()->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new job opening.
     */
    public function create(): Response
    {
        return Inertia::render('job-openings/Create', [
            'employmentTypes' => $this->employmentTypeOptions(),
        ]);
    }

    /**
     * Store a newly created job opening.
     */
    public function store(StoreJobOpeningRequest $request, CreateJobOpeningAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the job opening. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Job opening created.')]);

        return to_route('job-openings.index');
    }

    /**
     * Show the form for editing the specified job opening.
     */
    public function edit(JobOpening $jobOpening): Response
    {
        return Inertia::render('job-openings/Edit', [
            'jobOpening' => $jobOpening,
            'employmentTypes' => $this->employmentTypeOptions(),
        ]);
    }

    /**
     * Update the specified job opening.
     */
    public function update(UpdateJobOpeningRequest $request, JobOpening $jobOpening, UpdateJobOpeningAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($jobOpening, $data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the job opening. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Job opening updated.')]);

        return to_route('job-openings.index');
    }

    /**
     * Remove the specified job opening.
     */
    public function destroy(JobOpening $jobOpening, DeleteJobOpeningAction $action): RedirectResponse
    {
        try {
            $action->handle($jobOpening);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the job opening. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Job opening deleted.')]);

        return to_route('job-openings.index');
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function employmentTypeOptions(): array
    {
        return array_map(
            fn (EmploymentType $case) => ['value' => $case->value, 'label' => $case->label()],
            EmploymentType::cases(),
        );
    }
}
