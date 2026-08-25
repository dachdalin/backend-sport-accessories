<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Testimonials\CreateTestimonialAction;
use App\Actions\Testimonials\DeleteTestimonialAction;
use App\Actions\Testimonials\UpdateTestimonialAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreTestimonialRequest;
use App\Http\Requests\Backend\UpdateTestimonialRequest;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class TestimonialController extends Controller
{
    /**
     * Display a listing of the testimonials.
     */
    public function index(): Response
    {
        return Inertia::render('testimonials/Index', [
            'testimonials' => Testimonial::query()->latest()->paginate(15)->withQueryString(),
        ]);
    }

    /**
     * Show the form for creating a new testimonial.
     */
    public function create(): Response
    {
        return Inertia::render('testimonials/Create');
    }

    /**
     * Store a newly created testimonial.
     */
    public function store(StoreTestimonialRequest $request, CreateTestimonialAction $action): RedirectResponse
    {
        $data = $request->safe()->except('avatar');
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data, $request->file('avatar'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the testimonial. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Testimonial created.')]);

        return to_route('testimonials.index');
    }

    /**
     * Show the form for editing the specified testimonial.
     */
    public function edit(Testimonial $testimonial): Response
    {
        return Inertia::render('testimonials/Edit', [
            'testimonial' => $testimonial,
        ]);
    }

    /**
     * Update the specified testimonial.
     */
    public function update(UpdateTestimonialRequest $request, Testimonial $testimonial, UpdateTestimonialAction $action): RedirectResponse
    {
        $data = $request->safe()->except('avatar');
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($testimonial, $data, $request->file('avatar'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the testimonial. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Testimonial updated.')]);

        return to_route('testimonials.index');
    }

    /**
     * Remove the specified testimonial.
     */
    public function destroy(Testimonial $testimonial, DeleteTestimonialAction $action): RedirectResponse
    {
        try {
            $action->handle($testimonial);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the testimonial. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Testimonial deleted.')]);

        return to_route('testimonials.index');
    }
}
