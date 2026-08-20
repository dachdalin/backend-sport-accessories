<?php

namespace Tests\Feature;

use App\Models\AnalyticScript;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticScriptControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_analytic_scripts_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        AnalyticScript::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('analytic-scripts.index'));

        $response->assertOk();
    }

    public function test_analytic_script_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('analytic-scripts.create'));

        $response->assertOk();
    }

    public function test_analytic_script_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('analytic-scripts.store'), [
                'name' => 'Google Analytics 4',
                'type' => 'google_analytics',
                'script_id' => 'G-XXXXXXXXXX',
                'script' => '<script>gtag();</script>',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('analytic-scripts.index'));

        $analyticScript = AnalyticScript::sole();

        $this->assertSame('Google Analytics 4', $analyticScript->name);
        $this->assertTrue($analyticScript->status);
    }

    public function test_analytic_script_name_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('analytic-scripts.store'), [
                'name' => '',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_analytic_script_type_must_be_valid(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('analytic-scripts.store'), [
                'name' => 'Bad Tracker',
                'type' => 'not_a_real_type',
                'script' => '<script></script>',
            ]);

        $response->assertSessionHasErrors('type');
    }

    public function test_analytic_script_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $analyticScript = AnalyticScript::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('analytic-scripts.edit', $analyticScript));

        $response->assertOk();
    }

    public function test_analytic_script_can_be_updated(): void
    {
        $user = User::factory()->create();
        $analyticScript = AnalyticScript::factory()->create(['status' => false]);

        $response = $this
            ->actingAs($user)
            ->put(route('analytic-scripts.update', $analyticScript), [
                'name' => 'Updated Tracker',
                'type' => 'facebook_pixel',
                'script' => $analyticScript->script,
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('analytic-scripts.index'));

        $analyticScript->refresh();

        $this->assertSame('Updated Tracker', $analyticScript->name);
        $this->assertTrue($analyticScript->status);
    }

    public function test_analytic_script_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $analyticScript = AnalyticScript::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('analytic-scripts.destroy', $analyticScript));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('analytic-scripts.index'));

        $this->assertModelMissing($analyticScript);
    }
}
