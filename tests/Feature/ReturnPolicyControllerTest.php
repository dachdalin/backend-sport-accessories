<?php

namespace Tests\Feature;

use App\Models\ReturnPolicy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReturnPolicyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_return_policies_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        ReturnPolicy::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('return-policies.index'));

        $response->assertOk();
    }

    public function test_return_policy_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('return-policies.store'), [
                'title' => 'Standard Returns',
                'description' => 'Items can be returned within 30 days of purchase.',
                'days_allowed' => '30',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('return-policies.index'));

        $returnPolicy = ReturnPolicy::sole();

        $this->assertSame('Standard Returns', $returnPolicy->title);
        $this->assertSame(30, $returnPolicy->days_allowed);
        $this->assertTrue($returnPolicy->status);
    }

    public function test_return_policy_title_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('return-policies.store'), [
                'title' => '',
                'description' => 'Some description.',
            ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_return_policy_days_allowed_must_be_within_range(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('return-policies.store'), [
                'title' => 'Extended Returns',
                'description' => 'Some description.',
                'days_allowed' => '400',
            ]);

        $response->assertSessionHasErrors('days_allowed');
    }

    public function test_return_policy_can_be_updated(): void
    {
        $user = User::factory()->create();
        $returnPolicy = ReturnPolicy::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('return-policies.update', $returnPolicy), [
                'title' => 'Extended Returns',
                'description' => $returnPolicy->description,
                'days_allowed' => '60',
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('return-policies.index'));

        $returnPolicy->refresh();

        $this->assertSame('Extended Returns', $returnPolicy->title);
        $this->assertSame(60, $returnPolicy->days_allowed);
        $this->assertFalse($returnPolicy->status);
    }

    public function test_return_policy_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $returnPolicy = ReturnPolicy::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('return-policies.destroy', $returnPolicy));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('return-policies.index'));

        $this->assertModelMissing($returnPolicy);
    }
}
