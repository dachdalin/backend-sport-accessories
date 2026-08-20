<?php

namespace App\Actions\JobOpenings;

use App\Models\JobOpening;
use Illuminate\Support\Facades\DB;

class CreateJobOpeningAction
{
    /**
     * @param  array{title: string, department: ?string, location: ?string, employment_type: string, description: string, status: bool}  $data
     */
    public function handle(array $data): JobOpening
    {
        return DB::transaction(fn () => JobOpening::create($data));
    }
}
