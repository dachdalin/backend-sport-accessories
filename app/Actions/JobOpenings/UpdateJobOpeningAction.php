<?php

namespace App\Actions\JobOpenings;

use App\Models\JobOpening;
use Illuminate\Support\Facades\DB;

class UpdateJobOpeningAction
{
    /**
     * @param  array{title: string, department: ?string, location: ?string, employment_type: string, description: string, status: bool}  $data
     */
    public function handle(JobOpening $jobOpening, array $data): JobOpening
    {
        return DB::transaction(function () use ($jobOpening, $data) {
            $jobOpening->update($data);

            return $jobOpening;
        });
    }
}
