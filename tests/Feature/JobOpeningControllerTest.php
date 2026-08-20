<?php

namespace Tests\Feature;

use App\Models\JobOpening;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobOpeningControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_openings_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        JobOpening::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('job-openings.index'));

        $response->assertOk();
    }

    public function test_job_opening_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('job-openings.create'));

        $response->assertOk();
    }

    public function test_job_opening_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('job-openings.store'), [
                'title' => 'Warehouse Associate',
                'department' => 'Warehouse',
                'location' => 'Manchester',
                'employment_type' => 'full_time',
                'description' => 'Pick, pack, and ship customer orders.',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('job-openings.index'));

        $jobOpening = JobOpening::sole();

        $this->assertSame('Warehouse Associate', $jobOpening->title);
        $this->assertTrue($jobOpening->status);
    }

    public function test_job_opening_title_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('job-openings.store'), [
                'title' => '',
            ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_job_opening_employment_type_must_be_valid(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('job-openings.store'), [
                'title' => 'Store Manager',
                'employment_type' => 'freelance',
                'description' => 'Manage the retail store.',
            ]);

        $response->assertSessionHasErrors('employment_type');
    }

    public function test_job_opening_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $jobOpening = JobOpening::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('job-openings.edit', $jobOpening));

        $response->assertOk();
    }

    public function test_job_opening_can_be_updated(): void
    {
        $user = User::factory()->create();
        $jobOpening = JobOpening::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('job-openings.update', $jobOpening), [
                'title' => 'Senior Warehouse Associate',
                'employment_type' => 'part_time',
                'description' => $jobOpening->description,
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('job-openings.index'));

        $jobOpening->refresh();

        $this->assertSame('Senior Warehouse Associate', $jobOpening->title);
        $this->assertFalse($jobOpening->status);
    }

    public function test_job_opening_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $jobOpening = JobOpening::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('job-openings.destroy', $jobOpening));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('job-openings.index'));

        $this->assertModelMissing($jobOpening);
    }
}
