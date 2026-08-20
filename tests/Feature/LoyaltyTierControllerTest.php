<?php

namespace Tests\Feature;

use App\Models\LoyaltyTier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyTierControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_loyalty_tiers_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        LoyaltyTier::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('loyalty-tiers.index'));

        $response->assertOk();
    }

    public function test_loyalty_tier_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('loyalty-tiers.create'));

        $response->assertOk();
    }

    public function test_loyalty_tier_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('loyalty-tiers.store'), [
                'name' => 'Gold',
                'points_required' => '1500',
                'discount_percentage' => '15',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('loyalty-tiers.index'));

        $loyaltyTier = LoyaltyTier::sole();

        $this->assertSame('Gold', $loyaltyTier->name);
        $this->assertSame(1500, $loyaltyTier->points_required);
        $this->assertSame(15, $loyaltyTier->discount_percentage);
        $this->assertTrue($loyaltyTier->status);
    }

    public function test_loyalty_tier_name_must_be_unique_when_created(): void
    {
        $user = User::factory()->create();
        LoyaltyTier::factory()->create(['name' => 'Gold']);

        $response = $this
            ->actingAs($user)
            ->post(route('loyalty-tiers.store'), [
                'name' => 'Gold',
                'points_required' => '1000',
                'discount_percentage' => '10',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_loyalty_tier_discount_percentage_must_not_exceed_100(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('loyalty-tiers.store'), [
                'name' => 'Platinum',
                'points_required' => '3000',
                'discount_percentage' => '150',
            ]);

        $response->assertSessionHasErrors('discount_percentage');
    }

    public function test_loyalty_tier_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $loyaltyTier = LoyaltyTier::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('loyalty-tiers.edit', $loyaltyTier));

        $response->assertOk();
    }

    public function test_loyalty_tier_can_be_updated(): void
    {
        $user = User::factory()->create();
        $loyaltyTier = LoyaltyTier::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('loyalty-tiers.update', $loyaltyTier), [
                'name' => 'Diamond',
                'points_required' => '5000',
                'discount_percentage' => '25',
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('loyalty-tiers.index'));

        $loyaltyTier->refresh();

        $this->assertSame('Diamond', $loyaltyTier->name);
        $this->assertSame(25, $loyaltyTier->discount_percentage);
        $this->assertFalse($loyaltyTier->status);
    }

    public function test_loyalty_tier_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $loyaltyTier = LoyaltyTier::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('loyalty-tiers.destroy', $loyaltyTier));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('loyalty-tiers.index'));

        $this->assertModelMissing($loyaltyTier);
    }
}
