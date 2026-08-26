<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\JobOpeningResource;
use App\Models\JobOpening;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JobOpeningController extends Controller
{
    /**
     * Display a paginated listing of the active job openings.
     */
    public function index(): AnonymousResourceCollection
    {
        return JobOpeningResource::collection(
            JobOpening::query()->where('status', true)->latest()->paginate(15)->withQueryString(),
        );
    }

    /**
     * Display the specified active job opening.
     */
    public function show(JobOpening $jobOpening): JobOpeningResource
    {
        abort_unless($jobOpening->status, 404);

        return new JobOpeningResource($jobOpening);
    }
}
