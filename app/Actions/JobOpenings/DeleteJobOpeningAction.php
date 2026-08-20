<?php

namespace App\Actions\JobOpenings;

use App\Models\JobOpening;
use Illuminate\Support\Facades\DB;

class DeleteJobOpeningAction
{
    public function handle(JobOpening $jobOpening): void
    {
        DB::transaction(function () use ($jobOpening) {
            $jobOpening->delete();
        });
    }
}
