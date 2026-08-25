<?php

namespace Tests\Feature;

use App\Models\FeatureDeal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FeatureDealControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_feature_deals_index_page_is_displayed(): void
    {
        $user = User::factory()->create();
        FeatureDeal::factory()->count(3)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('feature-deals.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('feature-deals/Index')
                ->has('featureDeals.data', 3),
            );
    }

    public function test_feature_deals_index_page_is_paginated(): void
    {
        $user = User::factory()->create();
        FeatureDeal::factory()->count(16)->create();

        $response = $this
            ->actingAs($user)
            ->get(route('feature-deals.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('feature-deals/Index')
                ->has('featureDeals.data', 15),
            );
    }

    public function test_feature_deal_create_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('feature-deals.create'));

        $response->assertOk();
    }

    public function test_feature_deal_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('feature-deals.store'), [
                'url' => 'https://example.com/summer-sale',
                'status' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('feature-deals.index'));

        $featureDeal = FeatureDeal::sole();

        $this->assertSame('https://example.com/summer-sale', $featureDeal->url);
        $this->assertSame('def.png', $featureDeal->photo);
        $this->assertTrue($featureDeal->status);
    }

    public function test_feature_deal_url_must_be_a_valid_url(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('feature-deals.store'), [
                'url' => 'not-a-url',
            ]);

        $response->assertSessionHasErrors('url');
    }

    public function test_feature_deal_edit_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $featureDeal = FeatureDeal::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('feature-deals.edit', $featureDeal));

        $response->assertOk();
    }

    public function test_feature_deal_can_be_updated(): void
    {
        $user = User::factory()->create();
        $featureDeal = FeatureDeal::factory()->create(['status' => true]);

        $response = $this
            ->actingAs($user)
            ->put(route('feature-deals.update', $featureDeal), [
                'url' => 'https://example.com/winter-sale',
                'status' => '0',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('feature-deals.index'));

        $featureDeal->refresh();

        $this->assertSame('https://example.com/winter-sale', $featureDeal->url);
        $this->assertFalse($featureDeal->status);
    }

    public function test_feature_deal_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $featureDeal = FeatureDeal::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('feature-deals.destroy', $featureDeal));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('feature-deals.index'));

        $this->assertModelMissing($featureDeal);
    }

    public function test_guest_cannot_access_feature_deals(): void
    {
        $response = $this->get(route('feature-deals.index'));

        $response->assertRedirect(route('login'));
    }
}
