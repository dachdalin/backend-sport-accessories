<?php

namespace Tests\Feature\Api\V1;

use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestimonialControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_testimonials_are_listed(): void
    {
        Testimonial::factory()->count(3)->create(['status' => true]);
        Testimonial::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.testimonials.index'));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'customer_name', 'customer_role', 'content', 'rating', 'avatar_url']]]);
    }

    public function test_testimonial_list_is_paginated(): void
    {
        Testimonial::factory()->count(16)->create(['status' => true]);

        $response = $this->getJson(route('api.v1.testimonials.index'));

        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function test_active_testimonial_can_be_shown(): void
    {
        $testimonial = Testimonial::factory()->create(['status' => true]);

        $response = $this->getJson(route('api.v1.testimonials.show', $testimonial));

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $testimonial->id)
            ->assertJsonPath('data.customer_name', $testimonial->customer_name);
    }

    public function test_inactive_testimonial_is_not_found(): void
    {
        $testimonial = Testimonial::factory()->create(['status' => false]);

        $response = $this->getJson(route('api.v1.testimonials.show', $testimonial));

        $response->assertNotFound();
    }

    public function test_missing_testimonial_is_not_found(): void
    {
        $response = $this->getJson(route('api.v1.testimonials.show', 999999));

        $response->assertNotFound();
    }
}
