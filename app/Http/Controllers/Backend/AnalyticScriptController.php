<?php

namespace App\Http\Controllers\Backend;

use App\Actions\AnalyticScripts\CreateAnalyticScriptAction;
use App\Actions\AnalyticScripts\DeleteAnalyticScriptAction;
use App\Actions\AnalyticScripts\UpdateAnalyticScriptAction;
use App\Enums\AnalyticScriptType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreAnalyticScriptRequest;
use App\Http\Requests\Backend\UpdateAnalyticScriptRequest;
use App\Models\AnalyticScript;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class AnalyticScriptController extends Controller
{
    /**
     * Display a listing of the analytic scripts.
     */
    public function index(): Response
    {
        return Inertia::render('analytic-scripts/Index', [
            'analyticScripts' => AnalyticScript::query()->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new analytic script.
     */
    public function create(): Response
    {
        return Inertia::render('analytic-scripts/Create', [
            'types' => $this->typeOptions(),
        ]);
    }

    /**
     * Store a newly created analytic script.
     */
    public function store(StoreAnalyticScriptRequest $request, CreateAnalyticScriptAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the analytic script. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Analytic script created.')]);

        return to_route('analytic-scripts.index');
    }

    /**
     * Show the form for editing the specified analytic script.
     */
    public function edit(AnalyticScript $analyticScript): Response
    {
        return Inertia::render('analytic-scripts/Edit', [
            'analyticScript' => $analyticScript,
            'types' => $this->typeOptions(),
        ]);
    }

    /**
     * Update the specified analytic script.
     */
    public function update(UpdateAnalyticScriptRequest $request, AnalyticScript $analyticScript, UpdateAnalyticScriptAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($analyticScript, $data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the analytic script. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Analytic script updated.')]);

        return to_route('analytic-scripts.index');
    }

    /**
     * Remove the specified analytic script.
     */
    public function destroy(AnalyticScript $analyticScript, DeleteAnalyticScriptAction $action): RedirectResponse
    {
        try {
            $action->handle($analyticScript);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the analytic script. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Analytic script deleted.')]);

        return to_route('analytic-scripts.index');
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function typeOptions(): array
    {
        return array_map(
            fn (AnalyticScriptType $case) => ['value' => $case->value, 'label' => $case->label()],
            AnalyticScriptType::cases(),
        );
    }
}
