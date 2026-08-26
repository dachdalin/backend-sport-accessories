<?php

namespace Tests\Feature\Api\V1;

use App\Models\JobOpening;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobOpeningControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_job_openings_are_listed(): void
    {
        JobOpening::factory()->count(3)->create(['status' => true]);
        JobOpening::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.job-openings.index'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'title', 'department', 'location', 'employment_type', 'description']]]);
    }

    public function test_job_opening_list_is_paginated(): void
    {
        JobOpening::factory()->count(16)->create(['status' => true]);

        $response = $this->getJson(route('api.v1.job-openings.index'));

        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function test_active_job_opening_can_be_shown(): void
    {
        $jobOpening = JobOpening::factory()->create(['status' => true]);

        $response = $this->getJson(route('api.v1.job-openings.show', $jobOpening));

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $jobOpening->id)
            ->assertJsonPath('data.title', $jobOpening->title);
    }

    public function test_inactive_job_opening_is_not_found(): void
    {
        $jobOpening = JobOpening::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.job-openings.show', $jobOpening));

        $response->assertNotFound();
    }

    public function test_missing_job_opening_is_not_found(): void
    {
        $response = $this->getJson(route('api.v1.job-openings.show', 999999));

        $response->assertNotFound();
    }
}
